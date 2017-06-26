<?php

class CoursePress_View_Front_Student {

	public static function init() {
	}

	public static function render_enrollment_process_page() {

		if ( ! is_user_logged_in() ) {
			_e( 'You must be logged in in order to complete the action', 'cp' );
			return;
		}

		if ( ! isset( $_POST['course_id'] ) || ! is_numeric( $_POST['course_id'] ) ) {
			_e( 'Please select a course first you want to enroll in.', 'cp' );
			return;
		}

		$course_price = 0;

		check_admin_referer( 'enrollment_process' );

		$course_id = (int) $_POST['course_id'];
		$student_id = get_current_user_ID();
		$course = new Course( $course_id );
		$pass_errors = 0;

		global $coursepress;

		$is_paid = get_post_meta( $course_id, 'paid_course', true ) == 'on' ? true : false;

		/** This filter is documented in * include/coursepress/helper/integration/class-woocommerce.php */
		$is_user_purchased_course = apply_filters( 'coursepress_is_user_purchased_course', false, $course, $student_id );

		if ( $is_paid && isset( $course->details->marketpress_product ) && '' != $course->details->marketpress_product && $coursepress->marketpress_active ) {
			$course_price = 1; //forces user to purchase course / show purchase form
			$course->is_user_purchased_course( $course->details->marketpress_product, $student_id );
		}

		if ( 'passcode' == $course->details->enroll_type ) {
			if ( $_POST['passcode'] != $course->details->passcode ) {
				$pass_errors ++;
			}
		}

		if ( ! CoursePress_Data_Student::is_enrolled_in_course( $student_id, $course_id ) ) {
			if ( 0 == $pass_errors ) {
				if ( 0 == $course_price ) {//Course is FREE
					//Enroll student in
					if ( CoursePress_Data_Course::enroll_student( $student_id, $course_id ) ) {
						printf( __( 'Congratulations, you have successfully enrolled in "%s" course! Check your %s for more info.', 'cp' ), '<strong>' . $course->details->post_title . '</strong>', '<a href="' . $this->get_student_dashboard_slug( true ) . '">' . __( 'Dashboard', 'cp' ) . '</a>' );

					} else {
						_e( 'Something went wrong during the enrollment process. Please try again later.', 'cp' );
					}
				} else {
					if ( $course->is_user_purchased_course( $course->details->marketpress_product, $student_id ) ) {
						//Enroll student in
						if ( CoursePress_Data_Course::enroll_student( $student_id, $course_id ) ) {
							printf( __( 'Congratulations, you have successfully enrolled in "%s" course! Check your %s for more info.', 'cp' ), '<strong>' . $course->details->post_title . '</strong>', '<a href="' . $this->get_student_dashboard_slug( true ) . '">' . __( 'Dashboard', 'cp' ) . '</a>' );
						} else {
							_e( 'Something went wrong during the enrollment process. Please try again later.', 'cp' );
						}
					} else {
						$course->show_purchase_form( $course->details->marketpress_product );
					}
				}
			} else {
				printf( __( 'Passcode is not valid. Please %s and try again.', 'cp' ), '<a href="' . esc_url( $course->get_permalink() ) . '">' . __( 'go back', 'cp' ) . '</a>' );

			}
		} else {
			$course_status = CoursePress_Data_Course::get_course_status( $course_id );
			$suffix = 'units';

			if ( 'future' === $course_status ) {
				$suffix = '';
			}

			wp_redirect( trailingslashit( $course->get_permalink() ) . $suffix );
			exit;
		}

	}

