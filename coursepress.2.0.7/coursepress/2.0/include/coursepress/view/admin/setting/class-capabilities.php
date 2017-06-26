<?php
/**
 * Admin view.
 *
 * @package CoursePress
 */

/**
 * Capabilities for Instructors.
 */
class CoursePress_View_Admin_Setting_Capabilities {

	public static function init() {
		add_filter(
			'coursepress_settings_tabs',
			array( __CLASS__, 'add_tabs' )
		);
		add_action(
			'coursepress_settings_process_capabilities',
			array( __CLASS__, 'process_form' ),
			10, 2
		);
		add_filter(
			'coursepress_settings_render_tab_capabilities',
			array( __CLASS__, 'return_content' ),
			10, 3
		);
	}

	public static function add_tabs( $tabs ) {
		if ( current_user_can( 'manage_options' ) ) {
			/*
			 * Instructors can be allowed to access the Settings submenu.
			 * But the "Instructor Capabilities" tab is only available to
			 * WordPress admins, so instructors cannot edit their own caps...
			 */
			$tabs['capabilities'] = array(
				'title' => __( 'Instructor Capabilities', 'cp' ),
				'description' => sprintf(
					'%s %s',
					__( 'Here you can decide, what your instructors can do on your page. Those are special capabilities only relevant for CoursePress.', 'cp' ),
					__( 'NOTE: For security reasons this page is only available for WordPress administrators!', 'cp' )
				),
				'order' => 30,
			);
		}

		return $tabs;
	}

	public static function return_content( $content, $slug, $tab ) {
		$instructor_capabilities = CoursePress_Data_Capabilities::get_instructor_capabilities();
		$boxes = self::_capability_boxes();

		ob_start();
		?>
		<input type="hidden" name="page" value="' . esc_attr( $slug ) . '"/>
		<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '"/>
		<input type="hidden" name="action" value="updateoptions"/>
		<?php wp_nonce_field( 'update-coursepress-options', '_wpnonce' ); ?>

		<div class="capability-list">

		<?php foreach ( $boxes as $group => $data ) : ?>
			<div class="cp-content-box <?php echo esc_attr( $group ); ?>">
			<h3 class="hndle">
				<span><?php echo esc_html( $data['title'] ); ?></span>
			</h3>
			<div class="inside">
				<table class="form-table compressed">
					<tbody id="items">

						<?php foreach ( $data['items'] as $key => $value ) : ?>
							<?php $checked = ! empty( $instructor_capabilities[ $key ] ); ?>
							<?php $name = 'coursepress_settings[instructor][capabilities][' . $key . ']'; ?>

							<tr class="<?php echo esc_attr( $key ); ?>">
								<td>
									<label>
										<input type="checkbox"
											<?php checked( $checked ); ?>
											name="<?php echo esc_attr( $name ); ?>"
											value="1" />
										<?php echo esc_html( $value ); ?>
									</label>
								</td>
							</tr>
						<?php endforeach; ?>

					</tbody>
				</table>
			</div>
			</div>
		<?php endforeach; ?>

		</div>
		<?php

		$content = ob_get_clean();

		return $content;
	}

	private static function _capability_boxes() {
		$options = array(
			'general' => array(
				'title' => __( 'General', 'cp' ),
				'items' => self::_instructor_capabilities_general(),
			),
			'course' => array(
				'title' => __( 'Courses', 'cp' ),
				'items' => self::_instructor_capabilities_courses(),
			),
			'course-category' => array(
				'title' => __( 'Course Categories', 'cp' ),
				'items' => self::_instructor_capabilities_course_categories(),
			),
			'course-unit' => array(
				'title' => __( 'Units', 'cp' ),
				'items' => self::_instructor_capabilities_units(),
			),
			'instructor' => array(
				'title' => __( 'Instructors', 'cp' ),
				'items' => self::_instructor_capabilities_instructors(),
			),
			'facilitator' => array(
				'title' => __( 'Facilitators', 'cp' ),
				'items' => self::_facilitator_capabilities(),
			),
			'student' => array(
				'title' => __( 'Students', 'cp' ),
				'items' => self::_instructor_capabilities_students(),
			),
			'notification' => array(
				'title' => __( 'Notifications', 'cp' ),
				'items' => self::_instructor_capabilities_notifications(),
			),
			'discussion' => array(
				'title' => __( 'Discussions', 'cp' ),
				'items' => self::_instructor_capabilities_discussions(),
			),
		);
		/**
		 * Add this capabilities only when MarketPress is acctive.
		 */
		$is_marketpress_active = apply_filters( 'coursepress_is_marketpress_active', false );
		if ( $is_marketpress_active ) {
			$options['wordpress'] = array(
				'title' => __( 'Grant default WordPress capabilities', 'cp' ),
				'items' => self::_instructor_capabilities_posts_and_pages(),
			);
		}
		return $options;
	}

