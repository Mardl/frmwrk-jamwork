<?php

namespace unittest\jamwork\template;

use \jamwork\template\HtmlTemplate;
use \jamwork\template\CssStylesheet;
use \jamwork\template\Javascript;

class HtmlTemplateTest extends \PHPUnit_Framework_TestCase
{
	private $template = null;
	private $js = null;
	private $css = null;
	private $templateDir = 'testTemplate/';
	private $file1 = 'templateFile_1';
	private $file2 = 'templateFile_2';
	
	public function test__construct_scripts()
	{
		$js = $this->readAttribute($this->template, 'js');
		$css = $this->readAttribute($this->template, 'css');
		
		$this->assertInstanceOf('\jamwork\template\Javascript', $js);
		$this->assertInstanceOf('\jamwork\template\CssStylesheet', $css);
	}
	
	public function test__construct_templateDir()
	{
		$templateDir = $this->readAttribute($this->template, 'templateDir');
		$this->assertSame($templateDir, $this->templateDir);
	}
	
	public function testSetTemplateFile_negativ()
	{
		$templateFile = $this->readAttribute($this->template, 'templateFile');
		$this->assertEmpty($templateFile);

		$this->template->setTemplateFile('xyz');
		$templateFile = $this->readAttribute($this->template, 'templateFile');
		$this->assertEmpty($templateFile);
	}
	
	public function testSetTemplateFile_positiv()
	{
		$templateFile = $this->readAttribute($this->template, 'templateFile');
		$this->assertEmpty($templateFile);

		$this->template->setTemplateFile($this->file1);
		$templateFile = $this->readAttribute($this->template, 'templateFile');
		$this->assertSame($templateFile, $this->file1);
	}
	
	public function testJs()
	{
		$js = $this->template->js();
		$this->assertInstanceOf('\jamwork\template\Javascript', $js);
	}
	
	public function testCss()
	{
		$css = $this->template->css();
		$this->assertInstanceOf('\jamwork\template\CssStylesheet', $css);
	}
	
	public function testSection_positiv()
	{
		$this->template->setTemplateFile($this->file1);
		$section = $this->template->section('main');
		$this->assertInstanceOf('\jamwork\template\HtmlSection', $section);
	}
	
	public function testSection_Exception()
	{
		$this->template->setTemplateFile($this->file1);
		try
		{
			$section = $this->template->section('noarea');
		}
		catch ( \Exception $e )
		{
			return;
		}
		
		$this->Fail("Exception erwartet");
	}
	
	public function testSetMainSection_positiv()
	{
		$this->template->setTemplateFile($this->file1);
		
		$mainSection = $this->readAttribute($this->template, 'mainSection');
		$this->assertEmpty($mainSection);

		$this->template->setMainSection('main');
		$mainSection = $this->readAttribute($this->template, 'mainSection');
		$this->assertSame($mainSection, 'main');
	}
	
	public function testSetMainSection_Exception()
	{
		$this->template->setTemplateFile($this->file1);
		
		try
		{
			$this->template->setMainSection('noarea');
		}
		catch ( \Exception $e )
		{
			return;
		}
		
		$this->Fail("Exception erwartet");
	}
	
	public function testMainSection_positiv()
	{
		$this->template->setTemplateFile($this->file1);
		$this->template->setMainSection('main');
		$section = $this->template->mainSection();
		$this->assertInstanceOf('jamwork\template\HtmlSection',$section);
		
	}
	
	public function testMainSection_Exception()
	{
		$this->template->setTemplateFile($this->file1);
		try
		{
			$this->template->mainSection();
		}
		catch ( \Exception $e )
		{
			return;
		}
		
		$this->Fail("Exception erwartet");
	}
	
	public function testFlush()
	{
		$this->template->setTemplateFile($this->file1);
		$this->template->setXmlHeader('<!-- XML-Header -->');
		$this->template->setBaseUrl('http://jamwork.unittest/');
		$this->template->setDoctype('<!-- Doctype -->');
		$this->template->section('navigation')->append('<div class="test1">inhalt 1</div>');
		$this->template->section('main')->append('<span id="test2">inhalt 2</span>');
		
		$this->css->expects($this->exactly(1))->method('flush');
		$this->js->expects($this->exactly(1))->method('flush');
				
		$ret = $this->template->flush();
		
		$this->assertContains('<!-- XML-Header -->', $ret);
		$this->assertContains('<!-- Doctype -->', $ret);
		
		$matcher = array(
			'tag' => 'base',
			'attributes' => array('href' => 'http://jamwork.unittest/')
		);
		$this->assertTag($matcher, $ret);
		
		$matcher = array(
			'tag' => 'head',
			'child' => array('tag' => 'base')
		);
		$this->assertTag($matcher, $ret);
		
		$matcher = array(
			'tag' => 'div',
			'attributes' => array('id' => 'navi'),
			'child' => array(
				'tag' => 'div',
				'content' => 'inhalt 1'
			)
		);
		$this->assertTag($matcher, $ret);
		
		$matcher = array(
			'tag' => 'div',
			'attributes' => array('id' => 'main'),
			'child' => array(
				'tag' => 'span',
				'id' => 'test2',
				'content' => 'inhalt 2'
			)
		);
		$this->assertTag($matcher, $ret);
		
		$matcher = array(
			'tag' => 'title',
		);
		$this->assertTag($matcher, $ret);
	}

