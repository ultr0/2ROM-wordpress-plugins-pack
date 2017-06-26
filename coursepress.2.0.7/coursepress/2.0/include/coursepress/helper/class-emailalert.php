<?php
/**
 * Email alerts module sends out scheduled email alerts.
 *
 * Those emails are NOT triggered by events (like new dicussion reply, ...) but
 * are sent based on an automated schedule.
 *
 * Tipp:
 * Add more scheduled emails by adding a new action handler for the action
 * `coursepress_scheduled_mails`
 * Same structure as the process_course_start function ;)
 * Review how the function get_course_keys() is used to generate a 'notice' key.
 */
class CoursePress_Helper_EmailAlert {

	/**
	 * Prefix for usermeta values that store a certain 'notice sent' flag.
	 *
	 * Notes:
	 * - The "notified" usermeta value must always be a time() value!
	 * - The "notified" flags are deleted from DB after 4 weeks!
	 */
	const META_NOTICE_PREFIX = 'cp_notice-';

	/**
	 * This action is triggered hourly as WP scheduled event
	 */
	const SCHEDULE_NAME = 'coursepress_scheduled_mails';

	/**
	 * Number of emails that were processed (sent) during this request.
	 * @var int
	 */
	protected static $processed = 0;

	/**
	 * Max. number of emails to process (send) during this request.
	 * @var int
	 */
	protected static $max_emails = 50;

	/**
	 * When more emails need to be processed: Wait (seconds) before running the
	 * email task again. This is needed if more than $max_emails items exit.
	 * @var int
	 */
	protected static $batch_delay = 30;

	/**
	 * Flag is set to true if during this request more items are found that are
	 * allowed to process by $max_emails. True indicates, that the task needs
	 * to be executed again after $batch_delay seconds.
	 * @var bool
	 */
	protected static $has_more = false;

	/**
	 * Timestamp of todays date that is used to recognize notifications that
	 * need to be processed today.
	 * @var string
	 */
	protected static $today = '';

	/**
	 * Initialize the module: Setup the cron schedule to auto-send email alerts
	 * to users.
	 *
	 * @since  2.0.0
	 */
	public static function init() {
		// Handle the schedule-hook.
		add_action(
			self::SCHEDULE_NAME,
			array( __CLASS__, 'init_email_task' ),
			1
		);

		add_action(
			self::SCHEDULE_NAME,
			array( __CLASS__, 'finalize_email_task' ),
			99
		);

		add_action(
			self::SCHEDULE_NAME,
			array( __CLASS__, 'process_course_start' ),
			10
		);

		add_action(
			self::SCHEDULE_NAME,
			array( __CLASS__, 'process_unit_start' ),
			10
		);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

		add_action(
			'coursepress_admin_render_page',
			array( __CLASS__, 'setup_schedule' )
		);
	}

	/**
	 * Initialize the scheduled-email event if it's not yet set up.
	 * This function is called on every CoursePress admin page via action
	 * `coursepress_admin_render_page` (by class CoursePress_Helper_Setting)
	 *
	 * @since  2.0.0
	 */
	public static function setup_schedule() {
		// Check if we need to set up the scheduled event.
		$scheduled = wp_next_scheduled( self::SCHEDULE_NAME );

		if ( ! $scheduled ) {
			// First delete the old schedule settings...
			wp_clear_scheduled_hook( self::SCHEDULE_NAME );

			// Schedule a recurring event.
			wp_schedule_event( time(), 'hourly', self::SCHEDULE_NAME );

			// Trigger the cron-job:
			do_action( self::SCHEDULE_NAME );
		}
	}

	/**
	 * Send email to students of courses that start today.
	 *
	 * @since  2.0.0
	 */
	public static function process_course_start() {
		$courses = self::get_courses_that_start_today();

		// Loop all courses that start today and find enrolled students.
		foreach ( $courses as $course_id ) {
			$users = self::get_next_students_of_course( $course_id );
			$keys = self::get_course_keys( $course_id, false );

			foreach ( $users as $student ) {
				// Stop, if we reached the request limit.
				if ( self::reach_process_limit( true ) ) { break; }

				// Send the actual email to the student!
				self::send_email_course_start( $student, $keys['notified'], $course_id );
			}

			// Stop, if we reached the request limit.
			if ( self::reach_process_limit() ) { break; }
		}
	}

