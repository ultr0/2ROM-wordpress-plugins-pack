<?php
/**
 * Course Edit - Step 3
 **/
?>
<div class="step-title step-3">
	<?php _e( 'Step 3 &ndash; Instructors and Facilitators', 'cp' ); ?>
	<div class="status <?php echo $setup_class; ?>"></div>
</div>

<div class="step-content step-3">
	<input type="hidden" name="meta_setup_step_3" value="saved" />

	<?php if ( $can_assign_instructor ) : ?>
		<div class="wide">
			<label><?php _e( 'Course Instructor(s)', 'cp' ); ?>
				<p class="description"><?php _e( 'Select one or more instructor to facilitate this course', 'cp' ); ?></p>
			</label>
			<select id="instructors" style="width:350px;" name="instructors" data-nonce-search="<?php echo $search_nonce; ?>" class="medium"></select>
			<input type="button" class="button button-primary instructor-assign disabled" value="<?php esc_attr_e( 'Assign', 'cp' ); ?>" />
		</div>
	<?php endif; ?>

	<div class="instructors-info medium" id="instructors-info">
		<p><?php echo $can_assign_instructor ? __( 'Assigned Instructors:', 'cp' ) : __( 'You do not have sufficient permission to add instructor!' ); ?></p>

		<?php if ( empty( $instructors )  && $can_assign_instructor ) : ?>
			<div class="instructor-avatar-holder empty">
				<span class="instructor-name"><?php _e( 'Please Assign Instructor', 'cp' ); ?></span>
			</div>
			<?php echo CoursePress_Helper_UI::course_pendings_instructors_avatars( $course_id ); ?>
		<?php else: ?>
			<?php echo CoursePress_Helper_UI::course_instructors_avatars( $course_id, array(), true ); ?>
		<?php endif; ?>
	</div>

	<?php if ( $can_assign_facilitator ) : ?>
		<div class="wide">
			<label><?php _e( 'Course Facilitator(s)', 'cp' ); ?>
				<p class="description"><?php _e( 'Select one or more facilitator to facilitate this course', 'cp' ); ?></p>
			</label>
			<select data-nonce-search="<?php echo $facilitator_search_nonce; ?>" name="facilitators" style="width:350px;" id="facilitators" class="user-dropdown medium"></select>
			<input type="button" class="button button-primary facilitator-assign disabled" value="<?php esc_attr_e( 'Assign', 'cp' ); ?>" />
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $facilitators ) ) : ?>
		<div class="wide">
			<label><?php _e( 'Course Facilitators', 'cp' ); ?></label>
		</div>
	<?php endif; ?>

	<div class="wide facilitator-info medium" id="facilitators-info"><br />
		<?php echo CoursePress_Helper_UI::course_facilitator_avatars( $course_id, array(), true ); ?>
	</div>

	<?php if ( $can_assign_instructor || $can_assign_facilitator ) : ?>
		<div class="wide">
			<hr />
			<label><?php echo $label; ?>
				<p class="description"><?php echo $description; ?></p>
			</label>

			<div class="instructor-invite">

				<?php if ( $can_assign_instructor && $can_assign_facilitator ) : ?>
					<label><?php _e( 'Instructor or Facilitator', 'cp' ); ?></label>
					<ul>
						<li>
							<label>
								<input type="radio" name="invite_instructor_type" value="instructor" checked="checked" /> <?php _e( 'Instructor', 'cp' ); ?></label>
						</li>
						<li>
							<label>
								<input type="radio" name="invite_instructor_type" value="facilitator" /> <?php _e( 'Facilitator', 'cp' ); ?></label>
						</li>
					</ul>
				<?php elseif ( $can_assign_instructor ) : ?>
					<input type="hidden" name="invite_instructor_type="instructor" />
				<?php elseif ( $can_assign_facilitator ) : ?>
					<input type="hidden" name="invite_instructor_type="facilitator" />
				<?php endif; ?>

				<label for="invite_instructor_first_name"><?php _e( 'First Name', 'cp' ); ?></label>
				<input type="text" name="invite_instructor_first_name" placeholder="<?php esc_attr_e( 'First Name', 'cp' ); ?>"/>
				<label for="invite_instructor_last_name"><?php _e( 'Last Name', 'cp' ); ?></label>
				<input type="text" name="invite_instructor_last_name" placeholder="<?php esc_attr_e( 'Last Name', 'cp' ); ?>" />
				<label for="invite_instructor_email"><?php _e( 'E-Mail', 'cp' ); ?></label>
				<input type="text" name="invite_instructor_email" placeholder="<?php echo esc_attr( $placeholder ); ?>" />

				<div class="submit-message">
					<input class="button-primary" name="invite_instructor_trigger" id="invite-instructor-trigger" type="button" value="<?php _e( 'Send Invite', 'cp' ); ?>" />
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php
	// Include JS template
	echo CoursePress_Template_Course::javascript_templates();

	/**
	 * Trigger after printing step 3 fields.
	 **/
	echo apply_filters( 'coursepress_course_setup_step_3', '', $course_id );

	// Print buttons
	echo static::get_buttons( $course_id, 3 );
	?>
</div>