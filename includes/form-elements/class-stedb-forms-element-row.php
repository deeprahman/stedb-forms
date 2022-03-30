<?php

/**
 * Class STEDB_Forms_Element_Row
 * row
 */
if ( ! class_exists( 'STEDB_Forms_Element_Row' ) ) {

	class STEDB_Forms_Element_Row {

		/**
		 * get name
		 * @return string|void
		 */
		public function get_name() {
			return esc_html__( 'Row', 'stedb-forms' );
		}

		/**
		 * @return string
		 */
		public function get_icon() {
			return 'row-add';
		}

		/**
		 * render field for admin
		 */
		public function admin_render() {
			?>
            <script type="text/html" id="tmpl-stedb-forms-admin-element-row-html">

                <div class="stedb-form-element-row" data-id="{{-data.rowId}}">
                    <i class="stedb-form-element-row-remove stedb-icon-close"></i>

                    <input type="hidden" name="stedb_form_elements[rows][{{-data.rowId}}][fields]" value="">

                    <div class="stedb-form-child-elements-container stedb-form-element-fields-container">
                        {{=data.fields}}
                    </div>
                </div>

            </script>
			<?php
		}

		/**
		 * render row
		 *
		 * @param $id
		 * @param $fields
		 *
		 * @return false|string
		 */
		public function render( $id, $fields ) {
			ob_start();
			?>

            <div class="stedb-forms-row stedb-forms-row-<?php echo esc_attr( $id ); ?>">

				<?php echo $fields; ?>
            </div>

			<?php
			$html = ob_get_clean();

			return $html;
		}
	}
}