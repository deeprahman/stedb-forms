<?php

/**
 * Class STEDB_Forms_Template_Base
 * base class for the STEdb Forms Template
 */
if ( ! class_exists( 'STEDB_Forms_Template_Base' ) ) {

	abstract class STEDB_Forms_Template_Base {

		abstract public function get_name();

		/**
		 * get icon
		 * @return string
		 */
		public function get_icon() {
			return STEDB_FORMS_DIR_URL . '/assets/img/icon-form-template.png';
		}

		/**
		 * form builder content
		 */
		abstract public function form_builder_content();
	}
}