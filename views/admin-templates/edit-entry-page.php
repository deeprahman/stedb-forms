<?php defined( 'ABSPATH' ) or die(); ?>

<?php
/** @var object $stedb_forms_entry */
global $stedb_forms_entry;

/** @var object $stedb_forms_list */
?>

<div class="stedb-forms-content wrap">
    <h2><?php esc_html_e( 'Edit Entry', 'stedb-forms' ); ?></h2>

    <h3 class="title">
		<?php printf( esc_html__( 'Form: #%d %s', 'stedb-forms' ), $stedb_forms_list->id, $stedb_forms_list->name ); ?>
    </h3>

    <form id="stedb-forms-edit-entry" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

        <input type="hidden" name="action" value="stedb_forms_edit_entry">
        <input type="hidden" name="list_id"
               value="<?php echo esc_attr( $stedb_forms_list->list_id ); ?>">
        <input type="hidden" name="form_id"
               value="<?php echo esc_attr( isset( $_GET['form_id'] ) ? absint( wp_unslash( $_GET['form_id'] ) ) : '' ); ?>">
        <input type="hidden" name="id"
               value="<?php echo esc_attr( isset( $_GET['id'] ) ? absint( wp_unslash( $_GET['id'] ) ) : '' ); ?>">

		<?php wp_nonce_field( 'stedb_forms_edit_entry' ); ?>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">

                    <table class="form-table">
                        <tbody>

                        <tr>
                            <th scope="row">
                                <label>
									<?php esc_html_e( 'email' ); ?>
                                </label>
                            </th>
                            <td>
                                <p>
									<?php echo esc_html( $stedb_forms_entry->email ); ?>
                                </p>
                            </td>
                        </tr>

						<?php if ( ! empty( $stedb_forms_entry->custom_fields ) ): ?>
							<?php foreach ( $stedb_forms_entry->custom_fields as $custom_field_name => $custom_field_value ): ?>
                                <tr>
                                    <th scope="row">
                                        <label for="stedb-forms-entry-<?php echo esc_attr( $custom_field_name ); ?>">
											<?php echo esc_html( $custom_field_name ); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text"
                                               id="stedb-forms-entry-<?php echo esc_attr( $custom_field_name ); ?>"
                                               name="custom_fields[<?php echo esc_attr( $custom_field_name ); ?>]"
                                               class="regular-text"
                                               value="<?php echo esc_attr( $custom_field_value ); ?>">
                                    </td>
                                </tr>
							<?php endforeach; ?>
						<?php endif; ?>

                        <tr>
                            <th scope="row">
                                <label>
									<?php esc_html_e( 'date' ); ?>
                                </label>
                            </th>
                            <td>
                                <p>
									<?php echo esc_html( $stedb_forms_entry->date ); ?>
                                </p>
                            </td>
                        </tr>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <div class="clear"></div>

        <p class="submit">
            <button type="submit" class="button-primary">
				<?php esc_html_e( 'Save Entry', 'stedb-forms' ); ?>
            </button>
            <a href="<?php echo esc_url( admin_url( add_query_arg( array(
				'page'    => 'stedb-forms-entries.php',
				'form_id' => absint( wp_unslash( $_GET['form_id'] ) ),
			), 'admin.php' ) ) ); ?>" class="button">
				<?php esc_html_e( 'Back to All Entries', 'stedb-forms' ); ?>
            </a>
        </p>
    </form>
</div>
