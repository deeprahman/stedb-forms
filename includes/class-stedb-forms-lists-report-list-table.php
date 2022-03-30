<?php

/**
 * Class STEDB_Forms_Lists_Report_List_Table
 */
class STEDB_Forms_Lists_Report_List_Table extends WP_List_Table {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'stedb_forms_list_report',
			'plural'   => 'stedb_forms_lists_report',
			'ajax'     => false,
		) );
	}

	/**
	 * Get table classes
	 * @return array
	 */
	protected function get_table_classes() {
		return array( 'widefat', 'stedb-forms-lists-report', 'striped' );
	}

	/**
	 * get_columns function
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'              => '<input type="checkbox" />',
			'form_name'       => esc_html__( 'Form Name', 'stedb-forms' ),
			'submissions'     => esc_html__( 'Submissions', 'stedb-forms' ),
			'creation_date'   => esc_html__( 'Creation Date', 'stedb-forms' ),
			'last_submission' => esc_html__( 'Last Submission', 'stedb-forms' ),
		);

		return $columns;
	}

	/**
	 * Add bulk actions
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'export' => esc_html__( 'Export', 'stedb-forms' ),
		);

		return $actions;
	}

	/**
	 * Default column
	 *
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return null
	 */
	public function column_default( $item, $column_name ) {
		return null;
	}

	/**
	 * The checkbox column
	 *
	 * @param object $item
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="stedb_forms_lists_report[]" value="%s" />', $item->id );
	}

	/**
	 * The form name column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_form_name( $item ) {

		$view_link = add_query_arg( array(
			'page'    => 'stedb-forms-reports.php',
			'subpage' => 'subscribers',
			'list_id' => $item->id,
		), 'admin.php' );

		return sprintf( '<a href="%s">%s</a>', esc_url( $view_link ), esc_html( $item->name ) );
	}

	/**
	 * The submissions column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_submissions( $item ) {
		return $item->count;
	}

	/**
	 * The creation date column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_creation_date( $item ) {
		return $item->creation_date;
	}

	/**
	 * The last submission column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_last_submission( $item ) {
		return $item->last_subscription;
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @param string $which
	 */
	public function display_tablenav( $which ) {

		if ( 'top' == $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}
		?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

			<?php if ( $this->has_items() ) : ?>
                <div class="alignleft actions bulkactions">
					<?php $this->bulk_actions( $which ); ?>
                </div>
			<?php endif; ?>

			<?php
			$this->extra_tablenav( $which );
			?>
            <br class="clear"/>
        </div>
		<?php
	}

	/**
	 * Prepare items
	 */
	public function prepare_items() {

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		/** STEdb API */
		$stedb_forms_api_client = new STEDB_Forms_Api_Client();

		/** api get lists report */
		$lists_report = $stedb_forms_api_client->get_lists_report();

		STEDB_Forms_WordPress_Admin::update_lists_report_in_wpdb( $lists_report );

		$this->items = json_decode( json_encode( $lists_report, JSON_FORCE_OBJECT ) );
	}

	/**
	 * Process bulk actions
	 */
	public function process_bulk_action() {
		global $wpdb;

		$action                   = $this->current_action();
		$stedb_forms_lists_report = isset( $_REQUEST['stedb_forms_lists_report'] ) ? array_map( 'absint', wp_unslash( $_REQUEST['stedb_forms_lists_report'] ) ) : '';

		/** check action */
		if ( ! $action ) {
			return;
		}

		/** check list reports */
		if ( ! is_array( $stedb_forms_lists_report ) ) {
			return;
		}

		/**
		 * Export
		 * reports
		 */
		if ( 'export' == $action ) {

			error_reporting( 0 );

			define( 'DONOTCACHEPAGE', true );

			/**
			 * Output configs
			 */
			if ( ! ini_get( 'safe_mode' ) ) {
				@set_time_limit( 0 );
			}

			if ( function_exists( 'apache_setenv' ) ) {
				@apache_setenv( 'no-gzip', 1 );
			}

			@session_write_close();

			if ( ini_get( 'zlib.output_compression' ) ) {
				@ini_set( 'zlib.output_compression', 'Off' );
			}

			@ob_end_clean();

			while ( ob_get_level() > 0 ) {
				@ob_end_clean();
			}

			if ( ! isset( $_POST['_wpnonce'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'bulk-' . $this->_args['plural'] ) ) {
				wp_die( esc_html__( 'Sorry, you are not allowed to process bulk actions.', 'stedb-forms' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Sorry, you are not allowed to manage this STEdb Forms actions.', 'stedb-forms' ) );
			}

			/** get list reports from wpdb */
			$list_reports = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "stedb_forms_list_reports WHERE id IN(" . implode( ', ', array_fill( 0, sizeof( $stedb_forms_lists_report ), '%d' ) ) . ")", $stedb_forms_lists_report ) );

			/** STEdb API */
			$stedb_forms_api_client = new STEDB_Forms_Api_Client();

			/** zip */
			$zipname = 'export.zip';
			$zip     = new ZipArchive;
			$zip->open( $zipname, ZipArchive::CREATE );

			foreach ( $list_reports as $list_report ) {
				$file = sprintf( 'list_%d.csv', $list_report->id );
				$fp   = fopen( $file, 'w' );

				$subscribers = $stedb_forms_api_client->get_subscribers_report( $list_report->id, null, $list_report->count );

				foreach ( $subscribers as $subscriber ) {
					fputcsv( $fp, $subscriber );
				}

				fclose( $fp );

				$zip->addFile( $file );
			}
			$zip->close();

			header( 'Content-Type: application/zip' );
			header( 'Content-disposition: attachment; filename=' . $zipname );
			header( 'Content-Length: ' . filesize( $zipname ) );
			readfile( $zipname );
			exit;

			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/zip' );
			header( 'Content-Disposition: attachment; filename="' . $zipname . '"' );
			header( 'Content-Length: ' . filesize( $zipname ) );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Connection: Keep-Alive' );
			header( 'Expires: 0' );
			header( 'Cache-Control: no-cache, no-store, must-revalidate' );
			header( 'Pragma: no-cache' );
			readfile( $zipname );
			exit;
		}
	}
}