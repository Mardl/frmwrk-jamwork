<?php

namespace jamwork\template;

/**
 * Class Template
 *
 * @category Jamwork
 * @package  Jamwork\template
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
interface Template
{

	/**
	 * @param string $template
	 */
	public function __construct($template);

	/**
	 * destrucor
	 */
	public function __destruct();

	/**
	 * @param string $templateFile
	 * @return mixed
	 */
	public function setTemplateFile($templateFile);

	/**
	 * @return mixed
	 */
	public function js();

	/**
	 * @return mixed
	 */
	public function css();

	/**
	 * @param string $sectionKey
	 * @return mixed
	 */
	public function section($sectionKey);

	/**
	 * @param string $sectionName
	 * @return mixed
	 */
	public function setMainSection($sectionName);

	/**
	 * @return mixed
	 */
	public function mainSection();

	/**
	 * @return mixed
	 */
	public function getSectionList();

	/**
	 * @return mixed
	 */
	public function flush();

	/**
	 * @param string $doctype
	 * @return mixed
	 */
	public function setDoctype($doctype);

	/**
	 * @param string $xmlHeader
	 * @return mixed
	 */
	public function setXmlHeader($xmlHeader);

	/**
	 * @param string $baseUrl
	 * @return mixed
	 */
	public function setBaseUrl($baseUrl);
}
