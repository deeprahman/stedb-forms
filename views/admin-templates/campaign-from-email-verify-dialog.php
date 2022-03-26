<?php defined( 'ABSPATH' ) or die(); ?>

<div id="stedb-forms-campaign-from-email-verify-dialog" class="hidden"
     data-title="<?php esc_attr_e( 'Verify Email', 'stedb-forms' ); ?>">

    <h3>
		<?php esc_html_e( 'Please enter the 4-digit verification code we sent via E-mail:', 'stedb-forms' ); ?>
    </h3>

    <p class="stedb-forms-campaign-from-email"></p>

    <form id="stedb-forms-campaign-from-email-verify">
        <input type="number" name="stedb_forms_code[]" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}"
               placeholder="0" required>
        <input type="number" name="stedb_forms_code[]" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}"
               placeholder="0" required>
        <input type="number" name="stedb_forms_code[]" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}"
               placeholder="0" required>
        <input type="number" name="stedb_forms_code[]" maxlength="1" size="1" min="0" max="9" pattern="[0-9]{1}"
               placeholder="0" required>
    </form>
</div>