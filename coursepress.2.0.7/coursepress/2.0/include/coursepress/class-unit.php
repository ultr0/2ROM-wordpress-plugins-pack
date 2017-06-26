<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access!' );
}

if ( ! class_exists( 'CoursePress_Unit' ) ) :
	class CoursePress_Unit {
		var $attributes = array(
			'show_title' => false,
			'feature_image' => '',
			'feature_image_id' => 0,
			'unit_availability' => 'instant',
			'unit_date_availability' => '',
			'unit_delay_days' => '',
			'force_current_unit_completion' => false,
			'force_current_unit_successful_completion' => false,
			'pages' => array(),
		);

		function get_unit( $unit_id = 0 ) {
			if ( empty( $unit_id ) ) {
				return;
			}

			$unit = get_post( $unit_id );
			$unit->meta = array();

			foreach ( $this->attributes as $key => $default_value ) {
				$meta_value = get_post_meta( $unit_id, $key, true );
				$meta_value = empty( $meta_value ) ? $value : $meta_value;
				$unit->meta[ $key ] = $meta_value;
			}

			/**
			 * Fires after retrieving unit from DB but before return unit object.
			 **/
			$unit = apply_filters( 'coursepress_pre_get_unit', $unit );

			return $unit;
		}
	}
endif;
