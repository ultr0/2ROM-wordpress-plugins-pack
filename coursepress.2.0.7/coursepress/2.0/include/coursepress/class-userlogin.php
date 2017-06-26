<?php
/**
 * The class use to process user registration and login.
 *
 * @class CoursePress_UserLogin
 * @version 2.0.5
 **/
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'CoursePress_UserLogin' ) ) :
	class CoursePress_UserLogin extends CoursePress_Utility {
		/**
		 * Warning or error message to display on registration form.
		 *
		 * @var (string)
		 **/
		static $form_message = '';

		/**
		 * Form class to render on registration form.
		 *
		 * @var (string)
		 **/
		static $form_message_class = '';

		/**
		 * Process user registration submission.
		 **/
		public static function process_registration_form() {
			if ( isset( $_POST['student-settings-submit'] ) && isset( $_POST['_wpnonce'] )
				&& wp_verify_nonce( $_POST['_wpnonce'], 'student_signup' ) ) {

				check_admin_referer( 'student_signup' );

				/**
				 * Trigger before validating registration form
				 **/
				do_action( 'coursepress_before_signup_validation' );

				$min_password_length = apply_filters( 'coursepress_min_password_length', 6 );
				$username = $_POST['username'];
				$firstname = $_POST['first_name'];
				$lastname = $_POST['last_name'];
				$email = $_POST['email'];
				$passwd = $_POST['password'];
				$passwd2 = $_POST['password_confirmation'];
				$redirect_url = $_POST['redirect_url'];
				$found_errors = 0;

				if ( $username && $firstname && $lastname && $email && $passwd && $passwd2 ) {
					if ( username_exists( $username ) ) {
						self::$form_message = __( 'Username already exists. Please choose another one.', 'cp' );
						$found_errors++;
					}
					elseif ( ! validate_username( $username ) ) {
						self::$form_message = __( 'Invalid username!', 'cp' );
						$found_errors++;
					}
					elseif ( ! is_email( $email ) ) {
						self::$form_message = __( 'E-mail address is not valid.', 'cp' );
						$found_errors++;
					}
					elseif( email_exists( $email ) ) {
						self::$form_message = __( 'Sorry, that email address is already used!', 'cp' );
						$found_errors++;
					}
					elseif ( $passwd != $passwd2 ) {
						self::$form_message = __( 'Passwords don\'t match', 'cp' );
						$found_errors++;
					}
					elseif (!self::password_strong()) {
						self::$form_message = __('Your password is too weak.', 'cp');
						$found_errors++;
					}
					elseif (!self::password_criteria_met($passwd, $min_password_length)) {
						self::$form_message = sprintf(__('Your password must be at least %d characters long and have at least one letter and one number in it.', 'cp'), $min_password_length);
						$found_errors++;
					}
					elseif ( isset( $_POST['tos_agree'] ) && ! cp_is_true( $_POST['tos_agree'] ) ) {
						self::$form_message = __( 'You must agree to the Terms of Service in order to signup.', 'cp' );
						$found_errors++;
					}
				}
				else {
					self::$form_message = __( 'All fields are required.', 'cp' );
					$found_errors++;
				}

				if ( $found_errors > 0 ) {
					self::$form_message_class = 'red';
				}
				else {
					// Register new user
					$student_data = array(
						'default_role' => get_option( 'default_role', 'subscriber' ),
						'user_login' => $username,
						'user_email' => $email,
						'first_name' => $firstname,
						'last_name' => $lastname,
						'user_pass' => $passwd,
						'password_txt' => $passwd,
					);

					$student_data = CoursePress_Helper_Utility::sanitize_recursive( $student_data );
					$student_id = wp_insert_user( $student_data );

					if ( ! empty( $student_id ) ) {
						// Send registration email
						CoursePress_Data_Student::send_registration( $student_id, $student_data );

						$creds = array(
							'user_login' => $username,
							'user_password' => $passwd,
							'remember' => true,
						);
						$user = wp_signon( $creds, false );

						if ( is_wp_error( $user ) ) {
							self::$form_message = $user->get_error_message();
							self::$form_message_class = 'red';
						}

						if ( ! empty( $_POST['course_id'] ) ) {
							$url = get_permalink( (int) $_POST['course_id'] );
							wp_safe_redirect( $url );
						}
						else {
							if ( ! empty( $redirect_url ) ) {
								wp_safe_redirect( esc_url_raw( apply_filters( 'coursepress_redirect_after_signup_redirect_url', $redirect_url ) ) );
							}
							else {
								wp_safe_redirect( esc_url_raw( apply_filters( 'coursepress_redirect_after_signup_url', CoursePress_Core::get_slug( 'student_dashboard', true ) ) ) );
							}
						}
						exit;
					}
					else {
						self::$form_message = __( 'An error occurred while creating the account. Please check the form and try again.', 'cp' );
						self::$form_message_class = 'red';
					}
				}
			}
		}

		/**
		 * Render registration form if current user is not logged-in.
		 *
		 * @param (string) $redirect_url
		 * @param (string) $login_url
		 * @param (string) $signup_title
		 * @param (string) $signup_tag
		 *
		 * @return Returns registration form or null.
		 **/
		public static function get_registration_form( $redirect_url = '', $login_url = '', $signup_title = '', $signup_tag = '' ) {
			if ( is_user_logged_in() ) {
				return '';
			}

			ob_start();

			/**
			 * Allow $form_message_class to be filtered before applying.
			 *
			 * @param (string) $form_message_class
			 **/
			self::$form_message_class = apply_filters( 'signup_form_message_class', self::$form_message_class );

			/**
			 * Allow form message to be filtered before rendering.
			 *
			 * @param (string) $form_message
			 **/
			self::$form_message = apply_filters( 'signup_form_message', self::$form_message );

			$args = array(
				'signup_title' => $signup_title,
				'signup_tag' => $signup_tag,
				'form_message' => self::$form_message,
				'form_message_class' => self::$form_message_class,
				'course_id' => isset( $_GET['course_id'] ) ? (int) $_GET['course_id'] : 0,
				'redirect_url' => $redirect_url,
				'login_url' => $login_url,
				'first_name' => isset( $_POST['first_name'] ) ? $_POST['first_name'] : '',
				'last_name' => isset( $_POST['last_name'] ) ? $_POST['last_name'] : '',
				'username' => isset( $_POST['username'] ) ? $_POST['username'] : '',
				'email' => isset( $_POST['email'] ) ? $_POST['email'] : '',
			);

			self::render( 'include/coursepress/view/registration-form', $args );

			$content = ob_get_clean();
			$content = preg_replace( '%\\r\\n|\\n%', '', $content );

			return $content;
		}

		/**
		 * If the strength meter is enabled, this method checks a hidden field to make sure that the password is strong enough.
		 *
		 * If the strength meter is disabled then it simply returns true.
		 */
		private static function password_strong()
		{
			// If the password strength meter is not even enabled then we can't judge the strength of the password and this method should declare it valid.
			if(!self::is_password_strength_meter_enabled())
			{
				return true;
			}

			$confirm_weak_password = isset($_POST['confirm_weak_password']) ? (boolean)$_POST['confirm_weak_password'] : false;
			$password_strength = intval($_POST['password_strength_level']);

			return $confirm_weak_password || $password_strength >= 3;
		}

		/**
		 * If the strength meter is disabled then this method makes sure that the password meets the minimum length requirement and has the required characters.
		 *
		 * If the strength meter is enabled then it simply returns true.
		 *
		 * @param $password string The password to check.
		 * @param $min_password_length int The minimum password length.
		 * @return bool Whether the password meets the minimum criteria.
		 */
		private static function password_criteria_met($password, $min_password_length)
		{
			if(self::is_password_strength_meter_enabled())
			{
				return true;
			}

			$confirm_weak_password = isset($_POST['confirm_weak_password']) ? (boolean)$_POST['confirm_weak_password'] : false;
			$password_weak = strlen($password) < $min_password_length || !preg_match('#[0-9a-z]+#i', $password);

			return $confirm_weak_password || !$password_weak;
		}

		public static function is_password_strength_meter_enabled()
		{
			return apply_filters('coursepress_signup_display_strength_meter', true);
		}
	}
endif;