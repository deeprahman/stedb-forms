<?php

/**
 * Class STEDB_Forms_Entries_List_Table
 */
class STEDB_Forms_Entries_List_Table extends WP_List_Table {

	private $form_id = null;
	private $filter_month = '';

	/** @var bool $display_delete_message */
	private $display_delete_message = false;

	/**
	 * Constructor
	 *
	 * @param null|int $form_id
	 */
	public function __construct( $form_id = null ) {
		parent::__construct( array(
			'singular' => 'stedb_forms_entry',
			'plural'   => 'stedb_forms_entries',
			'ajax'     => false,
		) );

		/** set form id */
		if ( ! empty( $form_id ) ) {
			$this->form_id = intval( $form_id );
		}

		$this->filter_month = ! empty( $_REQUEST['filter_month'] ) ? sanitize_text_field( $_REQUEST['filter_month'] ) : '';
	}

	/**
	 * Get table classes
	 * @return array
	 */
	protected function get_table_classes() {
		return array( 'widefat', 'stedb-forms-entries' );
	}

	/**
	 * generates the required HTML for a list of row action links
	 *
	 * @param string[] $actions an array of action links
	 * @param bool $always_visible whether the actions should be always visible
	 *
	 * @return string The HTML for the row actions
	 */
	protected function row_actions( $actions, $always_visible = false ) {
		$action_count = count( $actions );

		if ( ! $action_count ) {
			return '';
		}

		$out = '<div class="row-actions visible">';

		$i = 0;

		foreach ( $actions as $action => $link ) {
			++ $i;

			$sep = ( $i < $action_count ) ? ' | ' : '';

			$out .= "<span class='$action'>$link$sep</span>";
		}

		$out .= '</div>';

		return $out;
	}

	/**
	 * get_columns function
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'email'   => esc_html__( 'Email', 'stedb-forms' ),
			'date'    => esc_html__( 'Date', 'stedb-forms' ),
			'actions' => esc_html__( 'Actions', 'stedb-forms' ),
		);

		return $columns;
	}

	/**
	 * Add bulk actions
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete' => esc_html__( 'Delete', 'stedb-forms' ),
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
		return sprintf( '<input type="checkbox" name="stedb_forms_entries[]" value="%s" />', $item->id );
	}

	/**
	 * The email column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_email( $item ) {

		$edit_link = add_query_arg( array(
			'page'    => 'stedb-forms-edit-entry.php',
			'form_id' => $item->form_id,
			'id'      => $item->id,
		), 'admin.php' );

		return sprintf( '<a href="%s">%s</a>', esc_url( $edit_link ), esc_html( $item->email ) );
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
	 * The actions column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_actions( $item ) {

		$edit_link = add_query_arg( array(
			'page'    => 'stedb-forms-edit-entry.php',
			'form_id' => $item->form_id,
			'id'      => $item->id,
		), 'admin.php' );

		$delete_link = wp_nonce_url( add_query_arg( array(
			'page'   => sanitize_file_name( wp_unslash( $_GET['page'] ) ),
			'action' => 'delete_entry',
			'id'     => $item->id,
			'paged'  => $this->get_pagenum(),
		), 'admin.php' ), $item->id . '_entry_delete' );

		$actions = array(
			'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( $edit_link ), esc_html__( 'Edit', 'stedb-forms' ) ),
			'delete' => sprintf( '<a href="%s">%s</a>', esc_url( $delete_link ), esc_html__( 'Delete', 'stedb-forms' ) ),
		);

		return $this->row_actions( $actions, true );
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

		if ( 'top' == $which && true === $this->display_delete_message ):
			?>
            <div id="message" class="updated notice notice-success">
                <p><?php _e( 'Entries deleted', 'stedb-forms' ); ?></p>
            </div>
		<?php
		endif;
		?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

			<?php if ( 'top' == $which ) : ?>
                <div class="alignleft actions">
					<?php
					global $wpdb, $wp_locale;

					$table_name  = $wpdb->prefix . 'stedb_forms_entries';
					$months      = $wpdb->get_results( "SELECT DISTINCT YEAR( date ) AS year, MONTH( date ) AS month FROM " . $table_name . " ORDER BY date DESC" );
					$month_count = count( $months );

					if ( $month_count && ! ( 1 == $month_count && 0 == $months[0]->month ) ) :
						$m = isset( $_REQUEST['filter_month'] ) ? wp_unslash( $_REQUEST['filter_month'] ) : 0;
						?>
                        <label for="filter-month"></label>
                        <select name="filter_month" id="filter-month">
                            <option <?php selected( $m, 0 ); ?> value='0'>
								<?php esc_html_e( 'Show all dates', 'stedb-forms' ); ?>
                            </option>
							<?php
							foreach ( $months as $arc_row ) {
								if ( 0 == $arc_row->year ) {
									continue;
								}

								$month = zeroise( $arc_row->month, 2 );
								$year  = $arc_row->year;

								printf( "<option %s value='%s'>%s</option>", selected( $m, $year . '-' . $month, false ), esc_attr( $year . '-' . $month ), sprintf( esc_html__( '%1$s %2$d' ), esc_html( $wp_locale->get_month( $month ) ), esc_html( $year ) ) );
							}
							?>
                        </select>

                        <input type="hidden" name="page" value="stedb-forms-entries"/>
                        <input type="submit" class="button" value="<?php esc_attr_e( 'Filter', 'stedb-forms' ); ?>"/>
					<?php
					endif;
					?>
                </div>
			<?php endif; ?>

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

		/** check form id */
		if ( empty( $this->form_id ) ) {
			return;
		}

