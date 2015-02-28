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
					$attributes['data-parsley-length'] = "[$value]";
				}
				else if($name == 'digits'){
					$attributes['data-parsley-type'] = "digits";
					$attributes['data-parsley-length'] = "[$value,$value]";
				}
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