	/**
	 * Send emails to students of all units that start today.
	 *
	 * @since  2.0.0
	 */
	public static function process_unit_start() {
		$units = self::get_units_that_start_today();

		// Loop all units that start today and find enrolled students.
		foreach ( $units as $unit_id ) {
			$course_id = CoursePress_Data_Unit::get_course_id_by_unit( $unit_id );
			$users = self::get_next_students_of_course( $course_id, $unit_id );
			$keys = self::get_course_keys( $course_id, $unit_id );

			foreach ( $users as $student ) {
				// Stop, if we reached the request limit.
				if ( self::reach_process_limit( true ) ) { break; }

				// Send the actual email to the student!
				self::send_email_unit_start( $student, $keys['notified'], $unit_id );
			}

			// Stop, if we reached the request limit.
			if ( self::reach_process_limit() ) { break; }
		}
	}

	/**
	 * Sends the "course started" email to a single student.
	 *
	 * @since  2.0.0
	 * @param  object $student Student details (not a WP_User object).
	 * @param  string $notify_key "notify" key generated by get_course_keys().
	 * @param  int    $course_id Course ID that starts.
	 */
	protected static function send_email_course_start( $student, $notify_key, $course_id ) {
		// IMPORTANT: Increase counter!
		self::$processed += 1;

		$variables = array(
			'course_id' => $course_id,
			'email' => $student->user_email,
			'first_name' => get_user_meta( $student->ID, 'first_name', true ),
			'last_name' => get_user_meta( $student->ID, 'last_name', true ),
			'display_name' => $student->display_name,
		);

		// Finally send notification!
		$res = CoursePress_Helper_Email::send_email(
			CoursePress_Helper_Email::COURSE_START_NOTIFICATION,
			$variables
		);

		if ( $res ) {
			// IMPORTANT: Add time-flag to user meta to skip this user on next run.
			update_user_meta( $student->ID, $notify_key, time() );
		}
	}

	/**
	 * Sends the "unit started" email to a single student.
	 *
	 * @since  2.0.0
	 * @param  object $student Student details (not a WP_User object).
	 * @param  string $notify_key "notify" key generated by get_course_keys().
	 * @param  int    $unit_id Unit ID that starts.
	 */
	protected static function send_email_unit_start( $student, $notify_key, $unit_id ) {
		// IMPORTANT: Increase counter!
		self::$processed += 1;

		$variables = array(
			'unit_id' => $unit_id,
			'email' => $student->user_email,
			'first_name' => get_user_meta( $student->ID, 'first_name', true ),
			'last_name' => get_user_meta( $student->ID, 'last_name', true ),
			'display_name' => $student->display_name,
		);

		// Finally send notification!
		$res = CoursePress_Helper_Email::send_email(
			CoursePress_Helper_Email::UNIT_STARTED_NOTIFICATION,
			$variables
		);

		if ( $res ) {
			// IMPORTANT: Add time-flag to user meta to skip this user on next run.
			update_user_meta( $student->ID, $notify_key, time() );
		}
	}


	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	// --- TASK INIT/FINALIZATION
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


	/**
	 * Initialize the scheduled task, before actually processing any emails.
	 *
	 * @since  2.0.0
	 */
	public static function init_email_task() {
		/*
		 * We only send 50 emails per request.
		 * if more emails need to be sent, they are scheduled to be processed
		 * 30 seconds later, to avoid killing our servers.
		 */
		self::$processed = 0;
		self::$max_emails = 50; // This is the max. number of emails sent per request.
		self::$batch_delay = 30; // Process next batch after 30 seconds.
		self::$has_more = false;

		/*
		 * Get the date (in UTC) of today.
		 * Use the i18n-function since other parts of CP use the same function
		 * and we use string comparison to recognize "today" items.
		 */
		$today_stamp = current_time( 'timestamp', 1 );
		self::$today = date_i18n( 'Y-m-d', $today_stamp );

		/**
		 * Allow timezone-conversion via filter.
		 * Default "today" is the date in UTC (i.e. current date in London).
		 *
		 * @var string Date in Y-m-d format.
		 */
		self::$today = apply_filters(
			'coursepress_scheduled_email_date',
			self::$today,
			$today_stamp
		);

		/**
		 * We allow to modify the batch-size and delay via WP filter so websites
		 * that use native cron-jobs or different timeout settings can optimize
		 * the settings here.
		 *
		 * @since  2.0.0
		 */
		self::$max_emails = apply_filters(
			'coursepress_scheduled_email_batch_size',
			self::$max_emails
		);
		self::$batch_delay = apply_filters(
			'coursepress_scheduled_email_batch_delay',
			self::$batch_delay
		);
	}

