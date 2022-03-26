<?php
defined( 'ABSPATH' ) or die();

require_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-premium-payments-list-table.php';

/** @var STEDB_Forms_Lists_Report_List_Table $stedb_forms_premium_payments_list_table */
$stedb_forms_premium_payments_list_table = new STEDB_Forms_Premium_Payments_List_Table();

/** prepare items */
$stedb_forms_premium_payments_list_table->prepare_items();
?>

<div class="stedb-forms-content wrap">

    <h1 class="wp-heading">
		<?php esc_html_e( 'Premium', 'stedb-forms' ); ?>
    </h1>

    <ul class="subsubsub">
        <li class="premium-plans">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-premium.php&subpage=plans' ) ); ?>">
				<?php esc_html_e( 'Plans', 'stedb-forms' ); ?>
            </a> |
        </li>
        <li class="premium-payments">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-premium.php&subpage=payments' ) ); ?>"
               class="current" aria-current="page">
				<?php esc_html_e( 'Payments', 'stedb-forms' ); ?>
            </a>
        </li>
    </ul>
    <br/>

    <div id="stedb-forms-premium-payments-table">
		<?php $stedb_forms_premium_payments_list_table->display(); ?>
    </div>
</div>