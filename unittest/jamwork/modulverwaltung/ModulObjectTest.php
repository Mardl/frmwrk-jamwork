<?php

namespace unittest\jamwork\modulverwaltung;

use \jamwork\modulverwaltung\ModulObject;
use \jamwork\common\Registry;

class ModulObjectTest extends \PHPUnit_Framework_TestCase
{
	private $modulObject = null;
	
	private $modul1 = 'unittest\module\testmodul1';
	private $modul1_path = '/var/www/libraries/jamwork/unittest/module/testmodul1/';
	
	private $testmodulnoversion = 'unittest\module\testmodulnoversion';
	private $testmodulnoversion_path = '/var/www/libraries/jamwork/unittest/module/testmodulnoversion/';
	
	private $testmodulnoname = 'unittest\module\testmodulnoname';
	private $testmodulnoname_path = '/var/www/libraries/jamwork/unittest/module/testmodulnoname/';
		
	public function testSetNamespace()
	{
		$attr = $this->readAttribute($this->modulObject, 'namespace');
		$this->assertSame($this->modul1, $attr);
		$method = new \ReflectionMethod($this->modulObject, 'setNamespace');
		$method->setAccessible(true);
		$method->invoke($this->modulObject, 'name1');		
		$attr = $this->readAttribute($this->modulObject, 'namespace');
		$this->assertSame('name1', $attr);
	}
	
	public function testGetNamespace()
	{
		$namespace = $this->modulObject->getNamespace();
		$this->assertSame($this->modul1, $namespace);
	}
	
	public function testSetPath()
	{
		$attr = $this->readAttribute($this->modulObject, 'path');
		$this->assertSame($this->modul1_path, $attr);
		$method = new \ReflectionMethod($this->modulObject, 'setPath');
		$method->setAccessible(true);
		
		$method->invoke($this->modulObject,'name1/');		
		$attr = $this->readAttribute($this->modulObject, 'path');
		$this->assertSame('name1/', $attr);
		
		$method->invoke($this->modulObject,'name2');		
		$attr = $this->readAttribute($this->modulObject, 'path');
		$this->assertSame('name2/', $attr);
	}
	
	public function testGetPath()
	{
		$path = $this->modulObject->getPath();
		$this->assertSame($this->modul1_path, $path);
	}
	
	public function testGetVersion()
	{
		$version = $this->modulObject->getVersion();
		$this->assertSame('1.0.0', $version);
	}
	
	public function testGetVersion_throwexception()
	{
		$this->modulObject = new ModulObject($this->testmodulnoversion_path, $this->testmodulnoversion);	
		try 
		{
			//exception bei get Versiuon abfangen falls in der config keine gesetzt ist
			$version = $this->modulObject->getVersion();
		}
		catch ( \Exception $expected) 
		{
            return;
        }
		$this->fail('An expected Exception has not been raised.');
	}
	
	public function testGetClearName()
	{
		$name = $this->modulObject->getClearName();
		$this->assertSame('Testmodul1', $name);
	}
	
	public function testGetClearName_throwexception()
	{
		$this->modulObject = new ModulObject($this->testmodulnoname_path, $this->testmodulnoname);	
		try 
		{
			//exception bei get ClearName abfangen falls in der config keiner gesetzt ist
			$version = $this->modulObject->getClearName();
		}
		catch ( \Exception $expected) 
		{
            return;
        }
		$this->fail('An expected Exception has not been raised.');
	}
	
	public function testGetShow()
	{
		$show = $this->modulObject->getShow();
		$this->assertSame(1, $show);
	}
	
	public function testGetSort()
	{
		$sort = $this->modulObject->getSort();
		$this->assertSame(1, $sort);
	}
	
	public function testInstall()
	{
		$install = $this->modulObject->install();
		$this->assertTrue($install);
	}
	
	public function testUpdate()
	{
		$version = $this->modulObject->getVersion('1');
		$this->assertSame('1.0.0', $version);
	}
	
	public function testIsVersionLower()
	{
		$mockConfig = $this->getMock('unittest\module\testmodul1\Testmodul1Config');
		$method = new \ReflectionMethod($this->modulObject, 'setConfig');
		$method->setAccessible(true);
		$method->invoke($this->modulObject,$mockConfig);
		
		$mockConfig->expects($this->exactly(1))->method('isVersionLower');
		$this->modulObject->isVersionLower(1, 2);
			
	}
	
	public function testGetCommandInfo()
	{
		$cmd = 'unittest\module\testmodul1\Testmodul1';
		$key = 'name';
		$name = $this->modulObject->getCommandInfo($cmd, $key);
		$this->assertSame('Testseite1', $name);
		$key = 'show';
		$name = $this->modulObject->getCommandInfo($cmd, $key);
		$this->assertSame(1, $name);
		$key = 'sort';
		$name = $this->modulObject->getCommandInfo($cmd, $key);
		$this->assertSame(1, $name);
	}
	
	public function test__construct ()
	{
		$attr = $this->readAttribute($this->modulObject, 'path');
		$this->assertSame($this->modul1_path, $attr);
	
		$attr = $this->readAttribute($this->modulObject, 'namespace');
		$this->assertSame($this->modul1, $attr);
		
		$attr = $this->readAttribute($this->modulObject, 'commands');
		$this->assertSame($this->modul1.'\Testmodul1', $attr['Testmodul1Command.php']);
		
		$attr = $this->readAttribute($this->modulObject, 'configFile');
		$this->assertSame('Testmodul1Config.php', $attr);
		
		$attr = $this->readAttribute($this->modulObject, 'config');
		$this->assertSame($this->modul1.'\Testmodul1Config', $attr);
		
		$attr = $this->readAttribute($this->modulObject, 'configObj');
		$this->assertInstanceOf($this->modul1.'\Testmodul1Config', $attr);
	}
	
	public function testReadFiles()
	{
		$attr = $this->readAttribute($this->modulObject, 'configFile');
		$this->assertSame('Testmodul1Config.php', $attr);
		
		$attr = $this->readAttribute($this->modulObject, 'config');
		$this->assertSame($this->modul1.'\Testmodul1Config', $attr);
		
		$attr = $this->readAttribute($this->modulObject, 'configObj');
		$this->assertInstanceOf($this->modul1.'\Testmodul1Config', $attr);		
	}
	
	public function testGetCommands()
	{
		$com = $this->modulObject->getCommands();
		$attr = $this->readAttribute($this->modulObject, 'commands');
		$this->assertSame($attr, $com);
	}
	
	public function testNewConfig()
	{
		//siehe test__Construct()
	}
	
	public function testSetConfig()
	{
		//siehe testIsVersionLower()
	}
		
	protected function setUp()
	{
		$this->modulObject = new ModulObject($this->modul1_path, $this->modul1);
	}

	protected function tearDown()
	{
		unset($this->modulObject);
	}
}
