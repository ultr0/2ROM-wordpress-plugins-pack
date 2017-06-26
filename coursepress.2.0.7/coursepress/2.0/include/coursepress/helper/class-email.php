<?php

class CoursePress_Helper_Email {

	/**
	 * Email type.
	 * Used by CoursePress_Data_Certificate::send_certificate().
	 */
	const BASIC_CERTIFICATE = 'basic_certificate';

	/**
	 * Email type.
	 * Used by CoursePress_Data_Student::send_registration().
	 */
	const REGISTRATION = 'registration';

	/**
	 * Email type.
	 * Used by CoursePress_Data_Course::enroll_student().
	 */
	const ENROLLMENT_CONFIRM = 'enrollment_confirm';

	/**
	 * Email type.
	 * Used by CoursePress_Data_Course::enroll_student().
	 */
	const INSTRUCTOR_ENROLLMENT_NOTIFICATION = 'instructor_enrollment_notification';

	/**
	 * Email type.
	 * Used by CoursePress_Data_Course::send_invitation().
	 */
	const COURSE_INVITATION = 'course_invitation';

	/**
	 * Email type.
	 * Used by CoursePress_Data_Course::send_invitation().
	 */
	const COURSE_INVITATION_PASSWORD = 'course_invitation_password';

	/**
	 * Email type.
	 * Used by CoursePress_Data_Instructor::send_invitation().
	 */
	const INSTRUCTOR_INVITATION = 'instructor_invitation';

	/**
	 * Email type.
	 * Used by CoursePress_Data_Facilitator::send_invitation().
	 */
	const FACILITATOR_INVITATION = 'facilitator_invitation';

	/**
	 * Email type.
	 * (not used anywhere yet)
	 */
	const NEW_ORDER = 'new_order';

	/**
	 * Email type.
	 * Used CoursePress_Data_Student::notify_student()
	 **/
	const COURSE_START_NOTIFICATION = 'course_start';

	/**
	 * Email type.
	 * Used CoursePress_Template_Discussion::notify_all()
	 **/
	const DISCUSSION_NOTIFICATION = 'discussion_notification';

	/**
	 * Email type.
	 * Used at CoursePress_Helper_EmailAlerts::unit_started()
	 **/
	const UNIT_STARTED_NOTIFICATION = 'unit_started';

	/**
	 * Stores the current email-template-type for usage in filter-callbacks.
	 *
	 * @var string
	 */
	protected static $current_type = '';

	/**
	 * Return default content for email, by email-type.
	 *
	 * @since  1.0.0
	 * @param  string $email_type Email-template-type.
	 * @return array Email specifications.
	 */
	public static function get_email_fields( $email_type ) {
		return apply_filters(
			'coursepress_get_email_fields-' . $email_type,
			array(
				'name' => self::from_name( $email_type ),
				'email' => self::from_email( $email_type ),
				'subject' => self::subject( $email_type ),
				'content' => self::content( $email_type ),
			)
		);
	}

