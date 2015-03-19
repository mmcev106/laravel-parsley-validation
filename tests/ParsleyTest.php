<?php

use Mmcev106\LaravelParsleyValidation\Parsley;

class ParsleyTest extends PHPUnit_Framework_TestCase {

	public function test_between_rule(){
		$validator = Validator::make(
    		array(),
    		array(
			    'between_string_test_field' => 'between:1,2',
			    'between_integer_test_field' => 'integer|between:3,4',
			    'between_numeric_test_field' => 'numeric|between:5,6'
			)
		);

		$this->assertValidBuildJSOutput($validator, array(
			'between_string_test_field' => array(
				'data-parsley-length' => '[1,2]'
			),
			'between_integer_test_field' => array(
				'type' => 'number',
				'data-parsley-range' => '[3,4]'
			),
			'between_numeric_test_field' => array(
				'data-parsley-type' => 'number',
				'data-parsley-range' => '[5,6]'
			)
		));
	}

	public function test_max_rule(){
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

	public function test_min_rule(){
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

	public function test_size_rule(){
    	$validator = Validator::make(
    		array(),
    		array(
			    'size_string_test_field' => 'size:1',
			    'size_integer_test_field' => 'integer|size:2',
			    'size_numeric_test_field' => 'numeric|size:3'
			)
		);

		$this->assertValidBuildJSOutput($validator, array(
			'size_string_test_field' => array(
				'data-parsley-length' => '[1,1]'
			),
			'size_integer_test_field' => array(
				'type' => 'number',
				'data-parsley-range' => '[2,2]'
			),
			'size_numeric_test_field' => array(
				'data-parsley-type' => 'number',
				'data-parsley-range' => '[3,3]'
			)
		));
	}

	public function test_all_other_rules()
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
			    'confirmed_test_field' => 'confirmed',
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
			    'numeric_test_field' => 'numeric',
			    'regex_test_field' => 'regex:/ensure backslashes escape parenthesis in javascript \\(\\)/',
			    'required_test_field' => 'required',
			    // 'required_if_test_field' => 'required_if',
			    // 'required_with_test_field' => 'required_with',
			    // 'required_with_all_test_field' => 'required_with_all',
			    // 'required_without_test_field' => 'required_without',
			    // 'required_without_all_test_field' => 'required_without_all',
			    'same_test_field' => 'same:another_test_field',
			    // 'unique_test_field' => 'unique:table,column,except,idColumn'
			    'url_test_field' => 'url'
			),
    		array(
				'accepted_test_field.accepted' => 'accepted_test_field message!',
				'active_url_test_field.active_url' => 'active_url_test_field message!',
				'alpha_test_field.alpha' => 'alpha_test_field message!',
				'alpha_dash_test_field.alpha_dash' => 'alpha_dash_test_field message!',
				'alpha_num_test_field.alpha_num' => 'alpha_num_test_field message!',
				'confirmed_test_field_confirmation.confirmed' => 'confirmed_test_field message!',
				'digits_test_field.digits' => 'digits_test_field message!',
				'digits_between_test_field.digits_between' => 'digits_between_test_field message!',
				'email_test_field.email' => 'email_test_field message!',
				'in_test_field.in' => 'in_test_field message!',
				'integer_test_field.integer' => 'integer_test_field message!',
				'not_in_test_field.not_in' => 'not_in_test_field message!',
				'numeric_test_field.numeric' => 'numeric_test_field message!',
				'regex_test_field.regex' => 'regex_test_field message!',
				'required_test_field.required' => 'required_test_field message!',
				'same_test_field.same' => 'same_test_field message!',
				'url_test_field.url' => 'url_test_field message!'
			)
		);

