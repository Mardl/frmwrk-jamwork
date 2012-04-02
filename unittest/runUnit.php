<?php
set_time_limit(0);
ob_start();
require_once 'PHPUnit/Autoload.php';
require_once 'Autoload.php';

$fb = \jamwork\debug\DebugLogger::getInstance();
$fb->setActive(true);


$suiteClass = 'unittest\SingleTest';

if(!isset($_GET['test']) && isset($_GET['dir']))
{
	$suiteClass = 'unittest\AllTest';
	$_GET['test'] = '/jamwork/'.(isset($_GET['dir']) ? $_GET['dir'] : '');
	if(substr($_GET['test'], -1, 1) != '/')
	{
		$_GET['test'] .= '/';
	}
}
elseif(isset($_GET['test']))
{
	if (!class_exists($_GET['test'].'Test',true))
	{
		die('WTF :: '.$_GET['test'].'Test');
	}
	
}

if (isset($_GET['test']))
{
	$suite  = new $suiteClass($_GET['test']);
	$result = new PHPUnit_TextUI_TestRunner();
	echo "<pre>";
	$result->run($suite->getSuite());
	echo "</pre>";
}

ob_flush();