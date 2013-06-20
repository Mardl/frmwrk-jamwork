<?php

namespace unittest\jamwork\template;

use \jamwork\template\Javascript;

class JavascriptTest extends \PHPUnit_Framework_TestCase
{
	
	private $javaclass = null;
	private $javaclassOhneShrink = null;
	
	public function testAdd_noFileexists()
	{
		$add = $this->javaclass->add('FOO.txt');
		$attr = $this->readAttribute($this->javaclass, 'scripts');
		$this->assertEmpty($attr);
		$this->assertFalse($add);
	}
	
	public function testAdd_Fileexists()
	{
		$handle = fopen("FOO.txt", "w");	
		$add = $this->javaclass->add('FOO.txt');
		$attr = $this->readAttribute($this->javaclass, 'scripts');
		$this->assertSame('FOO.txt', $attr['FOO.txt']);
		$this->assertTrue($add);
		fclose($handle);
		unlink("FOO.txt");
	}
	
	public function testRemove_keyexists()
	{
		$handle = fopen("FOO.txt", "w");	
		$add = $this->javaclass->add('FOO.txt');
		$attr = $this->readAttribute($this->javaclass, 'scripts');
		$this->assertSame('FOO.txt', $attr['FOO.txt']);
		$this->assertTrue($add);
		fclose($handle);
		unlink("FOO.txt");
		$rem = $this->javaclass->remove('FOO.txt');
		$attr = $this->readAttribute($this->javaclass, 'scripts');
		$this->assertEmpty($attr);
		$this->assertTrue($rem);
	}
	
	public function testRemove_nokeyexists()
	{
		$rem = $this->javaclass->remove('FOO.txt');
		$this->assertFalse($rem);
	}
	
	
	public function testFlush_withShrink()
	{
		$add = $this->javaclass->add('static/test.txt');
		$str = $this->javaclass->flush();
		$this->assertSame('<script type="text/javascript" src="static/8feb211fef744a43469cf07400094c70.js"></script>'."\n", $str);
		
		unlink('static/8feb211fef744a43469cf07400094c70.js');
	}

	public function testFlush_withoutShrink()
	{
		$add = $this->javaclassOhneShrink->add('static/test.txt');
		$str = $this->javaclassOhneShrink->flush();
		$this->assertSame('<script type="text/javascript" src="static/test.txt"></script>'."\n\n\n", $str);
	}
	
	public function testGetTmpFile()
	{
		$method = new \ReflectionMethod(
          $this->javaclass, 'getTmpFile'
        );
	    $method->setAccessible(TRUE);
	    
	    
		$add = $this->javaclass->add('static/test.txt');
	    $filename = $method->invoke($this->javaclass);	
		
		$contents = file_get_contents($filename);
		$this->assertStringEqualsFile('static/8feb211fef744a43469cf07400094c70.js', $contents);
		
		$contents = file_put_contents($filename,'teststs');
		
		$method->invoke($this->javaclass);	
		
		$contents = file_get_contents($filename);
		$this->assertStringEqualsFile('static/8feb211fef744a43469cf07400094c70.js', 'teststs');
		
		unlink('static/8feb211fef744a43469cf07400094c70.js');
			
	}
	
	public function testKsort()
	{
		$this->javaclass->add('static/zfile.txt');
		$this->javaclass->add('static/afile.txt');
		$this->javaclass->add('static/bfile.txt');
		$this->javaclass->ksort();
		
		$reflectedProperty = new \ReflectionProperty($this->javaclass, 'scripts');
		$reflectedProperty->setAccessible(true);
		$arrScripts = $reflectedProperty->getValue($this->javaclass);
		
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
	
	public function testGetAllScripts()
	{
		$this->javaclass->add('static/zfile.txt');
		$this->javaclass->add('static/afile.txt');
		$this->javaclass->add('static/bfile.txt');
		
		$method = new \ReflectionMethod(
          $this->javaclass, 'getAllScripts'
        );
	    $method->setAccessible(TRUE);		
		
		
	
		$strOut  = '<script type="text/javascript" src="static/zfile.txt"></script>'."\n";
		$strOut .= '<script type="text/javascript" src="static/afile.txt"></script>'."\n";
		$strOut .= '<script type="text/javascript" src="static/bfile.txt"></script>'."\n";
		$strOut .= "\n\n";
		
		$strRetOut = $method->invoke($this->javaclass);
		
		$this->assertSame($strOut, $strRetOut);
	}
	
	public function testGetCacheDir()
	{
		$path = 'static/';
		
		$method = new \ReflectionMethod(
          $this->javaclass, 'getCacheDir'
        );
	    $method->setAccessible(TRUE);		
		
		$this->assertSame($method->invoke($this->javaclass), $path);
	}
	
	protected function setUp()
	{
		$this->javaclass = new Javascript(true);
		$this->javaclassOhneShrink = new Javascript(false);		
	}
	
	protected function tearDown()
	{
		unset($this->javaclass);
	}

}