		$this->assertValidBuildJSOutput($validator, array(
			'accepted_test_field' => array(
				'pattern' => '/^(yes|on|1)$/',
				'data-parsley-pattern-message' => 'accepted_test_field message!'
			),
			'active_url_test_field' => array(
				'data-parsley-type' => 'url',
				'data-parsley-type-message' => 'active_url_test_field message!'
			),
			'alpha_test_field' => array(
				'pattern' => '/^[A-z]?$/',
				'data-parsley-pattern-message' => 'alpha_test_field message!'
			),
			'alpha_dash_test_field' => array(
				'pattern' => '/^[A-z-_]?$/',
				'data-parsley-pattern-message' => 'alpha_dash_test_field message!'
			),
			'alpha_num_test_field' => array(
				'data-parsley-type' => 'alphanum',
				'data-parsley-type-message' => 'alpha_num_test_field message!'
			),
			'confirmed_test_field_confirmation' => array(
				'required' => '',
				'data-parsley-required-message' => 'confirmed_test_field message!',
				'data-parsley-equalto' => 'input[name=confirmed_test_field]',
				'data-parsley-equalto-message' => 'confirmed_test_field message!',
			),
			'digits_test_field' => array(
				'data-parsley-type' => 'digits',
				'data-parsley-type-message' => 'digits_test_field message!',
				'data-parsley-length' => '[3,3]',
				'data-parsley-length-message' => 'digits_test_field message!'
			),
			'digits_between_test_field' => array(
				'data-parsley-type' => 'digits',
				'data-parsley-type-message' => 'digits_between_test_field message!',
				'data-parsley-length' => '[2,7]',
				'data-parsley-length-message' => 'digits_between_test_field message!'
			),
			'email_test_field' => array(
				'type' => 'email',
				'data-parsley-type-message' => 'email_test_field message!'
			),
			'in_test_field' => array(
				'pattern' => '/^(one|two|three)$/',
				'data-parsley-pattern-message' => 'in_test_field message!'
			),
			'integer_test_field' => array(
				'type' => 'number',
				'data-parsley-type-message' => 'integer_test_field message!'
			),
			'not_in_test_field' => array(
				'pattern' => '/^(?!(one|two|three)$)/',
				'data-parsley-pattern-message' => 'not_in_test_field message!'
			),
			'numeric_test_field' => array(
				'data-parsley-type' => 'number',
				'data-parsley-type-message' => 'numeric_test_field message!'
			),
			'regex_test_field' => array(
				'pattern' => '/ensure backslashes escape parenthesis in javascript \\\\(\\\\)/',
				'data-parsley-pattern-message' => 'regex_test_field message!'
			),
			'required_test_field' => array(
				'required' => '',
				'data-parsley-required-message' => 'required_test_field message!'
			),
			'same_test_field' => array(
				'data-parsley-equalto' => 'input[name=another_test_field]',
				'data-parsley-equalto-message' => 'same_test_field message!'
			),
			'url_test_field' => array(
				'type' => 'url',
				'data-parsley-type-message' => 'url_test_field message!'
			)
		));
	}

	function test_default_form_selector(){
		$validator = Validator::make(
    		array(),
    		array(
			    'test_field' => 'required',
			)
		);

		$this->assertValidBuildJSOutput($validator, array(
			'test_field' => array(
				'required' => ''
			),
		), '#someCustomFormId');
	}

	function test_conflicting_rules(){
		$validator = Validator::make(
    		array(),
    		array(
    			// The 'accepted' and 'regex' rules both generate a 'pattern' attribute in Parsley.
    			// This logically doesn't make sense, and should throw an exception (as opposed to one quietly overriding the other).
			    'test_field' => 'accepted|regex:/123/',
			)
		);

		$exception = NULL;
		try{
			$this->assertValidBuildJSOutput($validator, NULL);
		}
		catch(Exception $e){
			$exception = $e;
		}

		// Assert that an exception is thrown whenever one rule conflicts with another.
		$this->assertEquals("The 'regex' rule on the 'test_field' field conflicts with a previous rule!", $exception->getMessage());
	}

	// Asserts that the output of buildJS() returns valid javascript, and all the expected jQuery calls.
	private function assertValidBuildJSOutput($validator, $expectedAttributesByElementName, $formSelector=NULL){
		if($formSelector){
			$js = Parsley::buildJS($validator, $formSelector);
		}
		else{
			$js = Parsley::buildJS($validator);
			$formSelector = 'form';  // This is what the default formSelector should end up as.
		}

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