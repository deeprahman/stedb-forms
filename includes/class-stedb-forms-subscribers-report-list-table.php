<?php

/**
 * Class STEDB_Forms_Subscribers_Report_List_Table
 */
class STEDB_Forms_Subscribers_Report_List_Table extends WP_List_Table {

	private $list_id = null;

	/**
	 * Constructor
	 *
	 * @param null|int $list_id
	 */
	public function __construct( $list_id = null ) {
		parent::__construct( array(
			'singular' => 'stedb_forms_subscriber_report',
			'plural'   => 'stedb_forms_subscribers_report',
			'ajax'     => false,
		) );

		/** set form id */
		if ( ! empty( $list_id ) ) {
			$this->list_id = intval( $list_id );
		}
	}

	/**
	 * Get table classes
	 * @return array
	 */
	protected function get_table_classes() {
		return array( 'widefat', 'stedb-forms-subscribers-report', 'striped' );
	}

	/**
	 * get_columns function
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'email'           => esc_html__( 'Email', 'stedb-forms' ),
			'submission_date' => esc_html__( 'Submission Date', 'stedb-forms' ),
			'social_media'    => esc_html__( 'Sending Button', 'stedb-forms' ),
		);

		return $columns;
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
	 * The email column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_email( $item ) {
		return $item->email;
	}

	/**
	 * The submission date column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_submission_date( $item ) {
		return $item->subscription_date;
	}

	/**
	 * The social media column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_social_media( $item ) {
		return $item->social_media;
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
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>
            <br class="clear"/>
        </div>
		<?php
	}

	/**
	 * Prepare items
	 */
	public function prepare_items() {
		global $wpdb;

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		$per_page     = 10;
		$current_page = $this->get_pagenum();

		$total_items = $wpdb->get_var( $wpdb->prepare( 'SELECT count FROM ' . $wpdb->prefix . 'stedb_forms_list_reports WHERE id = %d LIMIT 1', absint( $this->list_id ) ) );

		/** STEdb API */
		$stedb_forms_api_client = new STEDB_Forms_Api_Client();

		/** api get subscribers report */
		$subscribers_report = $stedb_forms_api_client->get_subscribers_report( $this->list_id, ( $current_page - 1 ) * $per_page, $per_page );

		$this->items = json_decode( json_encode( $subscribers_report, JSON_FORCE_OBJECT ) );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ( ( $total_items > 0 ) ? ceil( $total_items / $per_page ) : 1 ),
		) );
	}
}