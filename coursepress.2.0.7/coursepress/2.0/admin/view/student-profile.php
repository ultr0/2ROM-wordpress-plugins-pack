<?php
$student_id = isset( $_GET['student_id'] ) ? intval( $_GET['student_id'] ) : 0;
$student = get_userdata( $student_id );
?>
<div class="wrap coursepress_wrapper course-student-profile">
	<h2><?php esc_html_e( 'Student Profile', 'cp' ); ?></h2>
<?php
$nonce_verify = CoursePress_Admin_Students::view_profile_verify_nonce( $student_id );
if ( is_a( $student, 'WP_User' ) && $nonce_verify ) {
	$avatar = get_avatar( $student->user_email, 92 );
	$enrolled_courses = CoursePress_Data_Student::get_enrolled_courses_ids( $student_id );
	$date_format = get_option( 'date_format' );
	$time_format = get_option( 'time_format' );
?>
	<table class="widefat striped">
		<tr>
			<td rowspan="4" width="5%"><?php echo $avatar; ?></td>
			<td width="15%"><?php esc_html_e( 'Student ID', 'cp' ); ?></td>
			<td><?php echo $student_id; ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'First Name', 'cp' ); ?></td>
			<td><?php echo $student->first_name; ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Last Name', 'cp' ); ?></td>
			<td><?php echo $student->last_name; ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Display Name', 'cp' ); ?></td>
			<td><?php echo $student->display_name; ?></td>
		</tr>
<?php if ( current_user_can( 'edit_users' ) ) { ?>
		<tr>
			<td rowspan="2"><a href="<?php echo get_edit_user_link( $student->ID ); ?>" class="button"><?php _e( 'Edit user', 'cp' ); ?></td>
			<td><?php esc_html_e( 'Email', 'cp' ); ?></td>
			<td><?php echo $student->user_email; ?></td>
		</tr>
		<tr>
			<td><?php esc_html_e( 'Registered', 'cp' ); ?></td>
			<td><?php echo $student->user_registered; ?></td>
		</tr>

<?php } ?>
	</table>
	<h3><?php esc_html_e( 'Enrolled Courses', 'cp' ); ?></h3>
	<?php $this->enrolled_courses->display(); ?>
<?php } else {
	_e( 'Student not found.', 'cp' );
} ?>
</div>
