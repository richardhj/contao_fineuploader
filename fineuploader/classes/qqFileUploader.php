<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package FineUploader
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Namespace
 */
namespace FineUploader;


/**
 * Class qqFileUploader
 *
 * Handle Fine Uploader request
 * Extended and adapted for Contao 3.1, using File() to keep the database up to date
 *
 * @package FineUploader
 * @author  Ray Nicholus
 * @author  Richard Henkenjohann
 * @link    https://github.com/Widen/fine-uploader-server/tree/master/php
 * @licence MIT
 */
class qqFileUploader
{
	/**
	 * Allowed extensions
	 * @var array
	 */
	public $allowedExtensions = array();

	/**
	 * Size limit
	 * @var int
	 */
	public $sizeLimit = null;

	/**
	 * Input name
	 * @var string
	 */
	public $inputName = 'qqfile';

	/**
	 * Chunks folder
	 * @var string
	 */
	public $chunksFolder = 'chunks';

	/**
	 * Probability for chunks cleanup
	 * @var float
	 */
	public $chunksCleanupProbability = 0.001; // Once in 1000 requests on average

	/**
	 * Chunks expiry
	 * @var int
	 */
	public $chunksExpireIn = 604800; // One week

	/**
	 * Upload name
	 * @var string
	 */
	protected $uploadName;

	/**
	 * Construct
	 */
	function __construct()
	{
		$this->sizeLimit = $this->toBytes(ini_get('upload_max_filesize'));
	}

	/**
	 * Get the original filename
	 * @return string Filename
	 */
	public function getName()
	{
		$name = 'uploaded_file';

		if (isset($_REQUEST['qqfilename']))
		{
			$name =  $_REQUEST['qqfilename'];
		}

		if (isset($_FILES[$this->inputName]))
		{
			$name = $_FILES[$this->inputName]['name'];
		}

		// Use the date as filename if required but skip if the name already has this format
		if (isset($_FILES[$this->inputName]) && $this->useExifDate && !is_numeric(substr($_FILES[$this->inputName]['name'], 0, 8)))
		{
			$filePathinfo = pathinfo($_FILES[$this->inputName]['name']);
			$fileExifData = exif_read_data($_FILES[$this->inputName]['tmp_name']);

			if ($fileExifData['DateTimeOriginal'])
			{
				$fileTimestamp = strtotime($fileExifData['DateTimeOriginal']);
			}
			elseif ($fileExifData['DateTimeDigitized'])
			{
				$fileTimestamp = strtotime($fileExifData['DateTimeDigitized']);
			}
			// DateTime at last, it could be modified
			else
			{
				$fileTimestamp = strtotime($fileExifData['DateTime']);
			}

			$name = date('Ymd_His', $fileTimestamp) .'.'. strtolower($filePathinfo['extension']);
		}

		return $name;
	}

	/**
	 * Get the name of the uploaded file
	 * @return string Upload name
	 */
	public function getUploadName()
	{
		return $this->uploadName;
	}