	private static function _instructor_capabilities_general() {
		return array(
			'coursepress_dashboard_cap' => __( 'See the main CoursePress menu', 'cp' ),
			'coursepress_courses_cap' => __( 'Access the Courses submenus', 'cp' ),
			// 'coursepress_instructors_cap' => __( 'Access the Intructors submenu', 'cp' ),
			'coursepress_students_cap' => __( 'Access the Students submenu', 'cp' ),
			'coursepress_assessment_cap' => __( 'Access the Assessment submenu', 'cp' ),
			'coursepress_reports_cap' => __( 'Access the Reports submenu', 'cp' ),
			'coursepress_notifications_cap' => __( 'Access the Notifications submenu', 'cp' ),
			'coursepress_discussions_cap' => __( 'Access the Forum submenu', 'cp' ),
			'coursepress_settings_cap' => __( 'Access the Settings submenu', 'cp' ),
		);
	}

	private static function _instructor_capabilities_courses() {
		return array(
			'coursepress_create_course_cap' => __( 'Create new courses', 'cp' ),
			'coursepress_view_others_course_cap' => __( 'View other instructors course', 'cp' ),
			'coursepress_update_my_course_cap' => __( 'Update own courses', 'cp' ),
			'coursepress_update_course_cap' => __( 'Update any assigned course', 'cp' ),
			// 'coursepress_update_all_courses_cap' => __( 'Update ANY course', 'cp' ),
			'coursepress_delete_my_course_cap' => __( 'Delete own courses', 'cp' ),
			'coursepress_delete_course_cap' => __( 'Delete any assigned course', 'cp' ),
			// 'coursepress_delete_all_courses_cap' => __( 'Delete ANY course', 'cp' ),
			'coursepress_change_my_course_status_cap' => __( 'Change status of own courses', 'cp' ),
			'coursepress_change_course_status_cap' => __( 'Change status of any assigned course', 'cp' ),
			// 'coursepress_change_all_courses_status_cap' => __( 'Change status of ALL course', 'cp' ),
		);
	}

	private static function _instructor_capabilities_course_categories() {
		return array(
			'coursepress_course_categories_manage_terms_cap' => __( 'View and create categories', 'cp' ),
			'coursepress_course_categories_edit_terms_cap' => __( 'Edit any category', 'cp' ),
			'coursepress_course_categories_delete_terms_cap' => __( 'Delete any category', 'cp' ),
		);
	}

	private static function _facilitator_capabilities() {
		return array(
			'coursepress_assign_my_course_facilitator_cap' => __( 'Assign facilitator to own course', 'cp' ),
			'coursepress_assign_facilitator_cap' => __( 'Assign facilitator to any course', 'cp' ),
		);
	}

	private static function _instructor_capabilities_units() {
		return array(
			'coursepress_create_course_unit_cap' => __( 'Create new course units', 'cp' ),
			'coursepress_view_all_units_cap' => __( 'View units in every course (also from other instructors)', 'cp' ),
			'coursepress_update_my_course_unit_cap' => __( 'Update own units', 'cp' ),
			'coursepress_update_course_unit_cap' => __( 'Update any unit within assigned courses', 'cp' ),
			// 'coursepress_update_all_courses_unit_cap' => __( 'Update units of ALL courses', 'cp' ),
			'coursepress_delete_my_course_units_cap' => __( 'Delete own units', 'cp' ),
			'coursepress_delete_course_units_cap' => __( 'Delete any unit within assigned courses', 'cp' ),
			// 'coursepress_delete_all_courses_units_cap' => __( 'Delete units of ALL courses', 'cp' ),
			'coursepress_change_my_course_unit_status_cap' => __( 'Change status of own units', 'cp' ),
			'coursepress_change_course_unit_status_cap' => __( 'Change status of any unit within assigned courses', 'cp' ),
			// 'coursepress_change_all_courses_unit_status_cap' => __( 'Change status of any unit of ALL courses', 'cp' ),
		);
	}

	private static function _instructor_capabilities_instructors() {
		return array(
			'coursepress_assign_and_assign_instructor_my_course_cap' => __( 'Assign other instructors to own courses', 'cp' ),
			'coursepress_assign_and_assign_instructor_course_cap' => __( 'Assign other instructors to any course', 'cp' ),
		);
	}

