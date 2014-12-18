<?php
	
	class OptionsCreator
	{	
		private $title;
		private $key;
		private $tabs;
		private $sectionDescriptions;

		/**
		 * Sets up options & adds the menu page
		 *
		 * @param array $options Initialisation options
		 * @return void		 
		 */

		public function __construct ($options = array())
		{
			$defaults = array(

				'title' => 'Website Options Page',
				'key'   => 'website_options_page',
				'tabs'  => false
			);

			$options = array_merge($defaults, $options);

			$this->title = $options['title'];
			$this->key   = $options['key'];
			$this->tabs  = $options['tabs'];

			$this->enqueueAssets();
			$this->addMenuPage();
		}

		/**
		 * Enqueues JS/CSS for the creator
		 *
		 * @return void
		 */

		public function enqueueAssets ()
		{	
			wp_enqueue_media();
			wp_enqueue_script('options_page_creator', plugin_dir_url(__FILE__) . 'assets/js/master.js', 'jquery', '1.0', true );
		}

		/**
		 * Adds new top level menu page via wordpress 'add_menu_page'
		 *
		 * @return void
		 */

		public function addMenuPage ()
		{
			add_menu_page(
				$this->title, // Page Title
				$this->title, // Menu Title
				'manage_options', // Capability needed to view page
				$this->key, // Menu Slug
				array(&$this, 'createMenuPage') // Callback to display page
			);
		}

		/**
		 * Used as a callback for addMenuPage, displays the new menu page
		 *
		 * @return void
		 */

		public function createMenuPage ()
		{	
			if (!$this->tabs)
			{
				$displaySection = $this->key;
			}
			else
			{
				$displaySection = (isset($_GET['tab'])) ? $_GET['tab'] : $this->returnFirstTab();
			}

			?>	
				<div class="wrap">
					<h2><?=$this->title;?></h2>
					<? $this->displayTabs();?>
					<form method="post" action="options.php">
						<? settings_fields($displaySection); ?>
						<? do_settings_sections($displaySection); ?>
						<? submit_button(); ?>
					</form>
				</div>
			<?
		}

		/**
		 * Returns the key for the first tab
		 *
		 * @return key for the first tab in $this->tabs array		 
		 */

		private function returnFirstTab ()
		{
			return array_search(reset($this->tabs), $this->tabs);
		}

		/**
		 * Returns where to add the new section/field too
		 *
		 * @param string $tab The key of the tab
		 * @return The menu page id if no tabs otherwise the passed tab		 
		 */

		private function addTo ($options)
		{
			return (!$this->tabs) ? $this->key : $options['tab'];
		}

		/**
		 * Displays HTML for tabs
		 *
		 * @return Returns false early if no tabs are found	 
		 */

		public function displayTabs ()
		{
			if (!$this->tabs)
			{
				return false;
			}

			$currentTab = (isset($_GET['tab'])) ? $_GET['tab'] : $this->returnFirstTab();

			echo '<h2 class="nav-tab-wrapper">';

			foreach ($this->tabs as $tabKey => $tabValue)
			{
		        $active = $currentTab === $tabKey ? 'nav-tab-active' : '';

		        echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->key . '&tab=' . $tabKey . '">' . $tabValue . '</a>';				
			}

			 echo '</h2>';
		}

		/**
		 * Adds new section of options
		 *
		 * @param array $options Options to be used to add new section
		 * @return void	 
		 */

		public function addSection ($options = array())
		{	
			$defaults = array(

				'id'      => 'rlk_website_options_section',
				'title'   => 'Website Options Section',
				'content' => 'This is Website Options Section'
			);

			$options = array_merge($defaults, $options);

			$this->sectionDescriptions[$options['id']] = $options['content'];

			add_settings_section(
				$options['id'], // Section id
				$options['title'], // Section title
				array(&$this, 'displaySection'), // Callback to display section content
				$this->addTo($options) // page/tab to display new section on
			);

			register_setting( // Register new section
				$this->addTo($options), // Page/Tab we are displaying
				$this->addTo($options), // We are saving an array so just register the page/tab id,
				array(&$this, 'sanitize_callback')
			);
		}

		/**
		 * Validates array of values when option page is saved, currently does nothing!
		 *
		 * @param array $values Values passed to validation
		 * @return $values Returns values back to Wordpress	 
		 */

		public function sanitize_callback ($values)
		{
			return $values;
		}

		/**
		 * Used as a callback for addSection, displays new section html
		 *
		 * @param array $args Arguments passed from the add_settings_section
		 * @return void	 
		 */

		public function displaySection ($args)
		{
			echo $this->sectionDescriptions[$args['id']];
		}

		/**
		 * Adds new option field
		 *
		 * @param array $options Options to be used to add new field
		 * @return void	 
		 */

		public function addField ($options)
		{
			switch ($options['type'])
			{
				case 'text':
					$callback = array(&$this, 'createTextInput');
					break;
				case 'textarea':
					$callback = array(&$this, 'createTextArea');
					break;
				case 'select':
					$callback = array(&$this, 'createSelectBox');
					break;
				case 'checkbox':
					$callback = array(&$this, 'createCheckbox');
					break;
				case 'radio':
					$callback = array(&$this, 'createRadio');
					break;
				case 'tinymce':
					$callback = array(&$this, 'createTinyMCE');
					break;
				case 'multiselect':
					$callback = array(&$this, 'createMultiSelect');
					break;
				case 'image':
					$callback = array(&$this, 'createImageUpload');
					break;
			}

			add_settings_field(
				$options['id'], // Field id, 
				$options['title'], // Field title
				$callback, // Callback to display the new field
				$this->addTo($options), // Page/Tab to add new field too
				$options['section'], // Section to add new field too
				array($this->getFieldAtts($options), $options) // Arguments to pass to display callback
			);
		}

		/**
		 * Used as a callback for addSection, displays new section html
		 *
		 * @param array $args Arguments passed from the add_settings_section
		 * @return void	 
		 */

		 private function getFieldAtts ($options)
		 {	
		 	$index  = (!$this->tabs) ? $this->key : $options['tab'];
		 	$values = get_option($index); 

		 	return array(

				'id'    => $options['id'],
				'name'  => $index . '['.$options['section'].']' .'['.$options['id'].']',
				'value' => $values[$options['section']][$options['id']]	 		
		 	);
		 }		

		/**
		 * Displays HTML for a text input
		 *
		 * @param array $args[0] contains field id, name & value | $args[1] contains all options passed to add_settings_field
		 * @return void	 
		 */

		public function createTextInput ($args)
		{	
			$fieldAtts = $args[0];

			echo '<input type="text" id="'.$fieldAtts['id'].'" name="'.$fieldAtts['name'].'" value="'.$fieldAtts['value'].'" />';
		}

		/**
		 * Displays HTML for a textarea
		 *
		 * @param array $args[0] contains field id, name & value | $args[1] contains all options passed to add_settings_field
		 * @return void	 
		 */

		public function createTextArea ($args)
		{	
			$fieldAtts = $args[0];

			echo '<textarea id="'.$fieldAtts['id'].'" name="'.$fieldAtts['name'].'">'.$fieldAtts['value'].'</textarea>';
		}

		/**
		 * Displays HTML for a select box
		 *
		 * @param array $args[0] contains field id, name & value | $args[1] contains all options passed to add_settings_field
		 * @return void	 
		 */

		public function createSelectBox ($args)
		{	
			$fieldAtts = $args[0];
			$options   = $args[1]['values'];

			echo '<select id="'.$fieldAtts['id'].'" name="'.$fieldAtts['name'].'">';

				foreach ($options as $option)
				{
					echo '<option '.(($option === $fieldAtts['value']) ? 'selected ' : '').'value="'.$option.'">'.$option.'</option>';
				}

			echo '</select>';
		}

		/**
		 * Displays HTML for checkboxes
		 *
		 * @param array $args[0] contains field id, name & value | $args[1] contains all options passed to add_settings_field
		 * @return void	 
		 */

		public function createCheckbox ($args)
		{
			$fieldAtts   = $args[0];
			$options     = $args[1]['values'];
			$saveAsArray = (count($options) > 1) ? '[]' : ''; // If there are multiple options save as array

			if (!$fieldAtts['value'] && count($options) > 1) // If no values are found and we have multiple options initialise as array to prevent in_array error
			{
				$fieldAtts['value'] = array();
			}

			foreach ($options as $label => $option)
			{	
				if (count($options) > 1)
				{
					$checked = (in_array($option, $fieldAtts['value'])) ? 'checked' : '';	
				}
				else
				{
					$checked = ($option == $fieldAtts['value']) ? 'checked' : '';
				}

				echo '<label for="'.$fieldAtts['id'].'_'.$option.'">'.$label.'</label>';
				echo '<input '.$checked.' type="checkbox" id="'.$fieldAtts['id'].'_'.$option.'" name="'.$fieldAtts['name'].$saveAsArray.'" value="'.$option.'" />';
			}
		}

		/**
		 * Displays HTML for radio buttons
		 *
		 * @param array $args[0] contains field id, name & value | $args[1] contains all options passed to add_settings_field
		 * @return void	 
		 */

		public function createRadio ($args)
		{
			$fieldAtts = $args[0];
			$options   = $args[1]['values'];

			foreach ($options as $label => $option)
			{
				$checked = ($fieldAtts['value'] == $option) ? 'checked' : '';

				echo '<label for="'.$fieldAtts['id'].'_'.$option.'">'.$label.'</label>';
				echo '<input '.$checked.' type="radio" id="'.$fieldAtts['id'].'_'.$option.'" name="'.$fieldAtts['name'].$saveAsArray.'" value="'.$option.'" />';
			} 
		}

		/**
		 * Displays TinyMCE Editor
		 *
		 * @param array $args[0] contains field id, name & value | $args[1] contains all options passed to add_settings_field
		 * @return void	 
		 */

		 public function createTinyMCE ($args)
		 {
		 	$fieldAtts = $args[0];
		 	$settings  = array_merge($args[1]['settings'], array('textarea_name' => $fieldAtts['name']));

		 	wp_editor($fieldAtts['value'], $fieldAtts['id'], $settings);
		 }

		/**
		 * Displays MultiSelect Box
		 *
		 * @param array $args[0] contains field id, name & value | $args[1] contains all options passed to add_settings_field
		 * @return void	 
		 */

		 public function createMultiSelect ($args)
		 {
		 	$fieldAtts = $args[0];
		 	$options   = $args[1]['values'];

		 	echo '<select id="'.$fieldAtts['id'].'" name="'.$fieldAtts['name'].'[]" multiple>';

		 	foreach ($options as $label => $option)
		 	{
		 		$selected = (in_array($option, $fieldAtts['value'])) ? 'selected' : '';

		 		echo '<option '.$selected.' value="'.$option.'">'.$label.'</option>';
		 	}

		 	echo '</select>';
		 }

		/**
		 * Displays Image Upload
		 *
		 * @param array $args[0] contains field id, name & value | $args[1] contains all options passed to add_settings_field
		 * @return void	 
		 */

		 public function createImageUpload ($args)
		 {
		 	$fieldAtts = $args[0];

		 	echo '<div data-image-parent>';
			 	echo '<input id="'.$fieldAtts['id'].'" type="text" name="'.$fieldAtts['name'].'" value="'.$fieldAtts['value'].'" />';
			 	echo '<button data-image-upload>Upload Image</button>';
			 	echo '<button data-remove-image>Remove Image</button>';
			 	echo '<img width="100" height="100" src="'.$fieldAtts['value'].'" />';
			 echo '</div>';
		 }			 		 
	}
	
?>