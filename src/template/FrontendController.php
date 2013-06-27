<?php

namespace jamwork\template;

use \jamwork\common\Response;
use \jamwork\template\Template;

class FrontendController
{

	private $sectionKeys = array();

	public function assignCommandToSection($command, $section)
	{
		if (is_object($command))
		{
			$command = get_class($command);
		}

		$this->sectionKeys[$command] = $section;
	}

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