	/**
	 * Called at the end of every scheduled-event handler to re-schedule the
	 * event in case there are more items to process.
	 *
	 * @since  2.0.0
	 */
	public static function finalize_email_task() {

		/*
		 * If we found more students than the max_emails limit allows us to
		 * process, then we re-schedule this hook again in 30 seconds.
		 */
		if ( self::$has_more ) {
			wp_clear_scheduled_hook( self::SCHEDULE_NAME );

			// Schedule this event again in 30 seconds to process next students.
			wp_schedule_event(
				time() + self::$batch_delay,
				'hourly',
				self::SCHEDULE_NAME
			);
		} elseif ( ! self::$processed ) {
			/*
			 * We only clean ols flags when we did not send emails. This is a
			 * precaution to not run into timeouts when processing too much
			 * data in a single request. The action runs hourly, so there is
			 * pleny of opportuniy for the cleanup.
			 */
			self::clean_old_flags();
		}
	}


	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	// --- HELPER FUNCTIONS
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


	/**
	 * Returns an array of course-IDs for all courses that start today.
	 *
	 * @since  2.0.0
	 * @return array List or course-IDs.
	 */
	protected static function get_courses_that_start_today() {
		$courses_args = array(
			'post_type' => CoursePress_Data_Course::get_post_type_name(),
			'posts_per_page' => -1,
			'post_status' => 'publish',
		);
		$all_courses = get_posts( $courses_args );

		$items = array();

		// Loop all published courses and find the ones that start today.
		foreach ( $all_courses as $course ) {
			$start_date = CoursePress_Data_Course::get_setting( $course->ID, 'course_start_date' );
			$start_stamp = CoursePress_Data_Course::strtotime( $start_date ); // UTC.
			$start_date = date_i18n( 'Y-m-d', $start_stamp );

			if ( self::is_today( $start_date ) ) {
				$items[] = $course->ID;
			}
		}

		return $items;
	}

	/**
	 * Returns an array of unit-IDs for all units that start today.
	 *
	 * @since  2.0.0
	 * @return array List or unit-IDs (= post-ID).
	 */
	protected static function get_units_that_start_today() {
		$unit_args = array(
			'post_type' => CoursePress_Data_Unit::get_post_type_name(),
			'posts_per_page' => -1,
			'post_status' => 'publish',
		);
		$all_units = get_posts( $unit_args );

		$items = array();

		// Loop all published courses and find the ones that start today.
		foreach ( $all_units as $unit ) {
			$course_id = CoursePress_Data_Unit::get_course_id_by_unit( $unit );
			$start_date = CoursePress_Data_Unit::get_unit_availability_date( $unit->ID, $course_id, 'Y-m-d' );

			$course_start_date = CoursePress_Data_Course::get_setting( $course_id, 'course_start_date' );
			$course_start_stamp = CoursePress_Data_Course::strtotime( $course_start_date ); // UTC.
			$course_start_date = date_i18n( 'Y-m-d', $course_start_stamp );

			// Check the date is in timestamp format
			if ( (int) $start_date > 0 ) {
				$start_date = date_i18n( 'Y-m-d', $start_date );
			}

			// Ignore units that start on same date as course.
			if ( $start_date == $course_start_date ) {
				continue;
			}

			if ( self::is_today( $start_date ) ) {
				$items[] = $unit->ID;
			}
		}

		return $items;
	}

	/**
	 * Returns a list of user-objects for students that are enrolled to the
	 * specified course but did not receive a notification yet.
	 *
	 * @since  2.0.0
	 * @param  int   $course_id The course-ID.
	 * @param  int   $unit_id The unit-ID of the course.
	 * @return array List of user-object (not WP_User objects).
	 */
	protected static function get_next_students_of_course( $course_id, $unit_id = 0 ) {
		global $wpdb;

		/*
		 * Template of our custom user-query.
		 *
		 * Notes:
		 *   1. We use a custom SQL query for performance reasons.
		 *   2. Also the custom SQL is easier to read and maintain.
		 *   3. The limit is added because we only process 50 users per request.
		 */
		$student_sql = "
		SELECT
			usr.ID,
			usr.user_email,
			usr.display_name
		FROM {$wpdb->users} usr
		INNER JOIN {$wpdb->usermeta} enrol ON usr.ID = enrol.user_id AND enrol.meta_key = %s
		LEFT JOIN {$wpdb->usermeta} notif ON usr.ID = notif.user_id AND notif.meta_key = %s
		WHERE
			notif.meta_value IS NULL
		ORDER BY usr.ID
		LIMIT 0, %d
		";

		$keys = self::get_course_keys( $course_id, $unit_id );

		// Find next 100 students of the courses that start today.
		// We use a custom SQL here for performance reasons.
		$sql = $wpdb->prepare(
			$student_sql,
			$keys['enrolled'],
			$keys['notified'],
			self::$max_emails + 1
		);

		$users = $wpdb->get_results( $sql );

		return $users;
	}

