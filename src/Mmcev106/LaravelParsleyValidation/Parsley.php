<?php

namespace Mmcev106\LaravelParsleyValidation;

class Parsley{
	static function buildJS($formSelector, $validator){

		$attributesByElementName = array();

		foreach($validator->getRules() as $elementName=>$rules){
			if(!isset($attributesByElementName[$elementName])){
				$attributesByElementName[$elementName] = array();
			}

			$attributes =& $attributesByElementName[$elementName];

			foreach($rules as $rule){
				$parts = explode(':', $rule);
				$name = $parts[0];
				$value = @$parts[1]; // Only some rules have a value.

				if($name == 'accepted'){
					$attributes['pattern'] = '/^(yes|on|1)$/';
				}
				else if($name == 'active_url'){
					$attributes['data-parsley-type'] = 'url';
				}
				else if($name == 'alpha'){
					$attributes['pattern'] = '/^[A-z]?$/';
				}
				else if($name == 'alpha_dash'){
					$attributes['pattern'] = '/^[A-z-_]?$/';
				}
				else if($name == 'alpha_num'){
					$attributes['data-parsley-type'] = 'alphanum';
				}
				else if($name == 'between'){
					// Assume we're working with a string by default.  We'll change this later if this field is determined to be numeric.
					$attributes['data-parsley-length'] = "[$value]";
				}
				else if($name == 'digits'){
					$attributes['data-parsley-type'] = "digits";
					$attributes['data-parsley-length'] = "[$value,$value]";
				}
				else if($name == 'digits_between'){
					$attributes['data-parsley-type'] = "digits";
					$attributes['data-parsley-length'] = "[$value]";
				}
				else if($name == 'email'){
					$attributes['type'] = "email";
				}
				else if($name == 'in'){
					$value = str_replace(',', '|', $value);
					$attributes['pattern'] = "/^($value)$/";
				}
				else if($name == 'integer'){
					$attributes['type'] = "number";
				}
				else if($name == 'max'){
					// Assume we're working with a string by default.  We'll change this later if this field is determined to be numeric.
					$attributes['maxlength'] = $value;
				}
				else if($name == 'min'){
					// Assume we're working with a string by default.  We'll change this later if this field is determined to be numeric.
					$attributes['minlength'] = $value;
				}
				else if($name == 'not_in'){
					$value = str_replace(',', '|', $value);
					$attributes['pattern'] = "/^(?!(one|two|three)$)/";
				}
				else if($name == 'numeric'){
					$attributes['data-parsley-type'] = "number";
				}
				else if($name == 'regex'){
					$attributes['pattern'] = $value;
				}
				else if($name == 'required'){
					$attributes['required'] = '';
				}
				else if($name == 'size'){
					// Assume we're working with a string by default.  We'll change this later if this field is determined to be numeric.
					$attributes['data-parsley-length'] = "[$value,$value]";
				}
				else if($name == 'url'){
					$attributes['type'] = 'url';
				}
			}

			if(self::isElementNumeric($attributes)){
				// Laravel assumes fields are strings by default, so we create string related Parsley rules by default.
				// If a rule is exists for a given field specifying it as numeric in Laravel, we must change the string related validators in Parsley to their number related equivalents.
				self::switchStringValidatorsToNumberValidators($attributes);
			}
		}

		foreach($validator->getCustomMessages() as $key=>$message){
			$parts = explode('.', $key);
			$elementName = $parts[0];
			$attributeName = $parts[1];

			$attributesByElementName[$elementName]["data-parsley-$attributeName-message"] = $message;
		}

		return self::buildJSForAttributes($formSelector, $formSelector, $attributesByElementName);
	}

	private static function isElementNumeric($attributes){
		return @$attributes['type'] == 'number' || @$attributes['data-parsley-type'] == 'number';
	}

	private static function switchStringValidatorsToNumberValidators(&$attributes){
		if(isset($attributes['data-parsley-length'])){
			$attributes['data-parsley-range'] = $attributes['data-parsley-length'];
			unset($attributes['data-parsley-length']);
		}

		if(isset($attributes['maxlength'])){
			$attributes['max'] = $attributes['maxlength'];
			unset($attributes['maxlength']);
		}

		if(isset($attributes['minlength'])){
			$attributes['min'] = $attributes['minlength'];
			unset($attributes['minlength']);
		}
	}

	private static function buildJSForAttributes($formSelector, $formSelector, $attributesByElementName){
		$js = PHP_EOL; // Start the js with an EOL so the beginning and ending script tags line up (regardless of the indentation level where the JS is included).
		$js .= "<script>" . PHP_EOL;
		$js .= "  $(function(){" . PHP_EOL;

		foreach($attributesByElementName as $elementName=>$attributes){
			foreach($attributes as $name=>$value){
				if($value == NULL){
					$value = '';
				}

				$js .= "    $('$formSelector input[name=$elementName], $formSelector select[name=$elementName]').attr('$name', '$value');" . PHP_EOL;
			}
		}

		$js .= "    $('$formSelector').parsley();" . PHP_EOL;

		$js .= "  })" . PHP_EOL;
		$js .= "</script>" . PHP_EOL;

		return $js;
	}
}