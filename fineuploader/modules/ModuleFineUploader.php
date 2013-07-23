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
 * Class ModuleFineUploader
 *
 * @copyright  Richard Henkenjohann 2013
 * @author     Richard Henkenjohann
 * @package    FineUploader
 */
class ModuleFineUploader extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_fineuploader';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### FINE UPLOADER ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		// Nothing to do here :(
		// TODO: add non-jquery support
	}
}
