<?php
defined( 'ABSPATH' ) or die();

require_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-campaigns-report-list-table.php';

/** @var STEDB_Forms_Campaigns_Report_List_Table $stedb_forms_campaigns_report_list_table */
$stedb_forms_campaigns_report_list_table = new STEDB_Forms_Campaigns_Report_List_Table();

/** prepare items */
$stedb_forms_campaigns_report_list_table->prepare_items();
?>

<div class="stedb-forms-content wrap">

    <h1 class="wp-heading">
		<?php esc_html_e( 'Reports', 'stedb-forms' ); ?>
    </h1>

    <ul class="subsubsub">
        <li class="lists-report">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-reports.php&subpage=lists' ) ); ?>">
				<?php esc_html_e( 'Form Report', 'stedb-forms' ); ?>
            </a> |
        </li>
        <li class="campaigns-report">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-reports.php&subpage=campaigns' ) ); ?>"
               class="current" aria-current="page">
				<?php esc_html_e( 'Send Email Report', 'stedb-forms' ); ?>
            </a>
        </li>
    </ul>

    <form id="stedb-forms-campaigns-report" method="post">
		<?php $stedb_forms_campaigns_report_list_table->display(); ?>
    </form>
</div>