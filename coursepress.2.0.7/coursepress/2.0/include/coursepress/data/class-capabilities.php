<?php
/**
 * Data access module.
 *
 * @package CoursePress
 */

/**
 * Helper class for working with CoursePress capabilities.
 *
 * Previously CoursePress_Capabilities
 *
 * @since 1.0.0
 */
class CoursePress_Data_Capabilities {
	protected static $is_admin = false;
	protected static $current_caps = array();

	public static $capabilities = array(
		'instructor' => array(
			/* General */
			'coursepress_dashboard_cap' => 1,
			'coursepress_courses_cap' => 1,
			'coursepress_instructors_cap' => 0, // DEPRECATED
			'coursepress_students_cap' => 1,
			'coursepress_assessment_cap' => 1,
			'coursepress_reports_cap' => 1,
			'coursepress_notifications_cap' => 1,
			'coursepress_discussions_cap' => 1,
			'coursepress_settings_cap' => 1,
			/* Courses */
			'coursepress_create_course_cap' => 1,
			'coursepress_view_others_course_cap' => 1,
			'coursepress_update_course_cap' => 1,
			'coursepress_update_my_course_cap' => 1,
			'coursepress_update_all_courses_cap' => 0, // NOT IMPLEMENTED YET
			'coursepress_delete_course_cap' => 1,
			'coursepress_delete_my_course_cap' => 1,
			'coursepress_delete_all_courses_cap' => 0, // NOT IMPLEMENTED YET
			'coursepress_change_course_status_cap' => 0,
			'coursepress_change_my_course_status_cap' => 1,
			'coursepress_change_all_courses_status_cap' => 0, // NOT IMPLEMENTED YET
			/* Units */
			'coursepress_create_course_unit_cap' => 1,
			'coursepress_view_all_units_cap' => 0,
			'coursepress_update_course_unit_cap' => 1,
			'coursepress_update_my_course_unit_cap' => 1,
			'coursepress_update_all_courses_unit_cap' => 0, // NOT IMPLEMENTED YET
			'coursepress_delete_course_units_cap' => 1,
			'coursepress_delete_my_course_units_cap' => 1,
			'coursepress_delete_all_courses_units_cap' => 0, // NOT IMPLEMENTED YET
			'coursepress_change_course_unit_status_cap' => 1,
			'coursepress_change_my_course_unit_status_cap' => 1,
			'coursepress_change_all_courses_unit_status_cap' => 0, // NOT IMPLEMENTED YET
			/* Instructors */
			'coursepress_assign_and_assign_instructor_course_cap' => 0,
			'coursepress_assign_and_assign_instructor_my_course_cap' => 1,
			/* Facilitators */
			'coursepress_assign_my_course_facilitator_cap' => 1,
			'coursepress_assign_facilitator_cap' => 1,
			/* Classes */
			'coursepress_add_new_classes_cap' => 0,
			'coursepress_add_new_my_classes_cap' => 0,
			'coursepress_delete_classes_cap' => 0,
			'coursepress_delete_my_classes_cap' => 0,
			/* Students */
			'coursepress_invite_students_cap' => 0,
			'coursepress_invite_my_students_cap' => 1,
			'coursepress_withdraw_students_cap' => 0,
			'coursepress_withdraw_my_students_cap' => 1,
			'coursepress_add_move_students_cap' => 0,
			'coursepress_add_move_my_students_cap' => 1,
			'coursepress_add_move_my_assigned_students_cap' => 1,
			// 'coursepress_change_students_group_class_cap' => 0,
			// 'coursepress_change_my_students_group_class_cap' => 0,
			//'coursepress_add_new_students_cap' => 0, // DEPRECATED
			//'coursepress_send_bulk_my_students_email_cap' => 0, // DEPRECATED
			//'coursepress_send_bulk_students_email_cap' => 0, // DEPRECATED
			//'coursepress_delete_students_cap' => 0, // DEPRECATED
			/* Groups */
			'coursepress_settings_groups_page_cap' => 0,
			// 'coursepress_settings_shortcode_page_cap' => 0,
			/* Notifications */
			'coursepress_create_my_assigned_notification_cap' => 1,
			'coursepress_create_my_notification_cap' => 1,
			'coursepress_update_notification_cap' => 1,
			'coursepress_update_my_notification_cap' => 1,
			'coursepress_delete_notification_cap' => 1,
			'coursepress_delete_my_notification_cap' => 1,
			'coursepress_change_notification_status_cap' => 0,
			'coursepress_change_my_notification_status_cap' => 1,
			/* Discussions */
			'coursepress_create_my_assigned_discussion_cap' => 1,
			'coursepress_create_my_discussion_cap' => 1,
			'coursepress_update_discussion_cap' => 0,
			'coursepress_update_my_discussion_cap' => 1,
			'coursepress_delete_discussion_cap' => 0,
			'coursepress_delete_my_discussion_cap' => 1,
			'coursepress_change_discussion_status_cap' => 0,
			'coursepress_change_my_discussion_status_cap' => 1,
			/* Certificates */
			'coursepress_certificates_cap' => 0,
			'coursepress_create_certificates_cap' => 0,
			'coursepress_update_certificates_cap' => 0,
			'coursepress_delete_certificates_cap' => 0,
			/* Course Categories */
			'coursepress_course_categories_manage_terms_cap' => 1,
			'coursepress_course_categories_edit_terms_cap' => 1,
			'coursepress_course_categories_delete_terms_cap' => 0,
			/* Posts and Pages */
			'edit_pages' => 0,
			'edit_published_pages' => 0,
			'edit_posts' => 0,
			'publish_pages' => 0,
			'publish_posts' => 0,
			'edit_comments' => 1,
		),
	);

	public static function init() {
		add_action( 'init', array( __CLASS__, 'init_caps' ), 1 );
		add_action( 'set_user_role', array( __CLASS__, 'assign_role_capabilities' ), 10, 3 );
		add_action( 'wp_login', array( __CLASS__, 'restore_capabilities' ), 10, 2 );
		add_action( 'admin_init', array( __CLASS__, 'fix_admin_capabilities' ) );
		add_filter( 'user_has_cap', array( __CLASS__, 'user_cap' ), 99, 3 );

		if ( ! current_user_can( 'manage_options' ) ) {
			// Filter the capability of the current user

			// If current user can view and create categories but not edit
			add_filter( 'tag_row_actions', array( __CLASS__, 'filter_row_actions' ), 10, 2 );
		}
	}

