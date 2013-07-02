<?php

namespace jamwork\form;

interface Field
{

	public function getType();

	public function isRequired();

	public function getLabel();

	public function getName();

	public function getValue();

	public function getId();

	public function getClasses();

	public function getDataAttr();
}