	/**
	 * Send an email.
	 *
	 * @param string $type One of the constants defined in this class or empty
	 *               if specifying the 'subject' and 'message'.
	 * @param array  $args Variables and email content.
	 *               email .. recipient.
	 *               message .. optional if specifying type.
	 *               subject .. optional if specifying type.
	 *               first_name
	 *               last_name
	 *               fields .. content variables, array of key-value pairs.
	 * @return bool True if email was accepted by wp_mail.
	 */
	public static function send_email( $type, $args ) {
		self::$current_type = $type;

		if ( ! empty( $type ) ) {
			add_filter( 'wp_mail_from', array( __CLASS__, 'wp_mail_from' ) );
			add_filter( 'wp_mail_from_name', array( __CLASS__, 'wp_mail_from_name' ) );

			$email_settings = self::get_email_fields( $type );

			if ( isset( $email_settings['subject'] ) && ! empty( $email_settings['subject'] ) ) {
				$args['subject'] = $email_settings['subject'];
			}

			switch ( $type ) {
				case self::BASIC_CERTIFICATE:
					$args['message'] = self::basic_certificate_message(
						$args,
						$email_settings['content']
					);
					break;

				case self::REGISTRATION:
					$args['message'] = self::registration_message(
						$args,
						$email_settings['content']
					);
					break;

				case self::ENROLLMENT_CONFIRM:
					$args['message'] = self::enrollment_confirm_message(
						$args,
						$email_settings['content']
					);
					break;

				case self::INSTRUCTOR_ENROLLMENT_NOTIFICATION:
					$args['message'] = self::instructor_enrollment_notification_message(
						$args,
						$email_settings['content']
					);
					break;

				case self::COURSE_INVITATION:
					$args['message'] = self::course_invitation_message(
						$args,
						$email_settings['content']
					);
					break;

				case self::COURSE_INVITATION_PASSWORD:
					$args['message'] = self::course_invitation_password_message(
						$args,
						$email_settings['content']
					);
					break;

				case self::INSTRUCTOR_INVITATION:
					$args['message'] = self::instructor_invitation_message(
						$args,
						$email_settings['content']
					);
					break;

				case self::FACILITATOR_INVITATION:
					$args['message'] = self::facilitator_invitation_message(
						$args,
						$email_settings['content']
					);
					break;

				case self::NEW_ORDER:
					// (not used anywhere yet)
					$args['message'] = self::new_order_message(
						$args,
						$email_settings['content']
					);
					break;

				case self::COURSE_START_NOTIFICATION:
					$args['message'] = self::course_start_notification_message(
						$args,
						$email_settings['content']
					);
					break;

				case self::DISCUSSION_NOTIFICATION:
					$args['message'] = self::discussion_notification_message(
						$args,
						$email_settings['content']
					);
					if ( ! empty( $args['uniq_subject'] ) ) {
						$args['subject'] = $args['uniq_subject'];
					}
					break;

				case self::UNIT_STARTED_NOTIFICATION:
					$args['message'] = self::units_started_notification_message(
						$args,
						$email_settings['content']
					);
					$args['subject'] = self::units_started_notification_subject(
						$args,
						$args['subject']
					);
					break;
			}
		}

		/**
		 * Check whether an email should be send or not.
		 *
		 * @since 2.0
		 *
		 * @param (bool) $send
		 **/
		$send = CoursePress_Data_Unsubscribe::is_send( $type, $args );

		if ( $send ) {
			return self::process_and_send( $type, $args );
		}

		return false;
	}

	/**
	 * Send a CoursePress email template to a single user.
	 *
	 * @since  1.0.0
	 * @param  array $args Email args.
	 * @return bool True if the email was processed correctly.
	 */
	protected static function process_and_send( $type, $args ) {
		// Legacy support for args['email']. Remove this in future!
		if ( ! empty( $args['email'] ) && empty( $args['to'] ) ) {
			$args['to'] = $args['email'];
		}

		if ( empty( $args['to'] ) ) {
			throw new Exception( 'Error: No email recipient!' );
		}
		if ( empty( $args['message'] ) ) {
			throw new Exception( 'Error: Empty email body!' );
		}
		if ( empty( $args['subject'] ) ) {
			throw new Exception( 'Error: Empty email subject!' );
		}

		// Prepare email content.
		$email = array(
			'to' => apply_filters(
				'coursepress_email_to_address',
				sanitize_email( $args['to'] ),
				$args
			),
			'subject' => apply_filters(
				'coursepress_email_subject',
				sanitize_text_field( $args['subject'] ) ,
				$args
			),
			'message' => apply_filters(
				'coursepress_email_message',
				$args['message'],
				$args
			),
			'headers' => apply_filters(
				'coursepress_email_headers',
				array(
					'Content-Type' => 'text/html',
				)
			),
			'attachments' => apply_filters(
				'coursepress_email_attachments',
				isset( $args['attachments'] ) ? $args['attachments'] : array()
			),
		);

		$email = apply_filters(
			'coursepress_email_fields',
			$email,
			$args,
			$type
		);
		$email = apply_filters(
			'coursepress_email_fields-' . $type,
			$email,
			$args
		);

		// Good one to hook if you want to hook WP specific filters (e.g. changing from address)
		do_action( 'coursepress_email_pre_send', $args, $type );
		do_action( 'coursepress_email_pre_send-' . $type, $args );

		if ( apply_filters( 'coursepress_email_strip_slashed', true, $args, $type ) ) {
			$email['subject'] = stripslashes( $email['subject'] );
			$email['message'] = stripslashes( nl2br( $email['message'] ) );
		}

		$header_string = '';

		if ( isset( $args['bcc'] ) ) {
			if ( is_array( $args['bcc'] ) ) {
				$bcc = implode( ',', $args['bcc'] );
			} else {
				$bcc = $args['bcc'];
			}
			if ( ! empty( $bcc ) ) {
				$header_string .= 'Bcc: ' . $bcc . ';';
			}
		}

		foreach ( $email['headers'] as $key => $value ) {
			$header_string .= $key . ': ' . $value . "\r\n";
		}

		$email['message'] = CoursePress_Helper_Utility::filter_content( $email['message'] );
		$email['headers'] = $header_string;

		/**
		 * Action offers other plugins to implement custom email sending code,
		 * for example to use a custom built HTML template or similar.
		 *
		 * @var bool  $result Output parameter, this should be set to true/false
		 *            if the email was processed by the custom action handler.
		 * @var array $email The email options for wp_mail.
		 * @var array $args Email parameters passed to the CoursePress function.
		 */
		$result = apply_filters(
			'coursepress_send_email',
			null,
			$email,
			$args,
			$type
		);

		// If custom send-option failed or was not used then send via wp_mail.
		if ( is_null( $result ) || ! $result ) {

			try {
				$result = wp_mail(
					$email['to'],
					$email['subject'],
					$email['message'],
					$email['headers'],
					$email['attachments']
				);
			} catch (phpmailerException $e) {
				// print_r($e->getMessage()); // for debugging purposes
				$result = false;
			}

		}

		do_action( 'coursepress_email_sent', $args, $type, $result );
		do_action( 'coursepress_email_sent-' . $type, $args, $result );

		return $result;
	}

