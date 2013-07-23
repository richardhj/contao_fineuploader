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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['fineuploader'] = '{title_legend},name,headline,type;{fineuploader_legend},fu_uploadPath,fu_useSubfolder,fu_sizeLimit,fu_provideZipDownload,fu_useExifDate,fu_allowedExtensions;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

/**
 * Subpalettes
 */


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['fu_uploadPath'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fu_uploadPath'],
	'exclude'                 => true,
	'inputType'               => 'fileTree',
	'eval'                    => array('fieldType'=>'radio', 'files'=> false, 'mandatory'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['fu_useSubfolder'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fu_useSubfolder'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['fu_sizeLimit'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fu_sizeLimit'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('tl_class'=>'long clr', 'rgxp'=>'digit'),
	'sql'                     => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['fu_provideZipDownload'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fu_provideZipDownload'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12 clr'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['fu_useExifDate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fu_useExifDate'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['fu_allowedExtensions'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fu_allowedExtensions'],
	'exclude'                 => true,
	'inputType'               => 'listWizard',
	'eval'                    => array('tl_class'=>'long clr'),
	'sql'                     => "blob NULL"
);