	/**
	 * Returns course-specific post meta keys.
	 *
	 * Every user can have a notification key (in usermeta table) that indicates
	 * that a specific notice was sent to the user. By default this notification
	 * key contains the course_id, but if a unit_id is specified it uses the
	 * unit_id instead.
	 * New: For special cases we now accept a third parameter to specify a
	 * custom notification key, which can be any value (string or int).
	 *
	 * @since  2.0.0
	 * @param  int    $course_id The course ID.
	 * @param  int    $unit_id Notification: Use unit-ID (overrules course_id).
	 * @param  string $custom Notification: Use custom value (overrules unit_id).
	 * @return array
	 */
	protected static function get_course_keys( $course_id, $unit_id = false, $custom = false ) {
		if ( $custom ) {
			$notice_key = 'cust_' . $custom;
		} elseif ( $unit_id ) {
			$notice_key = 'unit_' . $unit_id;
		} else {
			$notice_key = 'course_' . $course_id;
		}

		$keys = array(
			// Name of meta-key to find out if student is enrolled in course.
			'enrolled' => 'enrolled_course_date_' . $course_id,
			// Build the custom notification key for the specified params.
			'notified' => self::META_NOTICE_PREFIX . $notice_key,
		);

		if ( is_multisite() ) {
			$keys['enrolled'] = $wpdb->prefix . $keys['enrolled'];
			$keys['notified'] = $wpdb->prefix . $keys['notified'];
		}

		return $keys;
	}

	/**
	 * Checks if the max. number of emails were sent.
	 * If the param is set to true, then the $has_more flag will be set to true
	 * if max. number of emails is reached
	 *
	 * @since  1.0.0
	 * @param  bool $flag_more Whether to activate the $has_more flag if limit
	 *              is reached.
	 * @return bool True means that we processed as many emails as we are
	 *              allowed to. More emails need to be sent in next request.
	 */
	protected static function reach_process_limit( $flag_more = false ) {
		$limit_reached = (self::$processed >= self::$max_emails);

		if ( $limit_reached && $flag_more ) {
			self::$has_more = true;
		}

		return $limit_reached;
	}

	/**
	 * Checks if the specified date is TODAY or not.
	 *
	 * @since  1.0.0
	 * @param  string $date Date in format Y-m-d.
	 * @return bool True if the date string is todays date.
	 */
	protected static function is_today( $date ) {
		$is_today = ( $date == self::$today );

		return $is_today;
	}

	/**
	 * Remove old "notified" flags from the usermeta table.
	 *
	 * A notified flag is only needed for one day (to not send duplicate emails
	 * to the student on the notification-date) and to keep the DB clean we
	 * use this function to remove old notificaton keys again.
	 *
	 * @since  2.0.0
	 */
	protected static function clean_old_flags() {
		global $wpdb;

		$meta_prefix = self::META_NOTICE_PREFIX;
		if ( is_multisite() ) {
			$meta_prefix = $wpdb->prefix . $meta_prefix;
		}

		$cleanup_sql = $wpdb->prepare(
			"
			SELECT meta_key, user_id
			FROM {$wpdb->usermeta}
			WHERE
				meta_value < %d
				AND (meta_key LIKE %s OR meta_key LIKE %s OR meta_key LIKE %s)
			",
			time() - (4 * WEEK_IN_SECONDS),
			$meta_prefix . 'cust_%',
			$meta_prefix . 'unit_%',
			$meta_prefix . 'course_%'
		);

		$res = $wpdb->get_results( $cleanup_sql );

		foreach ( $res as $item ) {
			delete_user_meta( $item->user_id, $item->meta_key );
		}
	}
}
