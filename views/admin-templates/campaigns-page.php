<?php
defined( 'ABSPATH' ) or die();

/** @var STEDB_Forms_Campaigns_List_Table $stedb_forms_campaigns_list_table */
?>

<div class="stedb-forms-content wrap">

    <h1 class="wp-heading-inline">
		<?php esc_html_e( 'Campaigns', 'stedb-forms' ); ?>
    </h1>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-add-campaign.php' ) ); ?>"
       class="page-title-action">
		<?php esc_html_e( 'Add New', 'stedb-forms' ); ?>
    </a>

    <form id="stedb-forms-campaigns-form" method="post">
		<?php $stedb_forms_campaigns_list_table->display(); ?>
    </form>
</div>