	/*
	 ***************************************************************************
	 * Fetch email settings from DB.
	 ***************************************************************************
	 */

	protected static function from_name( $email_type ) {
		$fields = CoursePress_Helper_Setting_Email::get_defaults( $email_type );

		if ( ! empty( $fields['from'] ) ) {
			return CoursePress_Core::get_setting(
				'email/' . $email_type . '/from',
				$fields['from']
			);
		}
		return '';
	}

	protected static function from_email( $email_type ) {
		$fields = CoursePress_Helper_Setting_Email::get_defaults( $email_type );

		if ( ! empty( $fields['email'] ) ) {
			return CoursePress_Core::get_setting(
				'email/' . $email_type . '/email',
				$fields['email']
			);
		}
		return '';
	}

	protected static function subject( $email_type ) {
		$fields = CoursePress_Helper_Setting_Email::get_defaults( $email_type );

		if ( ! empty( $fields['subject'] ) ) {
			return CoursePress_Core::get_setting(
				'email/' . $email_type . '/subject',
				$fields['subject']
			);
		}
		return '';
	}

	protected static function content( $email_type ) {
		$fields = CoursePress_Helper_Setting_Email::get_defaults( $email_type );

		if ( ! empty( $fields['content'] ) ) {
			return CoursePress_Core::get_setting(
				'email/' . $email_type . '/content',
				$fields['content']
			);
		}
		return '';
	}

	/**
	 * Hooks into `wp_mail_from` to provide a custom sender email address.
	 *
	 * @since  2.0.0
	 * @param  string $from Default WP Sender address.
	 * @return string Custom sender address.
	 */
	public static function wp_mail_from( $from ) {
		return self::from_email( self::$current_type );
	}

	/**
	 * Hooks into `wp_mail_from_name` to provide a custom sender name.
	 *
	 * @since  2.0.0
	 * @param  string $from_name Default WP Sender name.
	 * @return string Custom sender name.
	 */
	public static function wp_mail_from_name( $from_name ) {
		return self::from_name( self::$current_type );
	}

	/*
	 ***************************************************************************
	 * Prepare default email contents.
	 ***************************************************************************
	 */

