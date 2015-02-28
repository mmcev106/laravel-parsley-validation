<?php

use Mmcev106\LaravelParsleyValidation\Parsley;

class ParsleyTest extends PHPUnit_Framework_TestCase {

	public function test_max(){
    	$validator = Validator::make(
    		array(),
    		array(
			    'max_string_test_field' => 'max:1',
			    'max_integer_test_field' => 'integer|max:2',
			    'max_numeric_test_field' => 'numeric|max:3'
			)
		);

		$this->assertValidBuildJSOutput($validator, array(
			'max_string_test_field' => array(
				'maxlength' => '1'
			),
			'max_integer_test_field' => array(
				'type' => 'number',
				'max' => '2'
			),
			'max_numeric_test_field' => array(
				'data-parsley-type' => 'number',
				'max' => '3'
			)
		));
	}

	public function test_min(){
    	$validator = Validator::make(
    		array(),
    		array(
			    'min_string_test_field' => 'min:1',
			    'min_integer_test_field' => 'integer|min:2',
			    'min_numeric_test_field' => 'numeric|min:3'
			)
		);

		$this->assertValidBuildJSOutput($validator, array(
			'min_string_test_field' => array(
				'minlength' => '1'
			),
			'min_integer_test_field' => array(
				'type' => 'number',
				'min' => '2'
			),
			'min_numeric_test_field' => array(
				'data-parsley-type' => 'number',
				'min' => '3'
			)
		));
	}

	public function test_everything_else()
	{
    	$validator = Validator::make(
    		array(),
    		array(
			    'accepted_test_field' => 'accepted',
			    'active_url_test_field' => 'active_url',
			    // 'after_test_field' => 'after:01/01/2001',
			    'alpha_test_field' => 'alpha',
			    'alpha_dash_test_field' => 'alpha_dash',
			    'alpha_num_test_field' => 'alpha_num',
			    // 'array_test_field' => 'array',
			    // 'before_test_field' => 'before:01/01/2001',
			    'between_test_field' => 'between:1,2',
			    // 'confirmed_test_field' => 'confirmed',
			    // 'date_test_field' => 'date',
			    // 'date_format_test_field' => 'date_format:MM/DD/YYYY',
			    // 'different_test_field' => 'different:another_test_field',
			    'digits_test_field' => 'digits:3',
			    'digits_between_test_field' => 'digits_between:2,7',
			    'email_test_field' => 'email',
			    // 'image_test_field' => 'image',
			    'in_test_field' => 'in:one,two,three',
			    'integer_test_field' => 'integer',
			    // 'ip_test_field' => 'ip',
			    // 'mimes_test_field' => 'mimes:jpeg,bmp,png'
			    'not_in_test_field' => 'not_in:one,two,three',
			    'numeric_test_field' => 'numeric'
			),
    		array(
				'message_test_field.test_validator_name' => 'Test message!'
			)
		);

		$this->assertValidBuildJSOutput($validator, array(
			'accepted_test_field' => array(
				'pattern' => '/^(yes|on|1)$/'
			),
			'active_url_test_field' => array(
				'data-parsley-type' => 'url'
			),
			'alpha_test_field' => array(
				'pattern' => '/^[A-z]?$/'
			),
			'alpha_dash_test_field' => array(
				'pattern' => '/^[A-z-_]?$/'
			),
			'alpha_num_test_field' => array(
				'data-parsley-type' => 'alphanum'
			),
			'between_test_field' => array(
				'data-parsley-length' => '[1,2]'
			),
			'digits_test_field' => array(
				'data-parsley-type' => 'digits',
				'data-parsley-length' => '[3,3]'
			),
			'digits_between_test_field' => array(
				'data-parsley-type' => 'digits',
				'data-parsley-length' => '[2,7]'
			),
			'email_test_field' => array(
				'type' => 'email',
			),
			'in_test_field' => array(
				'pattern' => '/^(one|two|three)$/',
			),
			'integer_test_field' => array(
				'type' => 'number',
			),
			'not_in_test_field' => array(
				'pattern' => '/^(?!(one|two|three)$)/',
			),
			'numeric_test_field' => array(
				'data-parsley-type' => 'number',
			),
			'message_test_field' => array(
				'data-parsley-test_validator_name-message' => 'Test message!'
			)
		));
	}

	// Asserts that the output of buildJS() returns valid javascript, and all the expected jQuery calls.
	private function assertValidBuildJSOutput($validator, $expectedAttributesByElementName){
		$formSelector = '#myForm';
		$js = Parsley::buildJS($formSelector, $validator);
		$lines = explode(PHP_EOL, $js);
		$lineNumber = 0;

		$this->assertEquals('', $lines[$lineNumber++]);
		$this->assertEquals('<script>', $lines[$lineNumber++]);
		$this->assertEquals('  $(function(){', $lines[$lineNumber++]);

		foreach($expectedAttributesByElementName as $elementName=>$attributes){
			foreach($attributes as $attributeName=>$value){
				$this->assertEquals("    $('$formSelector input[name=$elementName], $formSelector select[name=$elementName]').attr('$attributeName', '$value');", $lines[$lineNumber++]);
			}
		}

		$this->assertEquals("    $('$formSelector').parsley();", $lines[$lineNumber++]);
		$this->assertEquals('  })', $lines[$lineNumber++]);
		$this->assertEquals('</script>', $lines[$lineNumber++]);
		$this->assertEquals('', $lines[$lineNumber++]);
	}

}