	public static function render_student_dashboard_page( $student_id = 0, $atts = array() ) {

		if ( ! is_user_logged_in() ) {
			_e( 'You must be logged in in order to complete the action', 'cp' );
			exit;
		} else {
			if ( empty( $student_id ) ) {
				$student_id = get_current_user_id();
			}
		}

		$student_courses = CoursePress_Data_Student::get_enrolled_courses_ids( $student_id );
		?>
			<div class="student-dashboard-wrapper">
		<?php

		// Instructor Course List
		$show = 'dates,class_size';

		$course_list = do_shortcode( '[course_list instructor="' . $student_id . '" instructor_msg="" status="all" title_tag="h1" title_class="h1-title" list_wrapper_before="" show_divider="yes"  left_class="enroll-box-left" right_class="enroll-box-right" course_class="enroll-box" title_link="no" show="' . $show . '" show_title="no" admin_links="true" show_button="no" show_media="no"]' );

		$show_random_courses = true;

		if ( ! empty( $course_list )
			&& ( CoursePress_Data_Capabilities::is_instructor() || CoursePress_Data_Capabilities::is_facilitator() ) ) {
			echo '<div class="dashboard-managed-courses-list">';
			echo '<h1 class="title managed-courses-title">' . __( 'Courses you manage:', 'cp' ) . '</h1>';
			echo '<div class="course-list course-list-managed course course-student-dashboard">';
			echo $course_list;
			echo '</div>';
			echo '</div>';
			echo '<div class="clearfix"></div>';
		}

		$shortcode_attributes = array(
			'student' => get_current_user_id(),
			'student_msg' => '',
			'status' => 'incomplete',
		);

		if ( ! empty( $atts['show_withdraw_link'] ) && 'yes' == $atts['show_withdraw_link'] ) {
			$shortcode_attributes['show_withdraw_link'] = 'yes';
		}

		$shortcode_attributes = apply_filters( 'course_list_page_student_dashsboard', $shortcode_attributes );
		$shortcode_attributes = CoursePress_Helper_Utility::convert_array_to_params( $shortcode_attributes );
		$course_list = do_shortcode( '[course_list '.$shortcode_attributes.']' );

		// Add some random courses.
		if ( empty( $course_list ) && $show_random_courses ) {

			//Random Courses
			echo '<div class="dashboard-random-courses-list">';
			echo '<h3 class="title suggested-courses">' . __( 'You are not enrolled in any courses.', 'cp' ) . '</h3>';
			_e( 'Here are a few to help you get started:', 'cp' );
			echo '<hr />';
			echo '<div class="dashboard-random-courses">' . do_shortcode( '[course_random number="3" featured_title="" media_type="image"]' ) . '</div>';
			echo '</div>';
		} else {
			// Course List
			echo '<div class="dashboard-current-courses-list">';
			echo '<h1 class="title enrolled-courses-title current-courses-title">' . __( 'Your current courses:', 'cp' ) . '</h1>';
			echo '<div class="course-list course-list-current course course-student-dashboard">';
			echo $course_list;
			echo '</div>';
			echo '</div>';
			echo '<div class="clearfix"></div>';
		}

		// Completed courses
		$show = 'dates,class_size';

		$shortcode_attributes = array(
			'student' => get_current_user_id(),
			'student_msg' => '',
			'status' => 'completed',
		);
		/**
		 * Allow to change cshortcode attributes before fired.
		 *
		 * @since 2.0.4
		 */
		$shortcode_attributes = apply_filters( 'course_list_page_student_dashsboard', $shortcode_attributes );
		$shortcode_attributes = CoursePress_Helper_Utility::convert_array_to_params( $shortcode_attributes );
		$course_list = do_shortcode( '[course_list '.$shortcode_attributes.']' );
		if ( ! empty( $course_list ) ) {
			// Course List
			echo '<div class="dashboard-completed-courses-list">';
			echo '<h1 class="title completed-courses-title">' . __( 'Completed courses:', 'cp' ) . '</h1>';
			echo '<div class="course-list course-list-completed course course-student-dashboard">';
			echo $course_list;
			echo '</div>';
			echo '</div>';
			echo '<div class="clearfix"></div>';
		}
?>
	</div>  <!-- student-dashboard-wrapper -->
<?php
	}