	private static function _instructor_capabilities_students() {
		return array(
			'coursepress_invite_my_students_cap' => __( 'Invite students to own courses', 'cp' ),
			'coursepress_invite_students_cap' => __( 'Invite students to any course', 'cp' ),
			'coursepress_withdraw_my_students_cap' => __( 'Withdraw students from own courses', 'cp' ),
			'coursepress_withdraw_students_cap' => __( 'Withdraw students from any course', 'cp' ),
			'coursepress_add_move_my_students_cap' => __( 'Add students to own courses', 'cp' ),
			'coursepress_add_move_students_cap' => __( 'Add students to any course', 'cp' ),
			'coursepress_add_move_my_assigned_students_cap' => __( 'Add students to assigned courses', 'cp' ),
			// 'coursepress_change_my_students_group_class_cap' => __( 'Change students group within own courses', 'cp' ),
			// 'coursepress_change_students_group_class_cap' => __( 'Change students group in any course', 'cp' ),
			//'coursepress_send_bulk_my_students_email_cap' => __( 'Send bulk email to students of own courses', 'cp' ),
			//'coursepress_send_bulk_students_email_cap' => __( 'Send bulk email to all students', 'cp' ),
			//'coursepress_add_new_students_cap' => __( 'Create new users with student role to the blog', 'cp' ),
			//'coursepress_delete_students_cap' => __( 'Delete students (deletes ALL associated course records)', 'cp' ),
		);
	}

	private static function _instructor_capabilities_notifications() {
		return array(
			'coursepress_create_my_notification_cap' => __( 'Create new notifications for own courses', 'cp' ),
			'coursepress_create_my_assigned_notification_cap' => __( 'Create new notifications for assigned courses', 'cp' ),
			'coursepress_update_my_notification_cap' => __( 'Update own published notification', 'cp' ),
			'coursepress_update_notification_cap' => __( 'Update every notification', 'cp' ),
			'coursepress_delete_my_notification_cap' => __( 'Delete own notifications', 'cp' ),
			'coursepress_delete_notification_cap' => __( 'Delete every notification', 'cp' ),
			'coursepress_change_my_notification_status_cap' => __( 'Change statuses of own notifications', 'cp' ),
			'coursepress_change_notification_status_cap' => __( 'Change status of every notification', 'cp' ),
		);
	}

	private static function _instructor_capabilities_discussions() {
		return array(
			'coursepress_create_my_discussion_cap' => __( 'Create new discussions for own courses', 'cp' ),
			'coursepress_create_my_assigned_discussion_cap' => __( 'Create new discussions for assigned courses', 'cp' ),
			'coursepress_update_my_discussion_cap' => __( 'Update own published discussions', 'cp' ),
			'coursepress_update_discussion_cap' => __( 'Update every discussion', 'cp' ),
			'coursepress_delete_my_discussion_cap' => __( 'Delete own discussions', 'cp' ),
			'coursepress_delete_discussion_cap' => __( 'Delete every discussion', 'cp' ),
			'coursepress_change_my_discussion_status_cap' => __( 'Change statuses of own discussions', 'cp' ),
			'coursepress_change_discussion_status_cap' => __( 'Change status of every discussion', 'cp' ),
		);
	}

	private static function _instructor_capabilities_posts_and_pages() {
		return array(
			'edit_pages' => __( 'Edit Pages (required for MarketPress)', 'cp' ),
			'edit_published_pages' => __( 'Edit Published Pages', 'cp' ),
			'edit_posts' => __( 'Edit Posts', 'cp' ),
			'publish_pages' => __( 'Publish Pages', 'cp' ),
			'publish_posts' => __( 'Publish Posts', 'cp' ),
		);
	}


	public static function process_form( $page, $tab ) {
		if ( ! isset( $_POST['action'] ) ) { return; }
		if ( 'updateoptions' != $_POST['action'] ) { return; }
		if ( 'capabilities' != $tab ) { return; }
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'update-coursepress-options' ) ) { return; }

		$settings = CoursePress_Core::get_setting( false ); // false: Get all settings.
		$post_settings = (array) $_POST['coursepress_settings'];

		// Sanitize $post_settings, especially to fix up unchecked checkboxes.
		$caps = array_keys( CoursePress_Data_Capabilities::$capabilities['instructor'] );
		$set_caps = array_keys( $post_settings['instructor']['capabilities'] );

		foreach ( $caps as $cap ) {
			$is_set = in_array( $cap, $set_caps );
			$post_settings['instructor']['capabilities'][ $cap ] = $is_set;
		}

		// Don't replace settings if there is nothing to replace.
		if ( ! empty( $post_settings ) ) {
			CoursePress_Core::update_setting(
				false, // False will replace all settings.
				CoursePress_Core::merge_settings( $settings, $post_settings )
			);
		}
	}
}
