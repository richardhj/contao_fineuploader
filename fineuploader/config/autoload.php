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
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'FineUploader',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'FineUploader\qqFileUploader'     => 'system/modules/fineuploader/classes/qqFileUploader.php',

	// Modules
	'FineUploader\ModuleFineUploader' => 'system/modules/fineuploader/modules/ModuleFineUploader.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_fineuploader' => 'system/modules/fineuploader/templates',
	'tpl_fineuploader' => 'system/modules/fineuploader/templates',
));
