<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   FineUploader
 * @author    Richard Henkenjohann
 * @license   LGPL
 * @copyright Richard Henkenjohann 2013
 */


/**
 * Namespace
 */
namespace FineUploader;


/**
 * Initialize the system
 */
define('TL_MODE', 'FE');
require '../../../initialize.php';

/**
 * Load module settings
 */
$moduleId = \Input::get('moduleId');
$pageId = \Input::get('pageId');

if (is_numeric($moduleId) && is_numeric($pageId))
{
	$objModule = \Database::getInstance()->prepare("SELECT type,fu_uploadPath,fu_useSubfolder,fu_provideZipDownload
													FROM tl_module
													WHERE id=?")
										 ->execute($moduleId);
}
else
{
	die ('Unexpected or missing module/page id.');
}

// Someone deceived the module
if ($objModule->type != 'fineuploader' || !$objModule->fu_provideZipDownload)
{
	die ('This folder is not intended for download.');
}

/**
 * Define variables
 */
$pageAlias = \PageModel::findWithDetails($pageId)->alias;

$objUploadPath = \FilesModel::findByPk($objModule->fu_uploadPath);
$objUploadPathSubdirectory = new \Folder($objUploadPath->path . '/' . $pageAlias);

if (!$objModule->fu_useSubfolder)
{
	$strDownloadPath = $objUploadPath->path;
}
else
{
	$strDownloadPath = $objUploadPathSubdirectory->path;
}

$arrFiles = scandir(TL_ROOT . '/' . $strDownloadPath);

$strZipPath = 'system/tmp/fineuploader/' . $pageAlias . '.zip';

if (!file_exists(TL_ROOT . '/' . $strZipPath) ||
	(file_exists(TL_ROOT . '/' . $strZipPath) && filemtime(TL_ROOT . '/' . $strDownloadPath) > filemtime(TL_ROOT . '/' . $strZipPath)))
{
	// The folder has been changed
	// Generate the zip archive
	$zip = new \ZipWriter($strZipPath);

	foreach ($arrFiles as $file)
	{
		if ($file == '.' || $file == '..')
		{
			continue;
		}

		// Add file but ignore path
		$zip->addFile($strDownloadPath . '/' . $file, $file);
	}

	$zip->close();

	// Add a log entry
	\System::log('Generated zip archive for page "' . $pageAlias . '"', 'handleZipDownload()', TL_FILES);
}

header('Content-type: application/zip');
header('Content-Disposition: attachment; filename="' . $pageAlias . '"');
header('Content-Length: ' . filesize(TL_ROOT . '/' . $strZipPath));

readfile(TL_ROOT . '/' . $strZipPath);

// Add a log entry
\System::log('Existing zip archive for page "' . $pageAlias . '" was downloaded', 'handleZipDownload()', TL_FILES);
