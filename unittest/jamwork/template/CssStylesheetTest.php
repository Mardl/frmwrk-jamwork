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
		
		$method = new \ReflectionMethod(
          $this->sheet, 'getCacheDir'
        );
	    $method->setAccessible(TRUE);		
		
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
		foreach ($arrScripts as $value) {
			
			switch ($count) {
				case 0:
					$this->assertSame($arrScripts['static/afile.txt'],'static/afile.txt');		
					break;
				case 1:
					$this->assertSame($arrScripts['static/bfile.txt'],'static/bfile.txt');
					break;
				case 2:
					$this->assertSame($arrScripts['static/zfile.txt'],'static/zfile.txt');
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
		$add = $this->sheet->add('static/test.txt');
		$str = $this->sheet->flush();
		
		$this->assertSame('<link rel="stylesheet" type="text/css" href="static/8feb211fef744a43469cf07400094c70.css" />'."\n", $str);
		
		$filename = "static/8feb211fef744a43469cf07400094c70.css";
		$handle = fopen($filename, "r+");
		\jamwork\debug\DebugLogger::getInstance()->log($filename);
		\jamwork\debug\DebugLogger::getInstance()->log(filesize($filename));
		
		$contents = fread($handle, filesize($filename));
		$this->assertStringEqualsFile('static/8feb211fef744a43469cf07400094c70.css', $contents);
		ftruncate($handle, 0); 
		fclose($handle);	
		unlink($filename);
	}
	
	public function testGetTmpFile()
	{
		$add = $this->sheet->add('static/test.txt');
		$method = new \ReflectionMethod(
          $this->sheet, 'getTmpFile'
        );
		
	    $method->setAccessible(TRUE);	
		$ret = $method->invoke($this->sheet);

		$this->assertSame($ret, 'static/8feb211fef744a43469cf07400094c70.css');

	}
	
	public function testGetAllScripts()
	{
		$this->sheet->add('static/zfile.txt');
		$this->sheet->add('static/afile.txt');
		$this->sheet->add('static/bfile.txt');
		
		$method = new \ReflectionMethod(
          $this->sheet, 'getAllScripts'
        );
	    $method->setAccessible(TRUE);		
		
		
	
		$strOut  = '<link rel="stylesheet" type="text/css" href="static/zfile.txt" />'."\n";
		$strOut .= '<link rel="stylesheet" type="text/css" href="static/afile.txt" />'."\n";
		$strOut .= '<link rel="stylesheet" type="text/css" href="static/bfile.txt" />'."\n";
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
