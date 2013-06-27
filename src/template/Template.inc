<?php

namespace jamwork\template;

interface Template
{

	public function __construct($template);

	public function __destruct();

	public function setTemplateFile($templateFile);

	public function js();

	public function css();

	public function section($sectionKey);

	public function setMainSection($sectionName);

	public function mainSection();

	public function getSectionList();

	public function flush();

	public function setDoctype($doctype);

	public function setXmlHeader($xmlHeader);

	public function setBaseUrl($baseUrl);
}
