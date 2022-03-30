<?php
defined( 'ABSPATH' ) or die();

global $wpdb;

/** check form id */
if ( ! isset( $_REQUEST['list_id'] ) ) {
	return;
}

/** get list id */
$list_id = absint( wp_unslash( $_REQUEST['list_id'] ) );

/** get list (ie form) from wpdb */
$stedb_forms_list = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'stedb_forms_lists WHERE list_id = %d LIMIT 1', $list_id ) );

/** subscribers report list table */
require_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-subscribers-report-list-table.php';

/** @var STEDB_Forms_Subscribers_Report_List_Table $stedb_forms_subscribers_report_list_table */
$stedb_forms_subscribers_report_list_table = new STEDB_Forms_Subscribers_Report_List_Table( $list_id );

/** prepare items */
$stedb_forms_subscribers_report_list_table->prepare_items();

/** get list report from wpdb */
$stedb_forms_list_report = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'stedb_forms_list_reports WHERE id = %d LIMIT 1', $list_id ) );

/** format stat */
$stedb_forms_list_report->stat = json_decode( $stedb_forms_list_report->stat, true );

/** set chart data */
$chart_data = array();

foreach ( $stedb_forms_list_report->stat as $id => $stat ) {
	$data = array(
		'name'  => $id,
		'count' => intval( $stat ),
		'y'     => intval( $stat ) / $stedb_forms_list_report->count * 100,
	);

	switch ( $id ) {
		case 'google':
			$data['color'] = '#df4b38';
			break;
		case 'yahoo':
			$data['color'] = '#6b3695';
			break;
		case 'linkedin':
			$data['color'] = '#117bb8';
			break;
	}

	$chart_data[] = $data;
}
?>

<div class="stedb-forms-content wrap">

    <h1 class="wp-heading">
		<?php esc_html_e( 'Reports', 'stedb-forms' ); ?>
    </h1>

	<?php if ( ! empty( $stedb_forms_list ) ): ?>
        <h3 class="title">
			<?php printf( esc_html__( 'Form: #%d %s', 'stedb-forms' ), $stedb_forms_list->id, $stedb_forms_list->name ); ?>
        </h3>
	<?php endif; ?>

    <ul class="subsubsub">
        <li class="lists-report">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-reports.php&subpage=lists' ) ); ?>">
				<?php esc_html_e( 'Form Report', 'stedb-forms' ); ?>
            </a> |
        </li>
        <li class="subscribers-report">
            <a href="<?php echo esc_url( admin_url( add_query_arg( array(
				'page'    => 'stedb-forms-reports.php',
				'subpage' => 'subscribers',
				'list_id' => absint( wp_unslash( $_GET['list_id'] ) ),
			), 'admin.php' ) ) ); ?>"
               class="current" aria-current="page">
				<?php esc_html_e( 'Subscribers Report', 'stedb-forms' ); ?>
            </a> |
        </li>
        <li class="campaigns-report">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-reports.php&subpage=campaigns' ) ); ?>">
				<?php esc_html_e( 'Send Email Report', 'stedb-forms' ); ?>
            </a>
        </li>
    </ul>
    <br/>

    <div id="stedb-forms-subscribers-report-table">
		<?php $stedb_forms_subscribers_report_list_table->display(); ?>
    </div>

    <br/>

    <h3 class="title">
		<?php esc_html_e( 'Summary of leads generated per sending button', 'stedb-forms' ); ?>
    </h3>
    <h4>
		<?php printf( esc_html__( 'Total leads: %d', 'stedb-forms' ), $stedb_forms_list_report->count ); ?>
    </h4>

    <br/>

    <div class="stedb-forms-subscribers-report-chart">
        <div id="stedb-forms-subscribers-report-chart-canvasjs" style="height: 400px; width: 100%;"></div>
    </div>
</div>

<script>
    window.onload = function () {

        var chart = new CanvasJS.Chart('stedb-forms-subscribers-report-chart-canvasjs', {
            animationEnabled: true,

            backgroundColor: null,

            legend: {
                markerMargin: 8,
                itemWidth: 150,
                itemWrap: false,
                verticalAlign: 'center',
                horizontalAlign: 'right'
            },

            data: [{
                type: 'pie',
                yValueFormatString: "#,##0.00\"%\"",
                indexLabel: '{name} ({y})',
                showInLegend: true,
                legendText: '{name}: {count}',
                dataPoints: <?php echo json_encode( $chart_data, JSON_NUMERIC_CHECK ); ?>
            }]
        });
        chart.render();
    }
</script>