		$table_name = $wpdb->prefix . 'stedb_forms_entries';

		$per_page     = $this->get_items_per_page( 'stedb_forms_entries_per_page' );
		$current_page = $this->get_pagenum();

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		$query_where = sprintf( "WHERE form_id = %d ", $this->form_id );

		if ( $this->filter_month ) {
			$query_where .= " AND date >= '" . date( 'Y-m-01', strtotime( $this->filter_month ) ) . " 00:00:00' ";
			$query_where .= " AND date <= '" . date( 'Y-m-t', strtotime( $this->filter_month ) ) . " 23:59:59' ";
		}

		$total_items = $wpdb->get_var( "SELECT COUNT(id) FROM " . $table_name . " " . $query_where . ";" );
		$this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $table_name . " " . $query_where . " ORDER BY date DESC LIMIT %d, %d;", ( $current_page - 1 ) * $per_page, $per_page ) );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ( ( $total_items > 0 ) ? ceil( $total_items / $per_page ) : 1 ),
		) );
	}

	/**
	 * Process bulk actions
	 */
	public function process_bulk_action() {
		global $wpdb;

		$action              = $this->current_action();
		$stedb_forms_entries = isset( $_REQUEST['stedb_forms_entries'] ) ? array_map( 'absint', wp_unslash( $_REQUEST['stedb_forms_entries'] ) ) : '';

		/** check action */
		if ( ! $action ) {
			return;
		}

		/** check entries */
		if ( ! is_array( $stedb_forms_entries ) ) {
			return;
		}

		/**
		 * Delete
		 * forms
		 */
		if ( 'delete' == $action ) {

			if ( ! isset( $_POST['_wpnonce'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'bulk-' . $this->_args['plural'] ) ) {
				wp_die( esc_html__( 'Sorry, you are not allowed to process bulk actions.', 'stedb-forms' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Sorry, you are not allowed to manage this STEdb Forms actions.', 'stedb-forms' ) );
			}

			//todo: delete from api

			/** delete forms */
			$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "stedb_forms_entries WHERE id IN(" . implode( ', ', array_fill( 0, sizeof( $stedb_forms_entries ), '%d' ) ) . ")", $stedb_forms_entries ) );

			$this->display_delete_message = true;
		}
	}
}