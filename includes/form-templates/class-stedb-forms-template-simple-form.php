<?php

/**
 * Class STEDB_Forms_Template_Simple_Form
 * contact form template
 */
if ( ! class_exists( 'STEDB_Forms_Template_Simple_Form' ) ) {

	class STEDB_Forms_Template_Simple_Form extends STEDB_Forms_Template_Base {

		/**
		 * get name
		 * @return string|void
		 */
		public function get_name() {
			return esc_html__( 'Simple Form', 'stedb-forms' );
		}

		/**
		 * form builder content
		 * @return array[]
		 */
		public function form_builder_content() {
			return array(
				'rows' => array(
					array(
						'fields' => array(
							array(
								'type'   => 'input_text',
								'values' => array(
									'label' => esc_html__( 'First Name', 'stedb-forms' ),
								),
							),
							array(
								'type'   => 'input_text',
								'values' => array(
									'label' => esc_html__( 'Last Name', 'stedb-forms' ),
								),
							),
						),
					),
					array(
						'fields' => array(
							array(
								'type'   => 'textarea',
								'values' => array(
									'label' => esc_html__( 'Message', 'stedb-forms' ),
								),
							),
						),
					),
					array(
						'fields' => array(
							array(
								'type' => 'social_linkedin',
							),
							array(
								'type' => 'social_gmail',
							),
						),
					),
				),
			);
		}
	}
}