<?php

/**
 * Class STEDB_Forms_Campaign_Reports_List_Table
 */
class STEDB_Forms_Campaigns_Report_List_Table extends WP_List_Table {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'stedb_forms_campaign_report',
			'plural'   => 'stedb_forms_campaigns_report',
			'ajax'     => false,
		) );
	}

	/**
	 * Get table classes
	 * @return array
	 */
	protected function get_table_classes() {
		return array( 'widefat', 'stedb-forms-campaigns-report', 'striped' );
	}

	/**
	 * get_columns function
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'campaign_name' => esc_html__( 'Campaign Name', 'stedb-forms' ),
			'form_name'     => esc_html__( 'Form Name', 'stedb-forms' ),
			'sent'          => esc_html__( 'Message Sent', 'stedb-forms' ),
			'opened'        => esc_html__( 'Openers', 'stedb-forms' ),
			'clicked'       => esc_html__( 'Clickers', 'stedb-forms' ),
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
	 * The campaign column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_campaign_name( $item ) {
		return $item->name;
	}

	/**
	 * The form name column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_form_name( $item ) {
		return $item->list_name;
	}

	/**
	 * The sent column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_sent( $item ) {
		return $item->sent;
	}

	/**
	 * The opened column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_opened( $item ) {
		return $item->opened;
	}

	/**
	 * The clicked column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_clicked( $item ) {
		return $item->clicked;
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

			<?php if ( 'top' == $which ) : ?>
                <div class="alignleft actions">
					<?php
					$date_start = isset( $_REQUEST['date_start'] ) ? $_REQUEST['date_start'] : date( 'm-d-Y', strtotime( '-1 year' ) );
					$date_end   = isset( $_REQUEST['date_end'] ) ? $_REQUEST['date_end'] : date( 'm-d-Y', strtotime( 'now' ) );
					?>
                    <label for="stedb-forms-campaigns-report-date-start" class="stedb-forms-tablenav-label">
						<?php esc_html_e( 'Start Date:', 'stedb-forms' ); ?>
                    </label>
                    <input type="text" name="date_start" id="stedb-forms-campaigns-report-date-start"
                           value="<?php echo esc_attr( $date_start ); ?>">

                    <label for="stedb-forms-campaigns-report-date-end" class="stedb-forms-tablenav-label">
						<?php esc_html_e( 'Start Date:', 'stedb-forms' ); ?>
                    </label>
                    <input type="text" name="date_end" id="stedb-forms-campaigns-report-date-end"
                           value="<?php echo esc_attr( $date_end ); ?>">

                    <input type="hidden" name="page" value="stedb-forms-campaigns-report"/>
                    <input type="submit" class="button" value="<?php esc_attr_e( 'Filter', 'stedb-forms' ); ?>"/>
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

		$date_start = isset( $_REQUEST['date_start'] ) ? sanitize_text_field( $_REQUEST['date_start'] ) : date( 'm-d-Y', strtotime( '-1 year' ) );
		$date_end   = isset( $_REQUEST['date_end'] ) ? sanitize_text_field( $_REQUEST['date_end'] ) : date( 'm-d-Y', strtotime( 'now' ) );

		/** STEdb API */
		$stedb_forms_api_client = new STEDB_Forms_Api_Client();

		/** api get campaigns report */
		$campaigns_report = $stedb_forms_api_client->get_campaigns_report( $date_start, $date_end );

		$this->items = json_decode( json_encode( $campaigns_report, JSON_FORCE_OBJECT ) );
	}
}