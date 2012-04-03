<?php
namespace unittest;

require_once('unittest/Autoload.php');

/*define('ROOT_PATH',realpath('.'));

function my_autoload($className){

	$replace = array(
		'_' => '/',
		'\\' => '/'
	);

	$fileNameClass = ROOT_PATH.'/public/'.trim(strtr($className, $replace),'_\\').'.php';
	$fileNameInterface = ROOT_PATH.'/public/'.trim(strtr($className, $replace),'_\\').'.inc';

	if (file_exists($fileNameClass)){
		$fileName = $fileNameClass;
	} else if (file_exists($fileNameInterface)){
		$fileName = $fileNameInterface;
	} else {
		die("Could not load $className\n");
	}

	include_once($fileName);

}

\spl_autoload_register('unittest\my_autoload');
*/
?>
