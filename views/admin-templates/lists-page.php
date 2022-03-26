<?php
defined( 'ABSPATH' ) or die();

/** @var STEDB_Forms_List_Table $stedb_forms_list_table */
?>

<div class="stedb-forms-content wrap">

    <h1 class="wp-heading-inline">
		<?php esc_html_e( 'Forms', 'stedb-forms' ); ?>
    </h1>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-add-form.php' ) ); ?>"
       class="page-title-action">
		<?php esc_html_e( 'Add New', 'stedb-forms' ); ?>
    </a>

    <form id="stedb-forms-lists-form" method="post">
		<?php $stedb_forms_list_table->display(); ?>
    </form>
</div>