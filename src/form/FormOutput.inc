<?php

namespace jamwork\form;

interface FormOutput
{
	public function generate($fieldString, $form);
	public function generateFieldset(FormFactory $field);
	public function generateFormField(Field $field);
}
