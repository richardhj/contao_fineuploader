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
define('BYPASS_TOKEN_CHECK', true); /* Necessary because of last point in initialize.php */
require '../../../initialize.php';
header('Content-Type: text/plain');

/**
 * Load module settings
 */
$intModuleId = \Input::get('moduleId');
$intPageId = \Input::get('pageId');

if (is_numeric($intModuleId) && is_numeric($intPageId))
{
	$objModule = \Database::getInstance()->prepare("SELECT fu_uploadPath,fu_useSubfolder,fu_allowedExtensions,fu_sizeLimit,fu_useExifDate
													FROM tl_module
													WHERE id=?")
										->execute($intModuleId);
}
else
{
	die ('Unexpected or missing module/page id.');
}

// Define variables
$objUploadPath = \FilesModel::findByPk($objModule->fu_uploadPath);
$objUploadPathSubdirectory = new \Folder($objUploadPath->path . '/' . \PageModel::findWithDetails($intPageId)->alias); /* creates a folder if it does not exist */

/**
 * Specify settings
 */
$uploader = new qqFileUploader();

$uploader->allowedExtensions = deserialize($objModule->fu_allowedExtensions);
$uploader->sizeLimit = $objModule->fu_sizeLimit * 1024;
$uploader->inputName = 'qqfile';
$uploader->chunksFolder = 'chunks';
$uploader->useExifDate = $objModule->fu_useExifDate;

/**
 * Call handleUpload() with the name of the folder
 */
// Save in specified folder
if (!$objModule->fu_useSubfolder)
{
	$result = $uploader->handleUpload(TL_ROOT . '/' . $objUploadPath->path);
}
// Save in subdirectory (name = page alias)
else
{
	$result = $uploader->handleUpload(TL_ROOT . '/' . $objUploadPathSubdirectory->path);
}
$result['uploadName'] = $uploader->getUploadName();

// Return result details
echo json_encode($result);