	public static function render_student_settings_page() {

		if ( ! is_user_logged_in() ) {
			_e( 'You must be logged in in order to complete the action', 'cp' );
			exit;
		}

		$form_message_class = '';
		$form_message = '';

		if ( isset( $_POST['student-settings-submit'] ) ) {

			if ( ! isset( $_POST['student_settings_nonce'] ) || ! wp_verify_nonce( $_POST['student_settings_nonce'], 'student_settings_save' )
			) {
				_e( "Changed can't be saved because nonce didn't verify.", 'cp' );
			} else {
				$student_data = array();
				$student_data['ID'] = get_current_user_id();
				$form_errors = 0;

				do_action( 'coursepress_before_settings_validation' );

				if ( '' != $_POST['password'] ) {
					if ( $_POST['password'] == $_POST['password_confirmation'] ) {
						$student_data['user_pass'] = $_POST['password'];
					} else {
						$form_message = __( "Passwords don't match", 'cp' );
						$form_message_class = 'red';
						$form_errors ++;
					}
				}

				$student_data['user_email'] = $_POST['email'];
				$student_data['first_name'] = $_POST['first_name'];
				$student_data['last_name'] = $_POST['last_name'];

				if ( ! is_email( $_POST['email'] ) ) {
					$form_message = __( 'E-mail address is not valid.', 'cp' );
					$form_message_class = 'red';
					$form_errors ++;
				}

				if ( 0 == $form_errors ) {
					if ( CoursePress_Data_Student::update_student_data( get_current_user_id(), $student_data ) ) {
						$form_message = __( 'Profile has been updated successfully.', 'cp' );
						$form_message_class = 'regular';
					} else {
						$form_message = __( 'An error occured while updating. Please check the form and try again.', 'cp' );
						$form_message_class = 'red';
					}
				}
			}
		}
		$student = get_userdata( get_current_user_id() );
?>
	<p class="<?php echo esc_attr( 'form-info-' . $form_message_class ); ?>"><?php echo esc_html( $form_message ); ?></p>
	<?php do_action( 'coursepress_before_settings_form' ); ?>
	<form id="student-settings" name="student-settings" method="post" class="student-settings">
	<?php wp_nonce_field( 'student_settings_save', 'student_settings_nonce' ); ?>
	<p><label><?php _e( 'First Name', 'cp' ); ?>: <input type="text" name="first_name" value="<?php esc_attr_e( $student->user_firstname ); ?>"/></label></p><?php do_action( 'coursepress_after_settings_first_name' ); ?>

	<p><label><?php _e( 'Last Name', 'cp' ); ?>: <input type="text" name="last_name" value="<?php esc_attr_e( $student->user_lastname ); ?>"/></label></p><?php do_action( 'coursepress_after_settings_last_name' ); ?>

	<p><label><?php _e( 'E-mail', 'cp' ); ?>: <input type="text" name="email" value="<?php esc_attr_e( $student->user_email ); ?>"/></label></p><?php do_action( 'coursepress_after_settings_email' ); ?>

	<p><label><?php _e( 'Username', 'cp' ); ?>: <input type="text" name="username" value="<?php esc_attr_e( $student->user_login ); ?>" disabled="disabled"/> </label></p><?php do_action( 'coursepress_after_settings_username' ); ?>

	<p><label><?php _e( 'Password', 'cp' ); ?>: <input type="password" name="password" value="" placeholder="<?php _e( "Won't change if empty.", 'cp' ); ?>"/> </label></p><?php do_action( 'coursepress_after_settings_passwordon' ); ?>

	<p><label><?php _e( 'Confirm Password', 'cp' ); ?>: <input type="password" name="password_confirmation" value=""/> </label></p><?php do_action( 'coursepress_after_settings_pasword' ); ?>

<input type="submit" name="student-settings-submit" class="apply-button-enrolled" value="<?php _e( 'Save Changes', 'cp' ); ?>"/>
	</form><?php
		do_action( 'coursepress_after_settings_form' );
	}
}

