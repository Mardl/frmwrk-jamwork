<?php

namespace jamwork\common;

interface Request 
{
	public function __construct(array $get, array $post, array $server, array $cookie);
	
	public function getAllParameters();
	public function getParameterNames();
	public function hasParameter($name);
	public function getParameter($name);
	public function issetParameter($name);
	public function unsetParameter($name);
	
	public function getAllCookies();
	public function hasCookie($name);
	public function getCookie($name);
	public function getHeader($name);
	
	public function getAllPost();
	public function getPostNames();
	public function hasPost($name);
	public function getPost($name);
	public function issetPost($name);
	public function unsetPost($name);
	public function getRequestUri();
	public function getScriptName();
}
