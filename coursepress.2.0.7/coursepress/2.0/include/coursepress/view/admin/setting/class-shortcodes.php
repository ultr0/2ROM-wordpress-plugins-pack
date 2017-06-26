<?php

class CoursePress_View_Admin_Setting_Shortcodes {

	public static function init() {
		add_filter(
			'coursepress_settings_tabs',
			array( __CLASS__, 'add_tabs' )
		);
		add_action(
			'coursepress_settings_process_shortcodes',
			array( __CLASS__, 'process_form' ),
			10, 2
		);
		add_filter(
			'coursepress_settings_render_tab_shortcodes',
			array( __CLASS__, 'return_content' ),
			10, 3
		);
	}

	public static function add_tabs( $tabs ) {
		$tabs['shortcodes'] = array(
			'title' => __( 'Shortcodes', 'cp' ),
			'description' => __( 'Shortcodes allow you to include dynamic content in posts and pages on your site. Simply type or paste them into your post or page content where you would like them to appear. Optional attributes can be added in a format like <em>[shortcode attr1="value" attr2="value"]</em>.', 'cp' ),
			'order' => 50,
			'buttons' => 'none',
		);

		return $tabs;
	}

	public static function return_content( $content, $slug, $tab ) {
		$content = 'shortcodes!';
		$boxes = self::_boxes();

		ob_start();
		?>
		<div class="shortcodes-list">
			<?php foreach ( $boxes as $group => $data ) : ?>
            <div class="cp-content-box <?php echo esc_attr( $group ); ?>" id="shortcode-<?php echo esc_attr( $group ); ?>">
				<h3 class="hndle">
					<span><?php echo esc_html( $data['title'] ); ?></span>
				</h3>
				<div class="inside"><?php echo $data['content']; ?></div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php

		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public static function process_form() {
	}

	private static function _boxes() {
		$boxes = array(
			'course_instructors' => array(
				'title' => __( 'Instructors List', 'cp' ),
				'content' => self::_box_course_instructors(),
			),
			'course_instructor_avatar' => array(
				'title' => __( 'Instructors Avatar', 'cp' ),
				'content' => self::_box_course_instructor_avatar(),
			),
			'instructor_profile_url' => array(
				'title' => __( 'Instructor Profile URL', 'cp' ),
				'content' => self::_box_instructor_profile_url(),
			),
			'course' => array(
				'title' => __( 'Course', 'cp' ),
				'content' => self::_box_course(),
			),
			'course_details' => array(
				'title' => __( 'Course Details', 'cp' ),
				'content' => self::_box_course_details(),
			),
			'course_title' => array(
				'title' => __( 'Course Title', 'cp' ),
				'content' => self::_box_course_title(),
			),
			'course_summary' => array(
				'title' => __( 'Course Summary', 'cp' ),
				'content' => self::_box_course_summary(),
			),
			'course_description' => array(
				'title' => __( 'Course Description', 'cp' ),
				'content' => self::_box_course_description(),
			),
			'course_start' => array(
				'title' => __( 'Course Start Date', 'cp' ),
				'content' => self::_box_course_start_date(),
			),
			'course_end' => array(
				'title' => __( 'Course End Date', 'cp' ),
				'content' => self::_box_course_end_date(),
			),
			'course_dates' => array(
				'title' => __( 'Course Dates', 'cp' ),
				'content' => self::_box_course_dates(),
			),
			'course_enrollment_start' => array(
				'title' => __( 'Course Enrollment Start', 'cp' ),
				'content' => self::_box_course_enrollment_start(),
			),
			'course_enrollment_end' => array(
				'title' => __( 'Course Enrollment End', 'cp' ),
				'content' => self::_box_course_enrollment_end(),
			),
			'course_enrollment_dates' => array(
				'title' => __( 'Course Enrollment Dates', 'cp' ),
				'content' => self::_box_course_enrollment_dates(),
			),
			'course_enrollment_type' => array(
				'title' => __( 'Coure Enrollment Type', 'cp' ),
				'content' => self::_box_course_enrollment_type(),
			),
			'course_class_size' => array(
				'title' => __( 'Course Class Size', 'cp' ),
				'content' => self::_box_course_class_size(),
			),
			'course_cost' => array(
				'title' => __( 'Course Cost', 'cp' ),
				'content' => self::_box_course_cost(),
			),
			'course_time_estimation' => array(
				'title' => __( 'Course Time Estimation', 'cp' ),
				'content' => self::_box_course_time_estimation(),
			),
			'course_language' => array(
				'title' => __( 'Course Language', 'cp' ),
				'content' => self::_box_course_language(),
			),
			'course_list_image' => array(
				'title' => __( 'Course List Image', 'cp' ),
				'content' => self::_box_course_list_image(),
			),
			'course_featured_video' => array(
				'title' => __( 'Course Featured Video', 'cp' ),
				'content' => self::_box_course_featured_video(),
			),
			'course_media' => array(
				'title' => __( 'Course Media', 'cp' ),
				'content' => self::_box_course_media(),
			),
			'course_join_button' => array(
				'title' => __( 'Course Join Button', 'cp' ),
				'content' => self::_box_course_join_button(),
			),
			'course_action_links' => array(
				'title' => __( 'Course Action Links', 'cp' ),
				'content' => self::_box_course_action_links(),
			),
			'course_calendar' => array(
				'title' => __( 'Course Calendar', 'cp' ),
				'content' => self::_box_course_calendar(),
			),
			'course_list' => array(
				'title' => __( 'Course List', 'cp' ),
				'content' => self::_box_course_list(),
			),
			'course_featured' => array(
				'title' => __( 'Featured Course', 'cp' ),
				'content' => self::_box_course_featured(),
			),
			'course_structure' => array(
				'title' => __( 'Course Structure', 'cp' ),
				'content' => self::_box_course_structure(),
			),
			'course_signup' => array(
				'title' => __( 'Course Signup/Login Page', 'cp' ),
				'content' => self::_box_course_signup(),
			),
			'courses_student_dashboard' => array(
				'title' => __( 'Student Dashboard Template', 'cp' ),
				'content' => self::_box_courses_student_dashboard(),
			),
			'courses_student_settings' => array(
				'title' => __( 'Student Settings Template', 'cp' ),
				'content' => self::_box_courses_student_settings(),
			),
		);
		ksort( $boxes );
		return $boxes;
	}

	/**
	 * Produce help box for course_instructors.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_instructors() {
		$data = array(
			'shortcode' => 'course_instructors',
			'content' => __( 'Display a list or count of Instructors ( gravatar, name and link to profile page )', 'cp' ),
			'parameters' => array(
				'optional' => array(
					'course_id' => array(
						'content' => __( 'ID of the course instructors are assign to ( required if use it outside of a loop )', 'cp' ),
					),
					'style' => array(
						'content' => __( 'How to display the instructors.', 'cp' ),
						'options' => array( 'block', 'default', 'list', 'list-flat', 'count' ),
						'options_description' => __( 'count - counts instructors for the course.', 'cp' ),
					),
					'label' => array(
						'content' => __( 'Label to display for the output.', 'cp' ),
					),
					'label_plural' => array(
						'content' => __( 'Plural if more than one instructor.', 'cp' ),
						'default' => __( 'Instructors', 'cp' ),
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol to put after label.', 'cp' ),
						'default' => ':',
					),
					'label_tag' => array(
						'content' => __( 'HTML tag to wrap the label (without brackets, e.g. <em>h3</em>).', 'cp' ),
						'default' => __( 'empty', 'cp' ),
					),
					'link_text' => array(
						'content' => __( 'Text to click to link to full profiles.', 'cp' ),
						'default' => __( 'View Full Profile', 'cp' ),
					),
					'show_label' => array(
						'content' => __( 'Show the label.', 'cp' ),
						'options' => array( 'yes', 'no' ),
					),
					'summary_length' => array(
						'content' => __( 'Length of instructor bio to show when style is "block".', 'cp' ),
						'default' => __( 50, 'cp' ),
					),
					'list_separator' => array(
						'content' => __( 'Symbol to use to separate instructors when styl is "list" or "list-flat".', 'cp' ),
						'default' => ',',
					),
					'avatar_size' => array(
						'content' => __( 'Pixel size of the avatars when viewing in block mode.', 'cp' ),
						'default' => __( 80, 'cp' ),
					),
					'default_avatar' => array(
						'content' => __( 'URL to a default image if the user avatar cannot be found.', 'cp' ),
					),
					'show_divider' => array(
						'content' => __( 'Put a divider between instructor profiles when style is "block".', 'cp' ),
					),
					'link_all' => array(
						'content' => __( 'Make the entire instructor profile a link to the full profile.', 'cp' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_instructors]',
				'[course_instructors course_id="5"]',
				'[course_instructors style="list"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_instructor_avatar.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_instructor_avatar() {
		$data = array(
			'shortcode' => 'course_instructor_avatar',
			'content' => __( 'Display an instructor’s avatar.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'instructor_id' => array(
						'content' => __( 'The user id of the instructor.', 'cp' ),
					),
				),
				'optional' => array(
					'force_display' => array(
						'content' => __( 'Whether to always show the default image, never the Gravatar.', 'cp' ),
					),
					'thumb_size' => array(
						'content' => __( 'Size of avatar thumbnail.', 'cp' ),
						'default' => 80,
					),
					'class' => array(
						'content' => __( 'CSS class to use for the avatar.', 'cp' ),
						'default' => 'small-circle-profile-image',
					),
				),
			),
			'examples' => array(
				'[course_instructor_avatar instructor_id="1"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for instructor_profile_url.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_instructor_profile_url() {
		$data = array(
			'shortcode' => 'instructor_profile_url',
			'content' => __( 'Returns the URL to the instructor profile.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'instructor_id' => array(
						'content' => __( 'The user id of the instructor.', 'cp' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[instructor_profile_url instructor_id="1"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course() {
		$data = array(
			'shortcode' => 'course',
			'content' => __( 'This shortcode allows you to display details about your course.', 'cp' ),
		   'note' => __( 'All the same information can be retrieved by using the specific course shortcodes following.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
					'show' => array(
						'content' => __( 'All the fields you would like to show.', 'cp' ),
						'default' => 'summary',
						'options' => array( 'title', ' summary', ' description', ' start', ' end', ' dates', ' enrollment_start', ' enrollment_end', ' enrollment_dates', ' enrollment_type', ' class_size', ' cost', ' language', ' instructors', ' image', ' video', ' media', ' button', ' action_links', ' calendar', ' thumbnail' ),
					),
				),
				'optional' => array(
					'show_title' => array(
						'content' => __( 'Required when showing the "title" field.', 'cp' ),
						'defulat' => 'no',
						'options' => array( 'yes', 'no' ),
					),
					'date_format' => array(
						'content' => __( 'PHP style date format.', 'cp' ),
						'defulat' => 'WP',
					),
					'label_tag' => array(
						'content' => __( 'HTML tag (without brackets) to use for the individual labels.', 'cp' ),
						'defulat' => 'strong',
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol to use after the label.', 'cp' ),
						'defulat' => ':',
					),
				),
			),
			'examples' => array(
				'[course show="title,summary,cost,button" course_id="5"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course details.
	 *
	 * @since 2.0.2
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_details() {
		$data = array(
			'shortcode' => 'course_details',
			'content' => __( 'This shortcode is an alias to [course] shortcode. see section [course] shortcode for details.', 'cp' ),
			'examples' => array(
				'[course show="title,summary,cost,button" course_id="5"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_title.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_title() {
		$data = array(
			'shortcode' => 'course_title',
			'content' => __( 'Displays the course title.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'title_tag' => array(
						'content' => __( 'The HTML tag (without brackets) to use for the title.', 'cp' ),
						'default' => 'h3',
					),
					'link' => array(
						'content' => __( '.', 'cp' ),
						'default' => 'empty',
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_title course_id="4"]',
				'[course_title]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_summary.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_summary() {
		$data = array(
			'shortcode' => 'course_summary',
			'content' => __( 'Displays the course summary/excerpt.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
			),
			'examples' => array(
				'[course_summary course_id="4"]',
				'[course_summary]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_description.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_description() {
		$data = array(
			'shortcode' => 'course_description',
			'content' => __( 'Displays the longer course description (post content).', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'label' => array(
						'content' => __( 'Additional label will be displayed before description.', 'cp' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_description course_id="4"]<br />[course_description]'
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_description.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_start_date() {
		$data = array(
			'shortcode' => 'course_start',
			'content' => __( 'Shows the course start date.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'date_format' => array(
						'content' => __( 'PHP style date format.', 'cp' ),
						'default' => 'wp',
					),
					'label' => array(
						'content' => __( 'Label to display for the output. Set label to "" to hide the label completely.', 'cp' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML tag (without brackets) to use for the individual labels.', 'cp' ),
						'default' => __( 'strong', 'cp' ),
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol to use after the label.', 'cp' ),
						'default' => ':',
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_start]',
				'[course_start label="Awesomeness begins on" label_tag="h3"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_end.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_end_date() {
		$data = array(
			'shortcode' => 'course_end',
			'content' => __( 'Shows the course end date.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'date_format' => array(
						'content' => __( 'PHP style date format.', 'cp' ),
						'default' => 'wp',
						'description' => __( '<a href="https://codex.wordpress.org/Formatting_Date_and_Time">Documentation on date and time formatting</a>.' ),
					),
					'label' => array(
						'content' => __( 'Label to display for the output. Set label to "" to hide the label completely.', 'cp' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML tag (without brackets) to use for the individual labels.', 'cp' ),
						'default' => __( 'strong', 'cp' ),
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol to use after the label.', 'cp' ),
						'default' => ':',
					),
					'no_date_text' => array(
						'content' => __( 'Text to display if the course has no end date.', 'cp' ),
						'default' => __( 'No End Date', 'cp' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_end]',
				'[course_end label="The End." label_tag="h3" course_id="5"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_dates.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_dates() {
		$data = array(
			'shortcode' => 'course_dates',
			'content' => __( 'Displays the course start and end date range. Typically as [course_start] - [course_end].', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'date_format' => array(
						'content' => __( 'PHP style date format.', 'cp' ),
						'default' => 'wp',
					),
					'label' => array(
						'content' => __( 'Label to display for the output. Set label to "" to hide the label completely.', 'cp' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML tag (without brackets) to use for the individual labels.', 'cp' ),
						'default' => 'strong',
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol to use after the label.', 'cp' ),
						'default' => ':',
					),
					'no_date_text' => array(
						'content' => __( 'Text to display if the course has no end date.', 'cp' ),
						'default' => __( 'No End Date', 'cp' ),
					),
					'alt_display_text' => array(
						'content' => __( 'Alternate display when there is no end date.', 'cp' ),
						'default' => __( 'Open-ended', 'cp' ),
					),
					'show_alt_display' => array(
						'content' => __( 'If set to "yes" use the alt_display_text. If set to "no" use the "no_date_text".', 'cp' ),
						'default' => __( 'no', 'cp' ),
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_dates course_id="42"]',
				'[course_dates course_id="42" show_alt_display="yes" alt_display_text="Learn Anytime!"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_enrollment_start.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_enrollment_start() {
		$data = array(
			'shortcode' => 'course_enrollment_start',
			'content' => __( 'Displays the course enrollment start date.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'date_format' => array(
						'content' => __( 'PHP style date format.', 'cp' ),
						'default' => 'wp',
					),
					'label' => array(
						'content' => __( 'Label to display for the output. Set label to "" to hide the label completely.', 'cp' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML tag (without brackets) to use for the individual labels.', 'cp' ),
						'default' => __( 'strong', 'cp' ),
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol to use after the label.', 'cp' ),
						'default' => ':',
					),
					'no_date_text' => array(
						'content' => __( 'Text to display if the course has no defined enrollment start date.', 'cp' ),
						'default' => __( 'Enroll Anytime', 'cp' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_enrollment_start]',
				'[course_enrollment_start label="Signup from" label_tag="em"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_enrollment_end.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_enrollment_end() {
		$data = array(
			'shortcode' => 'course_enrollment_end',
			'content' => __( 'Shows the course enrollment end date.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'date_format' => array(
						'content' => __( 'PHP style date format.', 'cp' ),
						'default' => __( 'WordPress setting.', 'cp' ),
					),
					'label' => array(
						'content' => __( 'Label to display for the output. Set label to "" to hide the label completely.', 'cp' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML tag (without brackets) to use for the individual labels.', 'cp' ),
						'default' => 'strong',
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol to use after the label.', 'cp' ),
						'default' => ':',
					),
					'no_date_text' => array(
						'content' => __( 'Text to display if there is no enrollment end date.', 'cp' ),
						'default' => __( 'Enroll Anytime', 'cp' ),
					),
					'show_all_dates' => array(
						'content' => __( 'If "yes" it will display the no_date_text even if there is no date. If "no" then nothing will be displayed.', 'cp' ),
						'default' => __( 'no', 'cp' ),
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_end]',
				'[course_end label="End" label_delimeter="-"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_enrollment_dates.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_enrollment_dates() {
		$data = array(
			'shortcode' => 'course_enrollment_dates',
			'content' => __( 'Displays the course enrollment start and end date range. Typically as [course_enrollment_start] - [course_enrollment_end].', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'alt_display_text' => array(
						'content' => __( 'Alternate display when there is no enrollment start or end dates.', 'cp' ),
						'default' => __( 'Open-ended', 'cp' ),
					),
					'date_format' => array(
						'content' => __( 'PHP style date format.', 'cp' ),
						'default' => 'wp',
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol to use after the label.', 'cp' ),
						'default' => 'wp',
					),
					'label_enrolled' => array(
						'content' => __( 'Label to display for enroled date.', 'cp' ),
						'default' => __( 'You Enrolled on: ', 'cp' ),
					),
					'label' => array(
						'content' => __( 'Label to display for the output. Set label to "" to hide the label completely.', 'cp' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML tag (without brackets) to use for the individual labels.', 'cp' ),
						'default' => __( 'strong', 'cp' ),
					),
					'no_date_text' => array(
						'content' => __( 'Text to display if there is no enrollment start or end dates.', 'cp' ),
						'default' => __( 'Enroll Anytime', 'cp' ),
					),
					'show_alt_display' => array(
						'content' => __( 'If set to "yes" use the alt_display_text. If set to "no" use the "no_date_text".', 'cp' ),
						'default' => __( 'no', 'cp' ),
						'options' => array( 'yes', 'no' ),
					),
					'show_enrolled_display' => array(
						'content' => __( 'Display enrollment start label.', 'cp' ),
						'default' => __( 'yes', 'cp' ),
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_enrollment_dates]',
				'[course_enrollment_dates no_date_text="No better time than now!"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_enrollment_type.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_enrollment_type() {
		$data = array(
			'shortcode' => 'course_enrollment_type',
			'content' => __( 'Shows the type of enrollment (manual, prerequisite, passcode or anyone).', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'anyone_text' => array(
						'content' => __( 'Text to display when anyone can enroll.', 'cp' ),
						'default' => __( 'Anyone', 'cp' ),
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol to use after the label.', 'cp' ),
						'default' => ':',
					),
					'label' => array(
						'content' => __( 'Label to display for the output. Set label to "" to hide the label completely.', 'cp' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML tag (without brackets) to use for the individual labels.', 'cp' ),
						'default' => 'strong',
					),
					'manual_text' => array(
						'content' => __( 'Text to display for manual enrollments.', 'cp' ),
						'default' => __( 'Students are added by instructors.', 'cp' ),
					),
					'passcode_text' => array(
						'content' => __( 'Text to display when a passcode is required.', 'cp' ),
						'default' => __( 'A passcode is required to enroll.', 'cp' ),
					),
					'prerequisite_text' => array(
						'content' => __( 'Text to display when there is a prerequisite. Use %s as placeholder for prerequisite course title.', 'cp' ),
						'default' => __( 'Students need to complete "%s" first.', 'cp' ),
					),
					'registered_text' => array(
						'content' => __( 'Text to display for registered users.', 'cp' ),
						'default' => __( 'Registered users.', 'cp' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_enrollment_type]',
				'[course_enrollment_type course_id="42"]',
				'[course_enrollment_type passcode_text="Whats the magic word?"',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_class_size.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_class_size() {
		$data = array(
			'shortcode' => 'course_class_size',
			'content' => __( 'Shows the course class size, limits and remaining seats.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'label_delimeter' => array(
						'content' => __( 'Symbol to use after the label.', 'cp' ),
						'default' => ':',
					),
					'label' => array(
						'content' => __( 'Label to display for the output. Set label to "" to hide the label completely.', 'cp' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML tag (without brackets) to use for the individual labels.', 'cp' ),
						'default' => 'strong',
					),
					'no_limit_text' => array(
						'content' => __( 'Text to display for unlimited class sizes.', 'cp' ),
						'default' => __( 'Unlimited', 'cp' ),
					),
					'remaining_text' => array(
						'content' => __( 'Text to display for remaining places. Use %d for the remaining number.', 'cp' ),
						'default' => __( '(%d places left)', 'cp' ),
					),
					'show_no_limit' => array(
						'content' => __( 'If "yes" it will show the no_limit_text. If "no" then nothing will display for unlimited courses.', 'cp' ),
						'default' => 'no',
						'options' => array( 'yes', 'no' ),
					),
					'show_remaining' => array(
						'content' => __( 'If "yes" show remaining_text. If "no" don’t show remaining places.', 'cp' ),
						'default' => 'yes',
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_class_size]',
				'[course_class_size course_id="42" no_limit_text="The more the merrier"]',
				'[course_class_size remaining_text="Only %d places remaining!"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_cost.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_cost() {
		$data = array(
			'shortcode' => 'course_cost',
			'content' => __( 'Shows the pricing for the course or free for unpaid courses.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'label_delimeter' => array(
						'content' => __( 'Symbol to use after the label.', 'cp' ),
						'default' => ':',
					),
					'label' => array(
						'content' => __( 'Label to display for the output. Set label to "" to hide the label completely.', 'cp' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML tag (without brackets) to use for the individual labels.', 'cp' ),
						'default' => 'strong',
					),
					'no_cost_text' => array(
						'content' => __( 'Text to display for unpaid courses.', 'cp' ),
						'default' => __( 'FREE', 'cp' ),
					),
					'show_icon' => array(
						'content' => __( 'Add extra span with class "product_price" around no_cost_text.', 'cp' ),
						'default' => __( 'no', 'cp' ),
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_cost]',
				'[course_cost no_cost_text="'. __( 'Free as in beer.', 'cp' ) .'"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_time_estimation.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_time_estimation() {
		$data = array(
			'shortcode' => 'course_time_estimation',
			'content' => __( 'Shows the total time estimation based on calculation of unit elements.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'label_delimeter' => array(
						'content' => __( 'Symbol to use after the label.', 'cp' ),
						'default' => ':',
					),
					'label' => array(
						'content' => __( 'Label to display for the output. Set label to "" to hide the label completely.', 'cp' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML tag (without brackets) to use for the individual labels.', 'cp' ),
						'default' => 'strong',
					),
					'wrapper' => array(
						'content' => __( 'Wrap inside a div tag (yes|no).', 'cp' ),
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_time_estimation course_id="42" wrapper="yes"]',
				'[course_time_estimation course_id="42"]',
				'[course_time_estimation wrapper="yes"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_language.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_language() {
		$data = array(
			'shortcode' => 'course_language',
			'content' => __( 'Displays the language of the course (if set).', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'label' => array(
						'content' => __( 'Label to display for the output. Set label to "" to hide the label completely.', 'cp' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML tag (without brackets) to use for the individual labels.', 'cp' ),
						'default' => 'strong',
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol to use after the label.', 'cp' ),
						'default' => ':',
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_language]',
				'[course_language label="Delivered in"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_list_image.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_list_image() {
		$data = array(
			'shortcode' => 'course_list_image',
			'content' => __( 'Displays the course list image. (See [course_media]).', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'width' => array(
						'content' => __( 'Width of image.', 'cp' ),
						'default' => __( 'Original width', 'cp' ),
					),
					'height' => array(
						'content' => __( 'Height of image.', 'cp' ),
						'default' => __( 'Original height', 'cp' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_list_image]',
				'[course_list_image width="100" height="100"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_featured_video.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_featured_video() {
		$data = array(
			'shortcode' => 'course_featured_video',
			'content' => __( 'Embeds a video player with the course’s featured video. (See [course_media]).', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'width' => array(
						'content' => __( 'Width of video player.', 'cp' ),
						'default' => __( 'Default player width', 'cp' ),
					),
					'height' => array(
						'content' => __( 'Height of video player.', 'cp' ),
						'default' => __( 'Default player height', 'cp' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_featured_video]',
				'[course_featured_video width="320" height="240"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_media.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_media() {
		$data = array(
			'shortcode' => 'course_media',
			'content' => __( 'Displays either the list image or the featured video (with the other option as possible fallback).', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'width' => array(
						'content' => __( 'Width of media.', 'cp' ),
						'default' => __( 'CoursePress settings.', 'cp' ),
					),
					'height' => array(
						'content' => __( 'Height of media.', 'cp' ),
						'default' => __( 'CoursePress settings.', 'cp' ),
					),
					'list_page' => array(
						'content' => __( 'Use "yes" to use the CoursePress Settings for "Course Listings". Use "no" to use the CoursePress Settings for "Course Details Page".', 'cp' ),
						'default' => __( 'no', 'cp' ),
						'options' => array( 'yes', 'no' ),
					),
					'priority' => array(
						'content' => __( 'Use "image" to try to show the list image first. If not available, then try to use the featured video.  Use "video" to try to show the featured video first. If not available, try to use the list image.', 'cp' ),
						'default' => __( 'CoursePress Settings', 'cp' ),
						'options' => array( 'image', 'video', 'default' ),
					),
					'type' => array(
						'content' => __( 'Use "image" to only display list image if available. Use "video" to only show the video if available. Use "thumbnail" to show the course thumbnail (shortcut for type="image" and priority="image"). Use "default" to enable priority mode (see priority attribute).', 'cp' ),
						'default' => __( 'CoursePress Settings', 'cp' ),
						'options' => array( 'image', 'video', 'thumbnail', 'default' ),
					),
					'wrapper' => array(
						'content' => __( 'Wrap inside a tag.', 'cp' ),
						'default' => __( 'empty string, but if height or width is defined, then wrapper is a "div" tag.', 'cp' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_media]',
				'[course_media list_page="yes"]',
				'[course_media type="video"]',
				'[course_media priority="image"]',
				'[course_media type="thumbnail"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_join_button.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_join_button() {
		$data = array(
			'shortcode' => 'course_join_button',
			'content' => __( 'Shows the Join/Signup/Enroll button for the course. What it displays is dependent on the course settings and the user’s status/enrollment.<br />See the attributes for possible button labels.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'access_text' => array(
						'content' => __( 'Text to display when the user is enrolled and ready to learn.', 'cp' ),
						'default' => __( 'Start Learning', 'cp' ),
					),
					'continue_learning_text' => array(
						'content' => __( 'Text to display when the course can be continued.', 'cp' ),
						'default' => __( 'Continue Learning', 'cp' ),
					),
					'course_expired_text' => array(
						'content' => __( 'Text to display when the course has expired.', 'cp' ),
						'default' => __( 'Not available', 'cp' ),
					),
					'course_full_text' => array(
						'content' => __( 'Text to display if the course is full.', 'cp' ),
						'default' => __( 'Course Full', 'cp' ),
					),
					'details_text' => array(
						'content' => __( 'Text for the button that takes you to the full course page.', 'cp' ),
						'default' => __( 'Course Details', 'cp' ),
					),
					'enrollment_closed_text' => array(
						'content' => __( 'Text to display when enrollments haven’t started yet.', 'cp' ),
						'default' => __( 'Enrollments Closed', 'cp' ),
					),
					'enrollment_finished_text' => array(
						'content' => __( 'Text to display when enrollments are finished (expired).', 'cp' ),
						'default' => __( 'Enrollments Finished', 'cp' ),
					),
					'enroll_text' => array(
						'content' => __( 'Text to display when course is ready for enrollments.', 'cp' ),
						'default' => __( 'Enroll now', 'cp' ),
					),
					'instructor_text' => array(
						'content' => __( 'Text to display when current user is an instructor of this course.', 'cp' ),
						'default' => __( 'Access Course', 'cp' ),
					),
					'list_page' => array(
						'content' => __( 'Show button to course details..', 'cp' ),
						'default' => 'false',
					),
					'not_started_text' => array(
						'content' => __( 'Text to display when a student is enrolled, but the course hasn’t started yet.', 'cp' ),
						'default' => __( 'Not available', 'cp' ),
					),
					'passcode_text' => array(
						'content' => __( 'Text to display if the course requires a password.', 'cp' ),
						'default' => __( 'Passcode Required', 'cp' ),
					),
					'prerequisite_text' => array(
						'content' => __( 'Text to display if the course has a prerequisite.', 'cp' ),
						'default' => __( 'Pre-requisite Required', 'cp' ),
					),
					'signup_text' => array(
						'content' => __( 'Text to display when course is ready for enrollments, but the user is not logged in (visitor).', 'cp' ),
						'default' => __( 'Signup!', 'cp' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_join_button]',
				'[course_join_button course_id="11" course_expired_text="'. __( 'You missed out big time!', 'cp' ).'"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_action_links.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_action_links() {
		$data = array(
			'shortcode' => 'course_action_links',
			'content' => __( 'Shows  "Course Details" and "Withdraw" links to students.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_action_links]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_calendar.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_calendar() {
		$data = array(
			'shortcode' => 'course_calendar',
			'content' => __( 'Shows the course calendar (bounds are restricted by course start and end dates). Will always attempt to show today’s date on a calendar first.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'date_indicator' => array(
						'content' => __( 'Classes string added calendar table tag holder.', 'cp' ),
						'default' => __( 'indicator_light_block', 'cp' ),
					),
					'month' => array(
						'content' => __( 'Month to display as number (e.g. 03 for March).', 'cp' ),
						'default' => __( 'Today’s date', 'cp' ),
					),
					'next' => array(
						'content' => __( 'Text to display for next month link.', 'cp' ),
						'default' => __( 'Next &raquo;', 'cp' ),
					),
					'pre' => array(
						'content' => __( 'Text to display for previous month link.', 'cp' ),
						'default' => __( '&laquo; Previous', 'cp' ),
					),
					'year' => array(
						'content' => __( 'Year to display as 4-digit number (e.g. 2014).', 'cp' ),
						'default' => __( 'Today’s date', 'cp' ),
					),
				),
			),
			'examples' => array(
				'[course_calendar]',
				'[course_calendar pre="< Previous" next="Next >"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_list.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_list() {
		$data = array(
			'shortcode' => 'course_list',
			'content' => __( 'Displays a listing of courses. Can be for all courses or restricted by instructors or students (only one or the other, if both specified only students will be used).', 'cp' ),
			'parameters' => array(
				'optional' => array(
					'completed_label' => array(
						'content' => __( 'Label for completed courses list..', 'cp' ),
						'default' => __( 'Completed courses', 'cp' ),
					),
					'context' => array(
						'content' => __( 'Context for the courses list. Possible values: .', 'cp' ),
						'default' => 'all',
						'options' => array( 'enrolled', 'future', 'incomplete', 'completed', 'past', 'manage', 'facilitator', 'all' ),
					),
					'current_label' => array(
						'content' => __( 'Label for current courses.', 'cp' ),
						'default' => __( 'Current Courses', 'cp' ),
					),
					'dashboard' => array(
						'content' => __( 'If is true or "yes" then switch context to "dashboard".', 'cp' ),
						'default' => 'empty',
					),
					'facilitator_label' => array(
						'content' => __( 'Label before courses list for "facilitator" context.', 'cp' ),
						'default' => __( 'Facilitated Courses', 'cp' ),
					),
					'facilitator' => array(
						'content' => __( 'If this true or "yes" switch content to "facilitator".', 'cp' ),
						'default' => 'empty',
					),
					'future_label' => array(
						'content' => __( 'Label for future courses.', 'cp' ),
						'default' => __( 'Starting soon', 'cp' ),
					),
					'incomplete_label' => array(
						'content' => __( 'Label for incomplete courses.', 'cp' ),
						'default' => __( 'Incomplete courses', 'cp' ),
					),
					'instructor_msg' => array(
						'content' => __( 'Message displayed on intructor page, when instructor do not have any assigned courses.', 'cp' ),
						'default' => __( 'The Instructor does not have any courses assigned yet.', 'cp' ),
					),
					'instructor' => array(
						'content' => __( 'The instructor id to list courses for a specific instructor. Can also specify multiple instructors using commas. (e.g. instructor="1,2,3").', 'cp' ),
						'default' => 'empty',
						'description' => __( 'If both student and instructor are specified, only the student will be used.', 'cp' ),
					),
					'limit' => array(
						'content' => __( 'Limit the number of courses. Use -1 to show all.', 'cp' ),
						'default' => __( '-1', 'cp' ),
					),
					'manage_label' => array(
						'content' => __( 'Label before manageable courses.', 'cp' ),
						'default' => __( 'Manage Courses', 'cp' ),
					),
					'order' => array(
						'content' => __( 'Order the courses. "ASC" for ascending order. "DESC" for descending order.', 'cp' ),
						'default' => __( 'ASC', 'cp' ),
						'options' => array( 'ASC', 'DESC' ),
					),
					'orderby' => array(
						'content' => __( 'Orderby the courses by course date or by course title.', 'cp' ),
						'default' => __( 'meta', 'cp' ),
                        'options' => array( 'meta', 'title' ),
                        'description' => __('It works only with default "context".', 'cp' ),
					),
					'past_label' => array(
						'content' => __( 'Label before past courses.', 'cp' ),
						'default' => __( 'Past courses', 'cp' ),
					),
					'show_labels' => array(
						'content' => __( 'Show labels.', 'cp' ),
						'default' => 'false',
					),
					'status' => array(
						'content' => __( 'The status of courses to show (uses WordPress status).', 'cp' ),
						'default' => __( 'published', 'cp' ),
					),
					'student_msg' => array(
						'content' => __( 'Messge displayed when student is not enroled to any course.', 'cp' ),
						'default' => sprintf(
							__( 'You are not enrolled in any courses. %s', 'cp' ),
							htmlentities( __( '<a href="%s">See available courses.</a>', 'cp' ) )
						),
					),
					'student' => array(
						'content' => __( 'The student id to list courses for a specific student. Can also specify multiple students using commas. (e.g. student="1,2,3").', 'cp' ),
						'default' => 'empty',
						'description' => __( 'If both student and instructor are specified, only the student will be used.', 'cp' ),
						'suggested_label' => array(
							'content' => __( 'Label before suggested courses.', 'cp' ),
							'default' => __( 'Suggested courses', 'cp' ),
						),
						'suggested_msg' => array(
							'content' => __( 'Message will be show when student is not enrolled to any course, but we have some suggested courses.', 'cp' ),
							'default' => sprintf(
								__( 'You are not enrolled in any courses.<br />Here are a few you might like, or %s' ),
								htmlentities( __( ' <a href="%s">see all available courses.</a>', 'cp' ) )
							),
						),
					),
					'show_withdraw_link' => array(
						'content' => __( 'Allow to show withdraw link, but it work only when is a student and status is set on "incomplete".', 'cp' ),
						'default' => 'false',
					),
				),
			),
			'examples' => array(
				'[course_list]',
				'[course_list instructor="2"]',
				'[course_list student="3"]',
				'[course_list instructor="2,4,5"]',
				'[course_list show="dates,cost" limit="5"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_featured.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_featured() {
		$data = array(
			'shortcode' => 'course_featured',
			'content' => __( 'Shows a featured course.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'featured_title' => array(
						'content' => __( 'The title to display for the featured course.', 'cp' ),
						'default' => __( 'Featured Course', 'cp' ),
					),
					'button_title' => array(
						'content' => __( 'Text to display on the call to action button.', 'cp' ),
						'default' => __( 'Find out more.', 'cp' ),
					),
					'media_type' => array(
						'content' => __( 'Media type to use for featured course. See [course_media].', 'cp' ),
						'default' => 'default',
						'options' => array( 'image', 'video', 'thumbnail', 'default' ),
					),
					'media_priority' => array(
						'content' => __( 'Media priority to use for featured course. See [course_media].', 'cp' ),
						'default' => __( 'video', 'cp' ),
						'options' => array( 'image', 'video', 'default' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_featured course_id="42"]',
				'[course_featured course_id="11" featured_title="The best we got!"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_structure.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_structure() {
		$data = array(
			'shortcode' => 'course_structure',
			'content' => __( 'Displays a tree view of the course structure.', 'cp' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'If outside of the WordPress loop.', 'cp' ),
					),
				),
				'optional' => array(
					'deep' => array(
						'content' => __( 'Show all course modules.', 'cp' ),
						'default' => 'false',
					),
					'free_class' => array(
						'content' => __( 'Additional CSS classes for styling free preview items.', 'cp' ),
						'default' => __( 'free', 'cp' ),
					),
					'free_show' => array(
						'content' => __( 'Show for FREE preview items.', 'cp' ),
						'default' => 'true',
					),
					'free_text' => array(
						'content' => __( 'Text to show for FREE preview items.', 'cp' ),
						'default' => __( 'Preview', 'cp' ),
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol to use after the label.', 'cp' ),
						'default' => ':',
					),
					'label' => array(
						'content' => __( 'Label to display for the output.', 'cp' ),
						'default' => __( 'Course Structure', 'cp' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML tag (without brackets) to use for the individual labels.', 'cp' ),
						'default' => 'strong',
					),
					'show_divider' => array(
						'content' => __( 'Show divider between major items in the tree, "yes" or "no".', 'cp' ),
						'default' => __( 'yes', 'cp' ),
						'options' => array( 'yes', 'no' ),
					),
					'show_label' => array(
						'content' => __( 'Show label text as tree heading, "yes" or "no".', 'cp' ),
						'default' => __( 'no', 'cp' ),
						'options' => array( 'yes', 'no' ),
					),
					'show_title' => array(
						'content' => __( 'Show course title in structure, "yes" or "no".', 'cp' ),
						'default' => __( '"no"', 'cp' ),
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_structure]',
				'[course_structure course_id="42" free_text="'.__( 'Gratis!', 'cp' ).'" show_title="no"]',
				'[course_structure show_title="no" label="'.__( 'Curriculum', 'cp' ).'"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_signup.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_signup() {
		$data = array(
			'shortcode' => 'course_signup',
			'content' => __( 'Shows a custom login or signup page for front-end user registration and login.', 'cp' ),
		   'note' => __( 'This is already part of CoursePress and can be set in CoursePress Settings. Links to default pages can be found in Appearance > Menus > CoursePress.', 'cp' ),
			'parameters' => array(
				'optional' => array(
					'failed_login_class' => array(
						'content' => __( 'CSS class to use for invalid login.', 'cp' ),
						'default' => 'red',
					),
					'failed_login_text' => array(
						'content' => __( 'Text to display when user doesn’t authenticate.', 'cp' ),
						'default' => __( 'Invalid login.', 'cp' ),
					),
					'login_tag' => array(
						'content' => __( 'Title tag wrapper.', 'cp' ),
						'default' => 'h3',
					),
					'login_title' => array(
						'content' => __( 'Title to use for Login section.', 'cp' ),
						'default' => __( 'Login', 'cp' ),
					),
					'login_url' => array(
						'content' => __( 'URL to redirect to when clicking on "Already have an Account?".', 'cp' ),
						'default' => __( 'Plugin defaults.', 'cp' ),
					),
					'logout_url' => array(
						'content' => __( 'URL to redirect to when user logs out.', 'cp' ),
						'default' => __( 'Plugin defaults.', 'cp' ),
					),
					'page' => array(
						'content' => __( 'Page parameter if not set CoursePress try to use "page" variable from $_REQUEST.', 'cp' ),
						'default' => 'empty',
					),
					'signup_tag' => array(
						'content' => __( 'Title tag wrapper.', 'cp' ),
						'default' => 'h3',
					),
					'signup_title' => array(
						'content' => __( 'Title to use for Signup section.', 'cp' ),
						'default' => __( 'Signup', 'cp' ),
					),
					'signup_url' => array(
						'content' => __( 'URL to redirect to when clicking on "Don\'t have an account? Go to Signup!"', 'cp' ),
						'default' => 'empty',
					),
				),
			),
			'examples' => array(
				'[course_signup]',
				'[course_signup signup_title="&lt;h1&gt;'.__( 'Signup Now', 'cp' ).'&lt;/h1&gt;"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for courses_student_dashboard.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_courses_student_dashboard() {
		$data = array(
			'shortcode' => 'courses_student_dashboard',
			'content' => __( 'Loads the student dashboard template.', 'cp' ),
			'examples' => array(
				'[courses_student_dashboard]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for courses_student_settings.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_courses_student_settings() {
		$data = array(
			'shortcode' => 'courses_student_settings',
			'content' => __( 'Loads the student settings template.', 'cp' ),
			'examples' => array(
				'[courses_student_settings]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce Box.
	 *
	 * @since 2.0.2
	 * @access private
	 *
	 * @return string
	 */
	private static function _prepare_box( $data ) {
		$content = sprintf( '<span class="cp-shortcode-code">[%s]</span><br />', $data['shortcode'] );
		$content .= sprintf( '<p class="description">%s</p>', $data['content'] );
		if ( isset( $data['note'] ) ) {
			$content .= sprintf( __( '<p class="description"><strong>Note</strong>: %s</p>', 'cp' ), $data['note'] );
		}
		if ( isset( $data['parameters'] ) ) {
			$kinds = array(
				'required' => __( 'Required Attributes:', 'cp' ),
				'optional' => __( 'Optional Attributes:', 'cp' ),
			);
			if ( isset( $data['add_class_to_optional'] ) && $data['add_class_to_optional'] ) {
				if ( ! isset( $data['parameters'] ) ) {
					$data['parameters'] = array();
				}
				if ( ! isset( $data['parameters']['optional'] ) ) {
					$data['parameters']['optional'] = array();
				}
				$data['parameters']['optional']['class'] = array( 'content' => __( 'Additional CSS classes to use for further styling.', 'cp' ) );
			}
			foreach ( $kinds as $kind => $kind_label ) {
				if ( isset( $data['parameters'][ $kind ] ) && is_array( $data['parameters'][ $kind ] ) && ! empty( $data['parameters'][ $kind ] ) ) {
					$content .= sprintf( '<div class="cp-shortcode-attributes cp-shortcode-attributes-%s">', esc_attr( $kind ) );
					$content .= sprintf( '<p class="cp-shortcode-subheading">%s</p>', esc_html( $kind_label ) );
					$content .= '<ul class="cp-shortcode-options">';
					$attributes = $data['parameters'][ $kind ];
					ksort( $attributes );
					foreach ( $attributes as $attr_name => $attr_data ) {
                        $content .= sprintf( '<li class="shortcode-%s">', esc_attr( $attr_name ) );
                        $content .= '<p>';
						$content .= sprintf( '<span>%s</span>', esc_html( $attr_name ) );
						if ( isset( $attr_data['content'] ) ) {
							$content .= ' &ndash; ';
							$content .= $attr_data['content'];
						}
						if ( isset( $attr_data['options'] ) ) {
							$content .= '<p class="options">';
							$options = '<em>'.implode( '</em>, <em>', $attr_data['options'] ).'</em>';
                            $content .= sprintf( __( 'Options: %s.', 'cp' ), $options );
                            $content .= '</p>';
							if ( isset( $attr_data['options_description'] ) && ! empty( $attr_data['options_description'] ) ) {
								$content .= sprintf( '<p class="description">%s</p>', esc_html( $attr_data['options_description'] ) );
							}
                        }
                        $content .= '</p>';
						if ( isset( $attr_data['default'] ) && ! empty( $attr_data['default'] ) ) {
							$content .= '<p class="default">';
							switch ( $attr_data['default'] ) {
								case ':':
									$content .= __( 'Default is colon (<em>:</em>)', 'cp' );
								break;
								case ',':
									$content .= __( 'Default is coma (<em>,</em>)', 'cp' );
								break;
								case 'WP':
										$content .= sprintf( __( 'Default: <em>%s</em>.', 'cp' ), __( 'WordPress Settings' ) );
								break;
								case 'empty':
										$content .= sprintf( __( 'Default: <em>%s</em>.', 'cp' ), __( 'empty string' ) );
								break;
								default:
									if ( is_numeric( $attr_data['default'] ) ) {
										$content .= sprintf( __( 'Default: <em>%s</em>.', 'cp' ), htmlentities( $attr_data['default'] ) );
									} else {
										$content .= sprintf( __( 'Default: "<em>%s</em>"', 'cp' ), htmlentities( $attr_data['default'] ) );
									}
                            }
                            $content .= '</p>';
                        }
                            if ( isset( $attr_data['description'] ) ) {
                                $content .= sprintf( '<p class="description">%s</p>', $attr_data['description'] );
                            }
							$content .= '</li>';
					}
						$content .= '</ul>';
						$content .= '</div>';

				}
			}
		} else {
			$content .= wpautop( __( 'This shortcode has no parameters.', 'cp' ) );
		}
		if ( isset( $data['examples'] ) && is_array( $data['examples'] ) && ! empty( $data['examples'] ) ) {
			$content .= '<div class="cp-shortcode-examples">';
			$content .= sprintf( '<p class="cp-shortcode-subheading">%s</p>', esc_attr__( 'Examples:', 'cp' ) );
			$content .= '<code>';
			$content .= join( $data['examples'], '<br />' );
			$content .= '</code>';
					$content .= '</div>';
		}
			return $content;
	}
}
