<?php

/**
 * Class STEDB_Forms_WordPress_Admin
 * admin functions for the STEdb Forms
 */
if ( ! class_exists( 'STEDB_Forms_WordPress_Admin' ) ) {

	class STEDB_Forms_WordPress_Admin extends STEDB_Forms_WordPress_Base {

		/**
		 * form templates
		 * @var STEDB_Forms_Template_Base[] $form_templates
		 */
		public $form_templates = array();

		/**
		 * constructor
		 */
		public function __construct() {
			parent::__construct();

			$this->init_form_templates();

			/** load styles */
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );

			/** load scripts */
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'form_builder_enqueue_scripts' ), 20 );
			add_action( 'admin_print_footer_scripts', array( $this, 'init_form_builder_js' ), 20 );

			/** load form element templates */
			add_action( 'admin_print_footer_scripts', array( $this, 'print_form_element_templates' ) );

			/** load form element js */
			add_action( 'admin_print_footer_scripts', array( $this, 'print_form_element_js' ) );

			/** admin menu */
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

			add_action( 'admin_menu', array( $this, 'edit_form_admin_menu' ), 1 );
			add_action( 'admin_menu', array( $this, 'edit_form_page_args' ) );
			add_action( 'parent_file', array( $this, 'edit_form_parent_file' ) );
			add_action( 'submenu_file', array( $this, 'edit_form_submenu_file' ) );
			add_action( 'admin_title', array( $this, 'edit_form_admin_title' ) );

			add_action( 'admin_menu', array( $this, 'entries_admin_menu' ), 1 );
			add_action( 'admin_menu', array( $this, 'entries_page_args' ) );
			add_action( 'parent_file', array( $this, 'entries_parent_file' ) );
			add_action( 'submenu_file', array( $this, 'entries_submenu_file' ) );
			add_action( 'admin_title', array( $this, 'entries_admin_title' ) );

			add_action( 'admin_menu', array( $this, 'edit_entry_admin_menu' ), 1 );
			add_action( 'admin_menu', array( $this, 'edit_entry_page_args' ) );
			add_action( 'parent_file', array( $this, 'edit_entry_parent_file' ) );
			add_action( 'submenu_file', array( $this, 'edit_entry_submenu_file' ) );
			add_action( 'admin_title', array( $this, 'edit_entry_admin_title' ) );

			/** option filter */
			add_filter( 'pre_update_option_stedb_forms_account', array(
				$this,
				'update_option_stedb_forms_account',
			), 10, 2 );

			/** check has account */
			if ( $this->has_account() ) {
				add_filter( 'pre_update_option_stedb_forms_account_address', array(
					$this,
					'update_option_stedb_forms_account_address',
				), 10, 2 );
			}

			/** admin post */
			//todo: remove debug
			add_action( 'admin_post_stedb_forms_debug_pure_db', array( $this, 'admin_post_debug_pure_db' ) );

			add_action( 'admin_post_stedb_forms_create_account', array( $this, 'admin_post_create_account' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices_create_account' ) );
			add_action( 'admin_post_stedb_forms_reactivate_account', array( $this, 'admin_post_reactivate_account' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices_reactivate_account' ) );

			add_action( 'admin_post_stedb_forms_add_form', array( $this, 'admin_post_add_form' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices_add_form' ) );
			add_action( 'admin_post_stedb_forms_edit_form', array( $this, 'admin_post_edit_form' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices_edit_form' ) );
			add_action( 'admin_post_stedb_forms_edit_entry', array( $this, 'admin_post_edit_entry' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices_edit_entry' ) );
			add_action( 'admin_post_stedb_forms_add_campaign', array( $this, 'admin_post_add_campaign' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices_add_campaign' ) );

			add_action( 'admin_init', array( $this, 'admin_process_lists_report_bulk_action' ) );

			/** admin ajax */
			add_action( 'wp_ajax_stedb_forms_get_form', array( $this, 'ajax_get_form' ) );
			add_action( 'wp_ajax_stedb_forms_get_forms', array( $this, 'ajax_get_forms' ) );

			add_action( 'wp_ajax_stedb_forms_get_stedb_form_preview', array( $this, 'ajax_get_stedb_form_preview' ) );

			add_action( 'wp_ajax_stedb_forms_get_campaigns_check_from_email', array(
				$this,
				'ajax_get_campaigns_check_from_email',
			) );
			add_action( 'wp_ajax_stedb_forms_get_campaigns_check_from_email_with_code', array(
				$this,
				'ajax_get_campaigns_check_from_email_with_code',
			) );
			add_action( 'wp_ajax_stedb_forms_set_campaigns_check_from_email', array(
				$this,
				'ajax_set_campaigns_check_from_email',
			) );
		}

		/**
		 * init
		 * form templates
		 */
		public function init_form_templates() {

			/** include fields with autoloader */
			spl_autoload_register( array( $this, 'form_template_autoloader' ) );

			do_action( 'stedb_forms_before_add_form_templates', $this );

			/** add form templates */
			$this->add_form_template( 'contact_form', new STEDB_Forms_Template_Contact_Form() );
			$this->add_form_template( 'simple_form', new STEDB_Forms_Template_Simple_Form() );

			do_action( 'stedb_forms_after_add_form_templates', $this );
		}

		/**
		 * form template autoloader
		 *
		 * @param $class_name
		 */
		public function form_template_autoloader( $class_name ) {

			if ( ! preg_match( "/STEDB_Forms_Template_(.*)/", $class_name, $form_field_matches ) ) {
				return;
			}

			$file_name = str_replace( array( '_' ), array( '-' ), strtolower( $class_name ) );
			$file      = STEDB_FORMS_DIR_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'form-templates' . DIRECTORY_SEPARATOR . 'class-' . $file_name . '.php';

			if ( file_exists( $file ) ) {
				/** @noinspection PhpIncludeInspection */
				include_once $file;
			}
		}

		/**
		 * add form template
		 *
		 * @param string $type
		 * @param $instance
		 */
		public function add_form_template( $type = '', $instance = null ) {
			$this->form_templates[ $type ] = $instance;
		}

		/**
		 * is stedb forms page
		 */
		public function is_stedb_forms_page() {

			/** check is admin */
			if ( ! is_admin() ) {
				return false;
			}

			/** check page */
			if ( ! isset( $_REQUEST['page'] ) ) {
				return false;
			}

			$page = sanitize_file_name( wp_unslash( $_REQUEST['page'] ) );

			/** check stedb forms prefix */
			if ( strpos( $page, 'stedb-forms' ) === false ) {
				return false;
			}

			return true;
		}

		/**
		 * is form builder page
		 */
		public function is_form_builder_page() {

			/** check is admin */
			if ( ! is_admin() ) {
				return false;
			}

			/** check page */
			if ( ! isset( $_REQUEST['page'] ) ) {
				return false;
			}

			$page = sanitize_file_name( wp_unslash( $_REQUEST['page'] ) );

			$form_builder_pages = array( 'stedb-forms-add-form.php', 'stedb-forms-edit-form.php' );

			/** check form builder pages */
			if ( ! in_array( $page, $form_builder_pages ) ) {
				return false;
			}

			return true;
		}

		/**
		 * load styles in admin
		 */
		public function enqueue_styles() {

			if ( ! $this->is_stedb_forms_page() ) {
				return;
			}

			/** jquery ui */
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			wp_enqueue_style( 'jquery-ui-sortable' );
			wp_enqueue_style( 'jquery-ui-draggable' );

			/** stedb icons */
			wp_register_style( 'stedb-icons', STEDB_FORMS_DIR_URL . '/assets/css/stedb-icons.css', array(), STEDB_FORMS_VERSION );
			wp_enqueue_style( 'stedb-icons' );

			/** stedb forms admin */
			wp_register_style( 'stedb-forms-jquery-ui', STEDB_FORMS_DIR_URL . '/assets/css/stedb-forms-jquery-ui.css', array(), '1.13.1' );
			wp_enqueue_style( 'stedb-forms-jquery-ui' );

			/** stedb forms admin */
			wp_register_style( 'stedb-forms-admin', STEDB_FORMS_DIR_URL . '/assets/css/stedb-forms-admin.css', array(), STEDB_FORMS_VERSION );
			wp_enqueue_style( 'stedb-forms-admin' );
		}

		/**
		 * load scripts in admin
		 */
		public function enqueue_scripts() {

			if ( ! $this->is_stedb_forms_page() ) {
				return;
			}

			/** canvasjs js */
			wp_register_script( 'canvasjs', STEDB_FORMS_DIR_URL . '/assets/libs/canvasjs/canvasjs.min.js', array(
				'jquery',
			), '3.4.11', true );
			wp_enqueue_script( 'canvasjs' );

			/** stedb forms admin js */
			wp_register_script( 'stedb-forms-admin', STEDB_FORMS_DIR_URL . '/assets/js/stedb-forms-admin.js', array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-datepicker',
				'jquery-ui-dialog',
			), STEDB_FORMS_VERSION, true );
			wp_enqueue_script( 'stedb-forms-admin' );

			/** add args to stedb forms admin js */
			wp_localize_script( 'stedb-forms-admin', 'stedb_forms', array(
				'options_general_url'      => admin_url( 'options-general.php' ),
				'stedb_forms_settings_url' => admin_url( 'admin.php?page=stedb-forms-settings.php' ),
				'site_url'                 => site_url(),
			) );

			/** add l10n to stedb forms admin js */
			wp_localize_script( 'stedb-forms-admin', 'stedb_forms_l10n', array(
				'close'                           => esc_html__( 'Close', 'stedb-forms' ),
				'wordpress_settings'              => esc_html__( 'WordPress Settings', 'stedb-forms' ),
				'stedb_forms_settings'            => esc_html__( 'STEdb Forms Settings', 'stedb-forms' ),
				'verify'                          => esc_html__( 'Verify', 'stedb-forms' ),
				'confirm_delete_form_message'     => esc_html__( 'Are you sure you want to delete the form?', 'stedb-forms' ),
				'confirm_delete_entry_message'    => esc_html__( 'Are you sure you want to delete the entry?', 'stedb-forms' ),
				'confirm_delete_campaign_message' => esc_html__( 'Are you sure you want to delete the campaign?', 'stedb-forms' ),
			) );

			/** add ajax to stedb forms admin js */
			wp_localize_script( 'stedb-forms-admin', 'stedb_forms_ajax', array(
				'get_form_nonce'                                 => wp_create_nonce( 'stedb_forms_get_form' ),
				'get_forms_nonce'                                => wp_create_nonce( 'stedb_forms_get_forms' ),
				'get_campaigns_check_from_email_nonce'           => wp_create_nonce( 'stedb_forms_get_campaigns_check_from_email' ),
				'get_campaigns_check_from_email_with_code_nonce' => wp_create_nonce( 'stedb_forms_get_campaigns_check_from_email_with_code' ),
				'set_campaigns_check_from_email_nonce'           => wp_create_nonce( 'stedb_forms_set_campaigns_check_from_email' ),
			) );
		}

		/**
		 * load scripts in form builder admin
		 */
		public function form_builder_enqueue_scripts() {
			global $wpdb;
			global $stedb_forms_edit_form_page;

			/** check is form builder page */
			if ( ! $this->is_form_builder_page() ) {
				return;
			}

			/** get screen */
			$screen = get_current_screen();

			/** stedb form builder js */
			wp_register_script( 'stedb-form-builder', STEDB_FORMS_DIR_URL . '/assets/js/stedb-form-builder.js', array(
				'jquery',
				'underscore',
				'jquery-ui-core',
				'jquery-ui-sortable',
				'jquery-ui-draggable',
				'jquery-ui-droppable',
				'jquery-ui-dialog',
			), STEDB_FORMS_VERSION, true );
			wp_enqueue_script( 'stedb-form-builder' );

			/**
			 * edit form
			 * add form builder content from db
			 */
			if ( $screen->id == $stedb_forms_edit_form_page ) {

				/** check form id */
				if ( ! empty( $_REQUEST['id'] ) ) {

					/**
					 * get form id
					 * @var int $form_id
					 */
					$form_id = absint( wp_unslash( $_REQUEST['id'] ) );

					/** get form builder content and fields */
					$form_builder_content = $wpdb->get_var( $wpdb->prepare( 'SELECT form_builder_content FROM ' . $wpdb->prefix . 'stedb_forms_lists WHERE id = %d LIMIT 1', $form_id ) );
					$fields               = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'stedb_forms_fields WHERE form_id = %d', $form_id ), OBJECT_K );

					/** format json form builder content */
					$form_builder_content = json_decode( $form_builder_content, true );

					/** format json field values */
					$fields = array_map( function ( $field ) {
						$field->values = json_decode( $field->values, true );

						return $field;
					}, $fields );

					/** get fields from wpdb fields */
					if ( ! empty( $form_builder_content ) ) {
						if ( ! empty( $form_builder_content['rows'] ) ) {
							foreach ( $form_builder_content['rows'] as $row_id => $row ) {

								/** set fields to default */
								$form_builder_content['rows'][ $row_id ]['fields'] = array();

								if ( ! empty( $row['fields'] ) ) {
									foreach ( $row['fields'] as $field_id ) {

										/** set field from wpdb fields */
										$form_builder_content['rows'][ $row_id ]['fields'][] = $fields[ $field_id ];
									}
								}
							}
						}
					}

					/** add form builder content to stedb form builder js */
					wp_localize_script( 'stedb-form-builder', 'stedb_form_builder_content', $form_builder_content );
				}
			}

			/** add l10n to stedb form builder js */
			wp_localize_script( 'stedb-form-builder', 'stedb_form_builder_l10n', array(
				'close'                                       => esc_html__( 'Close', 'stedb-forms' ),
				'confirm_add_template_message'                => esc_html__( 'Are you sure you want to add the template? That will delete all previous content!', 'stedb-forms' ),
				'disabled_only_one_social_field_can_be_added' => esc_html__( 'Disabled: Only one social field can be added to the form!', 'stedb-forms' ),
			) );

			/** add ajax to stedb form builder js */
			wp_localize_script( 'stedb-form-builder', 'stedb_form_builder_ajax', array(
				'get_stedb_form_preview_nonce' => wp_create_nonce( 'stedb_forms_get_stedb_form_preview' ),
			) );

			/** add form element social fields to stedb form builder js */
			wp_localize_script( 'stedb-form-builder', 'stedb_form_element_social_fields', $this->form_element_social_fields );

			/** add form element fields to stedb form builder js */
			wp_localize_script( 'stedb-form-builder', 'stedb_form_element_fields', $this->form_element_fields );
		}

		/**
		 * init form builder js
		 */
		public function init_form_builder_js() {

			/** check is form builder page */
			if ( ! $this->is_form_builder_page() ) {
				return;
			}

			/** init form builder */
			?>
            <script type="text/javascript">
                var STEdbFormBuilder = new STEdbFormBuilder({});
            </script>
			<?php
		}

		/**
		 * load form element templates
		 */
		public function print_form_element_templates() {

			/** check is form builder page */
			if ( ! $this->is_form_builder_page() ) {
				return;
			}

			/** render form element row admin template */
			$this->form_element_row->admin_render();

			/** load form element social templates */
			if ( ! empty( $this->form_element_social_fields ) ) {
				foreach ( $this->form_element_social_fields as $form_element_social_field ) {

					/** render form element social field admin template */
					$form_element_social_field->admin_render();
				}
			}

			/** load form element field templates */
			if ( ! empty( $this->form_element_fields ) ) {
				foreach ( $this->form_element_fields as $form_element_field ) {

					/** render form element field admin template */
					$form_element_field->admin_render();
				}
			}
		}

		/**
		 * load form element js
		 */
		public function print_form_element_js() {

			/** check is form builder page */
			if ( ! $this->is_form_builder_page() ) {
				return;
			}

			/** load form element field templates */
			if ( ! empty( $this->form_element_fields ) ) {
				foreach ( $this->form_element_fields as $form_element_field ) {

					/** render form element field admin js */
					$form_element_field->admin_render_js();
				}
			}
		}

		/**
		 * search submenu position
		 *
		 * @param $page_slug
		 * @param $parent_slug
		 *
		 * @return int|string|null
		 */
		public function search_submenu_position( $page_slug, $parent_slug ) {
			global $submenu;

			if ( ! isset( $submenu[ $parent_slug ] ) ) {
				return null;
			}

			foreach ( $submenu[ $parent_slug ] as $i => $item ) {
				if ( $page_slug == $item[2] ) {
					return $i;
				}
			}

			return null;
		}

		/**
		 * add admin page
		 */
		public function admin_menu() {
			global $stedb_forms_lists_page;
			global $stedb_forms_add_form_page;
			global $stedb_forms_campaigns_page;
			global $stedb_forms_add_campaign_page;
			global $stedb_forms_settings_page;

			$has_account = $this->has_account();

			/** STEdb Forms page (Admin page) */
			add_menu_page( esc_html__( 'STEdb Forms', 'stedb-forms' ), esc_html__( 'STEdb Forms', 'stedb-forms' ), 'manage_options', 'stedb-forms.php', array(
				$this,
				'admin_page',
			), 'dashicons-text-page', 81 );

			/** All Forms (ie lists) page */
			if ( $has_account ) {
				$stedb_forms_lists_page = add_submenu_page( 'stedb-forms.php', esc_html__( 'All Forms', 'stedb-forms' ), esc_html__( 'All Forms', 'stedb-forms' ), 'manage_options', 'stedb-forms-lists.php', array(
					$this,
					'forms_page',
				) );
			}

			/** Add New Form page */
			if ( $has_account ) {
				$stedb_forms_add_form_page = add_submenu_page( 'stedb-forms.php', esc_html__( 'Add New Form', 'stedb-forms' ), esc_html__( 'Add New Form', 'stedb-forms' ), 'manage_options', 'stedb-forms-add-form.php', array(
					$this,
					'add_form_page',
				) );
			}

			/** All Campaigns page */
			if ( $has_account ) {
				$stedb_forms_campaigns_page = add_submenu_page( 'stedb-forms.php', esc_html__( 'All Campaigns', 'stedb-forms' ), esc_html__( 'All Campaigns', 'stedb-forms' ), 'manage_options', 'stedb-forms-campaigns.php', array(
					$this,
					'campaigns_page',
				) );
			}

			/** Add Campaign page */
			if ( $has_account ) {
				$stedb_forms_add_campaign_page = add_submenu_page( 'stedb-forms.php', esc_html__( 'Add New Campaign', 'stedb-forms' ), esc_html__( 'Add New Campaign', 'stedb-forms' ), 'manage_options', 'stedb-forms-add-campaign.php', array(
					$this,
					'add_campaign_page',
				) );
			}

			/** Reports page */
			$stedb_forms_settings_page = add_submenu_page( 'stedb-forms.php', esc_html__( 'Reports', 'stedb-forms' ), esc_html__( 'Reports', 'stedb-forms' ), 'manage_options', 'stedb-forms-reports.php', array(
				$this,
				'reports_page',
			) );

			/** Settings page */
			$stedb_forms_settings_page = add_submenu_page( 'stedb-forms.php', esc_html__( 'Settings', 'stedb-forms' ), esc_html__( 'Settings', 'stedb-forms' ), 'manage_options', 'stedb-forms-settings.php', array(
				$this,
				'settings_page',
			) );

			/** Premium page */
			$stedb_forms_settings_page = add_submenu_page( 'stedb-forms.php', esc_html__( 'Premium', 'stedb-forms' ), esc_html__( 'Reports', 'stedb-forms' ), 'manage_options', 'stedb-forms-premium.php', array(
				$this,
				'premium_page',
			) );

			/** call register settings function */
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			/** add screen options */
			add_action( 'load-' . $stedb_forms_lists_page, array( $this, 'forms_page_actions' ) );
			add_action( 'load-' . $stedb_forms_lists_page, array( $this, 'forms_page_screen_options' ) );
			add_action( 'load-' . $stedb_forms_campaigns_page, array( $this, 'campaigns_page_actions' ) );
			add_action( 'load-' . $stedb_forms_campaigns_page, array( $this, 'campaigns_page_screen_options' ) );
		}

		/**
		 * add edit form admin page
		 */
		public function edit_form_admin_menu() {
			global $pagenow;
			global $stedb_forms_edit_form_page;

			$has_account = $this->has_account();

			if ( 'admin.php' != $pagenow ) {
				return;
			}

			if ( ! isset( $_GET['page'] ) ) {
				return;
			}

			/** Edit Form page */
			if ( 'stedb-forms-edit-form.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {

				if ( $has_account ) {

					/** STEdb Forms page */
					add_submenu_page( 'stedb-forms.php', __( 'STEdb Forms', 'stedb-forms' ), __( 'STEdb Forms', 'stedb-forms' ), 'manage_options', 'stedb-forms.php' );

					/** Edit Form page */
					$stedb_forms_edit_form_page = add_submenu_page( 'stedb-forms.php', esc_html__( 'Edit Form', 'stedb-forms' ), esc_html__( 'Edit Form', 'stedb-forms' ), 'manage_options', 'stedb-forms-edit-form.php', array(
						$this,
						'edit_form_page',
					) );

					$this->edit_form_page_args();
				}
			}
		}

		/**
		 * add id query arg for edit form submenu page
		 */
		public function edit_form_page_args() {
			global $submenu;

			$page_slug   = 'stedb-forms-edit-form.php';
			$parent_slug = 'stedb-forms.php';
			$position    = $this->search_submenu_position( $page_slug, $parent_slug );

			/** check position */
			if ( ! is_numeric( $position ) ) {
				return;
			}

			/** check submenu */
			if ( $submenu[ $parent_slug ][ $position ][2] != $page_slug ) {
				return;
			}

			/** add args */
			$submenu[ $parent_slug ][ $position ][2] = add_query_arg( array(
				'page' => sanitize_file_name( $page_slug ),
				'id'   => absint( wp_unslash( $_GET['id'] ) ),
			), 'admin.php' );
		}

		/**
		 * change parent file in edit form
		 *
		 * @param $parent_file
		 *
		 * @return string
		 */
		public function edit_form_parent_file( $parent_file ) {
			global $pagenow;

			if ( 'admin.php' != $pagenow ) {
				return $parent_file;
			}

			if ( ! isset( $_GET['page'] ) ) {
				return $parent_file;
			}

			if ( 'stedb-forms-edit-form.php' != sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {
				return $parent_file;
			}

			return 'stedb-forms.php';
		}

		/**
		 * change submenu file in edit form
		 *
		 * @param $submenu_file
		 *
		 * @return string
		 */
		public function edit_form_submenu_file( $submenu_file ) {
			global $pagenow;

			if ( 'admin.php' != $pagenow ) {
				return $submenu_file;
			}

			if ( ! isset( $_GET['page'] ) ) {
				return $submenu_file;
			}

			if ( 'stedb-forms-edit-form.php' != sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {
				return $submenu_file;
			}

			return add_query_arg( array(
				'page' => sanitize_file_name( wp_unslash( $_GET['page'] ) ),
				'id'   => absint( wp_unslash( $_GET['id'] ) ),
			), 'admin.php' );
		}

		/**
		 * change admin title in edit form
		 *
		 * @param $admin_title
		 *
		 * @return string
		 */
		public function edit_form_admin_title( $admin_title ) {
			global $pagenow;

			if ( 'admin.php' != $pagenow ) {
				return $admin_title;
			}

			if ( ! isset( $_GET['page'] ) ) {
				return $admin_title;
			}

			if ( 'stedb-forms-edit-form.php' != sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {
				return $admin_title;
			}

			if ( ! isset( $_GET['id'] ) ) {
				return $admin_title;
			}

			/** change admin title */
			$admin_title = esc_html__( 'Edit Form', 'stedb-forms' ) . $admin_title;

			return $admin_title;
		}

		/**
		 * add entries admin page
		 */
		public function entries_admin_menu() {
			global $pagenow;
			global $stedb_forms_entries_page;

			$has_account = $this->has_account();

			if ( 'admin.php' != $pagenow ) {
				return;
			}

			if ( ! isset( $_GET['page'] ) ) {
				return;
			}

			/** Edit Form page */
			if ( 'stedb-forms-entries.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {

				if ( $has_account ) {

					/** STEdb Forms page */
					add_submenu_page( 'stedb-forms.php', __( 'STEdb Forms', 'stedb-forms' ), __( 'STEdb Forms', 'stedb-forms' ), 'manage_options', 'stedb-forms.php' );

					/** All Entries page */
					$stedb_forms_entries_page = add_submenu_page( 'stedb-forms.php', esc_html__( 'All Entries', 'stedb-forms' ), esc_html__( 'All Entries', 'stedb-forms' ), 'manage_options', 'stedb-forms-entries.php', array(
						$this,
						'entries_page',
					) );

					$this->entries_page_args();

					add_action( 'load-' . $stedb_forms_entries_page, array( $this, 'entries_page_actions' ) );
					add_action( 'load-' . $stedb_forms_entries_page, array( $this, 'entries_page_screen_options' ) );
				}
			}
		}

		/**
		 * add id query arg for entries submenu page
		 */
		public function entries_page_args() {
			global $submenu;

			$page_slug   = 'stedb-forms-entries.php';
			$parent_slug = 'stedb-forms.php';
			$position    = $this->search_submenu_position( $page_slug, $parent_slug );

			/** check position */
			if ( ! is_numeric( $position ) ) {
				return;
			}

			/** check submenu */
			if ( $submenu[ $parent_slug ][ $position ][2] != $page_slug ) {
				return;
			}

			/** add args */
			$submenu[ $parent_slug ][ $position ][2] = add_query_arg( array(
				'page'    => sanitize_file_name( $page_slug ),
				'form_id' => absint( wp_unslash( $_GET['form_id'] ) ),
			), 'admin.php' );
		}

		/**
		 * change parent file in entries
		 *
		 * @param $parent_file
		 *
		 * @return string
		 */
		public function entries_parent_file( $parent_file ) {
			global $pagenow;

			if ( 'admin.php' != $pagenow ) {
				return $parent_file;
			}

			if ( ! isset( $_GET['page'] ) ) {
				return $parent_file;
			}

			if ( 'stedb-forms-entries.php' != sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {
				return $parent_file;
			}

			return 'stedb-forms.php';
		}

		/**
		 * change submenu file in entries
		 *
		 * @param $submenu_file
		 *
		 * @return string
		 */
		public function entries_submenu_file( $submenu_file ) {
			global $pagenow;

			if ( 'admin.php' != $pagenow ) {
				return $submenu_file;
			}

			if ( ! isset( $_GET['page'] ) ) {
				return $submenu_file;
			}

			if ( 'stedb-forms-entries.php' != sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {
				return $submenu_file;
			}

			return add_query_arg( array(
				'page'    => sanitize_file_name( wp_unslash( $_GET['page'] ) ),
				'form_id' => absint( wp_unslash( $_GET['form_id'] ) ),
			), 'admin.php' );
		}

		/**
		 * change admin title in entries
		 *
		 * @param $admin_title
		 *
		 * @return string
		 */
		public function entries_admin_title( $admin_title ) {
			global $pagenow;

			if ( 'admin.php' != $pagenow ) {
				return $admin_title;
			}

			if ( ! isset( $_GET['page'] ) ) {
				return $admin_title;
			}

			if ( 'stedb-forms-entries.php' != sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {
				return $admin_title;
			}

			if ( ! isset( $_GET['form_id'] ) ) {
				return $admin_title;
			}

			/** change admin title */
			$admin_title = esc_html__( 'All Entries', 'stedb-forms' ) . $admin_title;

			return $admin_title;
		}


		/**
		 * add edit entry admin page
		 */
		public function edit_entry_admin_menu() {
			global $pagenow;
			global $stedb_forms_entries_page;
			global $stedb_forms_edit_entry_page;

			$has_account = $this->has_account();

			if ( 'admin.php' != $pagenow ) {
				return;
			}

			if ( ! isset( $_GET['page'] ) ) {
				return;
			}

			/** Edit Entry page */
			if ( 'stedb-forms-edit-entry.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {

				if ( $has_account ) {

					/** STEdb Forms page */
					add_submenu_page( 'stedb-forms.php', __( 'STEdb Forms', 'stedb-forms' ), __( 'STEdb Forms', 'stedb-forms' ), 'manage_options', 'stedb-forms.php' );

					/** All Entries page */
					$stedb_forms_entries_page = add_submenu_page( 'stedb-forms.php', esc_html__( 'All Entries', 'stedb-forms' ), esc_html__( 'All Entries', 'stedb-forms' ), 'manage_options', 'stedb-forms-entries.php', array(
						$this,
						'entries_page',
					) );

					//todo: test
					$this->entries_page_args();

					/** Edit Form page */
					$stedb_forms_edit_entry_page = add_submenu_page( 'stedb-forms.php', esc_html__( 'Edit Entry', 'stedb-forms' ), esc_html__( 'Edit Entry', 'stedb-forms' ), 'manage_options', 'stedb-forms-edit-entry.php', array(
						$this,
						'edit_entry_page',
					) );

					$this->edit_entry_page_args();
				}
			}
		}

		/**
		 * add id query arg for edit entry submenu page
		 */
		public function edit_entry_page_args() {
			global $submenu;

			$page_slug   = 'stedb-forms-edit-entry.php';
			$parent_slug = 'stedb-forms.php';
			$position    = $this->search_submenu_position( $page_slug, $parent_slug );

			/** check position */
			if ( ! is_numeric( $position ) ) {
				return;
			}

			/** check submenu */
			if ( $submenu[ $parent_slug ][ $position ][2] != $page_slug ) {
				return;
			}

			/** add args */
			$submenu[ $parent_slug ][ $position ][2] = add_query_arg( array(
				'page'    => sanitize_file_name( $page_slug ),
				'form_id' => absint( wp_unslash( $_GET['form_id'] ) ),
				'id'      => absint( wp_unslash( $_GET['id'] ) ),
			), 'admin.php' );
		}

		/**
		 * change parent file in edit entry
		 *
		 * @param $parent_file
		 *
		 * @return string
		 */
		public function edit_entry_parent_file( $parent_file ) {
			global $pagenow;

			if ( 'admin.php' != $pagenow ) {
				return $parent_file;
			}

			if ( ! isset( $_GET['page'] ) ) {
				return $parent_file;
			}

			if ( 'stedb-forms-edit-entry.php' != sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {
				return $parent_file;
			}

			return 'stedb-forms.php';
		}

		/**
		 * change submenu file in edit entry
		 *
		 * @param $submenu_file
		 *
		 * @return string
		 */
		public function edit_entry_submenu_file( $submenu_file ) {
			global $pagenow;

			if ( 'admin.php' != $pagenow ) {
				return $submenu_file;
			}

			if ( ! isset( $_GET['page'] ) ) {
				return $submenu_file;
			}

			if ( 'stedb-forms-edit-entry.php' != sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {
				return $submenu_file;
			}

			return add_query_arg( array(
				'page'    => sanitize_file_name( wp_unslash( $_GET['page'] ) ),
				'form_id' => absint( wp_unslash( $_GET['form_id'] ) ),
				'id'      => absint( wp_unslash( $_GET['id'] ) ),
			), 'admin.php' );
		}

		/**
		 * change admin title in edit entry
		 *
		 * @param $admin_title
		 *
		 * @return string
		 */
		public function edit_entry_admin_title( $admin_title ) {
			global $pagenow;

			if ( 'admin.php' != $pagenow ) {
				return $admin_title;
			}

			if ( ! isset( $_GET['page'] ) ) {
				return $admin_title;
			}

			if ( 'stedb-forms-edit-entry.php' != sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {
				return $admin_title;
			}

			if ( ! isset( $_GET['form_id'] ) ) {
				return $admin_title;
			}

			if ( ! isset( $_GET['id'] ) ) {
				return $admin_title;
			}

			/** change admin title */
			$admin_title = esc_html__( 'Edit Entry', 'stedb-forms' ) . $admin_title;

			return $admin_title;
		}

		/**
		 * STEdb Forms
		 * admin page
		 */
		public function admin_page() {
			global $stedb_forms_auth;

			$stedb_forms_auth = $this->has_account();

			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/header.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/admin-page.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/footer.php';
		}

		/**
		 * STEdb Forms
		 * forms page
		 */
		public function forms_page() {
			global $stedb_forms_auth;

			$stedb_forms_auth = $this->has_account();

			/** check stedb forms auth */
			if ( ! $stedb_forms_auth ) {
				return;
			}

			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
			require_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-list-table.php';

			$stedb_forms_list_table = new STEDB_Forms_List_Table();

			/** prepare items */
			$stedb_forms_list_table->prepare_items();

			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/header.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/lists-page.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/footer.php';
		}

		/**
		 * STEdb Forms
		 * forms page actions
		 */
		public function forms_page_actions() {
			global $wpdb;
			global $stedb_forms_lists_page;

			$screen = get_current_screen();

			if ( $screen->id != $stedb_forms_lists_page ) {
				return;
			}

			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
			require_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-list-table.php';

			$stedb_forms_list_table = new STEDB_Forms_List_Table();

			/** bulk action */
			$stedb_forms_list_table->process_bulk_action();

			/** manage actions */
			$action  = $stedb_forms_list_table->current_action();
			$form_id = isset( $_REQUEST['id'] ) ? absint( wp_unslash( $_REQUEST['id'] ) ) : '';

			/** current admin url */
			$current_admin_url = add_query_arg( array(
				'page' => sanitize_file_name( wp_unslash( $_GET['page'] ) ),
			), 'admin.php' );

			/** single action */
			if ( $action ) {

				/** delete form */
				if ( 'delete_form' == $action ) {

					/** STEdb API */
					$stedb_forms_api_client = new STEDB_Forms_Api_Client();

					/** get list id from wpdb */
					$list_id = $wpdb->get_var( $wpdb->prepare( 'SELECT list_id FROM ' . $wpdb->prefix . 'stedb_forms_lists WHERE id = %d LIMIT 1', $form_id ) );

					/** delete form from api */
					$deleted_list = $stedb_forms_api_client->delete_lists( $list_id );

					/** check wp error */
					if ( is_wp_error( $deleted_list ) ) {
						wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'error_api_request_delete_list', $current_admin_url ) ) );
						exit;
					}

					/** delete form */
					$wpdb->delete( $wpdb->prefix . 'stedb_forms_lists', array( 'id' => $form_id ), array( '%d' ) );

					/** delete fields */
					$wpdb->delete( $wpdb->prefix . 'stedb_forms_fields', array( 'form_id' => $form_id ), array( '%d' ) );
				}
			}
		}

		/**
		 * STEdb Forms
		 * forms page screen options
		 */
		public function forms_page_screen_options() {
			global $stedb_forms_lists_page;

			$screen = get_current_screen();

			if ( $screen->id != $stedb_forms_lists_page ) {
				return;
			}

			$args = array(
				'label'   => esc_html__( 'Forms per page', 'stedb-forms' ),
				'default' => 25,
				'option'  => 'stedb_forms_lists_per_page',
			);
			add_screen_option( 'per_page', $args );
		}

		/**
		 * STEdb Forms
		 * add form page
		 */
		public function add_form_page() {
			global $stedb_forms_auth;

			$stedb_forms_auth = $this->has_account();

			/** check stedb forms auth */
			if ( ! $stedb_forms_auth ) {
				return;
			}

			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/header.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/add-form-page.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/form-preview-dialog.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/form-templates-dialog.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/footer.php';
		}

		/**
		 * STEdb Forms
		 * edit form page
		 */
		public function edit_form_page() {
			global $stedb_forms_auth;
			global $stedb_forms_list;
			global $wpdb;

			$stedb_forms_auth = $this->has_account();

			/** check stedb forms auth */
			if ( ! $stedb_forms_auth ) {
				return;
			}

			/** check id */
			if ( ! isset( $_REQUEST['id'] ) ) {
				return;
			}

			/** get form id */
			$form_id = absint( wp_unslash( $_REQUEST['id'] ) );

			$stedb_forms_list = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'stedb_forms_lists WHERE id = %d LIMIT 1', $form_id ) );

			/** check list */
			if ( empty( $stedb_forms_list ) ) {
				wp_die( esc_html__( 'Failed to load the form.', 'stedb-forms' ) );
			}

			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/header.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/edit-form-page.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/form-preview-dialog.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/form-templates-dialog.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/footer.php';
		}

		/**
		 * STEdb Forms
		 * entries page
		 */
		public function entries_page() {
			global $wpdb;
			global $stedb_forms_auth;

			$stedb_forms_auth = $this->has_account();

			/** check stedb forms auth */
			if ( ! $stedb_forms_auth ) {
				return;
			}

			/** check form id */
			if ( ! isset( $_REQUEST['form_id'] ) ) {
				return;
			}

			/** get form id */
			$form_id = absint( wp_unslash( $_REQUEST['form_id'] ) );

			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
			require_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-entries-list-table.php';

			/** get list (ie form) from wpdb */
			$stedb_forms_list = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'stedb_forms_lists WHERE id = %d LIMIT 1', $form_id ) );

			/** check list */
			if ( empty( $stedb_forms_list ) ) {
				wp_die( esc_html__( 'Failed to load the list.', 'stedb-forms' ) );
			}

			/** list table */
			$stedb_forms_entries_list_table = new STEDB_Forms_Entries_List_Table( $form_id );

			/** prepare items */
			$stedb_forms_entries_list_table->prepare_items();

			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/header.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/entries-page.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/footer.php';
		}

		/**
		 * STEdb Forms
		 * entries page actions
		 */
		public function entries_page_actions() {
			global $wpdb;
			global $stedb_forms_entries_page;

			$screen = get_current_screen();

			if ( $screen->id != $stedb_forms_entries_page ) {
				return;
			}

			/** check form id */
			if ( ! isset( $_REQUEST['form_id'] ) ) {
				return;
			}

			/** get form id */
			$form_id = absint( wp_unslash( $_REQUEST['form_id'] ) );

			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
			require_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-entries-list-table.php';

			/** list table */
			$stedb_forms_entries_list_table = new STEDB_Forms_Entries_List_Table( $form_id );

			/** bulk action */
			$stedb_forms_entries_list_table->process_bulk_action();

			/** manage actions */
			$action = $stedb_forms_entries_list_table->current_action();
			$id     = isset( $_REQUEST['id'] ) ? absint( wp_unslash( $_REQUEST['id'] ) ) : '';

			/** current admin url */
			$current_admin_url = add_query_arg( array(
				'page' => sanitize_file_name( wp_unslash( $_GET['page'] ) ),
			), 'admin.php' );

			/** single action */
			if ( $action ) {

				/** delete entry */
				if ( 'delete_entry' == $action ) {

					//todo: delete entry from api

					$wpdb->delete( $wpdb->prefix . 'stedb_forms_entries', array( 'id' => $id ), array( '%d' ) );
				}
			}
		}

		/**
		 * STEdb Forms
		 * entries page screen options
		 */
		public function entries_page_screen_options() {
			global $stedb_forms_entries_page;

			$screen = get_current_screen();

			if ( $screen->id != $stedb_forms_entries_page ) {
				return;
			}

			$args = array(
				'label'   => esc_html__( 'Entries per page', 'stedb-forms' ),
				'default' => 25,
				'option'  => 'stedb_forms_entries_per_page',
			);
			add_screen_option( 'per_page', $args );
		}

		/**
		 * STEdb Forms
		 * edit entry page
		 */
		public function edit_entry_page() {
			global $stedb_forms_auth;
			global $stedb_forms_entry;
			global $wpdb;

			$stedb_forms_auth = $this->has_account();

			/** check stedb forms auth */
			if ( ! $stedb_forms_auth ) {
				return;
			}

			/** check form id */
			if ( ! isset( $_REQUEST['form_id'] ) ) {
				return;
			}

			/** get form id */
			$form_id = absint( wp_unslash( $_REQUEST['form_id'] ) );

			/** get list (ie form) from wpdb */
			$stedb_forms_list = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'stedb_forms_lists WHERE id = %d LIMIT 1', $form_id ) );

			/** check list */
			if ( empty( $stedb_forms_list ) ) {
				wp_die( esc_html__( 'Failed to load the list.', 'stedb-forms' ) );
			}

			/** check entry id */
			if ( ! isset( $_REQUEST['id'] ) ) {
				return;
			}

			/** get entry id */
			$id = absint( wp_unslash( $_REQUEST['id'] ) );

			/** @var object $stedb_forms_entry */
			$stedb_forms_entry = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'stedb_forms_entries WHERE id = %d AND form_id = %d', $id, $form_id ) );

			/** check entry */
			if ( empty( $stedb_forms_entry ) ) {
				wp_die( esc_html__( 'Failed to load the entry.', 'stedb-forms' ) );
			}

			/** format custom fields */
			$stedb_forms_entry->custom_fields = json_decode( $stedb_forms_entry->custom_fields, true );

			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/header.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/edit-entry-page.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/footer.php';
		}

		/**
		 * STEdb Forms
		 * campaigns page
		 */
		public function campaigns_page() {
			global $stedb_forms_auth;

			$stedb_forms_auth = $this->has_account();

			/** check stedb forms auth */
			if ( ! $stedb_forms_auth ) {
				return;
			}

			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
			require_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-campaigns-list-table.php';

			$stedb_forms_campaigns_list_table = new STEDB_Forms_Campaigns_List_Table();

			/** prepare items */
			$stedb_forms_campaigns_list_table->prepare_items();

			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/header.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/campaigns-page.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/footer.php';
		}

		/**
		 * STEdb Forms
		 * campaigns page actions
		 */
		public function campaigns_page_actions() {
			global $wpdb;
			global $stedb_forms_campaigns_page;

			$screen = get_current_screen();

			if ( $screen->id != $stedb_forms_campaigns_page ) {
				return;
			}

			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
			require_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-campaigns-list-table.php';

			$stedb_forms_campaigns_list_table = new STEDB_Forms_Campaigns_List_Table();

			/** bulk action */
			$stedb_forms_campaigns_list_table->process_bulk_action();

			/** manage actions */
			$action = $stedb_forms_campaigns_list_table->current_action();
			$id     = isset( $_REQUEST['id'] ) ? absint( wp_unslash( $_REQUEST['id'] ) ) : '';

			/** current admin url */
			$current_admin_url = add_query_arg( array(
				'page' => sanitize_file_name( wp_unslash( $_GET['page'] ) ),
			), 'admin.php' );

			/** single action */
			if ( $action ) {

				/** delete campaign */
				if ( 'delete_campaign' == $action ) {

					/** STEdb API */
					$stedb_forms_api_client = new STEDB_Forms_Api_Client();

					/** get campaign id from wpdb */
					$campaign_id = $wpdb->get_var( $wpdb->prepare( 'SELECT campaign_id FROM ' . $wpdb->prefix . 'stedb_forms_campaigns WHERE id = %d LIMIT 1', $id ) );

					/** delete campaign from api */
					$deleted_campaign = $stedb_forms_api_client->delete_campaigns( $campaign_id );

					/** check wp error */
					if ( is_wp_error( $deleted_campaign ) ) {
						wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'error_api_request_delete_campaign', $current_admin_url ) ) );
						exit;
					}

					$wpdb->delete( $wpdb->prefix . 'stedb_forms_campaigns', array( 'id' => $id ), array( '%d' ) );
				}
			}
		}

		/**
		 * STEdb Forms
		 * campaigns page screen options
		 */
		public function campaigns_page_screen_options() {
			global $stedb_forms_campaigns_page;

			$screen = get_current_screen();

			if ( $screen->id != $stedb_forms_campaigns_page ) {
				return;
			}

			$args = array(
				'label'   => esc_html__( 'Campaigns per page', 'stedb-forms' ),
				'default' => 25,
				'option'  => 'stedb_forms_campaigns_per_page',
			);
			add_screen_option( 'per_page', $args );
		}

		/**
		 * STEdb Forms
		 * add campaign page
		 */
		public function add_campaign_page() {
			global $stedb_forms_auth;

			$stedb_forms_auth = $this->has_account();

			/** check stedb forms auth */
			if ( ! $stedb_forms_auth ) {
				return;
			}

			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/header.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/add-campaign-page.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/campaign-from-email-verify-dialog.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/footer.php';
		}

		/**
		 * STEdb Forms
		 * reports page
		 */
		public function reports_page() {
			global $stedb_forms_auth;

			$stedb_forms_auth = $this->has_account();

			/** check stedb forms auth */
			if ( ! $stedb_forms_auth ) {
				return;
			}

			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

			/** default subpage */
			$subpage = 'lists';

			/** check subpage */
			if ( isset( $_GET['subpage'] ) ) {
				$subpage = sanitize_text_field( wp_unslash( $_GET['subpage'] ) );
			}

			/** format subpage */
			$subpage = sanitize_file_name( 'reports-' . $subpage . '-page.php' );

			/** check file exists */
			if ( file_exists( STEDB_FORMS_DIR_PATH . '/views/admin-templates/' . $subpage ) ) {

				include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/header.php';
				include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/' . $subpage;
				include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/footer.php';
			}
		}

		/**
		 * admin
		 * process lists report bulk action
		 */
		function admin_process_lists_report_bulk_action() {

			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
			require_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-lists-report-list-table.php';

			/** @var STEDB_Forms_Lists_Report_List_Table $stedb_forms_lists_report_list_table */
			$stedb_forms_lists_report_list_table = new STEDB_Forms_Lists_Report_List_Table();

			/** bulk action */
			$stedb_forms_lists_report_list_table->process_bulk_action();
		}

		/**
		 * STEdb Forms
		 * register settings
		 */
		public function register_settings() {

			register_setting( 'stedb-forms-settings-group', 'stedb_forms_account', array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_array' ),
			) );
			register_setting( 'stedb-forms-settings-group', 'stedb_forms_account_address', array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_array' ),
			) );
		}

		/**
		 * STEdb Forms
		 * settings page
		 */
		public function settings_page() {
			global $stedb_forms_auth;

			$stedb_forms_auth = $this->has_account();

			/** check subpage */
			if ( isset( $_GET['subpage'] ) ) {
				$subpage = sanitize_file_name( 'settings-' . sanitize_text_field( wp_unslash( $_GET['subpage'] ) ) . '-page.php' );

				/** check file exists */
				if ( file_exists( STEDB_FORMS_DIR_PATH . '/views/admin-templates/' . $subpage ) ) {
					include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/header.php';
					include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/' . $subpage;
					include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/footer.php';
				}

				return;
			}

			/** check auth and account address */
			if ( $stedb_forms_auth ) {
				$stedb_forms_account_address = get_option( 'stedb_forms_account_address', array() );

				/** check account address */
				if ( empty( $stedb_forms_account_address ) ) {

					/** get address from api */
					$stedb_forms_api_client = new STEDB_Forms_Api_Client();
					$account_address_in_api = $stedb_forms_api_client->account_view_address();

					/** check account address */
					if ( ! is_wp_error( $account_address_in_api ) ) {
						update_option( 'stedb_forms_account_address', array_map( 'sanitize_text_field', $account_address_in_api ) );
					}
				}
			}

			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/header.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/settings-page.php';
			include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/footer.php';
		}

		/**
		 * STEdb Forms
		 * premium page
		 */
		public function premium_page() {
			global $stedb_forms_auth;

			$stedb_forms_auth = $this->has_account();

			/** check stedb forms auth */
			if ( ! $stedb_forms_auth ) {
				return;
			}

			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

			/** default subpage */
			$subpage = 'plans';

			/** check subpage */
			if ( isset( $_GET['subpage'] ) ) {
				$subpage = sanitize_text_field( wp_unslash( $_GET['subpage'] ) );
			}

			/** format subpage */
			$subpage = sanitize_file_name( 'premium-' . $subpage . '-page.php' );

			/** check file exists */
			if ( file_exists( STEDB_FORMS_DIR_PATH . '/views/admin-templates/' . $subpage ) ) {

				include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/header.php';
				include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/' . $subpage;
				include_once STEDB_FORMS_DIR_PATH . '/views/admin-templates/footer.php';
			}
		}

		/**
		 * sanitize array
		 *
		 * @param $values
		 *
		 * @return array
		 */
		public function sanitize_array( $values ) {

			if ( ! is_array( $values ) ) {
				if ( empty( $values ) ) {
					return array();
				}

				$values = array( $values );
			}

			return array_filter( $values );
		}

		/**
		 * Has account?
		 * @return bool
		 */
		public function has_account() {

			/** account form option */
			$stedb_forms_account = get_option( 'stedb_forms_account', array() );

			/** check array */
			if ( empty( $stedb_forms_account ) ) {
				return false;
			}

			/** check array */
			if ( ! is_array( $stedb_forms_account ) ) {
				return false;
			}

			/**
			 * check account vars
			 */
			$required_account_vars = array( 'user_id', 'secret' );

			if ( count( array_intersect_key( array_flip( $required_account_vars ), $stedb_forms_account ) ) !== count( $required_account_vars ) ) {
				return false;
			}

			/**
			 * has account function
			 * @var bool $has_account
			 */
			$has_account = in_array( false, array_map( function ( $a ) {
				return ! empty( $a );
			}, $stedb_forms_account ) );

			/** return bool */
			return ( ! $has_account );
		}

		/**
		 * Update Option - STEdb Forms Account
		 *
		 * @param $new_account
		 * @param $old_account
		 */
		public function update_option_stedb_forms_account( $new_account, $old_account ) {

			/** check base url */
			if ( ! isset( $old_account['base_url'] ) ) {
				$new_account['base_url'] = 'https://opt4.stedb.com/dbm9x/api/';
			} else {
				$new_account['base_url'] = $old_account['base_url'];
			}

			return $new_account;
		}

		/**
		 * Update Option - STEdb Forms Account Address
		 *
		 * @param $new_account_address
		 * @param $old_account_address
		 */
		public function update_option_stedb_forms_account_address( $new_account_address, $old_account_address ) {

			/** STEdb API */
			$stedb_forms_api_client = new STEDB_Forms_Api_Client();

			$account_save_address = $stedb_forms_api_client->account_save_address( $new_account_address );

			/** if api not updated, return old account address */
			if ( is_wp_error( $account_save_address ) ) {

				/** settings error */
				add_settings_error( 'stedb_forms_account_address', 'stedb_forms_account_address_update_api_failed', sprintf( esc_html__( 'Account Address update failed in API: %s', 'stedb-forms' ), $account_save_address->get_error_message() ) );

				return $old_account_address;
			}

			return $new_account_address;
		}

		/**
		 * Admin Post
		 * debug pure db
		 * todo: delete function
		 */
		public function admin_post_debug_pure_db() {

			/**
			 * get settings url
			 * @var string $settings_url
			 */
			$settings_url = esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) );

			/** check nonce */
			if ( empty( $_POST['_wpnonce'] ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_empty', $settings_url ) ) );
				exit;
			}

			/** verify nonce */
			if ( ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'stedb_forms_debug_pure_db' ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_incorrect', $settings_url ) ) );
				exit;
			}

			global $wpdb;

			$tables = array(
				$wpdb->prefix . 'stedb_forms_entries',
			);

			$wpdb->query( "DROP TABLE IF EXISTS " . implode( ', ', $tables ) . ";" );

			wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'pure_success', $settings_url ) ) );
		}

		/**
		 * Admin Post
		 * create account
		 */
		public function admin_post_create_account() {

			/**
			 * get settings url
			 * @var string $settings_url
			 */
			$settings_url = esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) );

			/** check nonce */
			if ( empty( $_POST['_wpnonce'] ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_empty', $settings_url ) ) );
				exit;
			}

			/** verify nonce */
			if ( ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'stedb_forms_create_account' ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_incorrect', $settings_url ) ) );
				exit;
			}

			/** STEdb API */
			$stedb_forms_api_client = new STEDB_Forms_Api_Client( '', '', 'https://opt4.stedb.com/crm/' );
			$account                = $stedb_forms_api_client->create_account();

			/** check wp error */
			if ( is_wp_error( $account ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'error_api_request_create_account', $settings_url ) ) );
				exit;
			}

			/** check create account error */
			if ( isset( $account['error'] ) ) {

				/** error: reactivate account */
				if ( 1 == $account['err_code'] ) {

					/** redirect to account reactivate page */
					wp_safe_redirect( esc_url_raw( admin_url( add_query_arg( array(
						'page'    => 'stedb-forms-settings.php',
						'subpage' => 'account-reactivate',
					), 'admin.php' ) ) ) );
				}

				wp_safe_redirect( esc_url_raw( add_query_arg( array(
					'notice'     => 'error_api_create_account',
					'error_code' => $account['err_code'],
				), $settings_url ) ) );
				exit;
			}

			update_option( 'stedb_forms_account', $account );
			wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'created_success', $settings_url ) ) );
		}

		/**
		 * Admin Notices
		 * create account
		 */
		public function admin_notices_create_account() {
			global $stedb_forms_settings_page;

			$screen = get_current_screen();

			if ( $screen->id != $stedb_forms_settings_page ) {
				return;
			}

			if ( ! isset( $_GET['notice'] ) ) {
				return;
			}

			/** error: nonce field empty */
			if ( 'nonce_field_empty' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: The nonce field is empty.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: nonce field incorrect */
			if ( 'nonce_field_incorrect' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: That nonce field was incorrect.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: request create account api */
			if ( 'error_api_request_create_account' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: There is a problem with the API request.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: create account api */
			if ( 'error_api_create_account' == $_GET['notice'] ) {

				$error_messages = array(
					4 => esc_html__( 'Your WordPress installation is using an invalid email, please add a valid one and try again', 'stedb-forms' ),
					5 => esc_html__( 'The email used in your WordPress installation is using an invalid domain, please add a valid one and try again', 'stedb-forms' ),
					6 => esc_html__( 'The email used in your WordPress installation is using a domain that is blocked, please try with another email', 'stedb-forms' ),
					7 => esc_html__( 'The email used in your WordPress installation is blocked, please try with another email', 'stedb-forms' ),
				);

				$class   = 'notice notice-error is-dismissible';
				$message = sprintf( esc_html__( 'Error: %s.', 'stedb-forms' ), isset( $_GET['error_code'] ) ? $error_messages[ absint( wp_unslash( $_GET['error_code'] ) ) ] : '' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** created success */
			if ( 'created_success' == $_GET['notice'] ) {
				$class   = 'notice notice-success is-dismissible';
				$message = __( 'Account successfully created.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}
		}

		/**
		 * Admin Post
		 * reactivate account
		 */
		public function admin_post_reactivate_account() {

			/**
			 * get settings url
			 * @var string $settings_account_reactivate_url
			 * @var string $settings_url
			 */
			$settings_account_reactivate_url = esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) );

			$settings_url = esc_url_raw( admin_url( add_query_arg( array(
				'page' => 'stedb-forms-settings.php',
			), 'admin.php' ) ) );

			/** check nonce */
			if ( empty( $_POST['_wpnonce'] ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_empty', $settings_account_reactivate_url ) ) );
				exit;
			}

			/** verify nonce */
			if ( ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'stedb_forms_reactivate_account' ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_incorrect', $settings_account_reactivate_url ) ) );
				exit;
			}

			/** STEdb API */
			$stedb_forms_api_client = new STEDB_Forms_Api_Client( '', '', 'https://opt4.stedb.com/crm/' );

			$data = array(
				'email'  => get_bloginfo( 'admin_email' ),
				'domain' => get_option( 'siteurl' ),
				'code'   => implode( '', array_map( 'absint', wp_unslash( $_POST['stedb_forms_code'] ) ) ),
			);

			$account = $stedb_forms_api_client->reactivate_account( $data );

			/** check wp error */
			if ( is_wp_error( $account ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'error_api_request_reactivate_account', $settings_account_reactivate_url ) ) );
				exit;
			}

			/** check reactivate account error */
			if ( isset( $account['error'] ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( array(
					'notice'     => 'error_api_reactivate_account',
					'error_code' => $account['err_code'],
				), $settings_account_reactivate_url ) ) );
				exit;
			}

			update_option( 'stedb_forms_account', $account );
			wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'reactivated_success', $settings_url ) ) );
		}

		/**
		 * Admin Notices
		 * reactivate account
		 */
		public function admin_notices_reactivate_account() {
			global $stedb_forms_settings_page;

			$screen = get_current_screen();

			if ( $screen->id != $stedb_forms_settings_page ) {
				return;
			}

			if ( ! isset( $_GET['notice'] ) ) {
				return;
			}

			/** error: nonce field empty */
			if ( 'nonce_field_empty' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: The nonce field is empty.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: nonce field incorrect */
			if ( 'nonce_field_incorrect' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: That nonce field was incorrect.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: request reactivate account api */
			if ( 'error_api_request_reactivate_account' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: There is a problem with the API request.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: reactivate account api */
			if ( 'error_api_reactivate_account' == $_GET['notice'] ) {

				$error_messages = array(
					1 => esc_html__( 'Wrong code, try again', 'stedb-forms' ),
					2 => esc_html__( 'No more attempts, please contact support', 'stedb-forms' ),
					3 => esc_html__( 'Code expired', 'stedb-forms' ),
					4 => sprintf( esc_html__( 'Account with email %s not found', 'stedb-forms' ), get_bloginfo( 'admin_email' ) ),
				);

				$class   = 'notice notice-error is-dismissible';
				$message = sprintf( esc_html__( 'Error: %s.', 'stedb-forms' ), isset( $_GET['error_code'] ) ? $error_messages[ absint( wp_unslash( $_GET['error_code'] ) ) ] : '' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** reactivated success */
			if ( 'reactivated_success' == $_GET['notice'] ) {
				$class   = 'notice notice-success is-dismissible';
				$message = __( 'Account successfully reactivated.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}
		}

		/**
		 * Admin Post
		 * add form
		 */
		public function admin_post_add_form() {
			global $wpdb;

			/**
			 * get form url
			 * @var string $form_url
			 */
			$form_url = esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) );

			/** check nonce */
			if ( empty( $_POST['_wpnonce'] ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_empty', $form_url ) ) );
				exit;
			}

			/** verify nonce */
			if ( ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'stedb_forms_add_form' ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_incorrect', $form_url ) ) );
				exit;
			}

			/** form elements */
			$stedb_form_elements = wp_unslash( $_POST['stedb_form_elements'] );

			/**
			 * set wpdb lists data
			 * @var array $wpdb_lists_data
			 */
			$wpdb_lists_data = array(
				'user_id'  => get_current_user_id(),
				'name'     => sanitize_text_field( wp_unslash( $_POST['stedb_forms_from_name'] ) ),
				'receiver' => sanitize_email( wp_unslash( $_POST['stedb_forms_receiver'] ) ),
				'date'     => date( 'Y-m-d' ),
			);

			/**
			 * set wpdb fields data
			 * @var array $wpdb_fields_data
			 */
			$wpdb_fields_data = array();

			/**
			 * add form to wpdb lists
			 */
			if ( false !== $wpdb->insert( $wpdb->prefix . 'stedb_forms_lists', $wpdb_lists_data ) ) {

				/** get form id from insert */
				$wpdb_lists_data['id'] = $wpdb->insert_id;
			} else {

				/** wp insert error */
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'wpdb_insert_list_error', $form_url ) ) );
				exit;
			}

			/**
			 * add fields to wpdb fields
			 */
			if ( ! empty( $stedb_form_elements ) ) {
				if ( ! empty( $stedb_form_elements['rows'] ) ) {
					foreach ( $stedb_form_elements['rows'] as $row_id => $row ) {

						if ( ! empty( $row['fields'] ) ) {
							foreach ( $row['fields'] as $field_id => $field ) {

								/** sanitize field type */
								$field['type'] = sanitize_key( $field['type'] );

								/** field */
								if ( isset( $this->form_element_fields[ $field['type'] ] ) ) {
									$form_element_field = $this->form_element_fields[ $field['type'] ];

									/** sanitize */
									$field['values'] = $form_element_field->sanitize( $field['values'] );

									/** set field name */
									$field['name'] = sanitize_key( str_replace( ' ', '_', $field['values']['label'] ) );
								}

								/** social field */
								if ( isset( $this->form_element_social_fields[ $field['type'] ] ) ) {

									/** set field name */
									$field['name'] = sanitize_key( $field['type'] );
								}

								/** format field values */
								if ( ! is_null( $field['values'] ) ) {
									$field['values'] = json_encode( $field['values'] );
								}

								/**
								 * set wpb field data
								 * @var array $wpdb_field_data
								 */
								$wpdb_field_data = array_merge( array(
									'form_id' => $wpdb_lists_data['id'],
								), $field );

								/** insert to wpdb fields */
								if ( false !== $wpdb->insert( $wpdb->prefix . 'stedb_forms_fields', $wpdb_field_data ) ) {
									$wpdb_field_id = $wpdb->insert_id;

									/** get field id from insert in fields data */
									$wpdb_fields_data[ $wpdb_field_id ] = $wpdb_field_data;

									/** set field id from insert in form elements */
									$stedb_form_elements['rows'][ $row_id ]['fields'][ $field_id ]['id'] = $wpdb_field_id;
								} else {
									/** remove wpdb lists row where id */
									$wpdb->delete( $wpdb->prefix . 'stedb_forms_lists', array( 'id' => $wpdb_lists_data['id'] ) );

									/** remove all wpdb fields row where field_id is null */
									$wpdb->delete( $wpdb->prefix . 'stedb_forms_fields', array( 'field_id' => null ) );

									/** wp insert error */
									wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'wpdb_insert_field_error', $form_url ) ) );
									exit;
								}
							}
						}
					}
				}
			}

			/**
			 * create form builder content
			 * for lists
			 */
			$row_ids   = array( 0 );
			$field_ids = array( 0 );

			$form_builder_content = array( 'rows' => array() );

			/** create form builder content from form elements */
			if ( ! empty( $stedb_form_elements ) ) {
				if ( ! empty( $stedb_form_elements['rows'] ) ) {
					foreach ( $stedb_form_elements['rows'] as $row ) {

						$row_id = max( $row_ids );
						$fields = array();

						if ( ! empty( $row['fields'] ) ) {
							foreach ( $row['fields'] as $field ) {

								$field_id    = max( $field_ids );
								$field_ids[] = intval( $field_id + 1 );

								$fields[] = $field['id'];
							}
						}

						$row_ids[]                                         = intval( $row_id + 1 );
						$form_builder_content['rows'][ $row_id ]['fields'] = $fields;
					}
				}
			} else {
				/** remove wpdb lists row where id */
				$wpdb->delete( $wpdb->prefix . 'stedb_forms_lists', array( 'id' => $wpdb_lists_data['id'] ) );

				/** remove all wpdb fields row where field_id is null */
				$wpdb->delete( $wpdb->prefix . 'stedb_forms_fields', array( 'field_id' => null ) );

				/** wp insert error */
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'create_form_builder_content_error', $form_url ) ) );
				exit;
			}

			/** add form type to form builder content */
			$form_builder_content['form_type'] = sanitize_text_field( wp_unslash( $_POST['stedb_forms_from_type'] ) );

			/** update wp lists, add form builder content */
			if ( false === $wpdb->update( $wpdb->prefix . 'stedb_forms_lists', array( 'form_builder_content' => json_encode( $form_builder_content ) ), array( 'id' => $wpdb_lists_data['id'] ), array( '%d' ) ) ) {

				/** remove wpdb lists row where id */
				$wpdb->delete( $wpdb->prefix . 'stedb_forms_lists', array( 'id' => $wpdb_lists_data['id'] ) );

				/** remove all wpdb fields row where field_id is null */
				$wpdb->delete( $wpdb->prefix . 'stedb_forms_fields', array( 'field_id' => null ) );

				/** wp update error */
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'wpdb_update_list_error', $form_url ) ) );
				exit;
			}

			/** STEdb API */
			$stedb_forms_api_client = new STEDB_Forms_Api_Client();

			/** add lists to api */
			$list_id = $stedb_forms_api_client->create_lists( array_merge( array(
				'form_builder_content' => json_encode( $form_builder_content ),
			), $wpdb_lists_data ) );

			/** check error create lists with api */
			if ( is_wp_error( $list_id ) ) {

				/** remove wpdb lists row where id */
				$wpdb->delete( $wpdb->prefix . 'stedb_forms_lists', array( 'id' => $wpdb_lists_data['id'] ) );

				/** remove all wpdb fields row where field_id is null */
				$wpdb->delete( $wpdb->prefix . 'stedb_forms_fields', array( 'field_id' => null ) );

				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'error_api_request_create_lists', $form_url ) ) );
				exit;
			}

			/** update wp lists, add list id */
			if ( false === $wpdb->update( $wpdb->prefix . 'stedb_forms_lists', array( 'list_id' => $list_id ), array( 'id' => $wpdb_lists_data['id'] ), array( '%d' ) ) ) {

				/** remove wpdb lists row where id */
				$wpdb->delete( $wpdb->prefix . 'stedb_forms_lists', array( 'id' => $wpdb_lists_data['id'] ) );

				/** remove all wpdb fields row where field_id is null */
				$wpdb->delete( $wpdb->prefix . 'stedb_forms_fields', array( 'field_id' => null ) );

				/** wp update error */
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'wpdb_update_list_error', $form_url ) ) );
				exit;
			}

			/** get social provider urls */
			$sm_providers = $stedb_forms_api_client->get_sm_providers( $list_id );

			/** set social links */
			$social_links = array();

			if ( ! empty( $sm_providers ) ) {
				foreach ( $sm_providers as $sm_provider ) {
					$social_links[ strtolower( sanitize_key( $sm_provider['sm_name'] ) ) ] = esc_url_raw( $sm_provider['sm_url'] );
				}
			}

			/** update wp lists, add social provider urls */
			if ( false === $wpdb->update( $wpdb->prefix . 'stedb_forms_lists', array( 'list_id' => $list_id ), array( 'social_links' => json_encode( $social_links ) ), array( '%s' ) ) ) {

				/** remove wpdb lists row where id */
				$wpdb->delete( $wpdb->prefix . 'stedb_forms_lists', array( 'id' => $wpdb_lists_data['id'] ) );

				/** remove all wpdb fields row where field_id is null */
				$wpdb->delete( $wpdb->prefix . 'stedb_forms_fields', array( 'field_id' => null ) );

				/** wp update error */
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'wpdb_update_list_error', $form_url ) ) );
				exit;
			}

			/** add fields to api */
			if ( ! empty( $stedb_form_elements ) ) {
				if ( ! empty( $stedb_form_elements['rows'] ) ) {
					foreach ( $stedb_form_elements['rows'] as $row ) {
						if ( ! empty( $row['fields'] ) ) {
							foreach ( $row['fields'] as $field ) {

								/** do not add social fields */
								if ( in_array( $field['type'], $this->form_element_social_fields ) ) {
									continue;
								}

								/** create field in api */
								$field_id = $stedb_forms_api_client->create_fields( array_merge( array(
									'list_id' => $list_id,
								), $field ) );

								/** check error create fields with api */
								if ( is_wp_error( $field_id ) ) {

									/** remove wpdb lists row where id */
									$wpdb->delete( $wpdb->prefix . 'stedb_forms_lists', array( 'id' => $wpdb_lists_data['id'] ) );

									/** remove all wpdb fields row where id */
									$ids = implode( ',', array_map( 'absint', $field_ids ) );
									$wpdb->query( "DELETE FROM " . $wpdb->prefix . "stedb_forms_fields WHERE id IN(" . $ids . ");" );

									wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'error_api_request_create_fields', $form_url ) ) );
									exit;
								}

								/** update wp lists */
								if ( false === $wpdb->update( $wpdb->prefix . 'stedb_forms_fields', array( 'field_id' => $field_id ), array( 'id' => $field['id'] ), array( '%d' ) ) ) {

									/** remove wpdb lists row where id */
									$wpdb->delete( $wpdb->prefix . 'stedb_forms_lists', array( 'id' => $wpdb_lists_data['id'] ) );

									/** remove all wpdb fields row where id */
									$ids = implode( ',', array_map( 'absint', $field_ids ) );
									$wpdb->query( "DELETE FROM " . $wpdb->prefix . "stedb_forms_fields WHERE id IN(" . $ids . ");" );

									/** wp update error */
									wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'wpdb_update_field_error', $form_url ) ) );
									exit;
								}
							}
						}
					}
				}
			}

			/** get form edit url */
			$form_edit_url = esc_url_raw( admin_url( add_query_arg( array(
				'page' => 'stedb-forms-edit-form.php',
				'id'   => $wpdb_lists_data['id'],
			), 'admin.php' ) ) );

			/** added success */
			wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'added_success', $form_edit_url ) ) );
			exit;
		}

		/**
		 * Admin Notices
		 * add form
		 */
		public function admin_notices_add_form() {
			global $stedb_forms_add_form_page;

			$screen = get_current_screen();

			if ( $screen->id != $stedb_forms_add_form_page ) {
				return;
			}

			if ( ! isset( $_GET['notice'] ) ) {
				return;
			}

			/** error: nonce field empty */
			if ( 'nonce_field_empty' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: The nonce field is empty.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: nonce field incorrect */
			if ( 'nonce_field_incorrect' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: That nonce field was incorrect.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: create form builder content error */
			if ( 'create_form_builder_content_error' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: Failed to create form builder content.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: wpdb insert form error */
			if ( 'wpdb_insert_list_error' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: Failed to add form to database.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: wpdb insert field error */
			if ( 'wpdb_insert_field_error' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: Failed to add field to database.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: wpdb update form error */
			if ( 'wpdb_update_list_error' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: Failed to update form in database.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: wpdb update field error */
			if ( 'wpdb_update_field_error' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: Failed to update field in database.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: api request create lists */
			if ( 'error_api_request_create_lists' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: There is a problem with the create lists API request.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: api request create fields */
			if ( 'error_api_request_create_fields' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: There is a problem with the create fields API request.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** success */
			if ( 'success' == $_GET['notice'] ) {
				$class   = 'notice notice-success is-dismissible';
				$message = __( 'Form successfully added.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}
		}

		/**
		 * Admin Post
		 * edit form
		 */
		public function admin_post_edit_form() {
			global $wpdb;

			/** STEdb API */
			$stedb_forms_api_client = new STEDB_Forms_Api_Client();

			/**
			 * get form url
			 * @var string $form_url
			 */
			$form_url = esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) );

			/** check nonce */
			if ( empty( $_POST['_wpnonce'] ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_empty', $form_url ) ) );
				exit;
			}

			/** verify nonce */
			if ( ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'stedb_forms_edit_form' ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_incorrect', $form_url ) ) );
				exit;
			}

			/** check form id */
			if ( ! isset( $_POST['form_id'] ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'form_id_is_not_set', $form_url ) ) );
				exit;
			}

			$form_id = absint( $_POST['form_id'] );

			/** form elements */
			$stedb_form_elements = wp_unslash( $_POST['stedb_form_elements'] );

			/**
			 * set wpdb lists data
			 * @var array $wpdb_lists_data
			 */
			$wpdb_lists_data = array(
				'name'     => sanitize_text_field( wp_unslash( $_POST['stedb_forms_from_name'] ) ),
				'receiver' => sanitize_email( wp_unslash( $_POST['stedb_forms_receiver'] ) ),
			);

			/**
			 * set wpdb fields data
			 * @var array $wpdb_fields_data
			 */
			$wpdb_fields_data = array();

			/**
			 * set field ids
			 * @var array $field_ids
			 */
			$field_ids = $wpdb->get_results( $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . 'stedb_forms_fields WHERE form_id = %d', $form_id ), ARRAY_A );

			//todo check $field_ids is array

			$field_ids = wp_list_pluck( $field_ids, 'id' );

			/** update wpdb fields */
			if ( ! empty( $stedb_form_elements ) ) {
				if ( ! empty( $stedb_form_elements['rows'] ) ) {
					foreach ( $stedb_form_elements['rows'] as $row_id => $row ) {

						if ( ! empty( $row['fields'] ) ) {
							foreach ( $row['fields'] as $field_id => $field ) {

								/** sanitize field id */
								$field_id = intval( $field_id );

								/** sanitize field type */
								$field['type'] = sanitize_key( $field['type'] );

								/** field */
								if ( isset( $this->form_element_fields[ $field['type'] ] ) ) {
									$form_element_field = $this->form_element_fields[ $field['type'] ];

									/** sanitize */
									$field['values'] = $form_element_field->sanitize( $field['values'] );

									/** set field name */
									$field['name'] = sanitize_key( str_replace( ' ', '_', $field['values']['label'] ) );
								}

								/** social field */
								if ( isset( $this->form_element_social_fields[ $field['type'] ] ) ) {

									/** set field name */
									$field['name'] = sanitize_key( $field['type'] );
								}

								/** format field values */
								if ( ! is_null( $field['values'] ) ) {
									$field['values'] = json_encode( $field['values'] );
								}

								/**
								 * set wpb field data
								 * @var array $wpdb_field_data
								 */
								$wpdb_field_data = $field;

								/** check field id (already exists) */
								if ( in_array( $field_id, $field_ids ) ) {

									//todo: update field in api

									/** update wpdb fields */
									if ( false !== $wpdb->update( $wpdb->prefix . 'stedb_forms_fields', $wpdb_field_data, array( 'id' => $field_id ) ) ) {

										/** add to wpdb fields data */
										$wpdb_fields_data[ $field_id ] = $wpdb_field_data;

										/** set field id from insert in form elements */
										$stedb_form_elements['rows'][ $row_id ]['fields'][ $field_id ]['id'] = $field_id;
									} else {

										/** wp update error */
										wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'wpdb_update_field_error', $form_url ) ) );
										exit;
									}
								} else {

									/** add form id */
									$wpdb_field_data['form_id'] = $form_id;

									//todo: add field to api

									/** insert to wpdb fields */
									if ( false !== $wpdb->insert( $wpdb->prefix . 'stedb_forms_fields', $wpdb_field_data ) ) {
										$wpdb_field_id = $wpdb->insert_id;

										/** get field id from insert in fields data */
										$wpdb_fields_data[ $wpdb_field_id ] = $wpdb_field_data;

										/** set field id from insert in form elements */
										$stedb_form_elements['rows'][ $row_id ]['fields'][ $field_id ]['id'] = $wpdb_field_id;
									} else {

										/** wp insert error */
										wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'wpdb_insert_field_error', $form_url ) ) );
										exit;
									}
								}

							}
						}
					}
				}
			}

			/** delete wpdb fields where not in field ids */
			$delete_field_ids = array_diff( $field_ids, array_keys( $wpdb_fields_data ) );

			/** check there are ids to delete */
			if ( ! empty( $delete_field_ids ) ) {

				//todo: delete field from api

				/** delete from fields */
				if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "stedb_forms_fields WHERE id IN(" . implode( ', ', array_fill( 0, sizeof( $delete_field_ids ), '%d' ) ) . ")", $delete_field_ids ) ) ) {

					/** wp update error */
					wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'wpdb_delete_fields_error', $form_url ) ) );
					exit;
				}
			}

			/**
			 * create form builder content
			 * for lists
			 */
			$row_ids   = array( 0 );
			$field_ids = array( 0 );

			$form_builder_content = array( 'rows' => array() );

			/** create form builder content from form elements */
			if ( ! empty( $stedb_form_elements ) ) {
				if ( ! empty( $stedb_form_elements['rows'] ) ) {
					foreach ( $stedb_form_elements['rows'] as $row ) {

						$row_id = max( $row_ids );
						$fields = array();

						if ( ! empty( $row['fields'] ) ) {
							foreach ( $row['fields'] as $field ) {

								$field_id    = max( $field_ids );
								$field_ids[] = intval( $field_id + 1 );

								$fields[] = $field['id'];
							}
						}

						$row_ids[]                                         = intval( $row_id + 1 );
						$form_builder_content['rows'][ $row_id ]['fields'] = $fields;
					}
				}
			}

			/** add form type to form builder content */
			$form_builder_content['form_type'] = sanitize_text_field( wp_unslash( $_POST['stedb_forms_from_type'] ) );

			//todo: edit lists in api

			/** add form builder content json to wpdb lists data */
			$wpdb_lists_data['form_builder_content'] = json_encode( $form_builder_content );

			/** update wp lists */
			if ( false === $wpdb->update( $wpdb->prefix . 'stedb_forms_lists', $wpdb_lists_data, array( 'id' => $form_id ) ) ) {

				/** wp update error */
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'wpdb_update_list_error', $form_url ) ) );
				exit;
			}

			wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'success', $form_url ) ) );
			exit;
		}

		/**
		 * Admin Notices
		 * edit form
		 */
		public function admin_notices_edit_form() {
			global $stedb_forms_edit_form_page;

			$screen = get_current_screen();

			if ( $screen->id != $stedb_forms_edit_form_page ) {
				return;
			}

			if ( ! isset( $_GET['notice'] ) ) {
				return;
			}

			/** error: nonce field empty */
			if ( 'nonce_field_empty' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: The nonce field is empty.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: nonce field incorrect */
			if ( 'nonce_field_incorrect' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: That nonce field was incorrect.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: form id is not set */
			if ( 'form_id_is_not_set' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: Form id is not set.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: wpdb update form error */
			if ( 'wpdb_update_list_error' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: Failed to update form in database.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: wpdb insert field error */
			if ( 'wpdb_insert_field_error' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: Failed to add field to database.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: wpdb update field error */
			if ( 'wpdb_update_field_error' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: Failed to update field in database.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: wpdb delete fields error */
			if ( 'wpdb_delete_fields_error' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: Failed to delete fields in database.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** added success */
			if ( 'added_success' == $_GET['notice'] ) {
				$class   = 'notice notice-success is-dismissible';
				$message = __( 'Form successfully added.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** modified success */
			if ( 'success' == $_GET['notice'] ) {
				$class   = 'notice notice-success is-dismissible';
				$message = __( 'Form successfully modified.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}
		}

		/**
		 * Admin Post
		 * edit entry
		 */
		public function admin_post_edit_entry() {
			global $wpdb;

			/** STEdb API */
			$stedb_forms_api_client = new STEDB_Forms_Api_Client();

			/**
			 * get entry url
			 * @var string $form_url
			 */
			$entry_url = esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) );

			/** check nonce */
			if ( empty( $_POST['_wpnonce'] ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_empty', $entry_url ) ) );
				exit;
			}

			/** verify nonce */
			if ( ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'stedb_forms_edit_entry' ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_incorrect', $entry_url ) ) );
				exit;
			}

			/** check form id */
			if ( ! isset( $_POST['form_id'] ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'form_id_is_not_set', $entry_url ) ) );
				exit;
			}

			/** check entry id */
			if ( ! isset( $_POST['id'] ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'edit_id_is_not_set', $entry_url ) ) );
				exit;
			}

			/** check list id */
			if ( ! isset( $_POST['list_id'] ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'list_id_is_not_set', $entry_url ) ) );
				exit;
			}

			$form_id = absint( $_POST['form_id'] );
			$id      = absint( $_POST['id'] );
			$list_id = absint( $_POST['list_id'] );

			/**
			 * set wpdb lists data
			 * @var array $wpdb_entries_data
			 */
			$custom_fields = json_encode( array_map( 'sanitize_text_field', wp_unslash( $_POST['custom_fields'] ) ) );

			//todo: edit entry in api

			/** update wp entries */
			if ( false === $wpdb->update( $wpdb->prefix . 'stedb_forms_entries', array(
					'custom_fields' => $custom_fields,
				), array(
					'id'      => $id,
					'form_id' => $form_id,
				) ) ) {

				/** wp update error */
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'wpdb_update_entry_error', $entry_url ) ) );
				exit;
			}

			wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'success', $entry_url ) ) );
			exit;
		}

		/**
		 * Admin Notices
		 * edit entry
		 */
		public function admin_notices_edit_entry() {
			global $stedb_forms_edit_entry_page;

			$screen = get_current_screen();

			if ( $screen->id != $stedb_forms_edit_entry_page ) {
				return;
			}

			if ( ! isset( $_GET['notice'] ) ) {
				return;
			}

			/** error: nonce field empty */
			if ( 'nonce_field_empty' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: The nonce field is empty.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: nonce field incorrect */
			if ( 'nonce_field_incorrect' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: That nonce field was incorrect.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: form id is not set */
			if ( 'form_id_is_not_set' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: Form id is not set.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: entry id is not set */
			if ( 'entry_id_is_not_set' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: Entry id is not set.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: wpdb update entry error */
			if ( 'wpdb_update_entry_error' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: Failed to update entry in database.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** modified success */
			if ( 'success' == $_GET['notice'] ) {
				$class   = 'notice notice-success is-dismissible';
				$message = __( 'Entry successfully modified.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}
		}

		/**
		 * Admin Post
		 * add campaign
		 */
		public function admin_post_add_campaign() {

			/**
			 * get form url
			 * @var string $form_url
			 */
			$form_url = esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) );

			/** check nonce */
			if ( empty( $_POST['_wpnonce'] ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_empty', $form_url ) ) );
				exit;
			}

			/** verify nonce */
			if ( ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'stedb_forms_add_campaign' ) ) {
				wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'nonce_field_incorrect', $form_url ) ) );
				exit;
			}

			//todo: check from email

			/** type: regular list */
			if ( 0 == $_POST['campaign_type'] ) {
				//todo: handle regular list
			}

			/** type: autoresponder */
			if ( 1 == $_POST['campaign_type'] ) {
				//todo: handle autoresponder
			}

			wp_safe_redirect( esc_url_raw( add_query_arg( 'notice', 'success', $form_url ) ) );
			exit;
		}

		/**
		 * Admin Notices
		 * add campaign
		 */
		public function admin_notices_add_campaign() {
			global $stedb_forms_add_campaign_page;

			$screen = get_current_screen();

			if ( $screen->id != $stedb_forms_add_campaign_page ) {
				return;
			}

			if ( ! isset( $_GET['notice'] ) ) {
				return;
			}

			/** error: nonce field empty */
			if ( 'nonce_field_empty' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: The nonce field is empty.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** error: nonce field incorrect */
			if ( 'nonce_field_incorrect' == $_GET['notice'] ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Error: That nonce field was incorrect.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}

			/** success */
			if ( 'success' == $_GET['notice'] ) {
				$class   = 'notice notice-success is-dismissible';
				$message = __( 'Campaign successfully added.', 'stedb-forms' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}
		}

		/**
		 * Ajax
		 * get form
		 */
		public function ajax_get_form() {
			global $wpdb;

			check_ajax_referer( 'stedb_forms_get_form' );

			$args = wp_unslash( $_POST );

			/** check args */
			if ( empty( array_diff( array( 'id' ), $args ) ) ) {
				wp_send_json_error( array(
					'error' => esc_html__( 'Required form id missing.', 'stedb-forms' ),
				) );
			}

			/**
			 * get lists (ie form)
			 * @var array $form
			 */
			$form = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'stedb_forms_lists WHERE id = %d LIMIT 1', $args['id'] ) );

			wp_send_json_success(
				array(
					'form' => $form,
				)
			);
		}

		/**
		 * Ajax
		 * get forms
		 */
		public function ajax_get_forms() {
			global $wpdb;

			check_ajax_referer( 'stedb_forms_get_forms' );

			/**
			 * get lists (ie forms)
			 * @var array $forms
			 */
			$forms = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'stedb_forms_lists' ) );

			wp_send_json_success(
				array(
					'forms' => $forms,
				)
			);
		}

		/**
		 * Ajax
		 * get stedb form preview
		 */
		public function ajax_get_stedb_form_preview() {
			check_ajax_referer( 'stedb_forms_get_stedb_form_preview' );

			$form = array();

			/** check form and parse */
			if ( isset( $_POST['form'] ) ) {
				parse_str( wp_unslash( $_POST['form'] ), $form );
			}

			$form_elements = array( 'rows' => array() );
			$row_ids       = array( 0 );
			$field_ids     = array( 0 );

			/**
			 * format fields
			 */
			if ( ! empty( $form['stedb_form_elements'] ) ) {
				if ( ! empty( $form['stedb_form_elements']['rows'] ) ) {
					foreach ( $form['stedb_form_elements']['rows'] as $row ) {

						$row_id = max( $row_ids );
						$fields = array();

						if ( ! empty( $row['fields'] ) ) {
							foreach ( $row['fields'] as $field ) {

								$field_id = max( $field_ids );

								/** field */
								if ( isset( $this->form_element_fields[ $field['type'] ] ) ) {
									$form_element_field = $this->form_element_fields[ $field['type'] ];

									/** sanitize */
									$field['values'] = $form_element_field->sanitize( $field['values'] );

									/** set field name */
									$field['name'] = sanitize_key( str_replace( ' ', '_', $field['values']['label'] ) );
								}

								/** social field */
								if ( isset( $this->form_element_social_fields[ $field['type'] ] ) ) {

									/** set field name */
									$field['name'] = sanitize_key( $field['type'] );
								}

								$field_ids[]         = intval( $field_id + 1 );
								$fields[ $field_id ] = $field;
							}
						}

						$row_ids[]                                  = intval( $row_id + 1 );
						$form_elements['rows'][ $row_id ]['fields'] = $fields;
					}
				}
			}

			/**
			 * form preview output
			 * @var string $output
			 */
			$output = sprintf( '<div id="stedb-form-%d" class="stedb-form">', absint( $form['form_id'] ) );

			/** get form builder content html */
			if ( ! empty( $form_elements ) ) {
				if ( ! empty( $form_elements['rows'] ) ) {
					foreach ( $form_elements['rows'] as $row_id => $row ) {
						$fields = '';

						if ( ! empty( $row['fields'] ) ) {
							foreach ( $row['fields'] as $field_id => $field ) {

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

			$output .= '</div>';

			echo $output;

			wp_die();
		}

		/**
		 * Ajax
		 * get campaigns check from email
		 */
		public function ajax_get_campaigns_check_from_email() {
			check_ajax_referer( 'stedb_forms_get_campaigns_check_from_email' );

			$args = wp_unslash( $_POST );

			/** check email */
			if ( ! isset( $args['email'] ) ) {
				wp_send_json_error( array(
					'error' => esc_html__( 'Required email is missing.', 'stedb-forms' ),
				) );
			}

			if ( empty( $args['email'] ) ) {
				wp_send_json_error( array(
					'error' => esc_html__( 'Required email is empty.', 'stedb-forms' ),
				) );
			}

			/** STEdb API */
			$stedb_forms_api_client = new STEDB_Forms_Api_Client();

			/** api get campaigns check from email */
			$email = $stedb_forms_api_client->get_campaigns_check_from_email( $args['email'] );

			/** check email */
			if ( is_wp_error( $email ) ) {
				wp_send_json_error( array(
					'error_code' => $email->get_error_code(),
					'error'      => esc_html__( 'The API failed to check campaign "from email" field.', 'stedb-forms' ),
				) );
			}

			wp_send_json_success(
				array(
					'email' => $email,
				)
			);
		}

		/**
		 * Ajax
		 * get campaigns check from email with code
		 */
		public function ajax_get_campaigns_check_from_email_with_code() {
			check_ajax_referer( 'stedb_forms_get_campaigns_check_from_email_with_code' );

			$args = wp_unslash( $_POST );

			/** check email */
			if ( ! isset( $args['email'] ) ) {
				wp_send_json_error( array(
					'error' => esc_html__( 'Required email is missing.', 'stedb-forms' ),
				) );
			}

			if ( empty( $args['email'] ) ) {
				wp_send_json_error( array(
					'error' => esc_html__( 'Required email is empty.', 'stedb-forms' ),
				) );
			}

			/** check code */
			if ( ! isset( $args['code'] ) ) {
				wp_send_json_error( array(
					'error' => esc_html__( 'Required code is missing.', 'stedb-forms' ),
				) );
			}

			if ( empty( $args['code'] ) ) {
				wp_send_json_error( array(
					'error' => esc_html__( 'Required code is empty.', 'stedb-forms' ),
				) );
			}

			/** STEdb API */
			$stedb_forms_api_client = new STEDB_Forms_Api_Client();

			/** api get campaigns check from email with code */
			$email = $stedb_forms_api_client->get_campaigns_check_from_email( $args['email'], $args['code'] );

			/** check email */
			if ( is_wp_error( $email ) ) {
				wp_send_json_error( array(
					'error' => esc_html__( 'The API failed to verify campaign "from email".', 'stedb-forms' ),
				) );
			}

			wp_send_json_success(
				array(
					'email' => $email,
				)
			);
		}

		/**
		 * Ajax
		 * set campaigns check from email
		 */
		public function ajax_set_campaigns_check_from_email() {
			check_ajax_referer( 'stedb_forms_set_campaigns_check_from_email' );

			$args = wp_unslash( $_POST );

			/** check args */
			if ( empty( array_diff( array( 'email' ), $args ) ) ) {
				wp_send_json_error( array(
					'error' => esc_html__( 'Required email is missing.', 'stedb-forms' ),
				) );
			}

			/** STEdb API */
			$stedb_forms_api_client = new STEDB_Forms_Api_Client();

			/** api set campaigns check from email with code */
			$email = $stedb_forms_api_client->set_campaigns_check_from_email( $args['email'] );

			/** check email */
			if ( is_wp_error( $email ) ) {
				wp_send_json_error( array(
					'error' => esc_html__( 'The API failed to check campaign "from email" field.', 'stedb-forms' ),
				) );
			}

			wp_send_json_success(
				array(
					'email' => $email,
				)
			);
		}

		/**
		 * update lists report in wpdb (from api)
		 *
		 * @param $lists_report
		 */
		public static function update_lists_report_in_wpdb( $lists_report ) {
			global $wpdb;

			if ( ! empty( $lists_report ) ) {
				foreach ( $lists_report as $list_report ) {
					$wpdb->replace( $wpdb->prefix . 'stedb_forms_list_reports', array(
						'id'                => $list_report['id'],
						'creation_date'     => $list_report['creation_date'],
						'last_subscription' => $list_report['last_subscription'],
						'count'             => $list_report['count'],
						'stat'              => json_encode( $list_report['stat'] ),
					), array( '%d', '%s', '%s', '%d', '%s' ) );
				}
			}
		}
	}
}