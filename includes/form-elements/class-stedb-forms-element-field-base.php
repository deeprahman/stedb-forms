<?php

/**
 * Class STEDB_Forms_Element_Field_Base
 * base class for the STEdb Forms Element Field
 */
if ( ! class_exists( 'STEDB_Forms_Element_Field_Base' ) ) {

	abstract class STEDB_Forms_Element_Field_Base {

		abstract public function get_type();

		abstract public function get_name();

		abstract public function get_icon();

		/**
		 * render field for admin form builder
		 */
		abstract public function admin_render();

		/**
		 * render field js for admin form builder
		 */
		public function admin_render_js() {
			// admin js
		}

		/**
		 * get remove button html
		 * @return string
		 */
		public function get_render_remove_button_html() {
			return '<i class="stedb-form-element-field-remove stedb-icon-close"></i>';
		}

		/**
		 * get type input html
		 * @return string
		 */
		public function get_render_type_input_html() {
			return sprintf( '<input type="hidden" name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][type]" value="%s">', esc_attr( $this->get_type() ) );
		}

		/**
		 * render field
		 *
		 * @param $id
		 * @param $data
		 *
		 * @return false|string
		 */
		abstract public function render( $id, $data );

		/**
		 * validate
		 */
		public function validate() {
			// validate field
		}

		/**
		 * sanitize
		 *
		 * @param array $values
		 *
		 * @return array
		 */
		public function sanitize( $values ) {
			// todo: sanitize field value

			return $values;
		}
	}
}