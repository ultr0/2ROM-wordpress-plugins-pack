<?php

class CoursePress_Helper_UI_Module {

	const OUTPUT_TEXT = 'text';
	const OUTPUT_CHAT = 'chat';
	const OUTPUT_IMAGE = 'image';
	const OUTPUT_VIDEO = 'video';
	const OUTPUT_AUDIO = 'audio';
	const OUTPUT_DOWNLOAD = 'download';
	const OUTPUT_ZIPPED_OBJECT = 'zipped';
	const OUTPUT_SECTION = 'section';
	const OUTPUT_DISCUSSION = 'discussion';
	const INPUT_MULTIPLE_CHOICE = 'input-checkbox';
	const INPUT_SINGLE_CHOICE = 'input-radio';
	const INPUT_SELECT_CHOICE = 'input-select';
	const INPUT_SHORT_TEXT = 'input-text';
	const INPUT_LONG_TEXT = 'input-textarea';
	const INPUT_UPLOAD = 'input-upload';
	const INPUT_ADVANCED = 'input-mixed';
	const INPUT_QUIZ = 'input-quiz';
	const INPUT_FORM = 'input-form';

	public static function render( $data = 'TODO' ) {
		$content = '';

		return $content;
	}


	public static function render_test( $data = 'TODO' ) {

		$types = self::get_types();
		$labels = self::get_labels();

		$data = array(

			'id' => 12345,
			'title' => 'This is the title',
			'type' => self::INPUT_SHORT_TEXT,
			'duration' => '1:00',
			'show_title' => 1,
			'mandatory' => 1,
			'assessable' => 1,
			'minimum_grade' => 100,
			'allow_retries' => 1,
			'retry_attempts' => 10,
			'content' => 'Explain the meaning of life, the universe and everything else.',
			'order' => 0,
			'components' => array(
				// array(
				// 'id' => '12345_1',
				// 'order' => 0,
				// 'items' => array(
				// array(
				// 'text' => 'this is for later',
				// 'selected' => 0,
				// 'item_placeholder' => 'not always needed',
				// 'placeholder' => 'this goes on UI side',
				// 'button_primary' => 'Button 1',
				// 'button_secondary' => 'Button 2',
				// 'button_other' => 'Button 3',
				// 'answer' => 'Not always used',
				// 'keywords' => 'this, could, be, useful'
				// ) // item
				// ) // items
				// ),
				array(
					// 'id' => '12345_1',
					'order' => 0,
					'items' => array(
						array(
							'text' => 'this is for later',
							'selected' => 0,
							'item_placeholder' => 'not always needed',
							'placeholder' => 'this goes on UI side',
							'button_primary' => 'Button 1',
							'button_secondary' => 'Button 2',
							'button_other' => 'Button 3',
							'answer' => 'Not always used',
							'keywords' => 'this, could, be, useful',
						), // item
					),// items
				),
				array(
					'order' => 1,
					'items' => array(
						array(),

					),
				),

				// component
			),// components
		);

		$data = json_decode( self::get_template( self::INPUT_SHORT_TEXT ) );
		$data = CoursePress_Helper_Utility::object_to_array( $data );

		// If its not an accepted type there is no point trying to render it
		if ( ! in_array( $data['type'], array_keys( $types ) ) ) {
			return '';
		}

		$module_mode = $types[ $data['type'] ]['mode'];

		$content = '
			<div class="module-holder module-type-' . esc_attr( $data['type'] ) . ' mode-' . esc_attr( $module_mode ) . '" data-id="' . esc_attr( $data['id'] ) . '">
				<h3 class="module-title"><span class="label">' . esc_html( $data['title'] ) . '</span><span class="module-type">' . esc_html( $types[ $data['type'] ]['title'] ) . '</span></h3>';

		// Display the body of the module?
		if ( ( isset( $types[ $data['type'] ]['body'] ) && 'hidden' !== $types[ $data['type'] ]['body'] ) || ! isset( $types[ $data['type'] ]['body'] ) ) {
			$content .= '
				<div class="module-header">
					<label class="module-title"><span class="label">' . $labels['module_title'] . '</span>
						<span class="description">' . $labels['module_title_desc'] . '</span>
						<input type="text" name="title" value="' . $data['title'] . '" />
					</label>
					<label class="module-duration"><span class="label">' . $labels['module_duration'] . '</span>
						<input type="text" name="meta_duration" value="' . $data['duration'] . '" />
					</label>';

			// Show Title
			$content .= '
					<label class="module-show-title">
						<input type="checkbox" name="meta_show_title" value="1" ' . checked( $data['show_title'], 1, false ) . ' />
						<span class="label">' . $labels['module_show_title'] . '</span>
						<span class="description">' . $labels['module_show_title_desc'] . '</span>
					</label>';

			// Only for user inputs
			if ( 'input' === $module_mode ) {

				// required
				$content .= '
					<label class="module-mandatory">
						<input type="checkbox" name="meta_mandatory" value="1" ' . checked( $data['mandatory'], 1, false ) . ' />
						<span class="label">' . $labels['module_mandatory'] . '</span>
						<span class="description">' . $labels['module_mandatory_desc'] . '</span>
					</label>';

				// Assessable
				$content .= '
					<label class="module-assessable">
						<input type="checkbox" name="meta_assessable" value="1" ' . checked( $data['assessable'], 1, false ) . ' />
						<span class="label">' . $labels['module_assessable'] . '</span>
						<span class="description">' . $labels['module_assessable_desc'] . '</span>
					</label>';

				// Minimum Grade
				$content .= '
					<label class="module-minimum_grade">
						<span class="label">' . $labels['module_minimum_grade'] . '</span>
						<input type="text" name="meta_minimum_grade" value="' . $data['minimum_grade'] . '" />
						<span class="description">' . $labels['module_minimum_grade_desc'] . '</span>
					</label>';

				// Allow Retries
				$content .= '
					<label class="module-allow-retries">
						<input type="checkbox" name="meta_allow_retries" value="1" ' . checked( $data['allow_retries'], 1, false ) . ' />
						<span class="label">' . $labels['module_allow_retries'] . '</span>
						<input type="text" name="meta_retry_attempts" value="' . $data['retry_attempts'] . '" />
						<span class="description">' . $labels['module_allow_retries_desc'] . '</span>
					</label>';

			}

			// Excerpt
			if ( ( isset( $types[ $data['type'] ]['excerpt'] ) && 'hidden' !== $types[ $data['type'] ]['excerpt'] ) || ! isset( $types[ $data['type'] ]['excerpt'] ) ) {
				$args = array(
					'textarea_name' => 'module_excerpt_' . $data['id'],
				);

				ob_start();
				wp_editor( $data['content'], 'moduleExcerpt' . $data['id'], $args );
				$content_editor = ob_get_clean();

				$content_label = 'input' === $module_mode ? $labels['module_question'] : $labels['module_content'];
				$content .= '
					<label class="module-excerpt">
						<span class="label">' . $content_label . '</span>
						' . $content_editor . '
					</label>';
			}

			// Now it gets tricky...
			$content .= '
				</div>
				<div class="module-components">
					' . self::render_components( $data ) . '
				</div>';

		}
		$content .= '
			</div>
		';

		return $content;
	}

