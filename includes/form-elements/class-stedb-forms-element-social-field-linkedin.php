<?php

/**
 * Class STEDB_Forms_Element_Social_Field_Linkedin
 * linkedin social integration
 */
if ( ! class_exists( 'STEDB_Forms_Element_Social_Field_Linkedin' ) ) {

	class STEDB_Forms_Element_Social_Field_Linkedin extends STEDB_Forms_Element_Field_Base {

		/**
		 * get type
		 * @return string
		 */
		public function get_type() {
			return 'social_linkedin';
		}

		/**
		 * get name
		 * @return string|void
		 */
		public function get_name() {
			return esc_html__( 'LinkedIn', 'stedb-forms' );
		}

		/**
		 * get sm provider name
		 * @return string|void
		 */
		public function get_sm_provider_name() {
			return 'linkedin';
		}

		/**
		 * @return string
		 */
		public function get_icon() {
			return 'linkedin';
		}

		/**
		 * render social for admin
		 */
		public function admin_render() {
			?>
            <script type="text/html" id="tmpl-stedb-forms-admin-element-field-social_linkedin-html">

                <div class="stedb-form-element-field" data-id="{{-data.fieldId}}" data-type="{{-data.fieldType}}">
					<?php echo $this->get_render_remove_button_html(); ?>

					<?php echo $this->get_render_type_input_html(); ?>

                    <div class="stedb-form-element-social-field" data-type="{{-data.fieldType}}">
                        <div class="stedb-form-element-social-field-icon">
                            <img src="<?php echo esc_url( STEDB_FORMS_DIR_URL . '/assets/img/icon-social-linkedin.png' ); ?>"
                                 alt="<?php echo esc_attr( $this->get_name() ); ?>">
                        </div>
                        <div class="stedb-form-element-social-field-text">
							<?php esc_html_e( 'Submit via LinkedIn', 'stedb-forms' ); ?>
                        </div>
                    </div>
                </div>
            </script>
			<?php
		}

		/**
		 * render social
		 *
		 * @param $id
		 * @param $data
		 *
		 * @return false|string
		 */
		public function render( $id, $data ) {
			ob_start();
			?>

            <a class="stedb-forms-social-field stedb-forms-social-field-<?php echo esc_attr( $this->get_type() ); ?> stedb-forms-social-field-<?php echo esc_attr( $id ); ?>"
               data-sm_provider_name="<?php echo esc_attr( $this->get_sm_provider_name() ); ?>">
                <div class="stedb-forms-social-field-icon">
                    <img src="<?php echo esc_url( STEDB_FORMS_DIR_URL . '/assets/img/icon-social-linkedin.png' ); ?>"
                         alt="<?php echo esc_attr( $this->get_name() ); ?>">
                </div>
                <div class="stedb-forms-social-field-text">
					<?php esc_html_e( 'Submit via LinkedIn', 'stedb-forms' ); ?>
                </div>
            </a>

			<?php
			$html = ob_get_clean();

			return $html;
		}
	}
}