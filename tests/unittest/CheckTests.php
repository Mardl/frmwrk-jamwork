<?php
require_once 'PHPUnit/Autoload.php';
require_once 'Autoload.php';
require_once '/var/www/libraries/jamwork/unittest/TestsTester.php';

$tester = new TestsTester('jamwork', '../public/jamwork');
$tester->excludeFile('../public/jamwork/Autoload.php');
$tester->excludeFile('../public/jamwork/debug/FirePHPCore/fb.php');
$tester->excludeFile('../public/jamwork/debug/FirePHPCore/FirePHP.class.php');
$tester->flush();