	public static function get_types() {

		$input_types = self::get_input_types();
		$output_types = self::get_output_types();

		return apply_filters( 'coursepress_module_types', CoursePress_Helper_Utility::merge_distinct( $input_types, $output_types ) );

	}

	public static function get_input_types() {

		$types = array(
			self::INPUT_MULTIPLE_CHOICE => array(
				'title' => __( 'Multiple Choice', 'cp' ),
				'mode' => 'input',
				'icon' => 'default',
				'dashicon' => 'list-view',
			),
			self::INPUT_SINGLE_CHOICE => array(
				'title' => __( 'Single Choice', 'cp' ),
				'mode' => 'input',
				'icon' => 'default',
				'dashicon' => 'editor-ul',
			),
			self::INPUT_SELECT_CHOICE => array(
				'title' => __( 'Selectable', 'cp' ),
				'mode' => 'input',
				'icon' => 'default',
				'dashicon' => 'menu',
			),
			self::INPUT_SHORT_TEXT => array(
				'title' => __( 'Short Answer', 'cp' ),
				'mode' => 'input',
				'icon' => 'default',
				'dashicon' => 'editor-textcolor',
			),
			self::INPUT_LONG_TEXT => array(
				'title' => __( 'Long Answer', 'cp' ),
				'mode' => 'input',
				'icon' => 'default',
				'dashicon' => 'editor-alignleft',
			),
			self::INPUT_UPLOAD => array(
				'title' => __( 'File Upload', 'cp' ),
				'mode' => 'input',
				'icon' => 'default',
				'dashicon' => 'upload',
			),
			self::INPUT_QUIZ => array(
				'title' => __( 'Quiz', 'cp' ),
				'mode' => 'input',
				'icon' => 'default',
				'dashicon' => 'forms',
			),
			self::INPUT_FORM => array(
				'title' => __( 'Form', 'cp' ),
				'mode' => 'input',
				'icon' => 'default',
				'dashicon' => 'feedback',
			),

			// self::INPUT_ADVANCED => array(
			// 'title' => __( 'Advanced Action', 'cp' ),
			// 'mode' => 'input',
			// 'icon' => 'default',
			// ),
		);

		return apply_filters( 'coursepress_module_input_types', $types );
	}