	/**
	 * Email body with a Course Certificate (when course is completed).
	 * Triggered by CoursePress_Data_Certificate::send_certificate()
	 *
	 * Note: This uses the email settings defined in Settings > E-mail Settings
	 *       and _not_ the content defined in Settingd > Basic Certificate!
	 *
	 * @since  2.0.0
	 * @param  array $args Email params.
	 * @param  string $content Default email content, with placeholders.
	 * @return string Finished email content.
	 */
	protected static function basic_certificate_message( $args, $content ) {
		$course_id = (int) $args['course_id'];

		$vars = array(
			'COURSES_ADDRESS' => CoursePress_Core::get_slug( 'course', true ),
			'COURSE_ADDRESS' => esc_url( $args['course_address'] ),
			'FIRST_NAME' => sanitize_text_field( $args['first_name'] ),
			'LAST_NAME' => sanitize_text_field( $args['last_name'] ),
			'COURSE_NAME' => sanitize_text_field( $args['course_name'] ),
			'COMPLETION_DATE' => sanitize_text_field( $args['completion_date'] ),
			'CERTIFICATE_NUMBER' => sanitize_text_field( $args['certificate_id'] ),
			'CERTIFICATE_URL' => esc_url( CoursePress_Data_Certificate::get_encoded_url( $course_id, $args['student_id'] ) ),
			'UNIT_LIST' => $args['unit_list'],
		);
		$vars = CoursePress_Helper_Utility::add_site_vars( $vars );

		/**
		 * Filter the variables before applying changes.
		 *
		 * @param array $vars
		 * @param (int) $course_id
		 **/
		$vars = apply_filters( 'coursepress_fields_' . self::BASIC_CERTIFICATE, $vars, $course_id );

		return CoursePress_Helper_Utility::replace_vars( $content, $vars );
	}

	/**
	 * Email body for new user registration/welcome email.
	 * Triggered by CoursePress_Data_Student::send_registration()
	 *
	 * @since  2.0.0
	 * @param  array $args Email params.
	 * @param  string $content Default email content, with placeholders.
	 * @return string Finished email content.
	 */
	protected static function registration_message( $args, $content ) {

		// Email Content.
		$vars = array(
			'STUDENT_FIRST_NAME' => sanitize_text_field( $args['first_name'] ),
			'STUDENT_LAST_NAME' => sanitize_text_field( $args['last_name'] ),
			'STUDENT_USERNAME' => $args['fields']['student_username'],
			'STUDENT_PASSWORD' => $args['fields']['password'],
			'COURSES_ADDRESS' => CoursePress_Core::get_slug( 'course', true ),
		);
		$vars = CoursePress_Helper_Utility::add_site_vars( $vars );

		/**
		 * Filter the registration variables before applying the changes.
		 *
		 * @param array $vars
		 **/
		$vars = apply_filters( 'coursepress_fields_' . self::REGISTRATION, $vars );

		return CoursePress_Helper_Utility::replace_vars( $content, $vars );
	}

	/**
	 * Email body for confirmation of enrollment.
	 * Triggered by CoursePress_Data_Course::enroll_student()
	 *
	 * @since  2.0.0
	 * @param  array $args Email params.
	 * @param  string $content Default email content, with placeholders.
	 * @return string Finished email content.
	 */
	protected static function enrollment_confirm_message( $args, $content ) {
		$course_id = (int) $args['course_id'];
		$post = get_post( $course_id );
		$course_name = $post->post_title;
		$valid_stati = array( 'draft', 'pending', 'auto-draft' );

		if ( in_array( $post->post_status, $valid_stati ) ) {
			$course_address = CoursePress_Core::get_slug( 'course/', true ) . $post->post_name . '/';
		} else {
			$course_address = get_permalink( $course_id );
		}

		$unsubscribe_link = '';  // @todo: NOT IMPLEMENTED YET!!!

		// Email Content.
		$vars = array(
			'STUDENT_FIRST_NAME' => sanitize_text_field( $args['first_name'] ),
			'STUDENT_LAST_NAME' => sanitize_text_field( $args['last_name'] ),
			'COURSE_TITLE' => $course_name,
			'COURSE_ADDRESS' => esc_url( $course_address ),
			'STUDENT_DASHBOARD' => wp_login_url(),
			'COURSES_ADDRESS' => CoursePress_Core::get_slug( 'course/', true ),
			'UNSUBSCRIBE_LINK' => $unsubscribe_link,
		);
		$vars = CoursePress_Helper_Utility::add_site_vars( $vars );

		/**
		 * Filter the variables before applying changes.
		 *
		 * @param array $vars
		 * @param (int) $course_id
		 **/
		$vars = apply_filters( 'coursepress_fields_' . self::ENROLLMENT_CONFIRM, $vars, $course_id );

		return CoursePress_Helper_Utility::replace_vars( $content, $vars );
	}

