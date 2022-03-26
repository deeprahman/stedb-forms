<?php

/**
 * Class STEDB_Forms_Premium_Payments_List_Table
 */
class STEDB_Forms_Premium_Payments_List_Table extends WP_List_Table {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'stedb_forms_premium_payment',
			'plural'   => 'stedb_forms_premium_payments',
			'ajax'     => false,
		) );
	}

	/**
	 * Get table classes
	 * @return array
	 */
	protected function get_table_classes() {
		return array( 'widefat', 'stedb-forms-premium-payments', 'striped' );
	}

	/**
	 * get_columns function
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'id'     => esc_html__( 'ID', 'stedb-forms' ),
			'date'   => esc_html__( 'Date', 'stedb-forms' ),
			'amount' => esc_html__( 'Amount', 'stedb-forms' ),
			'status' => esc_html__( 'Status', 'stedb-forms' ),
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
	 * The name column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_id( $item ) {

		$view_link = add_query_arg( array(
			'page'    => 'stedb-forms-premium.php',
			'subpage' => 'payment',
			'id'      => $item->id,
		), 'admin.php' );

		return sprintf( '<a href="%s">#%d</a>', esc_url( $view_link ), esc_html( $item->id ) );
	}

	/**
	 * The date column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_date( $item ) {
		return $item->date;
	}

	/**
	 * The amount column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_amount( $item ) {
		return sprintf( __( '%.2f USD', 'stedb-forms' ), $item->amount );
	}

	/**
	 * The status column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_status( $item ) {
		if ( $item->status ) {
			return '<span class="dashicons dashicons-yes"></span>';
		} else {
			return '<span class="dashicons dashicons-no"></span>';
		}
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

		/** api get payments */
		$payments = $stedb_forms_api_client->get_payments();

		$this->items = json_decode( json_encode( $payments, JSON_FORCE_OBJECT ) );
	}
}