	public static function get_output_types() {

		$types = array(
			self::OUTPUT_TEXT => array(
				'title' => __( 'Text', 'cp' ),
				'mode' => 'output',
				'icon' => 'default',
				'dashicon' => 'media-text',
			),
			self::OUTPUT_CHAT => array(
				'title' => __( 'Chat', 'cp' ),
				'mode' => 'output',
				'icon' => 'default',
			),
			self::OUTPUT_IMAGE => array(
				'title' => __( 'Image', 'cp' ),
				'mode' => 'output',
				'excerpt' => 'hidden',
				'icon' => 'default',
				'dashicon' => 'format-image',
			),
			self::OUTPUT_VIDEO => array(
				'title' => __( 'Video', 'cp' ),
				'mode' => 'output',
				'excerpt' => 'hidden',
				'icon' => 'default',
				'dashicon' => 'video-alt3',
			),
			self::OUTPUT_AUDIO => array(
				'title' => __( 'Audio', 'cp' ),
				'mode' => 'output',
				'excerpt' => 'hidden',
				'icon' => 'default',
				'dashicon' => 'format-audio',
			),
			self::OUTPUT_DOWNLOAD => array(
				'title' => __( 'File Download', 'cp' ),
				'mode' => 'output',
			// 'excerpt' => 'hidden',
				'icon' => 'default',
				'dashicon' => 'media-text',
			),
			self::OUTPUT_ZIPPED_OBJECT => array(
				'title' => __( 'Zipped Object', 'cp' ),
				'mode' => 'output',
			// 'excerpt' => 'hidden',
				'icon' => 'default',
				'dashicon' => 'media-archive',
			),
			self::OUTPUT_DISCUSSION => array(
				'title' => __( 'Discussion', 'cp' ),
				'mode' => 'output',
				'icon' => 'default',
				'dashicon' => 'testimonial',
			),
		);

		return apply_filters( 'coursepress_module_output_types', $types );
	}

	public static function get_labels() {

		return apply_filters( 'coursepress_module_labels', array(
			'module_title' => __( 'Title', 'cp' ),
			'module_title_desc' => __( 'The title is used to identify this module element and is useful for assessment.', 'cp' ),
			'module_duration' => __( 'Student Completion Time Limit ([hh:]mm:ss)', 'cp' ),
			'module_show_title' => __( 'Show Title', 'cp' ),
			'module_show_title_desc' => __( 'Show title in unit view', 'cp' ),
			'module_mandatory' => __( 'Required', 'cp' ),
			'module_mandatory_desc' => __( 'A response is required', 'cp' ),
			'module_assessable' => __( 'Assessable', 'cp' ),
			'module_assessable_desc' => __( 'This is a gradable item', 'cp' ),
			'module_minimum_grade' => __( 'Minimum', 'cp' ),
			'module_minimum_grade_desc' => __( 'Minimum grade (%) required to pass', 'cp' ),
			'module_minimum_grade' => __( 'Minimum Grade', 'cp' ),
			'module_instructor_assessable' => __( 'Require instructor assessment.', 'cp' ),
			'module_instructor_assessable_desc' => __( 'Check this box to allow instructor to provide final grading assessment.', 'cp' ),
			'module_allow_retries' => __( 'Allow Retries', 'cp' ),
			'module_allow_retries_desc' => __( 'Allow and set amount of retries (0 unlimited)', 'cp' ),
			'module_use_timer' => __( 'Use Timer', 'cp' ),
			'module_use_timer_desc' => __( 'Use duration as time restriction', 'cp' ),
			'module_question' => __( 'Question/Task', 'cp' ),
			'module_question_desc' => __( 'The question or instructions to complete this task.', 'cp' ),
			'module_content' => __( 'Content', 'cp' ),
			'module_content_desc' => __( 'Content that will display on the unit page.', 'cp' ),
			'module_answer' => __( 'Answer', 'cp' ),
			'module_answer_desc' => __( 'Set the correct answer', 'cp' ),
			'module_answer_add_new' => __( 'Add', 'cp' ),
			'module_delete' => __( 'Delete Module', 'cp' ),
			'module_start_quiz' => __( 'Start Quiz', 'cp' ),
		) );

	}