	/**
	 * Email body for enrollment notification sent to instructor.
	 * Triggered by CoursePress_Data_Course::enroll_student()
	 *
	 * @since  2.0.0
	 * @param  array $args Email params.
	 * @param  string $content Default email content, with placeholders.
	 * @return string Finished email content.
	 */
	protected static function instructor_enrollment_notification_message( $args, $content ) {
		$course_id = (int)$args['course_id'];
		$post = get_post($course_id);
		$course_name = $post->post_title;
		$post_type_object = get_post_type_object($post->post_type);
		$edit_link = admin_url(sprintf($post_type_object->_edit_link . '&action=edit', $post->ID));
		$edit_students = add_query_arg('tab', 'students', $edit_link);

		// Email Content.
		$vars = array(
			'STUDENT_FIRST_NAME'            => sanitize_text_field($args['student_first_name']),
			'STUDENT_LAST_NAME'             => sanitize_text_field($args['student_last_name']),
			'INSTRUCTOR_FIRST_NAME'         => sanitize_text_field($args['instructor_first_name']),
			'INSTRUCTOR_LAST_NAME'          => sanitize_text_field($args['instructor_last_name']),
			'COURSE_TITLE'                  => $course_name,
			'COURSE_ADDRESS'                => get_permalink($course_id),
			'COURSE_ADMIN_ADDRESS'          => $edit_link,
			'COURSE_STUDENTS_ADMIN_ADDRESS' => esc_url_raw($edit_students),
			'WEBSITE_NAME'                  => get_bloginfo(),
			'WEBSITE_ADDRESS'               => home_url()
		);

		/**
		 * Filter the variables before applying changes.
		 *
		 * @param array $vars
		 * @param (int) $course_id
		 **/
		$vars = apply_filters('coursepress_fields_' . self::INSTRUCTOR_ENROLLMENT_NOTIFICATION, $vars, $course_id);

		return CoursePress_Helper_Utility::replace_vars($content, $vars);
	}

	/**
	 * Email body for Student Invitation Emails.
	 * Triggered by CoursePress_Data_Course::send_invitation()
	 *
	 * @since  2.0.0
	 * @param  array $args Email params.
	 * @param  string $content Default email content, with placeholders.
	 * @return string Finished email content.
	 */
	protected static function course_invitation_message( $args, $content ) {
		$course_id = (int) $args['course_id'];
		$post = get_post( $course_id );
		$course_name = $post->post_title;
		$course_summary = $post->post_excerpt;
		$valid_stati = array( 'draft', 'pending', 'auto-draft' );

		if ( in_array( $post->post_status, $valid_stati ) ) {
			$course_address = CoursePress_Core::get_slug( 'course/', true ) . $post->post_name . '/';
		} else {
			$course_address = get_permalink( $course_id );
		}

		// Email Content.
		$vars = array(
			'STUDENT_FIRST_NAME' => sanitize_text_field( $args['first_name'] ),
			'STUDENT_LAST_NAME' => sanitize_text_field( $args['last_name'] ),
			'COURSE_NAME' => $course_name,
			'COURSE_EXCERPT' => $course_summary,
			'COURSE_ADDRESS' => esc_url( $course_address ),
			'PASSCODE' => CoursePress_Data_Course::get_setting( $course_id, 'enrollment_passcode', '' ),
		);
		$vars = CoursePress_Helper_Utility::add_site_vars( $vars );

		/**
		 * Filter the variables before applying changes.
		 *
		 * @param array $vars.
		 * @param (int) $course_id
		 **/
		$vars = apply_filters( 'coursepress_fields_' . self::COURSE_INVITATION, $vars, $course_id );

		return CoursePress_Helper_Utility::replace_vars( $content, $vars );
	}

	/**
	 * Email body for Student Invitation Emails.
	 * Triggered by CoursePress_Data_Course::send_invitation()
	 *
	 * This uses the same function as the other invitation email. The difference
	 * is, that the passcode email has a different $content value, i.e. the
	 * actual email body is different.
	 *
	 * @since  2.0.0
	 * @param  array $args Email params.
	 * @param  string $content Default email content, with placeholders.
	 * @return string Finished email content.
	 */
	protected static function course_invitation_password_message( $args, $content ) {
		return self::course_invitation_message( $args, $content );
	}