	public function testGetSectionList()
	{
		$this->template->setTemplateFile($this->file1);
		$list = $this->template->getSectionList();
		$this->assertSame($list, array(0 => 'navigation', 1 => 'main'));
	}
	
	public function testSetDoctype()
	{
		$doctype = $this->readAttribute($this->template, 'doctype');
		$this->assertEmpty($doctype);
		
		$this->template->setDoctype('test-doctype');
		$doctype = $this->readAttribute($this->template, 'doctype');
		$this->assertSame($doctype, 'test-doctype');
	}
	
	public function testSetXmlHeader()
	{
		$header = $this->readAttribute($this->template, 'xmlHeader');
		$this->assertEmpty($header);
		
		$this->template->setXmlHeader('test-header');
		$header = $this->readAttribute($this->template, 'xmlHeader');
		$this->assertSame($header, 'test-header');
	}
	
	public function testSetBaseUrl()
	{
		$baseUrl = $this->readAttribute($this->template, 'baseUrl');
		$this->assertEmpty($baseUrl);
		
		$this->template->setBaseUrl('test-url');
		$baseUrl = $this->readAttribute($this->template, 'baseUrl');
		$this->assertSame($baseUrl, 'test-url');
	}
	
	public function testReadFiles()
	{
		
		$js = new Javascript(true);
		$css = new CssStylesheet(true);
				
		$reflectedProperty = new \ReflectionProperty($this->template, 'js');
		$reflectedProperty->setAccessible(true);
		$reflectedProperty->setValue($this->template,$js);

		$reflectedProperty = new \ReflectionProperty($this->template, 'css');
		$reflectedProperty->setAccessible(true);
		$reflectedProperty->setValue($this->template,$css);
		
		$method = new \ReflectionMethod($this->template, 'readFiles');
		$method->setAccessible(true);
		$method->invoke($this->template);
		
		
		$templateFiles = $this->readAttribute($this->template, 'templateFiles');
		$this->assertSame($templateFiles, array('templateFile_1.html' => 'templateFile_1', 'templateFile_2.html' => 'templateFile_2'));
		
		$cssScripts = $this->readAttribute($this->template->css(), 'scripts');
		$this->assertSame($cssScripts, array('testTemplate/css/cssFile_1.css' => 'testTemplate/css/cssFile_1.css'));
		
		$jsScripts = $this->readAttribute($this->template->js(), 'scripts');
		$this->assertSame($jsScripts, array('testTemplate/js/jsFile_1.js' => 'testTemplate/js/jsFile_1.js'));
	}

	public function testReadSubDir()
	{
		$method = new \ReflectionMethod($this->template, 'readSubDir');
		$method->setAccessible(true);
		
		$this->css->expects($this->exactly(1))->method('add');
		$method->invoke($this->template, $this->templateDir.'css/');

		$this->js->expects($this->exactly(1))->method('add');
		$method->invoke($this->template, $this->templateDir.'js/');
	}

	// file1 => 2 Variablen
	public function testReadTemplate_test1()
	{
		$sections = $this->readAttribute($this->template, 'sections');
		$sectionList = $this->readAttribute($this->template, 'sectionList');
		
		$this->assertEmpty($sections);
		$this->assertEmpty($sectionList);
		
		// Ruft intern readTemplate() auf
		$this->template->setTemplateFile($this->file1);
		
		$sections = $this->readAttribute($this->template, 'sections');
		$sectionList = $this->readAttribute($this->template, 'sectionList');
		
		$this->assertTrue(is_array($sectionList) && count($sectionList) == 2);
		foreach($sections as $section)
		{
			$this->assertInstanceOf('\jamwork\template\HtmlSection', $section);
		}
	}
	
	// file1 => 3 Variablen
	public function testReadTemplate_test2()
	{
		$sections = $this->readAttribute($this->template, 'sections');
		$sectionList = $this->readAttribute($this->template, 'sectionList');
		
		$this->assertEmpty($sections);
		$this->assertEmpty($sectionList);
		
		// Ruft intern readTemplate() auf
		$this->template->setTemplateFile($this->file2);
		
		$sections = $this->readAttribute($this->template, 'sections');
		$sectionList = $this->readAttribute($this->template, 'sectionList');
		
		$this->assertTrue(is_array($sectionList) && count($sectionList) == 3);
		foreach($sections as $section)
		{
			$this->assertInstanceOf('\jamwork\template\HtmlSection', $section);
		}
	}
	
