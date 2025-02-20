<?php
session_start();
error_reporting(0); // Set E_ALL for debuging

include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderConnector.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinder.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeDriver.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeLocalFileSystem.class.php';
// Required for MySQL storage connector
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeMySQL.class.php';
// Required for FTP connector support
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeFTP.class.php';


/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from  '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}

$id_user = @$_SESSION['idDosen'];
$opts = array(
	// 'debug' => true,
	'roots' => array(
		array(
			'driver'        => 'LocalFileSystem', 
			'path'          => '../../../../../storage/upload/materi/'.$id_user,
			'URL'           => dirname($_SERVER['PHP_SELF']) . '/../../../../../storage/upload/materi/'.$id_user,
			'accessControl' => 'access'
		),
		// array (
		//     'driver' 		=> 'LocalFileSystem',
		//     'path' 		=> '../../../../../storage/example/Media/',
		//     'URL'           => dirname($_SERVER['PHP_SELF']) . '/../../../../../storage/example/Media/',
		//     'alias' 		=> 'Media',
		//     'accessControl' => 'access'  
		// )
	)
);

// run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();

?>