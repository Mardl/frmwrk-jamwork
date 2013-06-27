<?php

require_once 'src/Autoload.php';

function unittestLoader($className)
{
	$fileName = str_replace('\\', '/', $className);
	$fileName = str_replace('unittest', __DIR__.'/../unittest', $fileName);
	$fileName .= '.php';
	if(file_exists($fileName))
	{
//		echo "$fileName \r\n";
		require_once $fileName;
	}
}

function NoFileFoundLoader($className)
{
	//echo "**** no found ".__DIR__."$className *****\r\n";
	throw new \Exception("Klasse $className konnte nicht geladen werden!");
}

/* Module Autoload */
function moduleLoader($className)
{	
	$fileName = str_replace('\\', '/', $className);
	$fileName = str_replace('jamwork/', __DIR__.'/../../src/', $fileName);
	$fileNameSuffix = $fileName.'.php';

	if(file_exists($fileNameSuffix))
	{
//		echo "$fileNameSuffix \r\n";
		require_once $fileNameSuffix;
	}
	$fileNameSuffix = $fileName.'.inc';
	if(file_exists($fileNameSuffix))
	{
//		echo "$fileNameSuffix \r\n";
		require_once $fileNameSuffix;
	}
}

spl_autoload_register('unittestLoader');
spl_autoload_register('moduleLoader');
spl_autoload_register('NoFileFoundLoader');