	/**
	 * Email body for Instructor Invitation Emails.
	 * Triggered by CoursePress_Data_Instructor::send_invitation()
	 *
	 * @since  2.0.0
	 * @param  array $args Email params.
	 * @param  string $content Default email content, with placeholders.
	 * @return string Finished email content.
	 */
	protected static function instructor_invitation_message( $args, $content ) {
		$course_id = (int) $args['course_id'];
		$post = get_post( $course_id );
		$course_name = $post->post_title;
		$course_summary = $post->post_excerpt;
		$valid_stati = array( 'draft', 'pending', 'auto-draft' );

		if ( in_array( $post->post_status, $valid_stati ) ) {
			$course_address = CoursePress_Core::get_slug( 'course/', true ) . $post->post_name . '/';
		} else {
			$course_address = get_permalink( $course_id );
		}

		$confirm_link = sprintf(
			'%s?action=course_invite&course_id=%s&c=%s&h=%s',
			$course_address,
			$course_id,
			$args['invite_code'],
			$args['invite_hash']
		);

		// Email Content.
		$vars = array(
			'INSTRUCTOR_FIRST_NAME' => sanitize_text_field( $args['first_name'] ),
			'INSTRUCTOR_LAST_NAME' => sanitize_text_field( $args['last_name'] ),
			'INSTRUCTOR_EMAIL' => sanitize_email( $args['email'] ),
			'CONFIRMATION_LINK' => esc_url( $confirm_link ),
			'COURSE_NAME' => $course_name,
			'COURSE_EXCERPT' => $course_summary,
			'COURSE_ADDRESS' => esc_url( $course_address ),
		);
		$vars = CoursePress_Helper_Utility::add_site_vars( $vars );

		/**
		 * Filter the variables before applying changes.
		 *
		 * @param array $vars
		 * @param array $course_id
		 **/
		$vars = apply_filters( 'coursepress_fields_' . self::INSTRUCTOR_INVITATION, $vars, $course_id );
		$message = CoursePress_Helper_Utility::replace_vars( $content, $vars );

		/**
		 * Filter the message before sending.
		 *
		 * @since 2.0
		 *
		 * @param string $message The message to send.
		 * @param int    $course_id The course_id the message is associated to.
		 **/
		$message = apply_filters( 'coursepress_course_invitation_message', $message, $course_id );

		return $message;
	}

	/**
	 * Email body for facilitator Invitation Emails.
	 * Triggered by CoursePress_Data_facilitator::send_invitation()
	 *
	 * @since  2.0.0
	 * @param  array $args Email params.
	 * @param  string $content Default email content, with placeholders.
	 * @return string Finished email content.
	 */
	protected static function facilitator_invitation_message( $args, $content ) {
		$course_id = (int) $args['course_id'];
		$post = get_post( $course_id );
		$course_name = $post->post_title;
		$course_summary = $post->post_excerpt;
		$valid_stati = array( 'draft', 'pending', 'auto-draft' );

		if ( in_array( $post->post_status, $valid_stati ) ) {
			$course_address = CoursePress_Core::get_slug( 'course/', true ) . $post->post_name . '/';
		} else {
			$course_address = get_permalink( $course_id );
		}

		$confirm_link = sprintf(
			'%s?action=course_invite_facilitator&course_id=%s&c=%s&h=%s',
			$course_address,
			$course_id,
			$args['invite_code'],
			$args['invite_hash']
		);

		// Email Content.
		$vars = array(
			'INSTRUCTOR_FIRST_NAME' => sanitize_text_field( $args['first_name'] ),
			'INSTRUCTOR_LAST_NAME' => sanitize_text_field( $args['last_name'] ),
			'INSTRUCTOR_EMAIL' => sanitize_email( $args['email'] ),
			'CONFIRMATION_LINK' => esc_url( $confirm_link ),
			'COURSE_NAME' => $course_name,
			'COURSE_EXCERPT' => $course_summary,
			'COURSE_ADDRESS' => esc_url( $course_address ),
		);
		$vars = CoursePress_Helper_Utility::add_site_vars( $vars );

		/**
		 * Filter the variables before applying changes.
		 *
		 * @param array $vars
		 * @param array $course_id
		 **/
		$vars = apply_filters( 'coursepress_fields_' . self::FACILITATOR_INVITATION, $vars, $course_id );
		$message = CoursePress_Helper_Utility::replace_vars( $content, $vars );

		/**
		 * Filter the message before sending.
		 *
		 * @since 2.0
		 *
		 * @param string $message The message to send.
		 * @param int    $course_id The course_id the message is associated to.
		 **/
		$message = apply_filters( 'coursepress_course_invitation_message', $message, $course_id );

		return $message;
	}

