<?php

namespace unittest\jamwork\common;

use jamwork\common\HttpRequest;

class HttpRequestTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var \jamwork\common\HttpRequest
	 */
	private $request;

	public function testInstanceOf()
	{
		$this->assertInstanceOf('\jamwork\common\Request', $this->request);
	}

	/*public function testAddCommand()
	{
		$this->assertAttributeEquals(array(),'command',$this->request);		
		$this->request->addCommand('command');
		$this->assertAttributeEquals(array('command'),'command',$this->request);		
	}*/

	public function testClearArray()
	{
		$arr = array(array('1/a/b', '2/a/b'));
		$arrnoslashes = array(array('1ab', '2ab'));

		$method = new \ReflectionMethod($this->request, 'clearArray');
		$method->setAccessible(true);
		$testRet = $method->invokeArgs($this->request, array($arr));
		if (get_magic_quotes_gpc())
		{
			$this->assertSame($arrnoslashes, $testRet);
		}
		else
		{
			$this->assertSame($arr, $testRet);
		}
	}

	public function testSetParameter()
	{
		$key = 'setterTest';
		$value = 'setterTest-Value';

		$this->request->setParameter($key, $value);
		$params = $this->readAttribute($this->request, 'parameters');

		$this->assertTrue(isset($params[$key]) && $params[$key] == $value);
	}

	public function testIssetParameter_positiv()
	{
		$testRet = $this->request->issetParameter('unit1');
		$this->assertTrue($testRet);
	}

	public function testIssetParameter_negativ()
	{
		$testRet = $this->request->issetParameter('gibtsnicht');
		$this->assertFalse($testRet);
	}

	public function testUnsetParameter()
	{
		$this->request->unsetParameter('unit1');
		$params = $this->readAttribute($this->request, 'parameters');

		$this->assertFalse(isset($params['unit1']));
	}

	public function testIsKeyInArray_positiv()
	{
		$key = 'test_key';
		$arr = array('test_key' => 'test_value', 'test2_key' => 'test2_value');

		$method = new \ReflectionMethod($this->request, 'isKeyInArray');
		$method->setAccessible(true);
		$testRet = $method->invokeArgs($this->request, array($arr, $key));

		$this->assertTrue($testRet);
	}

	public function testIsKeyInArray_negativ()
	{
		$key = 'not_set';
		$arr = array('test_key' => 'test_value', 'test2_key' => 'test2_value');

		$method = new \ReflectionMethod($this->request, 'isKeyInArray');
		$method->setAccessible(true);
		$testRet = $method->invokeArgs($this->request, array($arr, $key));

		$this->assertFalse($testRet);
	}

	public function testGetRequestUri()
	{
		$ret = $this->request->getRequestUri();
		$this->assertSame('?runUnit.php', $ret);
	}

	public function testGetParameterNames()
	{
		$arr = $this->request->getParameterNames();
		$this->assertTrue(in_array('unit1', $arr));
		$this->assertTrue(in_array('unit2', $arr));
	}

	public function testGetAllParameters()
	{
		$get['unit1'] = 'test1';
		$get['unit2'] = 'test2';
		$arr = $this->request->getAllParameters();
		$this->assertSame($get, $arr);
	}

	public function testHasParameter()
	{
		$this->assertTrue($this->request->hasParameter('unit1'));
		$this->assertFalse($this->request->hasParameter('unit8'));
	}

	public function testGetParameter()
	{
		$this->assertSame($this->request->getParameter('unit1'), 'test1');

	}

	public function testHasCookie()
	{
		$this->assertTrue($this->request->hasCookie('keks1'));
		$this->assertFalse($this->request->hasCookie('keks8'));
	}

	public function testGetAllCookies()
	{
		$cookie['keks1'] = 'testkeks1';
		$cookie['keks2'] = 'testkeks2';
		$this->assertSame($this->request->getAllCookies(), $cookie);
	}

	public function testGetCookie()
	{
		$this->assertSame($this->request->getCookie('keks1'), 'testkeks1');
	}

	public function testGetHeader()
	{
		$this->assertTrue($this->request->getHeader('unit-test') == 1);
	}

	public function testGetFromKeyInArray()
	{
		$ar['mykey'] = 'myvalue';
		$method = new \ReflectionMethod($this->request, 'getFromKeyInArray');
		$method->setAccessible(true);
		$this->assertSame('myvalue', $method->invoke($this->request, $ar, 'mykey'));
	}

	public function testgetFromKeyInArrayWithException()
	{
		$ar['mykey'] = 'myvalue';
		$method = new \ReflectionMethod($this->request, 'getFromKeyInArray');
		$method->setAccessible(true);

		try
		{
			$this->assertSame('myvalue', $method->invoke($this->request, $ar, 'mykeyFailed'));
		} catch (\Exception $expected)
		{
			return;
		}

		$this->fail('An expected Exception has not been raised.');

	}


	public function testGetPostNames()
	{
		$array = $this->request->getPostNames();
		$comparray = array_keys($this->post);

		$this->assertSame($comparray, $array);
	}

	public function testGetAllPost()
	{
		$array = $this->request->getAllPost();

		$this->assertSame($this->post, $array);
	}

	public function testHasPost()
	{
		$name = 'post1';
		$this->assertTrue($this->request->hasPost($name));

		$name = 'postFoo';
		$this->assertFalse($this->request->hasPost($name));
	}

	public function testIssetPost()
	{
		$name = 'post1';
		$this->assertTrue($this->request->issetPost($name));

		$name = 'postFoo';
		$this->assertFalse($this->request->issetPost($name));

		$name = 'postEmpty';
		$this->assertFalse($this->request->issetPost($name));
	}

	public function testUnsetPost()
	{
		$name = 'post1';
		$this->assertTrue($this->request->issetPost($name));

		$this->request->unsetPost($name);
		$this->assertFalse($this->request->issetPost($name));
	}

	public function testGetPost()
	{
		$name = 'post1';
		$post = $this->request->getPost($name);

		$this->assertSame($this->post[$name], $post);
	}

	public function testSetPost()
	{
		$name = 'post4';
		$value = 'testpost4';

		$post = $this->request->setPost($name, $value);
		$post = $this->request->getPost($name);

		$this->assertSame($value, $post);
	}


	public function testisKeyInArray()
	{
		$ar['mykey'] = 'myvalue';
		$method = new \ReflectionMethod($this->request, 'isKeyInArray');
		$method->setAccessible(true);
		$this->assertTrue($method->invoke($this->request, $ar, 'mykey'));

		$this->assertFalse($method->invoke($this->request, $ar, 'notmykey'));
	}

	public function testGetParamIfExist()
	{
		$this->assertSame($this->request->getParamIfExist('unit1', 'default'), 'test1');
	}

	public function testGetParamIfExist_notExist()
	{
		$this->assertSame($this->request->getParamIfExist('unit99', 'default'), 'default');
	}

	public function testGetPostIfExist()
	{
		$this->assertSame($this->request->getPostIfExist('post1', 'default'), 'testpost1');
	}

	public function testGetPostIfExist_notExist()
	{
		$this->assertSame($this->request->getPostIfExist('post99', 'default'), 'default');
	}

	public function testSetCookie()
	{
		$this->request->setCookie('testcookie', 'abctest');
		$this->assertSame($this->request->getCookie('testcookie'), 'abctest');
	}

	public function testDeleteCookie()
	{
		setcookie('testcookiedel', 'abctest');

		$this->request->deleteCookie('testcookiedel');
		$this->assertFalse($this->request->hasCookie('testcookiedel'));
	}

	public function testGetScriptName()
	{
		$this->assertSame($this->request->getScriptName(), '?/usr/bin/phpunit');
	}

	protected function setUp()
	{
		$get['unit1'] = 'test1';
		$get['unit2'] = 'test2';
		$server = $_SERVER;
		$server['HTTP_UNIT_TEST'] = true;
		$server['QUERY_STRING'] = 'runUnit.php';
		$cookie['keks1'] = 'testkeks1';
		$cookie['keks2'] = 'testkeks2';

		$this->post['post1'] = 'testpost1';
		$this->post['post2'] = 'testpost2';
		$this->post['postEmpty'] = '';


		$this->request = new HttpRequest($get, $this->post, $server, $cookie);
	}

	protected function tearDown()
	{
		unset($this->request);
	}
}
