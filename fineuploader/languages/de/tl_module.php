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
$GLOBALS['TL_LANG']['tl_module']['fineuploader_legend'] = 'Moduleinstellungen';


/**
 * Front end modules
 */
$GLOBALS['TL_LANG']['tl_module']['fu_uploadPath']           = array('Upload-Pfad', 'Geben Sie an, in welches Verzeichnis die Dateien hochgeladen werden sollen.');
$GLOBALS['TL_LANG']['tl_module']['fu_useSubfolder']         = array('In Unterordner speichern', 'Geben Sie an, ob die Dateien in einen Unterordner -  mit dem Alias der Seite (bspw. <em>index</em>) als Ordnernamen - gespeichert werden sollen.');
$GLOBALS['TL_LANG']['tl_module']['fu_sizeLimit']            = array('Maximale Dateigröße', 'Geben Sie die maximal erlaubte Dateigröße in KB an.');
$GLOBALS['TL_LANG']['tl_module']['fu_provideZipDownload']   = array('Zip-Download ermöglichen', 'Geben Sie dem Frontend-Benutzer die Möglichkeit, alle Dateien in dem Upload-Ordner, herunterzuladen.');
$GLOBALS['TL_LANG']['tl_module']['fu_useExifDate']          = array('Datum als Dateinamen verwenden', 'Geben Sie an, ob die Datei im Format <em>' . date('Ymd_His') . '</em> abgespeichert werden soll.');
$GLOBALS['TL_LANG']['tl_module']['fu_allowedExtensions']    = array('Datei-Typen', 'Definieren Sie eine Liste der erlaubten Datei-Erweiterungen, wie z.B. <em>jpg, jpeg</em>, an.');
