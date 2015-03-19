<?php

namespace Mmcev106\LaravelParsleyValidation;

class Parsley{

	private function __construct($validator, $formSelector){
		$this->validator = $validator;
		$this->formSelector = $formSelector;

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
					$this->addAttribute($attributes, $elementName, $name, 'pattern', '/^(yes|on|1)$/');
				}
				else if($name == 'active_url'){
					$this->addAttribute($attributes, $elementName, $name, 'data-parsley-type', 'url');
				}
				else if($name == 'alpha'){
					$this->addAttribute($attributes, $elementName, $name, 'pattern', '/^[A-z]?$/');
				}
				else if($name == 'alpha_dash'){
					$this->addAttribute($attributes, $elementName, $name, 'pattern', '/^[A-z-_]?$/');
				}
				else if($name == 'alpha_num'){
					$this->addAttribute($attributes, $elementName, $name, 'data-parsley-type', 'alphanum');
				}
				else if($name == 'between'){
					// Assume we're working with a string by default.  We'll change this later if this field is determined to be numeric.
					$this->addAttribute($attributes, $elementName, $name, 'data-parsley-length', "[$value]");
				}
				else if($name == 'confirmed'){
					$this->addAttribute($attributes, $elementName, $name, 'data-parsley-equalto', "input[name=" . $elementName . "_confirmation]");
				}
				else if($name == 'digits'){
					$this->addAttribute($attributes, $elementName, $name, 'data-parsley-type', 'digits');
					$this->addAttribute($attributes, $elementName, $name, 'data-parsley-length', "[$value,$value]");
				}
				else if($name == 'digits_between'){
					$this->addAttribute($attributes, $elementName, $name, 'data-parsley-type', 'digits');
					$this->addAttribute($attributes, $elementName, $name, 'data-parsley-length', "[$value]");
				}
				else if($name == 'email'){
					$this->addAttribute($attributes, $elementName, $name, 'type', 'email');
				}
				else if($name == 'in'){
					$value = str_replace(',', '|', $value);
					$this->addAttribute($attributes, $elementName, $name, 'pattern', "/^($value)$/");
				}
				else if($name == 'integer'){
					$this->addAttribute($attributes, $elementName, $name, 'type', 'number');
				}
				else if($name == 'max'){
					// Assume we're working with a string by default.  We'll change this later if this field is determined to be numeric.
					$this->addAttribute($attributes, $elementName, $name, 'maxlength', $value);
				}
				else if($name == 'min'){
					// Assume we're working with a string by default.  We'll change this later if this field is determined to be numeric.
					$this->addAttribute($attributes, $elementName, $name, 'minlength', $value);
				}
				else if($name == 'not_in'){
					$value = str_replace(',', '|', $value);
					$this->addAttribute($attributes, $elementName, $name, 'pattern', '/^(?!(one|two|three)$)/');
				}
				else if($name == 'numeric'){
					$this->addAttribute($attributes, $elementName, $name, 'data-parsley-type', 'number');
				}
				else if($name == 'regex'){
					$this->addAttribute($attributes, $elementName, $name, 'pattern', $value);
				}
				else if($name == 'required'){
					$this->addAttribute($attributes, $elementName, $name, 'required', '');
				}
				else if($name == 'size'){
					// Assume we're working with a string by default.  We'll change this later if this field is determined to be numeric.
					$this->addAttribute($attributes, $elementName, $name, 'data-parsley-length', "[$value,$value]");
				}
				else if($name == 'same'){
					$this->addAttribute($attributes, $elementName, $name, 'data-parsley-equalto', "input[name=$value]");
				}
				else if($name == 'url'){
					$this->addAttribute($attributes, $elementName, $name, 'type', 'url');
				}
			}

			if($this->isElementNumeric($attributes)){
				// Laravel assumes fields are strings by default, so we create string related Parsley rules by default.
				// If a rule is exists for a given field specifying it as numeric in Laravel, we must change the string related validators in Parsley to their number related equivalents.
				$this->switchStringValidatorsToNumberValidators($attributes);
			}
		}

		$this->attributesByElementName = $attributesByElementName;
	}

	static function buildJS($validator, $formSelector='form'){
		$parsley = new Parsley($validator, $formSelector);
		return $parsley->buildJSForAttributes($formSelector, $formSelector, $parsley->attributesByElementName);
	}

	private function addAttribute(&$attributes, $elementName, $ruleName, $attributeName, $attributeValue){

		if($attributeName == 'pattern'){
			// Escape backslashes so they'll end up in the final javascript regex.
			$attributeValue = str_replace('\\', '\\\\', $attributeValue);
		}

		if(isset($attributes[$attributeName])){
			throw new \Exception("The '$ruleName' rule on the '$elementName' field conflicts with a previous rule!");
		}

		$attributes[$attributeName] = $attributeValue;

		$customMessages = $this->validator->getCustomMessages();
		$message = @$customMessages["$elementName.$ruleName"];
		if($message){
			$errorAttributeName = $attributeName;

			$prefix = 'data-parsley-';
			if(strpos($errorAttributeName, $prefix) === 0){
				$errorAttributeName = substr($errorAttributeName, strlen($prefix));
			}

			$attributes["data-parsley-$errorAttributeName-message"] = $message;
		}
	}

	private function isElementNumeric($attributes){
		return @$attributes['type'] == 'number' || @$attributes['data-parsley-type'] == 'number';
	}

	private function switchStringValidatorsToNumberValidators(&$attributes){
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