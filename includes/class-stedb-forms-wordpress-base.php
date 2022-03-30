<?php

/**
 * Class STEDB_Forms_WordPress_Base
 * base class for the STEdb Forms WordPress
 */
if ( ! class_exists( 'STEDB_Forms_WordPress_Base' ) ) {

	class STEDB_Forms_WordPress_Base {

		/**
		 * form element row
		 * @var STEDB_Forms_Element_Row $form_element_row
		 */
		public $form_element_row;

		/**
		 * form element fields
		 * @var STEDB_Forms_Element_Field_Base[] $form_element_fields
		 */
		public $form_element_fields = array();

		/**
		 * form element social fields
		 * @var STEDB_Forms_Element_Field_Base[] $form_element_social_fields
		 */
		public $form_element_social_fields = array();

		/**
		 * constructor
		 */
		public function __construct() {

			$this->init_form_element_row();
			$this->init_form_element_fields();
			$this->init_form_element_social_fields();
		}

		/**
		 * init
		 * form element row
		 */
		public function init_form_element_row() {

			include_once STEDB_FORMS_DIR_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'form-elements' . DIRECTORY_SEPARATOR . 'class-stedb-forms-element-row.php';

			/** add form element row */
			$this->form_element_row = new STEDB_Forms_Element_Row();
		}

		/**
		 * init
		 * form element fields
		 */
		public function init_form_element_fields() {

			/** include fields with autoloader */
			spl_autoload_register( array( $this, 'form_element_field_autoloader' ) );

			do_action( 'stedb_forms_before_add_form_element_fields', $this );

			/** add form element fields */
			$this->add_form_element_field( 'input_checkbox', new STEDB_Forms_Element_Field_Input_Checkbox() );
			$this->add_form_element_field( 'input_date', new STEDB_Forms_Element_Field_Input_Date() );
			$this->add_form_element_field( 'input_number', new STEDB_Forms_Element_Field_Input_Number() );
			$this->add_form_element_field( 'input_radio', new STEDB_Forms_Element_Field_Input_Radio() );
			$this->add_form_element_field( 'input_text', new STEDB_Forms_Element_Field_Input_Text() );
			$this->add_form_element_field( 'input_url', new STEDB_Forms_Element_Field_Input_Url() );
			$this->add_form_element_field( 'select', new STEDB_Forms_Element_Field_Select() );
			$this->add_form_element_field( 'textarea', new STEDB_Forms_Element_Field_Textarea() );

			do_action( 'stedb_forms_after_add_form_element_fields', $this );
		}

		/**
		 * init
		 * form element socials
		 */
		public function init_form_element_social_fields() {

			/** include social fields with autoloader */
			spl_autoload_register( array( $this, 'form_element_social_autoloader' ) );

			do_action( 'stedb_forms_before_add_form_element_social_fields', $this );

			/** add form element social fields */
			$this->add_form_element_social_field( 'social_yahoo', new STEDB_Forms_Element_Social_Field_Yahoo() );
			$this->add_form_element_social_field( 'social_gmail', new STEDB_Forms_Element_Social_Field_Gmail() );
			$this->add_form_element_social_field( 'social_linkedin', new STEDB_Forms_Element_Social_Field_Linkedin() );

			do_action( 'stedb_forms_after_add_form_element_social_fields', $this );
		}

		/**
		 * form element field autoloader
		 *
		 * @param $class_name
		 */
		public function form_element_field_autoloader( $class_name ) {

			if ( ! preg_match( "/STEDB_Forms_Element_Field_(.*)/", $class_name, $form_field_matches ) ) {
				return;
			}

			$file_name = str_replace( array( '_' ), array( '-' ), strtolower( $class_name ) );
			$file      = STEDB_FORMS_DIR_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'form-elements' . DIRECTORY_SEPARATOR . 'class-' . $file_name . '.php';

			if ( file_exists( $file ) ) {
				/** @noinspection PhpIncludeInspection */
				include_once $file;
			}
		}

		/**
		 * form element social autoloader
		 *
		 * @param $class_name
		 */
		public function form_element_social_autoloader( $class_name ) {

			if ( ! preg_match( "/STEDB_Forms_Element_Social_Field_(.*)/", $class_name, $form_social_matches ) ) {
				return;
			}

			$file_name = str_replace( array( '_' ), array( '-' ), strtolower( $class_name ) );
			$file      = STEDB_FORMS_DIR_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'form-elements' . DIRECTORY_SEPARATOR . 'class-' . $file_name . '.php';

			if ( file_exists( $file ) ) {
				/** @noinspection PhpIncludeInspection */
				include_once $file;
			}
		}

		/**
		 * add form element field
		 *
		 * @param string $type
		 * @param $instance
		 */
		public function add_form_element_field( $type = '', $instance = null ) {
			$this->form_element_fields[ $type ] = $instance;
		}

		/**
		 * add form element social field
		 *
		 * @param string $type
		 * @param $instance
		 */
		public function add_form_element_social_field( $type = '', $instance = null ) {
			$this->form_element_social_fields[ $type ] = $instance;
		}
	}
}