<?php

namespace jamwork\form;

use jamwork\common\Registry;

class FormHTML implements FormOutput
{
	public function generate($fieldString, $form)
	{
		if($form === null || !($form instanceof Form))
		{
			return $fieldString;
		}
		
		$str = '<form method="'.$form->getMethod().'" action="'.$form->getAction().'" name="'.$form->getName().'" class="'.$form->getClasses().'" id="'.$form->getId().'" enctype="'.$form->getEnctype().'">';
		$str .= $fieldString;
		$str .= '</form>';
		
		return $str;
	}

	public function generateFieldset(FormFactory $field)
	{
		$cls = $field->getClasses();
		if(!empty($cls))
		{
			$cls = ' class="'.$cls.'"';
		}
		
		$str = '<fieldset'.$cls.$field->getDataAttr().'>';
		if($field->issetLegend())
		{
			$str .= '<legend>'.$field->getLegend().'</legend>';
		}
		
		$str .= $field->generate();
		$str .= '</fieldset>';
		
		return $str;
	}
	
	private function getPlaceholder($field)
	{
		$value = $field->getPlaceholder();
		if(!empty($value))
		{
			return ' placeholder="'.$value.'"';
		}
		return '';
	}
	
	public function generateFormField(Field $field)
	{
		$str = '';
		if($this->required($field))
		{
			$field->addClass('required');
		}
		
		$field->addClass('formBlock');
		$field->addClass($field->getType());
		
		$str .= '<span class="'.$field->getClasses().'">';
		
		if ( !$field->hasLabelRight() && $field->getLabel() != '' )
		{
			$str .= '<label for="'.$field->getId().'">'.$field->getLabel().'</label>';
		}
		switch($field->getType())
		{
			case 'text':
			case 'password':
			case 'file':
				$str .= '<input'.$field->getDataAttr().$this->getPlaceholder($field).' type="'.$field->getType().'" value="'.$field->getValue().'" name="'.$field->getName().'" id="'.$field->getId().'" maxlength="'.$field->getMaxLength().'" />';
				break;
				
			case 'hidden':
				$str = '<input'.$field->getDataAttr().' type="'.$field->getType().'" value="'.$field->getValue().'" name="'.$field->getName().'" id="'.$field->getId().'" maxlength="'.$field->getMaxLength().'" />';
				return $str;
				break;

			case 'checkbox':
			case 'radio':
				$str .= '<input'.$field->getDataAttr().' type="'.$field->getType().'" value="'.$field->getValue().'" name="'.$field->getName().'" id="'.$field->getId().'" '.($field->isChecked() ? 'checked="checked"' : '').' />';
				break;
			
			case 'textarea':
				$str .= '<textarea'.$field->getDataAttr().$this->getPlaceholder($field).' name="'.$field->getName().'" id="'.$field->getId().'">'.$field->getValue().'</textarea>';
				break;
			
			case 'select':
				$str .= '<select'.$field->getDataAttr().' name="'.$field->getName().'" id="'.$field->getId().'" '.($field->isMultiple() ? 'multiple="multiple"' : '').'>';
				$options = $field->getValue();
				if ( is_array($options) and !empty($options))
				{
					foreach ( $options as $option)
					{
						$str .= '<option'.$field->getDataAttr().' value="'.$option->getValue().'"'.($option->isSelected() ? ' selected="selected"' : '').'>'.$option->getText().'</option>';
					}
					$str .='</select>';
				}
				break;
			case 'button':
				$str .= '<button'.$field->getDataAttr().' name="'.$field->getName().'" type="'.$field->getButtonType().'" value="'.$field->getValue().'">'.$field->getText().'</button>';
				break;
			case 'p':
				$str .= '<p'.$field->getDataAttr().'>'.$field->getValue().'</p>';
				break;
			case 'hr':
				$str .= '<hr'.$field->getDataAttr().'></hr>';
				break;
			default:
				$str .= '';
				break;
		}
		if ( $field->hasLabelRight())
		{
			$str .= '<label for="'.$field->getId().'">'.$field->getLabel().'</label>';
		}
		
		$str .= '</span>';
		return $str;
	}

	private function required(Field $field)
	{
		$registry = Registry::getInstance();
		if ( $registry->hasRequest()) {
			$request = $registry->getRequest();
			if ( $request->hasParameter($field->getName()) && $field->isRequired() && $field->getValue() == '' ) 
			{
				return true;
			}
		}
		return false;
	}	
	
}
	