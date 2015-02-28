<?php

use Mmcev106\LaravelParsleyValidation\Parsley;

class ParsleyTest extends PHPUnit_Framework_TestCase {

	public function testBuildJS()
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
			),
    		array(
				'message_test_field.required' => 'Test message!'
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
			'message_test_field' => array(
				'data-parsley-required-message' => 'Test message!'
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