	public function testSetTemplateDir()
	{
		$templateDir = $this->readAttribute($this->template, 'templateDir');
		$this->assertSame($templateDir, $this->templateDir);
		
		$method = new \ReflectionMethod($this->template, 'setTemplateDir');
		$method->setAccessible(true);
		
		// Ohne Slash
		$method->invoke($this->template, 'testTemplate');
		$templateDir = $this->readAttribute($this->template, 'templateDir');
		$this->assertSame($templateDir, $this->templateDir);
		
		// Mit Slash
		$method->invoke($this->template, 'testTemplate/');
		$templateDir = $this->readAttribute($this->template, 'templateDir');
		$this->assertSame($templateDir, $this->templateDir);
	}
	
	public function testSetTemplateDir_Exception()
	{
		$method = new \ReflectionMethod($this->template, 'setTemplateDir');
		$method->setAccessible(true);
		
		try
		{
			$method->invoke($this->template, 'nicht/existentes/verzeichnis');
		}
		catch ( \Exception $e )
		{
			return;
		}
		
		$this->Fail("Exception erwartet");
	}
	
	public function testGetBody()
	{
		$this->template->setTemplateFile($this->file2);
		$method = new \ReflectionMethod($this->template, 'getBody');
		$method->setAccessible(true);
		
		$this->template->section('subarea')->append('test123');
		
		$ret = $method->invoke($this->template);
		
		$matcher = array(
			'tag' => 'div',
			'attributes' => array('id' => 'navi'),
			'ancestor' => array('tag' => 'body')
		);
		$this->assertTag($matcher, $ret);
		
		$matcher = array(
			'tag' => 'div',
			'attributes' => array('id' => 'main'),
			'ancestor' => array('tag' => 'body')
		);
		$this->assertTag($matcher, $ret);

		$matcher = array(
			'tag' => 'div',
			'attributes' => array('id' => 'sub'),
			'ancestor' => array('tag' => 'body'),
			'content' => 'test123'
		);
		$this->assertTag($matcher, $ret);
	}
	
	public function testGetDoctype()
	{
		$method = new \ReflectionMethod($this->template, 'getDoctype');
		$method->setAccessible(true);
		
		$doc = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"'."\n";
       	$doc .= "\t".'"http://www.w3.org/TR/html4/loose.dtd">'."\n";
		$doc .= '<html>'."\n";
		
		$this->assertSame( $doc, $method->invoke($this->template) );
		$this->template->setDoctype('test-doctype');
		$this->assertSame( $method->invoke($this->template), 'test-doctype'."\n" );
	}
	
	public function testGetXmlHeader()
	{
		$method = new \ReflectionMethod($this->template, 'getXmlHeader');
		$method->setAccessible(true);
		
		$this->assertEmpty( $method->invoke($this->template) );
		$this->template->setXmlHeader('test-header');
		$this->assertSame( $method->invoke($this->template), 'test-header'."\n" );
	}
	
	public function testGetBaseUrl()
	{
		$method = new \ReflectionMethod($this->template, 'getBaseUrl');
		$method->setAccessible(true);
		
		$this->assertEmpty( $method->invoke($this->template) );
		$this->template->setBaseUrl('test-url');
		$this->assertSame( $method->invoke($this->template), '<base href="test-url" />'."\n\n" );
	}
	
	public function testSetCacheDir_mkdir()
	{
		
		$this->template->setCacheDir('cache/');
		$this->assertTrue(is_dir('cache/'));
		rmdir('cache');
	}
	
	public function testSetCacheDir_callSubfunctions()
	{
		
		$this->css->expects($this->exactly(1))->method('setCacheDir');
		$this->js->expects($this->exactly(1))->method('setCacheDir');

		$this->template->setCacheDir('cache/');
		
		rmdir('cache');
			 
	}
	
	public function testGetTitle()
	{
		//wird in testFlush getestet
	}
	
	public function setUp()
	{
		$this->template = new HtmlTemplate($this->templateDir);

		$this->css = $this->getMock('\jamwork\template\CssStylesheet');
		$this->js = $this->getMock('\jamwork\template\Javascript');
		
		$reflectedProperty = new \ReflectionProperty($this->template, 'js');
		$reflectedProperty->setAccessible(true);
		$reflectedProperty->setValue($this->template,$this->js);

		$reflectedProperty = new \ReflectionProperty($this->template, 'css');
		$reflectedProperty->setAccessible(true);
		$reflectedProperty->setValue($this->template,$this->css);

	}
	
	public function tearDown()
	{
		unset($this->template);
	}
}