	public static function init_caps() {
		global $current_user;

		self::$is_admin = current_user_can( 'manage_options' );
		self::course_capabilities();

		if ( self::$is_admin ) {
			// Enable edit course cap
			$current_user->allcaps['edit_course'] = true;
			//$current_user->allcaps['coursepress_delete_course_cap'] = true;
			$current_caps = self::$capabilities['instructor'];
			self::$current_caps = $current_caps = array_map( '__return_true', $current_caps );
		} elseif ( self::is_instructor() || self::is_facilitator() ) {
			global $current_user;

			$current_caps = CoursePress_Data_Capabilities::get_instructor_capabilities();
			self::$current_caps = array_filter( $current_caps );

			// Reset user caps but don't save any changes!
			if ( ! empty( $current_user->allcaps ) ) {
				// Process caps
				$current_user->allcaps = wp_parse_args( $current_caps, $current_user->allcaps );
				$current_user->caps = wp_parse_args( $current_caps, $current_user->caps );
			}
		}
	}

	/**
	 * Assign appropriate CoursePress capabilities for roles
	 *
	 * @since 1.2.3.3.
	 */
	public static function assign_role_capabilities( $user_id, $role, $old_role ) {

		if ( 'administrator' == $role ) {
			self::assign_admin_capabilities( $user_id );
		} else {

			// Remove all CoursePress capabilities
			self::remove_instructor_capabilities( $user_id );

			$instructor_courses = CoursePress_Data_Instructor::get_assigned_courses_ids( $user_id );
			// If they are an instructor, give them their appropriate capabilities back
			if ( ! empty( $instructor_courses ) ) {
				self::assign_instructor_capabilities( $user_id );
			}

			// Add facilitator role
			$facilitated_courses = CoursePress_Data_Facilitator::get_facilitated_courses( $user_id, array( 'any' ), true, 0, 1 );
			if ( ! empty( $facilitated_courses ) ) {
				self::assign_facilitator_capabilities( $user_id );
			}
		}
	}

	/**
	 * Make sure the admin has required capabilities.
	 *
	 * This function is only executed on every login, so we do not worry about
	 * performance too much here. We make sure that the current user is assigned
	 * the correct user capabilities.
	 *
	 * @since 1.2.3.3
	 */
	public static function restore_capabilities( $user_login = false, $user ) {
		if ( user_can( $user, 'manage_options' ) ) {
			self::assign_admin_capabilities( $user );
		} else {
			$count = CoursePress_Data_Instructor::get_course_count( $user->ID );
			if ( ! empty( $count ) ) {
				self::assign_instructor_capabilities( $user->ID );
			} else {
				self::remove_instructor_capabilities( $user->ID );
			}

			// Add facilitator role
			$facilitated_courses = CoursePress_Data_Facilitator::get_facilitated_courses( $user->ID, array( 'any' ), true, 0, 1 );
			if ( ! empty( $facilitated_courses ) ) {
				self::assign_facilitator_capabilities( $user->ID );
			}
		}
	}

	public static function remove_instructor_capabilities( $user ) {
		if ( ! is_object( $user ) ) {
			$user = new WP_User( $user );
		}
		$capability_types = self::$capabilities['instructor'];
		foreach ( $capability_types as $key => $value ) {
			$user->remove_cap( $key );
		}
	}

	public static function fix_admin_capabilities() {
		$user_id = get_current_user_id();
		if ( user_can( $user_id, 'manage_options' )
			&& false === user_can( $user_id, 'coursepress_settings_cap' ) ) {
			self::assign_admin_capabilities( $user_id );
		}
	}

	public static function assign_admin_capabilities( $user ) {
		if ( ! is_object( $user ) ) {
			$user_id = CoursePress_Helper_Utility::get_id( $user );
			$user = new WP_User( $user_id );
		}

		$capability_types = self::$capabilities['instructor'];
		foreach ( $capability_types as $key => $value ) {
			$user->add_cap( $key );
		}
	}

	public static function can_manage_courses( $user_id = '' ) {
		global $current_user;

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		if ( user_can( $user_id, 'manage_options' ) ) {
			$return = true;
		} else {
			$return = ! empty( $current_user->allcaps['coursepress_courses_cap'] );
		}

		return $return;
	}

	/**
	 * Can an instructor view other intructors courses?
	 *
	 * @since 2.0
	 * @param (int) $user_id	Default current user ID.
	 * @return boolen
	 **/
	public static function can_view_others_course( $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );

		if ( ! $return ) {
			$return = user_can( $user_id, 'coursepress_view_others_course_cap' );
		}