	// Could've done this inline, but this is needed for JS translation
	public static function get_template( $component = false ) {

		$components = array(
			self::OUTPUT_TEXT => '
				{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::OUTPUT_TEXT . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"content": "",
					"editor_height": "500",
					"order": "0",
					"components": []
				}
			',
			self::OUTPUT_CHAT => '{
				"id": "0",
				"title": "' . __( 'Untitled', 'cp' ) . '",
				"duration": "0:00",
				"type": "' . self::OUTPUT_CHAT . '",
				"show_title": "1",
				"mandatory": "0",
				"assessable": "0",
				"minimum_grade": "100",
				"allow_retries": "1",
				"retry_attempts": "0",
				"content": "",
				"editor_height": "500",
				"order": "0",
				"components": []
			}',
			self::OUTPUT_IMAGE => '
				{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::OUTPUT_IMAGE . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"content": "",
					"editor_height": "200",
					"order": "0",
					"components": [
						{
							"label": "' . __( 'Image Source', 'cp' ) . '",
							"description": "' . __( 'Enter a URL or Browse for an image', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "media-browser",
									"name": "meta_image_url",
									"media_type": "image",
									"container_class": "wide",
									"class": "widemedium",
									"button_text": "' . __( 'Browse', 'cp' ) . '",
									"placeholder": "' . __( 'Add Media URL or Browse for Media', 'cp' ) . '"
								}
							]
						},
						{
							"label": "' . __( 'Image Caption', 'cp' ) . '",
							"description": "' . __( 'Hide, show and customise the image caption.', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "media-caption-settings",
									"class": "component-media-caption wide",
									"label": "' . __( 'Show Caption', 'cp' ) . '",
									"enable_name": "meta_show_media_caption",
									"option_name": "meta_caption_field",
									"input_name": "meta_caption_custom_text",
									"option_class": "caption-source",
									"no_caption": "' . __( 'Media has no caption', 'cp' ) . '",
									"media_type": "image",
									"option_labels": {
										"media": "' . __( 'Media Caption', 'cp' ) . '",
										"custom": "' . __( 'Custom Caption', 'cp' ) . '"
									},
									"selected": "0",
									"placeholder": "' . __( 'Please enter a custom caption here.', 'cp' ) . '"
								}
							]
						}
					]
				}
			',
			self::OUTPUT_VIDEO => '
				{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::OUTPUT_VIDEO . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"content": "",
					"editor_height": "200",
					"order": "0",
					"components": [
						{
							"label": "' . __( 'Video Source', 'cp' ) . '",
							"description": "' . __( 'You can enter a Youtube or Vimeo link (oEmbed support is required). Alternatively you can Browse for a file - supported video extensions (mp4, m4v, webm, ogv, wmv, flv)', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "media-browser",
									"name": "meta_video_url",
									"media_type": "video",
									"container_class": "wide",
									"class": "widemedium",
									"button_text": "' . __( 'Browse', 'cp' ) . '",
									"placeholder": "' . __( 'Add Media URL or Browse for Media', 'cp' ) . '"
								}
							]
						},
						{
							"label": "' . __( 'Video Caption', 'cp' ) . '",
							"description": "' . __( 'Hide, show and customise the video caption.', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "media-caption-settings",
									"class": "component-media-caption wide",
									"label": "' . __( 'Show Caption', 'cp' ) . '",
									"enable_name": "meta_show_media_caption",
									"option_name": "meta_caption_field",
									"input_name": "meta_caption_custom_text",
									"option_class": "caption-source",
									"no_caption": "' . __( 'Media has no caption', 'cp' ) . '",
									"media_type": "video",
									"option_labels": {
										"media": "' . __( 'Media Caption', 'cp' ) . '",
										"custom": "' . __( 'Custom Caption', 'cp' ) . '"
									},
									"selected": "0",
									"placeholder": "' . __( 'Please enter a custom caption here.', 'cp' ) . '"
								}
							]
						},
						{
							"label": "' . __( 'Related Videos', 'cp' ) . '",
							"description": "' . __( 'Hide related videos for some video services (e.g. YouTube). Services like Vimeo sets this per video.', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "checkbox",
									"label": "' . __( 'Hide related videos', 'cp' ) . '",
									"name": "meta_hide_related_media"
								}
							]
						}
					]
				}
			',
			self::OUTPUT_AUDIO => '
				{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::OUTPUT_AUDIO . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"content": "",
					"editor_height": "200",
					"order": "0",
					"components": [
						{
							"label": "' . __( 'Audio Source', 'cp' ) . '",
							"description": "' . __( 'Enter a URL or Browse for an audio file. Supported audio extensions (mp3, ogg, wma, m4a, wav)', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "media-browser",
									"name": "meta_audio_url",
									"media_type": "audio",
									"container_class": "wide",
									"class": "widemedium",
									"button_text": "' . __( 'Browse', 'cp' ) . '",
									"placeholder": "' . __( 'Add Media URL or Browse for Media', 'cp' ) . '"
								}
							]
						},
						{
							"label": "' . __( 'Audio Playback', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "checkbox",
									"label": "' . __( 'Loop audio', 'cp' ) . '",
									"name": "meta_loop"
								},
								{
									"type": "checkbox",
									"label": "' . __( 'Autoplay audio', 'cp' ) . '",
									"name": "meta_autoplay"
								}
							]
						}
					]
				}
			',
			self::OUTPUT_DOWNLOAD => '
				{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::OUTPUT_DOWNLOAD . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"content": "",
					"editor_height": "200",
					"order": "0",
					"components": [
						{
							"label": "' . __( 'Download Source', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "media-browser",
									"name": "meta_file_url",
									"media_type": "any",
									"container_class": "wide",
									"class": "widemedium",
									"button_text": "' . __( 'Browse', 'cp' ) . '",
									"placeholder": "' . __( 'Add File URL or Browse for File to download', 'cp' ) . '"
								}
							]
						},
						{
							"label": "' . __( 'Link Text', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "text-input",
									"name": "meta_link_text",
									"class": "medium"
								}
							]
						}
					]
				}
			',
			self::OUTPUT_ZIPPED_OBJECT => '
				{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::OUTPUT_ZIPPED_OBJECT . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"content": "",
					"editor_height": "200",
					"order": "0",
					"components": [
						{
							"label": "' . __( 'Object Source', 'cp' ) . '",
							"description": "' . __( 'Browse for the zip file that contains your resources.', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "media-browser",
									"name": "meta_zip_url",
									"media_type": "file",
									"container_class": "wide",
									"class": "widemedium",
									"button_text": "' . __( 'Browse', 'cp' ) . '",
									"placeholder": "' . __( 'Browse for zipped file', 'cp' ) . '"
								}
							]
						},
						{
							"label": "' . __( 'Primary File', 'cp' ) . '",
							"description": "' . __( 'This is the file of the object that will be loaded first.', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "text-input",
									"name": "meta_primary_file",
									"class": "medium",
									"placeholder": "' . __( 'e.g. index.html', 'cp' ) . '"
								}
							]
						},
						{
							"label": "' . __( 'Link Text', 'cp' ) . '",
							"description": "' . __( 'This is the text of the link that will open your primary file.', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "text-input",
									"name": "meta_link_text",
									"class": "medium"
								}
							]
						}
					]
				}
			',
			self::OUTPUT_SECTION => '
			{
				"id": "0",
				"title": "' . __( 'Untitled', 'cp' ) . '",
				"duration": "0:00",
				"type": "' . self::OUTPUT_SECTION . '",
				"show_title": "1",
				"mandatory": "0",
				"assessable": "0",
				"minimum_grade": "100",
				"allow_retries": "1",
				"retry_attempts": "0",
				"content": "",
				"editor_height": "200",
				"order": "0",
				"components": []
			}
			',
			self::OUTPUT_DISCUSSION => '
				{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::OUTPUT_DISCUSSION . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"content": "",
					"editor_height": "500",
					"order": "0",
					"components": []
				}
			',
			self::INPUT_MULTIPLE_CHOICE => '
			{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::INPUT_MULTIPLE_CHOICE . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"content": "",
					"editor_height": "200",
					"order": "0",
					"components": [
						{
							"label": "' . __( 'Answer', 'cp' ) . '",
							"description": "' . __( 'Add checkboxes next to the correct answers', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "checkbox-select",
									"class": "component-checkbox-answer wide",
									"name": "meta_answers",
									"answers": [
										"' . __( 'Answer A', 'cp' ) . '",
										"' . __( 'Answer B', 'cp' ) . '"
									],
									"selected": [
										"0"
									]
								}
							]
						}
					]
				}
			',
			self::INPUT_SINGLE_CHOICE => '
				{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::INPUT_SINGLE_CHOICE . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"content": "",
					"editor_height": "200",
					"order": "0",
					"components": [
						{
							"label": "' . __( 'Answer', 'cp' ) . '",
							"description": "' . __( 'Select the correct answer', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "radio-select",
									"class": "component-radio-answer wide",
									"name": "meta_answers",
									"answers": [
										"' . __( 'Answer A', 'cp' ) . '",
										"' . __( 'Answer B', 'cp' ) . '"
									],
									"selected": "0"
								}
							]
						}
					]
				}
			',
			self::INPUT_SELECT_CHOICE => '
						{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::INPUT_SELECT_CHOICE . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"content": "",
					"editor_height": "200",
					"order": "0",
					"components": [
						{
							"label": "' . __( 'Answer', 'cp' ) . '",
							"description": "' . __( 'Select the correct answer', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "select-select",
									"class": "component-select-answer wide",
									"name": "meta_answers",
									"answers": [
										"' . __( 'Answer A', 'cp' ) . '",
										"' . __( 'Answer B', 'cp' ) . '"
									],
									"selected": "0"
								}
							]
						}
					]
				}
			',
			self::INPUT_SHORT_TEXT => '
				{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::INPUT_SHORT_TEXT . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"content": "",
					"editor_height": "200",
					"order": "0",
					"components": [
						{
							"label": "' . __( 'Placeholder Text', 'cp' ) . '",
							"description": "' . __( 'Placeholder text to put inside the textbox (additional information)', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "text-input",
									"class": "component-placeholder-text wide",
									"name": "meta_placeholder_text"
								}
							]
						}
					]
				}
			',
			self::INPUT_LONG_TEXT => '
				{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::INPUT_LONG_TEXT . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"content": "",
					"editor_height": "200",
					"order": "0",
					"components": [
						{
							"label": "' . __( 'Placeholder Text', 'cp' ) . '",
							"description": "' . __( 'Placeholder text to put inside the textbox (additional information)', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"type": "text-input",
									"class": "component-placeholder-text wide",
									"name": "meta_placeholder_text"
								}
							]
						}
					]
				}
			',
			self::INPUT_UPLOAD => '
				{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::INPUT_UPLOAD . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"content": "",
					"editor_height": "200",
					"order": "0",
					"components": []
				}
			',
			self::INPUT_QUIZ => '
				{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::INPUT_QUIZ . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"use_timer": "1",
					"content": "",
					"editor_height": "200",
					"order": "0",
					"components": [
						{
							"label": "' . __( 'Questions', 'cp' ) . '",
							"description": "' . __( 'Add all the questions for your quiz here', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"title": "' . __( 'Multiple Choice', 'cp' ) .'",
									"type": "action",
									"class": "input-element module-input-checkbox quiz-action-button multiple wide",
									"action": "multiple",
									"dashicon": "list-view"
								},
								{
									"title": "' . __( 'Single Choice', 'cp' ) .'",
									"type": "action",
									"class": "input-element module-input-radio quiz-action-button single wide",
									"action": "single",
									"dashicon": "editor-ul"
								}
							]
						},
						{
							"items": [
								{
									"type": "quiz"
								}
							]
						}
					]
				}
			',
			self::INPUT_ADVANCED => '

			',
			self::INPUT_FORM => '
				{
					"id": "0",
					"title": "' . __( 'Untitled', 'cp' ) . '",
					"duration": "0:00",
					"type": "' . self::INPUT_FORM . '",
					"show_title": "1",
					"mandatory": "0",
					"assessable": "0",
					"minimum_grade": "100",
					"allow_retries": "1",
					"retry_attempts": "0",
					"use_timer": "1",
					"content": "",
					"editor_height": "200",
					"order": "0",
					"components": [
						{
							"label": "' . __( 'Form elements', 'cp' ) . '",
							"description": "' . __( 'Add all the elements for your form here', 'cp' ) . '",
							"class": "wide",
							"items": [
								{
									"title": "' . __( 'Short Answer', 'cp' ) .'",
									"type": "action",
									"class": "input-element form-action-button short wide",
									"action": "short",
									"dashicon": "editor-textcolor"
								},
								{
									"title": "' . __( 'Long Answer', 'cp' ) .'",
									"type": "action",
									"class": "input-element form-action-button long wide",
									"action": "long",
									"dashicon": "editor-alignleft"
								},
								{
									"title": "' . __( 'Selectable', 'cp' ) . '",
									"type": "action",
									"class": "input-element form-action-button selectable wide",
									"action": "selectable",
									"dashicon": "menu"
								}
							]
						},
						{
							"items": [
								{
									"type": "form"
								}
							]
						}
					]
				}
			'
		);

		if ( $component && true !== $component ) {
			return $components[ $component ];
		} else {
			return $components;
		}

	}

	private static function render_components( $data ) {

		$types = self::get_types();
		$labels = self::get_labels();

		$content = '';

		$module_mode = $types[ $data['type'] ]['mode'];

		$components = is_array( $data['components'] ) ? $data['components'] : array();

		// if ( 'input' === $module_mode && ! empty( $components ) ) {
		// $content .= '
		// <label class="module-question-label">
		// <span class="label">' . $labels['module_answer'] . '</span>
		// <span class="description">' . $labels['module_answer_desc'] . '</span>
		// </label>';
		// }
		// Now deal with each component
		foreach ( $components as $key => $component ) {

			$component_id = isset( $component['id'] ) ? $component['id'] : 0;
			$content .= '
				<div class="module-component module-component-' . $key . '">
					<label data-key="label">
						<span class="label">' . $component['label'] . '</span>
						<span class="description">' . $component['description'] . '</span>
				';
			foreach ( (array) $component['items'] as $idx => $item ) {

				switch ( $item['type'] ) {

					case 'text-input':
						$attr = isset( $item['name'] ) ? ' name="' . $item['name'] . '"' : '';
						$attr .= isset( $item['class'] ) ? ' class="' . $item['class'] . '"' : '';
						$content .= '<input type="text"' . $attr . ' />';
						break;

					case 'text':
						$attr = isset( $item['name'] ) ? ' name="' . $item['name'] . '"' : '';
						$attr .= isset( $item['class'] ) ? ' class="' . $item['class'] . '"' : '';
						$text = isset( $item['text'] ) ? $item['text'] : '';
						$content .= '<span' . $attr . '>' . $text . '</span>';
						break;

				}
			}

			$content .= '
					</label>
				</div>
			';

		}

		return $content;
	}


	// Items for QUIZ
	// Only using Multiple Choice for now
	// "items": [
	// {
	// "type": "action",
	// "class": "quiz-action-button single wide",
	// "action": "single"
	// },
	// {
	// "type": "action",
	// "class": "quiz-action-button multiple wide",
	// "action": "multiple"
	// },
	// {
	// "type": "action",
	// "class": "quiz-action-button short wide",
	// "action": "short"
	// },
	// {
	// "type": "action",
	// "class": "quiz-action-button long wide",
	// "action": "long"
	// }
	// ]
	// Just leaving this here for when you need to know how to deal with the JS
	// meta_items = $( '.module-holder [name^="meta_"]').serializeArray()
	// CoursePress.utility.fix_checkboxes( meta_items, '.module-holder', "0" )
}
