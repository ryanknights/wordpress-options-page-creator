<?php

	if (!defined( 'ABSPATH'))
	{
		exit();
	}
	
	function create_tabbed_options_page ()
	{
		$optionsPage = new tmsOptionsCreator(array(

			'title' => 'Tabbed Options',
			'key'   => 'tabbed_options',
			'tabs'  => array(
				'tab-one'   => 'Tab One',
				'tab-two'   => 'Tab Two'
			)
		));

		$optionsPage->addSection(array(
			'id' => 'section_one',
			'title' => 'Section One',
			'content' => 'This is section one',
			'tab'     => 'tab-one'
		));

			$optionsPage->addField(array(
				'id'      => 'test_field',
				'title'   => 'Text Input',
				'type'    => 'text',
				'section' => 'section_one',
				'tab'     => 'tab-one'
			));	

			$optionsPage->addField(array(
				'id'      => 'test_field_2',
				'title'   => 'Text Input 2',
				'type'    => 'text',
				'section' => 'section_one',
				'tab'     => 'tab-one'
			));

			$optionsPage->addField(array(
				'id'      => 'test_field_3',
				'title'   => 'TextArea',
				'type'    => 'textarea',
				'section' => 'section_one',
				'tab'     => 'tab-one'
			));		

		$optionsPage->addSection(array(
			'id' => 'section_one',
			'title' => 'Section One',
			'content' => 'This is section one',
			'tab'     => 'tab-two'
		));

			$optionsPage->addField(array(
				'id'      => 'test_field',
				'title'   => 'Text Input',
				'type'    => 'text',
				'section' => 'section_one',
				'tab'     => 'tab-two'
			));	

			$optionsPage->addField(array(
				'id'      => 'test_field_2',
				'title'   => 'Text Input 2',
				'type'    => 'text',
				'section' => 'section_one',
				'tab'     => 'tab-two'
			));	

			$optionsPage->addField(array(
				'id'      => 'test_field_3',
				'title'   => 'Select Box',
				'type'    => 'select',
				'values'  => array('one', 'two', 'three'),
				'section' => 'section_one',
				'tab'     => 'tab-two'
			));					


	}

	add_action('admin_menu', 'create_tabbed_options_page');

	function create_static_options_page ()
	{	
		$optionsPage = new tmsOptionsCreator(array(

			'title' => 'Static Options',
			'key'   => 'static_options'
		));

			$optionsPage->addSection(array(

				'id' => 'section_one',
				'title' => 'Section One',
				'content' => 'This is section one'
			));

				$optionsPage->addField(array(
					'id'      => 'test_field',
					'title'   => 'Text Input',
					'type'    => 'text',
					'section' => 'section_one',
				));

			$optionsPage->addSection(array(

				'id' => 'section_two',
				'title' => 'Section Two',
				'content' => 'This is section two'
			));

				$optionsPage->addField(array(
					'id'      => 'test_field',
					'title'   => 'Text Input',
					'type'    => 'text',
					'section' => 'section_two',
				));

				$optionsPage->addField(array(
					'id'      => 'test_field_2',
					'title'   => 'TextArea',
					'type'    => 'textarea',
					'section' => 'section_two',
				));	

				$optionsPage->addField(array(
					'id'      => 'test_field_3',
					'title'   => 'Select Box',
					'type'    => 'select',
					'values'  => array('one', 'two', 'three'),
					'section' => 'section_two'
				));

				$optionsPage->addField(array(
					'id'      => 'animals',
					'title'   => 'Animals',
					'type'    => 'checkbox',
					'values'  => array(
						'Dog'  => 'dog',
						'Cat'  => 'cat',
						'Frog' => 'frog'
					),
					'section' => 'section_two'
				));	

				$optionsPage->addField(array(
					'id'      => 'enabled',
					'title'   => 'Enabled?',
					'type'    => 'checkbox',
					'values'  => array(
						'Am i enabled' => true,
					),
					'section' => 'section_two'
				));

				$optionsPage->addField(array(
					'id'      => 'age',
					'title'   => 'Age',
					'type'    => 'radio',
					'values'  => array(
						'10-20' => '10_20',
						'20-30' => '20_30',
						'30_40' => '30_40'
					),
					'section' => 'section_two'
				));

				$optionsPage->addField(array(
					'id'       => 'intro_text',
					'title'    => 'Introduction Text',
					'type'     => 'tinymce',
					'settings' => array('textarea_rows' => 10),
					'section'  => 'section_two'
				));																			
	}

	add_action('admin_menu', 'create_static_options_page');

?>
