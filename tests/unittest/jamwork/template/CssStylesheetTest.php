<?php

namespace unittest\jamwork\template;

use \jamwork\template\CssStylesheet;

class CssStylesheetTest extends \PHPUnit_Framework_TestCase
{

	public function testAdd_noFileexists()
	{
		$add = $this->sheet->add('C:FOO.txt');
		$attr = $this->readAttribute($this->sheet, 'scripts');
		$this->assertEmpty($attr);
		$this->assertFalse($add);
	}

	public function testAdd_Fileexists()
	{
		$handle = fopen("c:FOO.txt", "w");
		$add = $this->sheet->add('c:FOO.txt');
		$attr = $this->readAttribute($this->sheet, 'scripts');
		$this->assertSame('c:FOO.txt', $attr['c:FOO.txt']);
		$this->assertTrue($add);
		fclose($handle);
		unlink("c:FOO.txt");
	}

	public function testSetCacheDir()
	{
		$path = 'static_folder/';
		$this->sheet->setCacheDir($path);
		$attr = $this->readAttribute($this->sheet, 'cacheDir');

		$this->assertSame($attr, $path);
	}

	public function testGetCacheDir()
	{
		$path = 'static/';

		$method = new \ReflectionMethod($this->sheet, 'getCacheDir');
		$method->setAccessible(true);

		$this->assertSame($method->invoke($this->sheet), $path);
	}

	public function testKsort()
	{
		$this->sheet->add('static/zfile.txt');
		$this->sheet->add('static/afile.txt');
		$this->sheet->add('static/bfile.txt');
		$this->sheet->ksort();

		$reflectedProperty = new \ReflectionProperty($this->sheet, 'scripts');
		$reflectedProperty->setAccessible(true);
		$arrScripts = $reflectedProperty->getValue($this->sheet);

		$count = 0;
		foreach ($arrScripts as $value)
		{

			switch ($count)
			{
				case 0:
					$this->assertSame($arrScripts['static/afile.txt'], 'static/afile.txt');
					break;
				case 1:
					$this->assertSame($arrScripts['static/bfile.txt'], 'static/bfile.txt');
					break;
				case 2:
					$this->assertSame($arrScripts['static/zfile.txt'], 'static/zfile.txt');
					break;
				default:
					continue;
					break;
			}

			$count++;
		}

	}

	public function testRemove_keyexists()
	{
		$handle = fopen("c:FOO.txt", "w");
		$add = $this->sheet->add('c:FOO.txt');
		$attr = $this->readAttribute($this->sheet, 'scripts');
		$this->assertSame('c:FOO.txt', $attr['c:FOO.txt']);
		$this->assertTrue($add);
		fclose($handle);
		unlink("c:FOO.txt");
		$rem = $this->sheet->remove('c:FOO.txt');
		$attr = $this->readAttribute($this->sheet, 'scripts');
		$this->assertEmpty($attr);
		$this->assertTrue($rem);
	}

	public function testRemove_nokeyexists()
	{
		$rem = $this->sheet->remove('c:FOO.txt');
		$this->assertFalse($rem);
	}


	public function testFlush()
	{

		$this->sheet->setCacheDir('tests/unittest/static/');
		$this->sheet->add('tests/unittest/static/test.txt');
		$str = $this->sheet->flush();

		$this->assertSame('<link rel="stylesheet" type="text/css" href="tests/unittest/static/8feb211fef744a43469cf07400094c70.css" />' . "\n", $str);

		unlink('tests/unittest/static/8feb211fef744a43469cf07400094c70.css');
	}

	public function testGetTmpFile()
	{
		$this->sheet->setCacheDir('tests/unittest/static/');
		$add = $this->sheet->add('tests/unittest/static/test.txt');
		$method = new \ReflectionMethod($this->sheet, 'getTmpFile');

		$method->setAccessible(true);
		$ret = $method->invoke($this->sheet);

		$this->assertSame($ret, 'tests/unittest/static/8feb211fef744a43469cf07400094c70.css');

	}

	public function testGetAllScripts()
	{
		$this->sheet->add('tests/unittest/static/zfile.txt');
		$this->sheet->add('tests/unittest/static/afile.txt');
		$this->sheet->add('tests/unittest/static/bfile.txt');

		$method = new \ReflectionMethod($this->sheet, 'getAllScripts');
		$method->setAccessible(true);


		$strOut = '<link rel="stylesheet" type="text/css" href="tests/unittest/static/zfile.txt" />' . "\n";
		$strOut .= '<link rel="stylesheet" type="text/css" href="tests/unittest/static/afile.txt" />' . "\n";
		$strOut .= '<link rel="stylesheet" type="text/css" href="tests/unittest/static/bfile.txt" />' . "\n";
		$strOut .= "\n\n";

		$strRetOut = $method->invoke($this->sheet);

		$this->assertSame($strOut, $strRetOut);
	}

	protected function setUp()
	{
		$this->sheet = new CssStylesheet(true);

	}

	protected function tearDown()
	{
		unset($this->sheet);
	}

}