	/**
	 * (not used anywhere yet)
	 *
	 * @since  2.0.0
	 * @param  array $args Email params.
	 * @param  string $content Default email content, with placeholders.
	 * @return string Finished email content.
	 */
	protected static function new_order_message( $args, $content ) {
		$vars = array();

		return CoursePress_Helper_Utility::replace_vars( $content, $vars );
	}

	/**
	 * Use to send notification message when a course have started.
	 *
	 * Expected args:
	 *  - course_id
	 *  - first_name
	 *  - last_name
	 *  - display_name
	 *  - email
	 *
	 * @since 2.0
	 *
	 * @param array  $args An array of email arguments.
	 * @param string $content The message to send.
	 **/
	public static function course_start_notification_message( $args, $content ) {
		$course_id = (int) $args['course_id'];
		$post = get_post( $course_id );
		$course_name = $post->post_title;
		$course_summary = $post->post_excerpt;
		$valid_stati = array( 'draft', 'pending', 'auto-draft' );

		if ( in_array( $post->post_status, $valid_stati ) ) {
			$course_address = CoursePress_Core::get_slug( 'course/', true ) . $post->post_name . '/';
		} else {
			$course_address = get_permalink( $course_id );
		}

		$unsubscribe_link = '';  // @todo: NOT IMPLEMENTED YET!!!

		// Email Content.
		$vars = array(
			'COURSE_NAME' => $course_name,
			'COURSE_OVERVIEW' => $course_summary,
			'COURSE_ADDRESS' => esc_url( $course_address ),
			'STUDENT_FIRST_NAME' => $args['first_name'],
			'STUDENT_LAST_NAME' => $args['last_name'],
			'STUDENT_LOGIN' => $args['display_name'],
			'UNSUBSCRIBE_LINK' => $unsubscribe_link,
		);
		$vars = CoursePress_Helper_Utility::add_site_vars( $vars );

		/**
		 * Filter the variables before applying changes.
		 *
		 * @param array $vars
		 * @param array $course_id
		 **/
		$vars = apply_filters( 'coursepress_fields_' . self::COURSE_START_NOTIFICATION, $vars, $course_id );

		$message = CoursePress_Helper_Utility::replace_vars( $content, $vars );

		/**
		 * Filter the message before sending.
		 *
		 * @since 2.0
		 *
		 * @param string $message The message to send.
		 * @param int    $course_id The course_id the message is associated to.
		 **/
		$message = apply_filters( 'coursepress_course_start_notification_message', $message, $course_id );

		return $message;
	}

	/**
	 * Use to send notification message to students and instructors.
	 *
	 * @since 2.0
	 *
	 * @param array  $args An array of email arguments.
	 * @param string $content The message to send.
	 **/
	public static function discussion_notification_message( $args, $content ) {
		$course_id = (int) $args['course_id'];
		$post = get_post( $course_id );
		$course_name = $post->post_title;
		$course_summary = $post->post_excerpt;
		$valid_stati = array( 'draft', 'pending', 'auto-draft' );

		if ( in_array( $post->post_status, $valid_stati ) ) {
			$course_address = CoursePress_Core::get_slug( 'course/', true ) . $post->post_name . '/';
		} else {
			$course_address = get_permalink( $course_id );
		}

		// Email Content.
		$vars = array(
			'COURSE_NAME' => $course_name,
			'COURSE_OVERVIEW' => $course_summary,
			'COURSE_ADDRESS' => esc_url( $course_address ),
			'COMMENT_MESSAGE' => $args['comment'],
			'COURSE_DISCUSSION_ADDRESS' => $args['discussion_link'],
			'UNSUBSCRIBE_LINK' => $args['unsubscribe_link'],
			'COMMENT_AUTHOR' => $args['comment_author'],
		);
		$vars = CoursePress_Helper_Utility::add_site_vars( $vars );

		/**
		 * Filter the variables before applying changes.
		 *
		 * @param array $vars
		 * @param array $course_id
		 **/
		$vars = apply_filters( 'coursepress_fields_' . self::DISCUSSION_NOTIFICATION, $vars, $course_id );

		$message = CoursePress_Helper_Utility::replace_vars( $content, $vars );

		/**
		 * Filter the message before sending.
		 *
		 * @since 2.0
		 *
		 * @param string $message The message to send.
		 * @param (int) $course_id The course_id the message is associated to.
		 **/
		$message = apply_filters( 'coursepress_discussion_notification_message', $message, $course_id );

		return $message;
	}

