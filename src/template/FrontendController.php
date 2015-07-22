<?php

namespace jamwork\template;

use \jamwork\common\Response;
use \jamwork\template\Template;

/**
 * Class FrontendController
 *
 * @category Jamwork
 * @package  Jamwork\template
 * @author   Martin Eisenführer <martin@dreiwerken.de>
 */
class FrontendController
{

	private $sectionKeys = array();

	/**
	 * @param string $command
	 * @param string $section
	 * @return void
	 */
	public function assignCommandToSection($command, $section)
	{
		if (is_object($command))
		{
			$command = get_class($command);
		}

		$this->sectionKeys[$command] = $section;
	}

	/**
	 * @param Template $template
	 * @param Response $response
	 * @return void
	 */
	public function prozess(Template $template, Response $response)
	{
		$data = $response->getReturns();
		$sections = $template->getSectionList();

		foreach ($data as $key => $value)
		{
			if (isset($this->sectionKeys[$key]) && in_array($this->sectionKeys[$key], $sections))
			{
				$template->section($this->sectionKeys[$key])->append($value);
				continue;
			}
			$template->mainSection()->append($value);
		}

		$response->setBody($template->flush());
		$response->flush();
	}
}
