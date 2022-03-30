<?php

/**
 * Class STEDB_Forms_WordPress_Public
 * public functions for the STEdb Forms
 */
if ( ! class_exists( 'STEDB_Forms_WordPress_Public' ) ) {

	class STEDB_Forms_WordPress_Public extends STEDB_Forms_WordPress_Base {

		/**
		 * constructor
		 */
		public function __construct() {
			parent::__construct();

			/** load styles */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

			/** load scripts */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			/** shortcode */
			add_shortcode( 'stedb_form', array( $this, 'stedb_form_shortcode' ) );

			/** callback */
			add_action( 'wp_head', array( $this, 'api_request_callback' ) );

			/** admin post request handle */
			add_action( 'admin_post_stedb_forms_save_form', array( $this, 'admin_post_save_form' ) );
			add_action( 'admin_post_nopriv_stedb_forms_save_form', array( $this, 'admin_post_save_form' ) );
		}

		/**
		 * load styles in admin
		 */
		public function enqueue_styles() {

			/** magnific popup */
			wp_register_style( 'magnific-popup', STEDB_FORMS_DIR_URL . '/assets/libs/magnific-popup/css/jquery-magnific-popup.min.css', array(), '1.1.0' );
			wp_enqueue_style( 'magnific-popup' );

			/** stedb forms */
			wp_register_style( 'stedb-forms', STEDB_FORMS_DIR_URL . '/assets/css/stedb-forms.css', array(), STEDB_FORMS_VERSION );
			wp_enqueue_style( 'stedb-forms' );
		}

		/**
		 * load scripts in admin
		 */
		public function enqueue_scripts() {

			/** magnific popup js */
			wp_register_script( 'magnific-popup', STEDB_FORMS_DIR_URL . '/assets/libs/magnific-popup/js/jquery-magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );
			wp_enqueue_script( 'magnific-popup' );

			/** stedb forms */
			wp_register_script( 'stedb-forms', STEDB_FORMS_DIR_URL . '/assets/js/stedb-forms.js', array( 'jquery' ), STEDB_FORMS_VERSION, true );
			wp_enqueue_script( 'stedb-forms' );
		}

		/**
		 * stedb form shortcode
		 *
		 * @param $atts
		 *
		 * @return string
		 */
		public function stedb_form_shortcode( $atts ) {
			global $wpdb;
			global $stedb_form;

			$a = shortcode_atts( array(
				'id'      => '',
				'list_id' => '',
			), $atts );

			/** get form builder content data */
			$form_builder_content_json = $wpdb->get_var( $wpdb->prepare( 'SELECT form_builder_content FROM ' . $wpdb->prefix . 'stedb_forms_lists WHERE id = %d AND list_id = %d LIMIT 1', absint( $a['id'] ), absint( $a['list_id'] ) ) );
			$form_builder_content      = json_decode( $form_builder_content_json, true );

			$form_classes = array( 'stedb-form' );

			/** form type - popup default */
			if ( 'popup-default' == $form_builder_content['form_type'] ) {
				$form_classes = array_merge( $form_classes, array(
					'stedb-forms-type--popup',
					'stedb-forms-type--popup-default',
					'mfp-with-anim',
					'mfp-hide',
				) );
			}

			/** form type - popup sidebar */
			if ( 'popup-sidebar' == $form_builder_content['form_type'] ) {
				$form_classes = array_merge( $form_classes, array(
					'stedb-forms-type--popup',
					'stedb-forms-type--popup-sidebar-right',
					'mfp-with-anim',
					'mfp-hide',
				) );
			}

			$output = sprintf( '<form id="stedb-form-%d" class="%s" action="%s" method="post">', $a['id'], implode( ' ', $form_classes ), admin_url( 'admin-post.php' ) );

			/**
			 * post request callback
			 * notices
			 */
			if ( isset( $_GET['form_notice'] ) ) {
				if ( ( isset( $_GET['form_id'] ) && $_GET['form_id'] == $a['id'] ) || ! isset( $_GET['form_id'] ) ) {
					$output .= $this->post_request_get_notices();
				}
			}

			/**
			 * api request callback
			 * infos and errors
			 */
			if ( isset( $stedb_form['is_api_request_callback'] ) ) {
				if ( ( isset( $stedb_form['form_id'] ) && $stedb_form['form_id'] == $a['id'] ) || ! isset( $stedb_form['form_id'] ) ) {
					$output .= $this->api_request_get_infos();
					$output .= $this->api_request_get_errors();
				}
			}

			$output .= '<input type="hidden" name="action" value="stedb_forms_save_form">';
			$output .= wp_nonce_field( 'stedb_forms_save_form', '_wpnonce', true, false );

			$output .= '<input type="hidden" name="sm_provider_name" value="">';

			$output .= sprintf( '<input type="hidden" name="id" value="%s">', $a['id'] );
			$output .= sprintf( '<input type="hidden" name="list_id" value="%s">', $a['list_id'] );

			/** get form builder content html */
			if ( ! empty( $form_builder_content ) ) {
				if ( ! empty( $form_builder_content['rows'] ) ) {
					foreach ( $form_builder_content['rows'] as $row_id => $row ) {
						$fields = '';

						if ( ! empty( $row['fields'] ) ) {
							foreach ( $row['fields'] as $field_id ) {

								/** get field from wpdb */
								$field = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'stedb_forms_fields WHERE id = %d LIMIT 1', absint( $field_id ) ), ARRAY_A );

								/** format values */
								$field['values'] = json_decode( $field['values'], true );

								/** field */
								if ( isset( $this->form_element_fields[ $field['type'] ] ) ) {
									$form_element_field = $this->form_element_fields[ $field['type'] ];

									$fields .= $form_element_field->render( $field_id, $field );
								}

								/** social field */
								if ( isset( $this->form_element_social_fields[ $field['type'] ] ) ) {
									$form_element_social_field = $this->form_element_social_fields[ $field['type'] ];

									$fields .= $form_element_social_field->render( $field_id, $field );
								}
							}
						}

						$output .= $this->form_element_row->render( $row_id, $fields );
					}
				}
			}

			$output .= '</form>';

			return $output;
		}

		/**
		 * Admin Post
		 * save form
		 */
		public function admin_post_save_form() {
			global $wpdb;

			/**
			 * get page url
			 * @var string $page_url
			 */
			$page_url = esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) );

			/**
			 * set post
			 * from $_POST
			 */
			$post = array();

			if ( isset( $_POST ) ) {
				$post = wp_unslash( $_POST );
			}

			/** check post request */
			if ( empty( $post ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( array(
					'form_notice' => 'post_request_empty',
				), $page_url ) ) );
				exit;
			}

			/** check nonce */
			if ( empty( $_POST['_wpnonce'] ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( array(
					'form_notice' => 'nonce_field_empty',
					'form_id'     => $post['id'],
				), $page_url ) ) );
				exit;
			}

			/** verify nonce */
			if ( ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'stedb_forms_save_form' ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( array(
					'form_notice' => 'nonce_field_incorrect',
					'form_id'     => $post['id'],
				), $page_url ) ) );
				exit;
			}

			/** get social links data */
			$social_links_json = $wpdb->get_var( $wpdb->prepare( 'SELECT social_links FROM ' . $wpdb->prefix . 'stedb_forms_lists WHERE id = %d AND list_id = %d LIMIT 1', absint( $post['id'] ), absint( $post['list_id'] ) ) );
			$social_links      = json_decode( $social_links_json, true );

			/** get social link */
			$social_link = $social_links[ $post['sm_provider_name'] ];

			/** error empty social link */
			if ( empty( $social_link ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( array(
					'form_notice' => 'social_link_empty',
					'form_id'     => $post['id'],
				), $page_url ) ) );
				exit;
			}

			$form_hash = time() . '|' . md5( json_encode( $post ) );
			$form_id   = absint( $post['id'] );
			$token     = sanitize_text_field( $post['_wpnonce'] );

			/** insert temp entry */
			$wpdb->insert( $wpdb->prefix . 'stedb_forms_entries_temp', array(
				'form_hash' => $form_hash,
				'form_id'   => $form_id,
				'token'     => $token,
				'data'      => json_encode( $post['stedb_form'] ),
			), array( '%s', '%d', '%s', '%s' ) );

			/** set u */
			if ( is_ssl() ) {
				$u = esc_url_raw( 'https://' . wp_unslash( $_SERVER['HTTP_HOST'] ) . $post['_wp_http_referer'] );
			} else {
				$u = esc_url_raw( 'http://' . wp_unslash( $_SERVER['HTTP_HOST'] ) . $post['_wp_http_referer'] );
			}

			/** redirect */
			wp_redirect( $social_link . '&' . http_build_query( array(
					'form_hash' => $form_hash,
					'token'     => $token,
					'u'         => $u,
				) ) );
			exit;
		}

		/**
		 * API
		 * request callback
		 */
		public function api_request_callback() {
			global $wpdb;
			global $stedb_form;

			if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
				return;
			}

			$request_args = wp_unslash( $_REQUEST );

			/** check action */
			if ( ! isset( $request_args['action'] ) ) {
				return;
			}

			/** check action */
			if ( 'stedb_forms_api_callback' != $request_args['action'] ) {
				return;
			}

			/** set is callback */
			$stedb_form['is_api_request_callback'] = true;

			/**
			 * Errors
			 * @var WP_Error $errors
			 */
			$stedb_form['errors'] = new WP_Error();

			/**
			 * Infos
			 */
			$stedb_form['infos'] = array();

			/** get account */
			$stedb_forms_account = get_option( 'stedb_forms_account', array() );

			/** check stedb account array */
			if ( empty( $stedb_forms_account ) ) {
				$stedb_form['errors']->add( 'empty_stedb_account', __( 'The stedb account is not set.', 'stedb-forms' ) );

				return;
			}

			/** check stedb account array */
			if ( ! is_array( $stedb_forms_account ) ) {
				$stedb_form['errors']->add( 'not_valid_stedb_account', __( 'The stedb account is invalid.', 'stedb-forms' ) );

				return;
			}

			/** check token */
			if ( ! isset( $request_args['token'] ) ) {
				$stedb_form['errors']->add( 'not_isset_token', __( 'The token is missing.', 'stedb-forms' ) );

				return;
			}

			/** check email */
			if ( ! isset( $request_args['email'] ) ) {
				$stedb_form['errors']->add( 'not_isset_email', __( 'The email is missing.', 'stedb-forms' ) );

				return;
			}

			$email = sanitize_email( $request_args['email'] );

			/** validate email */
			if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				$stedb_form['errors']->add( 'not_valid_email', __( 'The email is invalid.', 'stedb-forms' ) );

				return;
			}

			/** check form_hash */
			if ( ! isset( $request_args['form_hash'] ) ) {
				$stedb_form['errors']->add( 'not_isset_form_hash', __( 'The form hash is missing.', 'stedb-forms' ) );

				return;
			}

			$stedb_forms_entry_temp = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'stedb_forms_entries_temp WHERE form_hash = %s LIMIT 1', $request_args['form_hash'] ), ARRAY_A );

			/** validate token */
			if ( $request_args['token'] !== hash_hmac( 'SHA256', $stedb_forms_entry_temp['token'] . "|" . $request_args['email'], $stedb_forms_account['secret'] ) ) {
				$stedb_form['errors']->add( 'not_valid_token', __( 'The token is invalid.', 'stedb-forms' ) );

				return;
			}

			/** check form id in session */
			if ( ! isset( $stedb_forms_entry_temp['form_id'] ) ) {
				$stedb_form['errors']->add( 'not_isset_session_form_id', __( 'The form id is missing.', 'stedb-forms' ) );

				return;
			}

			/** set form id */
			$stedb_form['form_id'] = $stedb_forms_entry_temp['form_id'];

			/** get list (ie form) from wpdb */
			$stedb_forms_list = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'stedb_forms_lists WHERE id = %d LIMIT 1', absint( $stedb_forms_entry_temp['form_id'] ) ), ARRAY_A );

			/** validate form */
			if ( empty( $stedb_forms_list ) ) {
				$stedb_form['errors']->add( 'empty_wpdb_list', __( 'The form is invalid.', 'stedb-forms' ) );

				return;
			}

			/** save entries in wpdb */
			$max_entry_id = 1;

			/** max id from wpdb */
			if ( $get_wpdb_max_entry_id = $wpdb->get_var( $wpdb->prepare( 'SELECT MAX(entry_id) FROM ' . $wpdb->prefix . 'stedb_forms_entries WHERE form_id = %d', $stedb_forms_list['id'] ) ) ) {
				$max_entry_id = intval( $get_wpdb_max_entry_id ) + 1;
			}

			/** save email */
			$wpdb->insert( $wpdb->prefix . 'stedb_forms_entries', array(
				'form_id'       => absint( $stedb_forms_list['id'] ),
				'entry_id'      => absint( $max_entry_id ),
				'email'         => $email,
				'custom_fields' => $stedb_forms_entry_temp['data'],
				'date'          => date( 'Y-m-d' ),
			), array( '%d', '%d', '%s', '%s', '%s' ) );

			/** delete temp */
			$wpdb->delete( $wpdb->prefix . 'stedb_forms_entries_temp', array( 'form_hash' => $request_args['form_hash'] ) );

			/** save email in api */
			$email_data = array(
				'email'         => $email,
				'list_id'       => $stedb_forms_list['list_id'],
				'custom_fields' => wp_json_encode( array_combine( explode( ',', $stedb_forms_list['list_id'] ), json_decode( $stedb_forms_entry_temp['data'], true ) ) ),
			);

			/** STEdb API */
			$stedb_forms_api_client = new STEDB_Forms_Api_Client();

			/** create email in api */
			if ( $stedb_forms_api_client->create_emails( $email_data ) ) {

				/** success */
				$stedb_form['infos'][] = __( 'Thanks for contacting us! We will get in touch with you shortly.', 'stedb-forms' );
			} else {

				/** error */
				$stedb_form['errors']->add( 'error_api_request_create_email', __( 'There is a problem with the API request.', 'stedb-forms' ) );
			}
		}

		/**
		 * get request infos
		 * @return false|string|void
		 */
		public function api_request_get_infos() {
			global $stedb_form;

			if ( ! isset( $stedb_form['infos'] ) ) {
				return;
			}

			/** @var array $infos */
			$infos = $stedb_form['infos'];

			ob_start();

			if ( ! empty( $infos ) ) :
				foreach ( $infos as $info ) :
					?>
                    <div class="stedb-forms-alert stedb-forms-alert-success" role="alert">
                        <strong><?php _e( 'Info', 'stedb-forms' ); ?>!</strong>
						<?php echo $info; ?>
                    </div>
				<?php
				endforeach;
			endif;

			$output = ob_get_clean();

			return $output;
		}

		/**
		 * get request errors
		 * @return false|string|void
		 */
		public function api_request_get_errors() {
			global $stedb_form;

			if ( ! isset( $stedb_form['errors'] ) ) {
				return;
			}

			if ( ! is_a( $stedb_form['errors'], 'WP_Error' ) ) {
				return;
			}

			/** @var WP_Error $errors */
			$errors = $stedb_form['errors'];

			ob_start();

			if ( ! empty( $errors->get_error_codes() ) ) :
				foreach ( $errors->get_error_messages() as $error_message ) :
					?>
                    <div class="stedb-forms-alert stedb-forms-alert-danger" role="alert">
                        <strong><?php _e( 'Error', 'stedb-forms' ); ?>!</strong>
						<?php echo $error_message; ?>
                    </div>
				<?php
				endforeach;
			endif;

			$output = ob_get_clean();

			return $output;
		}

		/**
		 * get request notices
		 * @return false|string|void
		 */
		public function post_request_get_notices() {

			if ( ! isset( $_GET['form_notice'] ) ) {
				return;
			}

			ob_start();

			$form_notices = array(
				'post_request_empty'    => __( 'The post request is empty.', 'stedb-forms' ),
				'nonce_field_empty'     => __( 'The nonce field is empty.', 'stedb-forms' ),
				'nonce_field_incorrect' => __( 'That nonce field was incorrect.', 'stedb-forms' ),
				'social_link_empty'     => __( 'The social provider link is empty.', 'stedb-forms' ),
			);

			$form_notice_id = sanitize_key( $_GET['form_notice'] );

			?>
            <div class="stedb-forms-alert stedb-forms-alert-danger" role="alert">
                <strong><?php _e( 'Error', 'stedb-forms' ); ?>!</strong>
				<?php echo esc_html( $form_notices[ $form_notice_id ] ); ?>
            </div>
			<?php

			$output = ob_get_clean();

			return $output;
		}
	}
}
