<?php
defined( 'ABSPATH' ) or die();

global $wpdb;
?>

<?php
/**
 * get wp current user
 * @var WP_User $user
 */
$user = wp_get_current_user();

/**
 * get lists (ie forms)
 * @var object $stedb_forms_lists
 */
$stedb_forms_lists = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . 'stedb_forms_lists' . ";" );

/**
 * check campaign from email
 * with api
 */
$stedb_forms_campaign_verify_from_email_button_classes = array( 'button', 'stedb-forms-campaign-verify-from-email' );

/** STEdb API */
$stedb_forms_api_client = new STEDB_Forms_Api_Client();

/** api get campaigns check from email */
$stedb_forms_check_campaign_from_email = $stedb_forms_api_client->get_campaigns_check_from_email( 'grof.marton@gmail.com' );

/** check is confirmed and add class */
if ( ! is_wp_error( $stedb_forms_check_campaign_from_email ) ) {
	$stedb_forms_campaign_verify_from_email_button_classes = 'is-confirmed';
}
?>

<div class="stedb-forms-content wrap">
    <h2><?php esc_html_e( 'Add New Campaign', 'stedb-forms' ); ?></h2>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

        <input type="hidden" name="action" value="stedb_forms_add_campaign">
		<?php wp_nonce_field( 'stedb_forms_add_campaign' ); ?>

        <table class="form-table">
			<?php if ( ! empty( $stedb_forms_lists ) ): ?>
                <tr>
                    <th scope="row">
                        <label for="stedb-forms-campaign-list-id">
							<?php esc_html_e( 'Form List', 'stedb-forms' ); ?>
                        </label>
                    </th>
                    <td>
                        <select name="stedb_forms_campaign_list_id" id="stedb-forms-campaign-list-id"
                                class="regular-text">
                            <option hidden disabled selected>
								<?php esc_html_e( 'Select a Form List', 'stedb-forms' ); ?>
                            </option>
							<?php foreach ( $stedb_forms_lists as $stedb_forms_list ): ?>
                                <option value="<?php echo esc_attr( $stedb_forms_list->list_id ); ?>">
									<?php echo esc_html( '#' . $stedb_forms_list->list_id . ' ' . $stedb_forms_list->name ); ?>
                                </option>
							<?php endforeach; ?>
                        </select>
                    </td>
                </tr>
			<?php else: ?>
                <tr>
                    <th scope="row">
                        <label>
							<?php esc_html_e( 'Form List', 'stedb-forms' ); ?>
                        </label>
                    </th>
                    <td>
                        <p>
							<?php esc_html_e( 'No list found. Add a form to be able to send campaign:', 'stedb-forms' ); ?>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-add-form.php' ) ); ?>">
								<?php esc_html_e( 'Add New Form', 'stedb-forms' ); ?>
                            </a>
                        </p>
                    </td>
                </tr>
			<?php endif; ?>
            <tr>
                <th scope="row">
                    <label for="stedb-forms-campaign-from-name">
						<?php esc_html_e( 'From Name', 'stedb-forms' ); ?>
                    </label>
                </th>
                <td>
                    <input type="text" name="stedb_forms_campaign_from_name" id="stedb-forms-campaign-from-name"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="stedb-forms-campaign-from-email">
						<?php esc_html_e( 'From Email', 'stedb-forms' ); ?>
                    </label>
                </th>
                <td>
                    <input type="text" name="stedb_forms_campaign_from_email" id="stedb-forms-campaign-from-email"
                           class="regular-text" value="<?php echo esc_attr( $user->user_email ); ?>">

                    <button type="button"
                            class="<?php echo esc_attr( implode( ' ', $stedb_forms_campaign_verify_from_email_button_classes ) ); ?>">
                        <span class="spinner"></span>
						<?php esc_html_e( 'Verify Email', 'stedb-forms' ); ?>
                    </button>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="stedb-forms-campaign-subject">
						<?php esc_html_e( 'Subject', 'stedb-forms' ); ?>
                    </label>
                </th>
                <td>
                    <input type="text" name="stedb_forms_campaign_subject" id="stedb-forms-campaign-subject"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="stedb-forms-campaign-content">
						<?php esc_html_e( 'Content', 'stedb-forms' ); ?>
                    </label>
                </th>
                <td>
					<?php
					wp_editor( '', 'stedb-forms-campaign-content', array(
						'textarea_name' => 'stedb_forms_campaign_content',
						'media_buttons' => false,
						'teeny'         => true,
						'textarea_rows' => 10,
					) );
					?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                </th>
                <td>
                    <p class="submit">
                        <button type="submit" class="button-primary" name="campaign_type" value="0">
							<?php esc_html_e( 'Email Entire List', 'stedb-forms' ); ?>
                        </button>
                        <button type="submit" class="button-primary" name="campaign_type" value="1">
							<?php esc_html_e( 'Run Autoresponder', 'stedb-forms' ); ?>
                        </button>
                        <input type="button" class="button"
                               value="<?php esc_attr_e( 'Preview', 'stedb-forms' ); ?>"/>
                        <input type="reset" class="button"
                               value="<?php esc_attr_e( 'Cancel', 'stedb-forms' ); ?>"/>
                    </p>
                </td>
            </tr>
        </table>
    </form>
</div>
