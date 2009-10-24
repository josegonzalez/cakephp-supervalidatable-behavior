<?php
App::import('Security');
class SuperValidatableTestCase extends CakeTestCase {
    var $fixtures = array( 'app.super_validatable' );
	
	function startTest() {
		$this->Validatable =& ClassRegistry::init('Validatable');
		$this->Validatable->Behaviors->attach('SuperValidatable');
	}
    
    function testconfirmFields() {

		// Testing default hash option salted sha1
		$hashedPw = Security::hash('abcdefg', 'sha1', true);
		$data = array(
			'Validatable' => array(
				'password' => $hashedPw,
				'pw_confirm' => 'abcdefg',
				'another_field' => 'abcdefg'
			)
		);
		$this->Validatable->data = $data;
		$valid = $this->Validatable->confirmFields(
			array('password' => $hashedPw),
			array( 
				'fields' => array('pw_confirm', 'another_field'),
				'hash' => true
			)
		);
		$this->assertTrue($valid);

		// Testing different hash Options unsalted md5
		$hashedPw = Security::hash('abcdefg', 'md5', false);
		$data = array(
			'Validatable' => array(
				'password' => $hashedPw,
				'pw_confirm' => 'abcdefg',
				'another_field' => 'abcdefg'
			)
		);
		$this->Validatable->data = $data;
		$valid = $this->Validatable->confirmFields(
			array('password' => $hashedPw),
			array(
				'fields' => array('pw_confirm', 'another_field'),
				'hash' => true,
				'hashOptions' => array(
					'method' => 'md5',
					'salt' => false
				)
			)
		);
		$this->assertTrue($valid);
		
		// Testing salt hashOption
		$valid = $this->Validatable->confirmFields(
			array('password' => $hashedPw),
			array(
				'fields' => array('pw_confirm', 'another_field'),
				'hash' => true,
				'hashOptions' => array(
					'method' => 'md5',
					'salt' => true
				)
			)
		);
		$this->assertFalse($valid);

		// testing plain default
		$data = array(
			'Validatable' => array(
				'password' => 'abcdefg',
				'pw_confirm' => 'abcdefg',
				'another_field' => 'abcdefg'
			)
		);
		$this->Validatable->data = $data;
		$valid = $this->Validatable->confirmFields(
			array('password' => 'abcdefg'),
			array('fields' => array('pw_confirm', 'another_field'))
		);
		$this->assertTrue($valid);

		// testing plain default false
		$data = array(
			'Validatable' => array(
				'password' => 'abcdefg',
				'pw_confirm' => 'abcdefg',
				'another_field' => 'argkifsdföpj'
			)
		);
		$this->Validatable->data = $data;
		$valid = $this->Validatable->confirmFields(
			array('password' => 'abcdefg'),
			array('fields' => array('pw_confirm', 'another_field'))
		);
		$this->assertFalse($valid);
		
		// testing emypty fields
		$valid = $this->Validatable->confirmFields(
			array('password' => 'abcdefg'),
			array('fields' => array())
		);
		$this->assertFalse($valid);

		// testing field exclusion when hashing		
		$data = array(
			'Validatable' => array(
				'password' => $hashedPw,
				'pw_confirm' => 'abcdefg',
				'another_field' => 'abcdefg'
			)
		);
		$this->Validatable->data = $data;
		$valid = $this->Validatable->confirmFields(
			array('password' => $hashedPw),
			array(
				'hash' => true,
				'fields' => array('pw_confirm'),
				'hashOptions' => array(
					'fields' => array('another_field')
				)
			)
		);
		$this->assertFalse($valid);

		// testing skipping	
		$data = array(
			'Validatable' => array(
				'password' => 'abcdefg',
				'pw_confirm' => 'abcdefg',
				'another_field' => 'abcdefg'
			)
		);
		$this->Validatable->data = $data;
		$valid = $this->Validatable->confirmFields(
			array('password' => 'abcdefg'),
			array(
				'fields' => array('pw_confirm', 'invalid')
			)
		);
		$this->assertFalse($valid);
		$valid = $this->Validatable->confirmFields(
			array('password' => $hashedPw),
			array(
				'fields' => array('pw_confirm', 'invalid'),
				'skip' => true
			)
		);
		$this->assertTrue($valid);
    }
} 
?>