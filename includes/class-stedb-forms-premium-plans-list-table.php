<?php

/**
 * Class STEDB_Forms_Premium_Plans_List_Table
 */
class STEDB_Forms_Premium_Plans_List_Table extends WP_List_Table {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'stedb_forms_premium_plan',
			'plural'   => 'stedb_forms_premium_plans',
			'ajax'     => false,
		) );
	}

	/**
	 * Get table classes
	 * @return array
	 */
	protected function get_table_classes() {
		return array( 'widefat', 'stedb-forms-premium-plans', 'striped' );
	}

	/**
	 * get_columns function
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'name'          => esc_html__( 'Plan Name', 'stedb-forms' ),
			'price'         => esc_html__( 'Price', 'stedb-forms' ),
			'emails_amount' => esc_html__( 'Emails Amount', 'stedb-forms' ),
			'volume_amount' => esc_html__( 'Volume Amount', 'stedb-forms' ),
			'phone_support' => esc_html__( 'Phone Support', 'stedb-forms' ),
			'email_support' => esc_html__( 'Email Support', 'stedb-forms' ),
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
	public function column_name( $item ) {

		$view_link = add_query_arg( array(
			'page'    => 'stedb-forms-premium.php',
			'subpage' => 'plan',
			'id'      => $item->plan_id,
		), 'admin.php' );

		return sprintf( '<a href="%s">%s</a>', esc_url( $view_link ), esc_html( $item->name ) );
	}

	/**
	 * The price column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_price( $item ) {
		return sprintf( __( '%.2f USD/month', 'stedb-forms' ), $item->price );
	}

	/**
	 * The email amount column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_emails_amount( $item ) {
		/** default text */
		if ( empty( $item->emails_amount ) ) {
			return '-';
		}

		return number_format_i18n( $item->emails_amount );
	}

	/**
	 * The volume amount column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_volume_amount( $item ) {
		/** default text */
		if ( empty( $item->volume_amount ) ) {
			return '-';
		}

		return number_format_i18n( $item->volume_amount );
	}

	/**
	 * The phone support column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_phone_support( $item ) {
		if ( $item->phone_support ) {
			return sprintf( __( '%s Yes', 'stedb-forms' ), '<span class="dashicons dashicons-yes"></span>' );
		} else {
			return sprintf( __( '%s No', 'stedb-forms' ), '<span class="dashicons dashicons-no"></span>' );
		}
	}

	/**
	 * The email support column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_email_support( $item ) {
		if ( $item->email_support ) {
			return sprintf( __( '%s Yes', 'stedb-forms' ), '<span class="dashicons dashicons-yes"></span>' );
		} else {
			return sprintf( __( '%s No', 'stedb-forms' ), '<span class="dashicons dashicons-no"></span>' );
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

		/** api get plans */
		$plans = $stedb_forms_api_client->get_plans();

		$this->items = json_decode( json_encode( $plans, JSON_FORCE_OBJECT ) );
	}
}