	/**
	 * Process the upload.
	 * @param string $uploadDirectory Target directory
	 * @param string $name Overwrites the name of the file
	 * @return array Error/success message used for json_encode()
	 */
	public function handleUpload($uploadDirectory, $name = null)
	{
		// Cleanup chunks folder if chunksCleanupProbability matches
		if (is_writable($this->chunksFolder) && 1 == mt_rand(1, 1 / $this->chunksCleanupProbability))
		{
			$objChunksFolder = new \Folder($this->chunksFolder);
			$objChunksFolder->purge();

			// Add a log entry
			\System::log('Chunks folder "' . $this->chunksFolder . '" purged.', 'qqFileUploader handleUpload()', TL_FILES);
		}

		// Check that the max upload size specified in class configuration does not exceed size allowed by server config
		if ($this->toBytes(ini_get('post_max_size')) < $this->sizeLimit ||
			$this->toBytes(ini_get('upload_max_filesize')) < $this->sizeLimit)
		{
			$size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
			return array('error' => "Server error. Increase post_max_size and upload_max_filesize to ". $size);

			// Add a log entry
			\System::log('Increase post_max_size and upload_max_filesize to "'. $size .'".', 'qqFileUploader handleUpload()', TL_ERROR);
		}

		// is_writable() is not reliable on Windows (http://www.php.net/manual/en/function.is-executable.php#111146)
		// The following tests if the current OS is Windows and if so, merely checks if the folder is writable;
		// otherwise, it checks additionally for executable status (like before).
		$isWin = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
		$folderInaccessible = ($isWin) ? !is_writable($uploadDirectory) : (!is_writable($uploadDirectory) && !is_executable($uploadDirectory));

		if ($folderInaccessible)
		{
			return array('error' => "Server error. Uploads directory isn't writable" . (!$isWin) ? " or executable." : ".");

			// Add a log entry
			\System::log('Uploads directory isn\'t writable or executable.', 'qqFileUploader handleUpload()', TL_ERROR);
		}

		if (!isset($_SERVER['CONTENT_TYPE']))
		{
			return array('error' => "No files were uploaded.");
		}
		elseif (strpos(strtolower($_SERVER['CONTENT_TYPE']), 'multipart/') !== 0)
		{
			return array('error' => "Server error. Not a multipart request. Please set forceMultipart to default value (true).");

			// Add a log entry
			\System::log('Not a multipart request. Please set forceMultipart to default value (true).', 'qqFileUploader handleUpload()', TL_ERROR);
		}

		// Get size and name
		$file = $_FILES[$this->inputName];
		$size = $file['size'];

		if ($name === null)
		{
			$name = $this->getName();
		}

		// Validate name
		if ($name === null || $name === '')
		{
			return array('error' => 'File name empty.');
		}

		// Validate file size
		if ($size == 0)
		{
			return array('error' => 'File is empty.');
		}

		if ($size > $this->sizeLimit)
		{
			return array('error' => 'File is too large.');
		}

		// Validate file extension
		$pathinfo = pathinfo($name);
		$ext = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';

		if ($this->allowedExtensions && !in_array(strtolower($ext), array_map("strtolower", $this->allowedExtensions)))
		{
			$these = implode(', ', $this->allowedExtensions);
			return array('error' => 'File has an invalid extension, it should be one of ' . $these . '.');
		}

		// Save a chunk
		$totalParts = isset($_REQUEST['qqtotalparts']) ? (int)$_REQUEST['qqtotalparts'] : 1;

		if ($totalParts > 1)
		{
			$partIndex = (int)$_REQUEST['qqpartindex'];

			if (!is_writable($this->chunksFolder) && !is_executable($uploadDirectory))
			{
				return array('error' => "Server error. Chunks directory isn't writable or executable.");
			}

			$targetFolder = $this->chunksFolder .'/'. $_REQUEST['qquuid'];

			if (!file_exists($targetFolder))
			{
				mkdir($targetFolder);
			}

			$target = str_replace(TL_ROOT .'/', '', $targetFolder .'/'. $partIndex);

			// Last chunk saved successfully
			if (\File::putContent($target, file_get_contents($file['tmp_name'])) && ($totalParts - 1 == $partIndex))
			{
				$target = $this->getUniqueTargetPath($uploadDirectory, $name);
				$this->uploadName = basename($target);

				for ($i = 0; $i < $totalParts; $i++)
				{
					$chunk = fopen($targetFolder .'/'. $i, "rb");
					\File::putContent($target, file_get_contents($chunk));
					fclose($chunk);
				}

				for ($i = 0; $i < $totalParts; $i++)
				{
					unlink($targetFolder .'/'. $i);
				}

				rmdir($targetFolder);

				return array("success" => true);

			}

			// Add a log entry
			\System::log('File "'. $target .'" has been uploaded', 'qqFileUploader handleUpload()', TL_FILES);

			return array("success" => true);

		}
		else
		{
			$target = str_replace(TL_ROOT .'/', '', $this->getUniqueTargetPath($uploadDirectory, $name));

			if ($target)
			{
				\File::putContent($target, file_get_contents($file['tmp_name']));

				// Add a log entry
				\System::log('File "'. $target .'" has been uploaded', 'qqFileUploader handleUpload()', TL_FILES);

				return array('success' => true);
			}

			return array('error' => 'Could not save uploaded file.' .
				'The upload was cancelled, or server error encountered');
		}
	}

	/**
	 * Returns a path to use with this upload. Check that the name does not exist,
	 * and appends a suffix otherwise.
	 * @param string $uploadDirectory Target directory
	 * @param string $filename The name of the file to use.
	 * @return bool|string Path used for upload
	 */
	protected function getUniqueTargetPath($uploadDirectory, $filename)
	{
		$lock = 0;

		// Allow only one process at the time to get a unique file name, otherwise
		// if multiple people would upload a file with the same name at the same time
		// only the latest would be saved.
		if (function_exists('sem_acquire'))
		{
			$lock = sem_get(ftok(__FILE__, 'u'));
			sem_acquire($lock);
		}

		$pathinfo = pathinfo($filename);
		$base = $pathinfo['filename'];
		$ext = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
		$ext = $ext == '' ? $ext : '.'. $ext;

		$unique = $base;
		$suffix = 0;

		// Get unique file name for the file, by appending random suffix.
		while (file_exists($uploadDirectory .'/'. $unique . $ext))
		{
			$suffix += rand(1, 999);
			$unique = $base . '-' . $suffix;
		}

		$result = $uploadDirectory .'/'. $unique . $ext;

		// Create an empty target file
		if (!touch($result))
		{
			// Failed
			$result = false;
		}

		if (function_exists('sem_acquire'))
		{
			sem_release($lock);
		}

		return $result;
	}

	/**
	 * Converts a given size with units to bytes.
	 * @param string $str
	 * @return int|string Size in bytes
	 */
	protected function toBytes($str)
	{
		$val = trim($str);
		$last = strtolower($str[strlen($str) - 1]);
		switch ($last)
		{
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		return $val;
	}
}
