<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access!' );
}

if ( ! class_exists( 'CoursePress_Admin_Module_Model' ) ) :
	class CoursePress_Admin_Module_Model extends CoursePress_Module_Model {
		var $icon = '';

		function get_edit_template() {
		}
	}
endif;
