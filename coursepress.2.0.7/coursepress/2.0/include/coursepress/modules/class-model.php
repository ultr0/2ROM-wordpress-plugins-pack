<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access!' );
}

if ( ! class_exists( 'CoursePress_Module_Model' ) ) :
	class CoursePress_Module_Model extends CoursePress_Utility {
		var $type;
		var $answer;

		var $choices = array();
		var $attributes = array(
			'show_title' => true,
			'required' => false,
			'assessable' => false,
			'use_timer' => false,
			'allow_retries' => true,
			'retry_attempts' => 0,
			'minimum_required_grade' => 100,
			'duration' => 0,
		);
	}
endif;
