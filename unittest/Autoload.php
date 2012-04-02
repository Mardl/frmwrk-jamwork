<?php

require_once '../public/jamwork/Autoload.php';

function unittestLoader($className)
{
	$fileName = str_replace('\\', '/', $className);
	$fileName = str_replace('unittest/jamwork', 'jamwork', $fileName);
	$fileName = str_replace('unittest/', '', $fileName);
	$fileName .= '.php';
	if(file_exists($fileName))
	{
		require_once $fileName;
	}
}

function NoFileFoundLoader($className)
{
	throw new \Exception("Klasse $className konnte nicht geladen werden!");
}

/* Module Autoload */
function moduleLoader($className)
{	
	echo 'test';
	
	$fileName = str_replace('\\', '/', $className);
	$fileName = str_replace('unittest/', '', $fileName);
	$fileName .= '.php';
	if(file_exists($fileName))
	{
		require_once $fileName;
	}
}

spl_autoload_register('unittestLoader');
spl_autoload_register('moduleLoader');
spl_autoload_register('NoFileFoundLoader');


