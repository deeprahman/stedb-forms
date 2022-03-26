<?php
defined( 'ABSPATH' ) or die();

/** @var STEDB_Forms_Entries_List_Table $stedb_forms_entries_list_table */
/** @var object $stedb_forms_list */
?>

<div class="stedb-forms-content wrap">

    <h1 class="wp-heading-inline">
		<?php esc_html_e( 'Entries', 'stedb-forms' ); ?>
    </h1>

    <h3 class="title">
		<?php printf( esc_html__( 'Form: #%d %s', 'stedb-forms' ), $stedb_forms_list->id, $stedb_forms_list->name ); ?>
    </h3>

    <form id="stedb-forms-entries-form" method="post">
		<?php $stedb_forms_entries_list_table->display(); ?>
    </form>
</div>