<?php defined( 'ABSPATH' ) or die(); ?>

<div class="stedb-forms-content wrap">
    <h2><?php esc_html_e( 'STEdb Settings', 'stedb-forms' ); ?></h2>
    <hr/>

    <h2 class="title">
		<?php esc_html_e( 'Account Reactivate', 'stedb-forms' ); ?>
    </h2>
    <p>
		<?php echo sprintf( esc_html__( 'An account has already been registered for this website. A verification code has been sent to %s.', 'stedb-forms' ), esc_html( get_bloginfo( 'admin_email' ) ) ); ?>
    </p>

    <h2 class="title">
		<?php esc_html_e( 'Please enter the 4-digit verification code we sent via E-mail:', 'stedb-forms' ); ?>
    </h2>

    <form id="stedb-forms-settings-account-reactivate-email-verify" method="post"
          action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

        <input type="hidden" name="action" value="stedb_forms_reactivate_account">
		<?php wp_nonce_field( 'stedb_forms_reactivate_account' ); ?>

        <input type="number" name="stedb_forms_code[]" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}"
               placeholder="0" required>
        <input type="number" name="stedb_forms_code[]" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}"
               placeholder="0" required>
        <input type="number" name="stedb_forms_code[]" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}"
               placeholder="0" required>
        <input type="number" name="stedb_forms_code[]" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}"
               placeholder="0" required>

        <p class="submit">
            <input type="submit" class="button-primary"
                   value="<?php esc_attr_e( 'Reactivate Account', 'stedb-forms' ); ?>"/>
        </p>
    </form>
</div>