	/**
	 * Prepare the email body for the unit-started email notification.
	 *
	 * Expected args:
	 *  - unit_id
	 *  - first_name
	 *  - last_name
	 *  - display_name
	 *  - email
	 *
	 * @since  2.0.0
	 * @param  array $args List of variables.
	 * @param  string $content Email body template.
	 * @return string Parsed email body.
	 */
	public static function units_started_notification_message( $args, $content ) {
		$unit_id = (int) $args['unit_id'];
		$unit = get_post( $unit_id );
		$course_id = CoursePress_Data_Unit::get_course_id_by_unit( $unit );
		$course = get_post( $course_id );
		$course_name = $course->post_title;
		$course_summary = $course->post_excerpt;
		$unit_title = $unit->post_title;
		$unit_sumary = $unit->post_content;
		$valid_stati = array( 'draft', 'pending', 'auto-draft' );

		if ( in_array( $course->post_status, $valid_stati ) ) {
			$course_address = CoursePress_Core::get_slug( 'course/', true ) . $unit->post_name . '/';
		} else {
			$course_address = get_permalink( $course_id );
		}

		$unit_address = $course_address . CoursePress_Core::get_slug( 'units/' ) . $unit->post_name . '/';
		$unsubscribe_link = '';  // @todo: NOT IMPLEMENTED YET!!!

		// Email Content.
		$vars = array(
			'COURSE_NAME' => $course_name,
			'COURSE_ADDRESS' => esc_url( $course_address ),
			'STUDENT_FIRST_NAME' => $args['first_name'],
			'STUDENT_LAST_NAME' => $args['last_name'],
			'UNIT_TITLE' => $unit_title,
			'UNIT_OVERVIEW' => $unit_sumary,
			'UNIT_ADDRESS' => $unit_address,
			'UNSUBSCRIBE_LINK' => $unsubscribe_link,
		);
		$vars = CoursePress_Helper_Utility::add_site_vars( $vars );

		/**
		 * Filter the variables before applying changes.
		 *
		 * @param array $vars
		 * @param array $course_id
		 **/
		$vars = apply_filters( 'coursepress_fields_' . self::UNIT_STARTED_NOTIFICATION, $vars, $course_id );

		$message = CoursePress_Helper_Utility::replace_vars( $content, $vars );
		/**
		 * Filter the message before sending.
		 *
		 * @since 2.0
		 *
		 * @param string $message The message to send.
		 * @param int    $course_id The course_id the message is associated to.
		 **/
		$message = apply_filters( 'coursepress_units_started_notification_message', $message, $course_id );

		return $message;
	}

	/**
	 * Prepare the email subject for the unit-started email notification.
	 *
	 * Expected args:
	 *  - unit_id
	 *  - first_name
	 *  - last_name
	 *  - display_name
	 *  - email
	 *
	 * @since  2.0.0
	 * @param  array $args List of variables.
	 * @param  string $subject Email subject.
	 * @return string Parsed email subject.
	 */
	public static function units_started_notification_subject( $args, $subject ) {
		$unit_id = (int) $args['unit_id'];
		$unit = get_post( $unit_id );
		$course_id = CoursePress_Data_Unit::get_course_id_by_unit( $unit );
		$course = get_post( $course_id );
		$course_name = $course->post_title;
		$unit_title = $unit->post_title;

		// Email Content.
		$vars = array(
			'COURSE_NAME' => $course_name,
			'UNIT_TITLE' => $unit_title,
		);

		$subject = CoursePress_Helper_Utility::replace_vars( $subject, $vars );

		/**
		 * Filter the message before sending.
		 *
		 * @since 2.0
		 *
		 * @param string $message The message to send.
		 * @param int    $course_id The course_id the message is associated to.
		 **/
		$subject = apply_filters( 'coursepress_units_started_notification_subject', $subject, $course_id );

		return $subject;
	}
}
