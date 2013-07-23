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
 * Legends
 */
$GLOBALS['TL_LANG']['tl_module']['fineuploader_legend'] = 'Module settings';


/**
 * Front end modules
 */
$GLOBALS['TL_LANG']['tl_module']['fu_uploadPath']           = array('Upload path', 'Choose the path for uploads.');
$GLOBALS['TL_LANG']['tl_module']['fu_useSubfolder']         = array('Save in subfolder', 'Save the images in a subfolder with the name of the page alias (e.g. <em>index</em>).');
$GLOBALS['TL_LANG']['tl_module']['fu_sizeLimit']            = array('Size limit', 'Enter the maximum file size in KB.');
$GLOBALS['TL_LANG']['tl_module']['fu_provideZipDownload']   = array('Provide zip download', 'Allow the front end user to download all files in the upload folder.');
$GLOBALS['TL_LANG']['tl_module']['fu_useExifDate']          = array('Use date as filename', 'Save the image with <em>' . date('Ymd_His') . '</em> as filename.');
$GLOBALS['TL_LANG']['tl_module']['fu_allowedExtensions']    = array('Allowed Extensions', 'Define the allowed extensions like <em>jpg, jpeg</em>.');