		return $return;
	}

	/**
	 * Can add course?
	 *
	 * @since 2.0.0
	 *
	 * @param integer $user_id User ID or empty.
	 *
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_create_course( $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$return = self::$is_admin;

		if ( ! $return ) {
			if ( self::can_manage_courses( $user_id ) ) {
				$return = user_can( $user_id, 'coursepress_create_course_cap' );
			}
		}

		return $return;
	}

	/**
	 * Can the user update this course?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function can_update_course( $course_id, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );
		$post_status = get_post_status( $course_id );

		if ( false === $return ) {
			$course_creator = self::is_course_creator( $course_id, $user_id );
			$is_instructor = self::is_course_instructor( $course_id, $user_id );
			$is_facilitator = CoursePress_Data_Facilitator::is_course_facilitator( $course_id, $user_id );

			if ( $course_creator ) {
				if ( in_array( $post_status, array( 'private', 'draft' ) ) ) {
					// If the course is not public yet, always give the owner write permission
					$return = true;
				} else {
					$return = user_can( $user_id, 'coursepress_update_my_course_cap' );
				}
			} elseif ( $is_instructor || $is_facilitator ) {
				$return = user_can( $user_id, 'coursepress_update_course_cap' );
			}
		}

		return $return;
	}

	/**
	 * Can the user delete this course?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function can_delete_course( $course_id, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );

		if ( ! $return ) {
			if ( (int) $course_id > 0 ) {
				$course_creator = self::is_course_creator( $course_id, $user_id );
				$is_instructor = self::is_course_instructor( $course_id, $user_id );
				$is_facilitator = CoursePress_Data_Facilitator::is_course_facilitator( $course_id, $user_id );

				if ( $course_creator ) {
					$return = user_can( $user_id, 'coursepress_delete_my_course_cap' );
				} elseif ( $is_instructor || $is_facilitator ) {
					$return = user_can( $user_id, 'coursepress_delete_course_cap' );
				}
			} else {
				$return = user_can( $user_id, 'coursepress_delete_course_cap' ) || user_can( $user_id, 'coursepress_delete_my_course_cap' );
			}
		}

		return $return;
	}

	/**
	 * Can the user change the course status?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function can_change_course_status( $course_id, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );

		if ( ! $return ) {
			if ( (int) $course_id > 0 ) {
				$course_creator = self::is_course_creator( $course_id, $user_id );
				$is_instructor = self::is_course_instructor( $course_id, $user_id );
				$is_facilitator = CoursePress_Data_Facilitator::is_course_facilitator( $course_id, $user_id );

				if ( $course_creator ) {
					$return = user_can( $user_id, 'coursepress_change_my_course_status_cap' );
				} elseif ( $is_instructor || $is_facilitator ) {
					$return = user_can( $user_id, 'coursepress_change_course_status_cap' );
				}
			} else {
				$return = user_can( $user_id, 'coursepress_change_my_course_status_cap' ) || user_can( $user_id, 'coursepress_change_course_status_cap' );
			}
		}

		return $return;
	}

	public static function can_manage_categories( $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );

		if ( ! $return ) {
			$return = user_can( $user_id, 'coursepress_course_categories_manage_terms_cap' );
		}

		return $return;
	}

	/**
	 * Can the user create units?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function can_create_unit( $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}
		/** This filter is documented in include/coursepress/helper/class-setting.php */
		$capability = apply_filters( 'coursepress_capabilities', 'coursepress_create_course_unit_cap' );
		if ( user_can( $user_id, $capability ) ) {
			return true;
		}
		return false;

	}

	/**
	 * Can the user create units in this course?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function can_create_course_unit( $course_id, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );

		if ( ! $return ) {
			$course_creator = self::is_course_creator( $course_id, $user_id );
			$is_instructor = self::is_course_instructor( $course_id, $user_id );
			$is_facilitator = CoursePress_Data_Facilitator::is_course_facilitator( $course_id, $user_id );

			if ( $course_creator ) {
				$post_status = get_post_status( $course_id );

				if ( 'publish' != $post_status ) {
					// If the course is not public yet, always give the owner the write permission
					$return = true;
				} else {
					$return = user_can( $user_id, 'coursepress_update_my_course_unit_cap' );
				}
			} elseif ( $is_instructor || $is_facilitator ) {
				$return = self::can_create_unit() || user_can( $user_id, 'coursepress_update_course_unit_cap' );
			}
		}

		return $return;
	}

	/**
	 * Can the user view units?
	 *
	 * @since 1.0.0
	 *
	 * @param (int) $course_id	The ID of the currently course.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_view_course_units( $course_id, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );

		if ( ! $return ) {
			if ( self::can_manage_courses( $user_id ) ) {
				$return = self::can_update_course( $course_id );
			}
		}

		return $return;

	}

	/**
	 * Can the user update the units?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function can_update_course_unit( $course, $unit_id = '', $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}
		/**
		 * Update own units
		 */
		$my_unit = self::is_unit_creator( $unit_id, $user_id );
		if ( $my_unit ) {
			/** This filter is documented in include/coursepress/helper/class-setting.php */
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_update_my_course_unit_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		} else {
			/** This filter is documented in include/coursepress/helper/class-setting.php */
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_update_course_unit_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Can the user delete the units?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function can_delete_course_unit( $course, $unit_id = '', $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}
		/**
		 * Delete own units
		 */
		$my_unit = self::is_unit_creator( $unit_id, $user_id );
		if ( $my_unit ) {
			/** This filter is documented in include/coursepress/helper/class-setting.php */
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_delete_my_course_units_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		} else {
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_delete_course_units_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Can the user change the unit state?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function can_change_course_unit_status( $course, $unit_id = '', $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}
		/**
		 * change_status my Course unit
		 */
		$my_unit = self::is_unit_creator( $unit_id, $user_id );
		if ( $my_unit ) {
			/** This filter is documented in include/coursepress/helper/class-setting.php */
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_change_my_course_unit_status_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		} else {
			/** This filter is documented in include/coursepress/helper/class-setting.php */
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_change_course_unit_status_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Can the user assign a course instructor?
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $course Course data.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_assign_course_instructor( $course_id, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$return = user_can( $user_id, 'manage_options' );

		if ( ! $return ) {
			$course_creator = self::is_course_creator( $course_id, $user_id );

			if ( ! $course_id || $course_creator ) {
				$return = user_can( $user_id, 'coursepress_assign_and_assign_instructor_my_course_cap' );
			} else {
				$return = user_can( $user_id, 'coursepress_assign_and_assign_instructor_course_cap' );
			}
		}

		return $return;
	}

	public static function can_view_students( $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );

		if ( ! $return ) {
			$return = ( user_can( $user_id, 'coursepress_courses_cap' ) && user_can( $user_id, 'coursepress_students_cap' ) );
		}

		return $return;

	}

	public static function can_view_course_students( $course_id, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );
		$post_status = get_post_status( $course_id );

		if ( ! $return ) {
			$return = user_can( $user_id, 'coursepress_students_cap' ) && 'publish' == $post_status;

			$is_facilitator = CoursePress_Data_Facilitator::is_course_facilitator( $course_id, $user_id );
			if ( ! $return && $is_facilitator ) {
				return true;
			}
		}

		return $return;
	}
	/**
	 * Can the user add students?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function can_assign_course_student( $course_id, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' ) || user_can( $user_id, 'coursepress_add_move_students_cap' );

		if ( ! $return ) {
			$course_creator = self::is_course_creator( $course_id, $user_id );
			$is_instructor = self::is_course_instructor( $course_id, $user_id );
			$is_facilitator = CoursePress_Data_Facilitator::is_course_facilitator( $course_id, $user_id );

			if ( $course_creator ) {
				$return = user_can( $user_id, 'coursepress_add_move_my_students_cap' );
			} elseif ( $is_instructor || $is_facilitator ) {
				$return = user_can( $user_id, 'coursepress_add_move_my_assigned_students_cap' );
			}
		}

		return $return;
	}

	public static function can_invite_students( $course_id, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );

		if ( ! $return ) {
			$course_creator = self::is_course_creator( $course_id, $user_id );
			$is_instructor = self::is_course_instructor( $course_id, $user_id );
			$is_facilitator = CoursePress_Data_Facilitator::is_course_facilitator( $course_id, $user_id );

			if ( $course_creator ) {
				$return = user_can( $user_id, 'coursepress_invite_my_students_cap' );
			} elseif ( $is_instructor || $is_facilitator ) {
				$return = user_can( $user_id, 'coursepress_invite_students_cap' );
			}
		}

		return $return;
	}

	public static function can_withdraw_students( $course_id, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );

		if ( ! $return ) {
			$course_creator = self::is_course_creator( $course_id, $user_id );
			$is_instructor = self::is_course_instructor( $course_id, $user_id );
			$is_facilitator = CoursePress_Data_Facilitator::is_course_facilitator( $course_id, $user_id );

			if ( $course_creator ) {
				$return = user_can( $user_id, 'coursepress_withdraw_my_students_cap' );
			} elseif ( $is_instructor || $is_facilitator ) {
				$return = user_can( $user_id, 'coursepress_withdraw_students_cap' );
			}
		}

		return $return;
	}

	public static function can_create_student( $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );

		if ( ! $return ) {
			$return = user_can( $user_id, 'coursepress_add_new_students_cap' );
		}

		return $return;
	}

	public static function can_delete_student( $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );

		if ( ! $return ) {
			$return = user_can( $user_id, 'coursepress_delete_students_cap' );
		}

		return $return;
	}

	/**
	 * Can add student
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $course Course data.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_add_course_student( $course, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}
		/**
		 * Add students to any course
		 */
		/** This filter is documented in include/coursepress/helper/class-setting.php */
		$capability = apply_filters( 'coursepress_capabilities', 'coursepress_add_move_students_cap' );
		if ( user_can( $user_id, $capability ) ) {
			return true;
		}
		/**
		 * Add students to own courses
		 */
		$course_id = is_object( $course )? $course->ID : $course;
		if ( self::is_course_creator( $course, $user_id ) ) {
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_add_move_my_students_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		}
		/**
		 * Add students to assigned courses
		 */
		// Facilitator
		$is_facilitator = CoursePress_Data_Facilitator::is_course_facilitator( $course_id, $user_id );
		if ( CoursePress_Data_Instructor::is_assigned_to_course( $user_id, $course_id ) || $is_facilitator ) {
			/** This filter is documented in include/coursepress/helper/class-setting.php */
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_add_move_my_assigned_students_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Can withdraw student
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $course Course data.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_withdraw_course_student( $course, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}
		/**
		 * withdraw students to any course
		 */
		/** This filter is documented in include/coursepress/helper/class-setting.php */
		$capability = apply_filters( 'coursepress_capabilities', 'coursepress_withdraw_students_cap' );
		if ( user_can( $user_id, $capability ) ) {
			return true;
		}
		/**
		 * withdraw students to own courses
		 */
		$course_id = is_object( $course )? $course->ID : $course;
		if ( self::is_course_creator( $course, $user_id ) ) {
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_withdraw_my_students_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 *
	 * NOTIFICATIONS
	 *
	 */

	/**
	 * Can edit "some" notification?
	 *
	 * @since 2.0.0
	 *
	 * @param integer/null $user_id User ID
	 */
	public static function can_add_notifications( $user_id = null ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$can = self::can_add_notification_to_all( $user_id );
		if ( $can ) {
			return true;
		}
		$courses = CoursePress_Data_Instructor::get_assigned_courses_ids( $user_id );
		if ( empty( $courses ) ) {
			return false;
		}
		return self::can_add_notification( $courses[0], $user_id );
	}

	/**
	 * Can withdraw student
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $course Course data.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_add_notification_to_all( $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Can add notification
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post/integer $course Course data or course ID.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_add_notification( $course, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );
		if ( $return ) {
			return true;
		}
		$course_id = is_object( $course ) ? $course->ID : $course;
		/**
		 * Create new notifications
		 */
		/** This filter is documented in include/coursepress/helper/class-setting.php */
		$capability_my = apply_filters( 'coursepress_capabilities', 'coursepress_create_my_notification_cap' );
		$capability_assigned = apply_filters( 'coursepress_capabilities', 'coursepress_create_my_assigned_notification_cap' );
		$is_facilitator = CoursePress_Data_Facilitator::is_course_facilitator( $course_id, $user_id );
		if ( ! is_object( $course ) ) {
			$return = user_can( $user_id, $capability_my ) || user_can( $user_id, $capability_assigned );
		} else {
			if ( self::is_course_creator( $course, $user_id ) ) {
				$return = user_can( $user_id, $capability_my );
			} elseif ( self::is_course_instructor( $course, $user_id ) ) {
				$return = user_can( $user_id, $capability_assigned );
			}
		}
		return $return;
	}

	/**
	 * Can update notification
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post/integer $notification notification data or notification ID.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_update_notification( $notification, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}
		/**
		 * Update every notification
		 */
		/** This filter is documented in include/coursepress/helper/class-setting.php */
		$capability = apply_filters( 'coursepress_capabilities', 'coursepress_update_notification_cap' );
		if ( user_can( $user_id, $capability ) ) {
			return true;
		}
		/**
		 * Update own notifications
		 */
		$notification_id = is_object( $notification )? $notification->ID : $notification;
		if ( self::is_notification_creator( $notification, $user_id ) ) {
			/** This filter is documented in include/coursepress/helper/class-setting.php */
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_update_my_notification_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Can delete notification
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post/integer $notification notification data or notification ID.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_delete_notification( $notification, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}
		/**
		 * delete every notification
		 */
		/** This filter is documented in include/coursepress/helper/class-setting.php */
		$capability = apply_filters( 'coursepress_capabilities', 'coursepress_delete_notification_cap' );
		if ( user_can( $user_id, $capability ) ) {
			return true;
		}
		/**
		 * delete own notifications
		 */
		$notification_id = is_object( $notification )? $notification->ID : $notification;
		if ( self::is_notification_creator( $notification, $user_id ) ) {
			/** This filter is documented in include/coursepress/helper/class-setting.php */
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_delete_my_notification_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Can change_status notification
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post/integer $notification notification data or notification ID.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_change_status_notification( $notification, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}
		/**
		 * change_status every notification
		 */
		/** This filter is documented in include/coursepress/helper/class-setting.php */
		$capability = apply_filters( 'coursepress_capabilities', 'coursepress_change_notification_status_cap' );
		if ( user_can( $user_id, $capability ) ) {
			return true;
		}
		/**
		 * change_status own notifications
		 */
		if ( empty( $notification ) ) {
			return false;
		}
		$notification_id = is_object( $notification )? $notification->ID : $notification;
		if ( self::is_notification_creator( $notification, $user_id ) ) {
			/** This filter is documented in include/coursepress/helper/class-setting.php */
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_change_my_notification_status_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 *
	 * DISCUSSIONS
	 *
	 */

	/**
	 * Can edit "some" discussion?
	 *
	 * @since 2.0.0
	 *
	 * @param integer/null $user_id User ID
	 */
	public static function can_add_discussions( $user_id = null ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$can = self::can_add_discussion_to_all( $user_id );
		if ( $can ) {
			return true;
		}
		$courses = CoursePress_Data_Instructor::get_assigned_courses_ids( $user_id );
		if ( empty( $courses ) ) {
			return false;
		}
		return self::can_add_discussion( $courses[0], $user_id );
	}

	/**
	 * Can withdraw student
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $course Course data.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_add_discussion_to_all( $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Can add discussion
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post/integer $course Course data or course ID.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_add_discussion( $course, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}

		if ( ! $course ) {
			return ( user_can( $user_id, 'courespress_create_discussion_cap' ) ||
					user_can( $user_id, 'coursepress_create_my_discussion_cap' ) ||
					user_can( $user_id, 'coursepress_create_my_assigned_discussion_cap' ) );
		}

		/**
		 * Create new discussions for own courses
		 */
		$course_id = is_object( $course )? $course->ID : $course;
		if ( self::is_course_creator( $course, $user_id ) ) {
			/** This filter is documented in include/coursepress/helper/class-setting.php */
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_create_my_discussion_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		}
		/**
		 * Create new discussions for assigned courses
		 */
		if ( self::is_course_instructor( $course, $user_id ) || CoursePress_Data_Facilitator::is_course_facilitator( $course_id, $user_id ) ) {
			/** This filter is documented in include/coursepress/helper/class-setting.php */
			$capability = apply_filters( 'coursepress_capabilities', 'coursepress_create_my_assigned_discussion_cap' );
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Can update discussion
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post/integer $discussion discussion data or discussion ID.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_update_discussion( $discussion, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}

		/** This filter is documented in include/coursepress/helper/class-setting.php */
		$capability = apply_filters( 'coursepress_capabilities', 'coursepress_update_discussion_cap' );
		$capability2 = apply_filters( 'coursepress_capabilities', 'coursepress_update_my_discussion_cap' );

		if ( empty( $discussion ) ) {
			return user_can( $user_id, $capability ) || user_can( $capability2 );
		} else {
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}

			$discussion_id = is_object( $discussion )? $discussion->ID : $discussion;
			if ( self::is_discussion_creator( $discussion, $user_id ) && user_can( $user_id, $capability2 ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Can delete discussion
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post/integer $discussion discussion data or discussion ID.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_delete_discussion( $discussion, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}

		/** This filter is documented in include/coursepress/helper/class-setting.php */
		$capability = apply_filters( 'coursepress_capabilities', 'coursepress_delete_discussion_cap' );
		$capability2 = apply_filters( 'coursepress_capabilities', 'coursepress_delete_my_discussion_cap' );

		if ( ! $discussion ) {
			return user_can( $user_id, $capability ) || user_can( $user_id, $capability2 );
		} else {
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}

			$discussion_id = is_object( $discussion )? $discussion->ID : $discussion;
			if ( self::is_discussion_creator( $discussion, $user_id )  && user_can( $user_id, $capability2 ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Can change_status discussion
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post/integer $discussion discussion data or discussion ID.
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function can_change_status_discussion( $discussion, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}

		/** This filter is documented in include/coursepress/helper/class-setting.php */
		$capability = apply_filters( 'coursepress_capabilities', 'coursepress_change_discussion_status_cap' );
		$capability2 = apply_filters( 'coursepress_capabilities', 'coursepress_change_my_discussion_status_cap' );

		if ( ! $discussion ) {
			return user_can( $user_id, $capability ) || user_can( $user_id, $capability2 );
		} else {
			if ( user_can( $user_id, $capability ) ) {
				return true;
			}

			$discussion_id = is_object( $discussion )? $discussion->ID : $discussion;
			if ( self::is_discussion_creator( $discussion, $user_id ) && user_can( $user_id, $capability2 ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * HELPERS
	 */

	/**
	 * Is the user an instructor of this course?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_course_instructor( $course, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$course_id = is_object( $course )? $course->ID : $course;
		$instructor_courses = CoursePress_Data_Instructor::get_assigned_courses_ids( $user_id );
		return in_array( $course_id, $instructor_courses );
	}

	/**
	 * Is the user the course author?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_course_creator( $course, $user_id = '' ) {
		return self::is_creator(
			$course,
			CoursePress_Data_Course::get_post_type_name(),
			$user_id
		);
	}

	/**
	 * Is the user the unit author?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_unit_creator( $unit = '', $user_id = '' ) {
		return self::is_creator(
			$unit,
			CoursePress_Data_Unit::get_post_type_name(),
			$user_id
		);
	}

	/**
	 * Is the user the notification author?
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post/integer $course Course data or course ID.
	 * @param integer $user_id user ID, can be empty
	 *
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function is_notification_creator( $notification, $user_id = '' ) {
		return self::is_creator(
			$notification,
			CoursePress_Data_Notification::get_post_type_name(),
			$user_id
		);
	}

	/**
	 * Is the user the discussion author?
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post/integer $course Course data or course ID.
	 * @param integer $user_id user ID, can be empty
	 *
	 * @return boolean Can or can't? - this is a question.
	 */
	public static function is_discussion_creator( $discussion, $user_id = '' ) {
		return self::is_creator(
			$discussion,
			CoursePress_Data_Discussion::get_post_type_name(),
			$user_id
		);
	}

	/**
	 * Is the user the discussion author?
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post/integer $course Course data or course ID.
	 * @param integer $user_id user ID, can be empty
	 *
	 * @return boolean Can or can't? - this is a question.
	 */
	private static function is_creator( $post, $post_type, $user_id = '' ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$post_id = is_object( $post )? $post->ID : $post;
		if ( empty( $post_id ) ) {
			return false;
		} else {
			/**
			 * check post type
			 */
			if ( get_post_type( $post ) != $post_type ) {
				return false;
			}
			/**
			 * check author
			 */
			return get_post_field( 'post_author', $post_id ) == $user_id;
		}
	}

	public static function grant_private_caps( $user_id ) {
		$user = new WP_User( $user_id );

		$capability_types = array(
			'course',
			'unit',
			'module',
			'module_response',
			'notification',
			'discussion',
		);

		foreach ( $capability_types as $capability_type ) {
			$user->add_cap( "read_private_{$capability_type}s" );
		}
	}

	public static function drop_private_caps( $user_id = '', $role = '' ) {

		if ( empty( $user_id ) && empty( $role ) ) {
			return;
		}
		/**
		 * do not add_cap if user can manage_options
		 */
		if ( user_can( $user_id, 'manage_options' ) ) {
			return;
		}

		$user = false;
		if ( ! empty( $user_id ) ) {
			$user = new WP_User( $user_id );
		}

		$capability_types = array( 'course', 'unit', 'module', 'module_response', 'notification', 'discussion' );

		foreach ( $capability_types as $capability_type ) {
			if ( ! empty( $user ) ) {
				$user->remove_cap( "read_private_{$capability_type}s" );
			}
			if ( ! empty( $role ) ) {
				$role->remove_cap( "read_private_{$capability_type}s" );
			}
		}
	}

	/**
	 * Check if the specified user is an instructor (of any course).
	 *
	 * We check the role_ins usermeta option here, so we do not need to loop
	 * all courses to find out if the user is assigned to a specific course.
	 *
	 * @since  2.0.0
	 * @param  int $user_id The user ID. If empty current user is checked.
	 * @return bool True if user is an instructor.
	 */
	public static function is_instructor( $user_id = 0 ) {
		$user_id = ! $user_id ? get_current_user_id() : $user_id;

		return ( 'instructor' == get_user_option( 'role_ins', $user_id ) );
	}

	/**
	 * Reset the capabilities of the specified user to the default that is
	 * defined in the users roles.
	 *
	 * @since  2.0.0
	 * @param  WP_User $user The user to reset.
	 */
	public static function reset_user_capabilities( $user ) {
		$has_caps = array();

		/**
		 * Allow other plugins to overwrite the reset routine to manually
		 * add and remove capabilities.
		 */
		$custom_reset = apply_filters(
			'coursepress_reset_user_capabilities',
			false,
			$user
		);

		if ( $custom_reset ) { return; }

		// Find out, which caps the user has according to his roles.
		foreach ( $user->roles as $role_name ) {
			$role = get_role( $role_name );
			foreach ( $role->capabilities as $cap => $flag ) {
				if ( $flag ) {
					$has_caps[] = $cap;
				}
			}
		}

		// Next remove all existing capabilities.
		foreach ( $user->caps as $cap => $flag ) {
			$user->remove_cap( $cap );
		}

		// Finally add all caps that are defined by his roles.
		foreach ( $has_caps as $cap ) {
			$user->add_cap( $cap );
		}

		do_action( 'coursepress_did_reset_user_capabilities', $user );
	}

	/**
	 * Mark the specified user as an instructor.
	 *
	 * - The user gets all instructor capabilities.
	 * - The uesroption "role_ins" is added, that marks the user as instructor.
	 *
	 * "role_ins" is only used to list the user in the "Instructors" page and
	 * in some custom Academy code to quickly identify instructors from normal
	 * users.
	 *
	 * @since  2.0.0
	 * @param  int|WP_User $user The user to modify.
	 */
	public static function assign_instructor_capabilities( $user ) {
		$user_id = CoursePress_Helper_Utility::get_id( $user );

		// The default capabilities for an instructor
		$instructor_capabilities = self::get_instructor_capabilities();

		$user_obj = new WP_User( $user_id );

		// This marks a user as "instructor" for any course.
		$global_option = ! is_multisite();
		update_user_option( $user_id, 'role_ins', 'instructor', $global_option );

		// do not use reset_user_capabilities()
		// very dangerous and needs to be rewritten, destroys WP capabilites which we shouldn't be touching
		// self::reset_user_capabilities( $user_obj );

		// no need to add READ capability as all WP users have this up to Subscriber level
		// $user_obj->add_cap( 'read' );

		// only add `upload_files` cap to Contributor and Subscriber because the rest already have it
		// refer to https://codex.wordpress.org/Roles_and_Capabilities#upload_files
		if ( $user_obj->roles && ( in_array( 'contributor', $user_obj->roles ) || in_array( 'subscriber', $user_obj->roles ) ) ) {
			$user_obj->add_cap( 'upload_files' );
			/**
			 * WooCommerce integration
			 */
			$user_obj->add_cap( 'view_admin_dashboard' );
		}

		foreach ( $instructor_capabilities as $capability_name => $capability_status ) {
			if ( $capability_status ) {
				$user_obj->add_cap( $capability_name );
			}
		}
	}

	/**
	 * Change an instructor back to a normal user by removing the role_ins flag
	 * and all special capabilities.
	 *
	 * @since  2.0.0
	 * @param  int|WP_User $user The user to modify.
	 */
	public static function drop_instructor_capabilities( $user ) {
		$user_id = CoursePress_Helper_Utility::get_id( $user );

		if ( user_can( $user_id, 'manage_options' ) ) {
			return;
		}

		$user_obj = new WP_User( $user_id );

		// Remove the "instructor" flag from the user again.
		$global_option = ! is_multisite();
		delete_user_option( $user_id, 'role_ins', $global_option );

		// do not use reset_user_capabilities()
		// very dangerous and needs to be rewritten, destroys WP capabilites which we shouldn't be touching
		// self::reset_user_capabilities( $user_obj );

		self::remove_cp_instructor_capabilities( $user_obj );
		self::grant_private_caps( $user_id );

		// Add facilitator role
		$facilitated_courses = CoursePress_Data_Facilitator::get_facilitated_courses( $user_id, array( 'any' ), true, 0, 1 );
		if ( ! empty( $facilitated_courses ) ) {
			self::assign_facilitator_capabilities( $user_id );
		} else {
			// only remove `upload_files` cap to Contributor and Subscriber, don't ever remove for other User Roles
			// refer to: https://codex.wordpress.org/Roles_and_Capabilities#upload_files
			if ( $user_obj->roles && ( in_array( 'contributor', $user_obj->roles ) || in_array( 'subscriber', $user_obj->roles ) ) ) {
				$user_obj->remove_cap( 'upload_files' );
			}
		}
	}

	/**
	 * Removes all special CoursePress capabilites for an instructor
	 *
	 * @since  2.0.0
	 * @param  WP_User $user The user to modify.
	 */
	private static function remove_cp_instructor_capabilities( $user ) {
		if ( $user && is_object( $user ) && $user instanceof WP_User ) {
			$instructor_capabilities = self::get_instructor_capabilities();
			foreach ( $instructor_capabilities as $capability_name => $capability_status ) {
				if ( $user->has_cap( $capability_name ) ) { $user->remove_cap( $capability_name ); }
			}
		}
	}

	// Add new roles and user capabilities
	public static function add_user_roles_and_caps() {
		/* ---------------------- Add initial capabilities for the admins */
		$role = get_role( 'administrator' );
		$role->add_cap( 'read' );

		// Add ALL instructor capabilities
		$admin_capabilities = array_keys( self::$capabilities['instructor'] );
		foreach ( $admin_capabilities as $cap ) {
			$role->add_cap( $cap );
		}

		self::drop_private_caps( '', $role );
	}

	public static function get_instructor_capabilities() {
		$default_capabilities = array_keys( CoursePress_Data_Capabilities::$capabilities['instructor'], 1 );
		$instructor_capabilities = CoursePress_Core::get_setting( 'instructor/capabilities' );

		if ( empty( $instructor_capabilities ) ) {
			$instructor_capabilities = array();
			foreach ( $default_capabilities as $cap ) {
				$instructor_capabilities[ $cap ] = true;
			}
		}

		return $instructor_capabilities;
	}

	public static function user_cap( $allcaps, $cap, $args ) {
		if ( ! empty( self::$current_caps ) && ( self::is_instructor() || self::is_facilitator() ) ) {
			$allcaps = wp_parse_args( self::$current_caps, $allcaps );
		}

		return $allcaps;
	}

	public static function add_all_courses_cap( $allcaps ) {
		$instructor_capabilities = array_keys( self::$capabilities['instructor'], 1 );

		foreach ( $instructor_capabilities as $instructor_cap => $is_true ) {
			$allcaps[ $instructor_cap ] = true;
		}

		return $allcaps;
	}

	public static function filter_row_actions( $actions, $tag ) {
		if ( ! empty( $tag->taxonomy ) && CoursePress_Data_Course::get_post_category_name() == $tag->taxonomy ) {
			$instructor_capabilities = CoursePress_Data_Capabilities::get_instructor_capabilities();

			if ( ! $instructor_capabilities['coursepress_course_categories_edit_terms_cap'] ) {
				// Remove edit link
				if ( isset( $actions['edit'] ) ) {
					unset( $actions['edit'] );
				}
				// Remove quick-edit
				if ( isset( $actions['inline hide-if-no-js'] ) ) {
					unset( $actions['inline hide-if-no-js'] );
				}
			}
		}
		return $actions;
	}

	public static function get_user_capabilities() {
		$user_id = get_current_user_id();
		$userdata = get_userdata( $user_id );
		$caps = $userdata->caps;

		$caps = apply_filters( 'coursepress_current_user_capabilities', $caps );

		return $caps;
	}

	/**
	 * Assign facilitator capabilities.
	 *
	 * Facilitators have similar capabilites with instructors.
	 * @todo: Add new set of capabilities for facilitators.
	 *
	 * @since 2.0
	 *
	 * @param (int) $user		The user ID.
	 **/
	public static function assign_facilitator_capabilities( $user_id ) {
		if ( empty( $user_id ) ) {
			return; // Bail !
		}

		$user_obj = new WP_User( $user_id );

		// The default capabilities for an instructor
		// @todo: add own set of facilitator capabilities.
		$instructor_capabilities = self::get_instructor_capabilities();

		$global_option = ! is_multisite();
		update_user_option( $user_id, 'cp_role', 'facilitator', $global_option );
		add_user_meta( $user_id, 'cp_role', 'facilitator' );

		// do not use reset_user_capabilities()
		// very dangerous and needs to be rewritten, destroys WP capabilites which we shouldn't be touching
		// self::reset_user_capabilities( $user_obj );

		// no need to add READ capability as all WP users have this up to Subscriber level
		// $user_obj->add_cap( 'read' );

		// only add `upload_files` cap to Contributor and Subscriber because the rest already have it
		// refer to https://codex.wordpress.org/Roles_and_Capabilities#upload_files
		if ( $user_obj->roles && ( in_array( 'contributor', $user_obj->roles ) || in_array( 'subscriber', $user_obj->roles ) ) ) {
			$user_obj->add_cap( 'upload_files' );
			/**
			 * WooCommerce integration
			 */
			$user_obj->add_cap( 'view_admin_dashboard' );
		}

		foreach ( $instructor_capabilities as $capability_name => $capability_status ) {
			if ( $capability_status ) {
				$user_obj->add_cap( $capability_name );
			}
		}
	}

	/**
	 * Drop facilitator capabilities
	 *
	 * @since 2.0
	 *
	 * @param (int) $user_id	WP_User ID.
	 **/
	public static function drop_facilitator_capabilites( $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			return false; // Bail!
		}

		$user_obj = new WP_User( $user_id );

		// Dropped instructor capabilites
		$instructor_capabilities = self::get_instructor_capabilities();

		foreach ( $instructor_capabilities as $cap => $is_true ) {
			$user_obj->remove_cap( $cap );
		}

		// Remove facilitator user key
		$global_option = ! is_multisite();
		delete_user_option( $user_id, 'cp_role', $global_option );
		delete_user_meta( $user_id, 'cp_role' );

		// only remove `upload_files` cap to Contributor and Subscriber, don't ever remove for other User Roles
		// refer to: https://codex.wordpress.org/Roles_and_Capabilities#upload_files
		if ( $user_obj->roles && ( in_array( 'contributor', $user_obj->roles ) || in_array( 'subscriber', $user_obj->roles ) ) ) {
			$user_obj->remove_cap( 'upload_files' );
		}

		// do not use reset_user_capabilities()
		// very dangerous and needs to be rewritten, destroys WP capabilites which we shouldn't be touching
		// self::reset_user_capabilities( $user_obj );
		self::grant_private_caps( $user_id );
	}

	/**
	 * Check if current user can assign facilitators.
	 *
	 * @since 2.0
	 *
	 * @param (int) $course_id		The course ID.
	 * @param (int) $user_id		Optional. Will use current user ID if nothing specified.
	 * @return (bool) 	True if has capability otherwise false.
	 **/
	public static function can_assign_facilitator( $course_id, $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$return = user_can( $user_id, 'manage_options' );

		if ( ! $return ) {
			// Check if current user is the course author.
			$is_author = self::is_course_creator( $course_id, $user_id );

			if ( $is_author ) {
				$return = user_can( $user_id, 'coursepress_assign_my_course_facilitator_cap' );
			}

			// If no cap, check if user can assign facilitator to any course
			if ( ! $return ) {
				$return = user_can( $user_id, 'coursepress_assign_facilitator_cap' );
			}
		}

		return $return;
	}

	/**
	 * Check can edit comments.
	 *
	 * @since 2.0
	 *
	 * @param integer $course_id		The course ID.
	 */
	public static function can_edit_comment( $comment_id, $user_id = null ) {
		/**
		 * do not check admins
		 */
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		$comment = get_comment( $comment_id );
		if ( empty( $comment ) ) {
			return false;
		}
		if ( ! is_a( $comment, 'WP_Comment' ) ) {
			return false;
		}
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		$module_id = $comment->comment_post_ID;
		$course_id = CoursePress_Data_Module::get_course_id_by_module( $module_id );

		if ( CoursePress_Data_Facilitator::is_course_facilitator( $course_id, $user_id ) ) {
			return true;
		}

		if ( CoursePress_Data_Instructor::is_assigned_to_course( $user_id, $course_id ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check if user_id or current user is of type facilitator.
	 *
	 * @since 2.0
	 **/
	public static function is_facilitator( $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$is_facilitator = get_user_meta( $user_id, 'cp_role', true );

		return 'facilitator' === $is_facilitator;
	}

	public static function course_capabilities() {
		global $wp_post_types;

		$course_type = CoursePress_Data_Course::get_post_type_name();
		$module_type = CoursePress_Data_Module::get_post_type_name();
		$unit_type = CoursePress_Data_Unit::get_post_type_name();
		$noti = CoursePress_Data_Notification::get_post_type_name();

		$coursepress_post_types = compact( 'course_type', 'unit_type', 'module_type', 'noti' );//compact( $course_type, $unit_type, $module_type, $noti );

		foreach ( $coursepress_post_types as $post_type ) {

			if ( isset( $wp_post_types[ $post_type ] ) ) {
				$caps = $wp_post_types['post']->cap;

				foreach ( $caps as $cap_key => $cap_value ) {
					unset( $caps[ $cap_key ] );

					$cap_key = str_replace( 'post', $post_type, $cap_key );
					$cap_value = str_replace( 'post', $post_type, $cap_value );
					$caps[ $cap_key ] = $cap_value;
				}

				$wp_post_types[ $post_type ]->cap = $caps;
			}
		}
	}

	/**
	 * Dynamically filter a user's capabilities, for
	 * "edit_posts"->"edit_course".
	 *
	 * @since 2.0.4
	 *
	 * @param array   $allcaps An array of all the user's capabilities.
	 * @param array   $caps    Actual capabilities for meta capability.
	 * @param array   $args    Optional parameters passed to has_cap(), typically object ID.
	 * @param WP_User $user    The user object.
	 */
	public static function user_has_cap_edit_course( $allcaps, $caps, $args, $user ) {
		if ( ! in_array( 'edit_post', $args ) ) {
			return $allcaps;
		}
		if ( 2 < sizeof( $args ) ) {
			return $allcaps;
		}
		if ( ! CoursePress_Data_Course::is_course( $args[2] ) ) {
			return $allcaps;
		}
		$can_update = self::can_update_course( $args[2], $user->ID );
		if ( ! $can_update ) {
			foreach ( (array) $caps as $cap ) {
				$allcaps[ $cap ] = false;
			}
		}
		return $allcaps;
	}
}
