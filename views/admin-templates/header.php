<?php defined( 'ABSPATH' ) or die(); ?>

<?php /**  @var bool $stedb_forms_auth */ ?>

<div id="stedb-forms-header" class="stedb-forms-header">
    <img class="stedb-forms-header-logo"
         src="<?php echo esc_url( STEDB_FORMS_DIR_URL . '/assets/img/stedb-logo.png' ); ?>"
         alt="<?php esc_attr_e( 'STEdb Logo', 'stedb-forms' ); ?>">
    <h3 class="stedb-forms-header-name"><?php esc_html_e( 'Forms', 'stedb-forms' ); ?></h3>
</div>

<div class="stedb-forms-header-tabs">
    <ul>
        <li>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms.php' ) ); ?>"
               class="<?php echo esc_attr( isset( $_GET['page'] ) && 'stedb-forms.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ? 'active' : '' ); ?>">
				<?php esc_html_e( 'STEdb Forms', 'stedb-forms' ); ?>
            </a>
        </li>

		<?php if ( $stedb_forms_auth ): ?>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-lists.php' ) ); ?>"
                   class="<?php echo esc_attr( isset( $_GET['page'] ) && 'stedb-forms-lists.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ? 'active' : '' ); ?>">
					<?php esc_html_e( 'All Forms', 'stedb-forms' ); ?>
                </a>
            </li>
		<?php endif; ?>

		<?php if ( isset( $_GET['page'] ) ): ?>
			<?php if ( 'stedb-forms-edit-form.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ): ?>
				<?php if ( $stedb_forms_auth ): ?>
                    <li>
                        <a href="<?php echo esc_url( admin_url( add_query_arg( array(
							'page' => sanitize_file_name( wp_unslash( $_GET['page'] ) ),
							'id'   => absint( wp_unslash( $_GET['id'] ) ),
						), 'admin.php' ) ) ); ?>"
                           class="active">
							<?php esc_html_e( 'Edit Form', 'stedb-forms' ); ?>
                        </a>
                    </li>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( isset( $_GET['page'] ) && isset( $_GET['form_id'] ) ): ?>
			<?php if ( 'stedb-forms-entries.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ): ?>
				<?php if ( $stedb_forms_auth ): ?>
                    <li>
                        <a href="<?php echo esc_url( admin_url( add_query_arg( array(
							'page'    => sanitize_file_name( wp_unslash( $_GET['page'] ) ),
							'form_id' => absint( wp_unslash( $_GET['form_id'] ) ),
						), 'admin.php' ) ) ); ?>"
                           class="active">
							<?php esc_html_e( 'All Entries', 'stedb-forms' ); ?>
                        </a>
                    </li>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( isset( $_GET['page'] ) && isset( $_GET['form_id'] ) ): ?>
			<?php if ( 'stedb-forms-edit-entry.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ): ?>
				<?php if ( $stedb_forms_auth ): ?>
                    <li>
                        <a href="<?php echo esc_url( admin_url( add_query_arg( array(
							'page'    => 'stedb-forms-entries.php',
							'form_id' => absint( wp_unslash( $_GET['form_id'] ) ),
						), 'admin.php' ) ) ); ?>">
							<?php esc_html_e( 'All Entries', 'stedb-forms' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url( admin_url( add_query_arg( array(
							'page'    => 'stedb-forms-edit-entry.php',
							'form_id' => absint( wp_unslash( $_GET['form_id'] ) ),
							'id'      => absint( wp_unslash( $_GET['id'] ) ),
						), 'admin.php' ) ) ); ?>"
                           class="active">
							<?php esc_html_e( 'Edit Entry', 'stedb-forms' ); ?>
                        </a>
                    </li>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( $stedb_forms_auth ): ?>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-add-form.php' ) ); ?>"
                   class="<?php echo esc_attr( isset( $_GET['page'] ) && 'stedb-forms-add-form.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ? 'active' : '' ); ?>">
					<?php esc_html_e( 'Add New Form', 'stedb-forms' ); ?>
                </a>
            </li>
		<?php endif; ?>

		<?php if ( $stedb_forms_auth ): ?>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-campaigns.php' ) ); ?>"
                   class="<?php echo esc_attr( isset( $_GET['page'] ) && 'stedb-forms-campaigns.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ? 'active' : '' ); ?>">
					<?php esc_html_e( 'All Campaigns', 'stedb-forms' ); ?>
                </a>
            </li>
		<?php endif; ?>

		<?php if ( $stedb_forms_auth ): ?>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-add-campaign.php' ) ); ?>"
                   class="<?php echo esc_attr( isset( $_GET['page'] ) && 'stedb-forms-add-campaign.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ? 'active' : '' ); ?>">
					<?php esc_html_e( 'Add Campaign', 'stedb-forms' ); ?>
                </a>
            </li>
		<?php endif; ?>

		<?php if ( $stedb_forms_auth ): ?>
            <li>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-reports.php' ) ); ?>"
                   class="<?php echo esc_attr( isset( $_GET['page'] ) && 'stedb-forms-reports.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ? 'active' : '' ); ?>">
					<?php esc_html_e( 'Reports', 'stedb-forms' ); ?>
                </a>
            </li>
		<?php endif; ?>

        <li>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-settings.php' ) ); ?>"
               class="<?php echo esc_attr( isset( $_GET['page'] ) && 'stedb-forms-settings.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ? 'active' : '' ); ?>">
				<?php esc_html_e( 'Settings', 'stedb-forms' ); ?>
            </a>
        </li>

		<?php if ( $stedb_forms_auth ): ?>
            <li class="align-right">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-premium.php' ) ); ?>"
                   class="<?php echo esc_attr( isset( $_GET['page'] ) && 'stedb-forms-premium.php' == sanitize_file_name( wp_unslash( $_GET['page'] ) ) ? 'active' : '' ); ?>">
                    <strong>
                        <span class="dashicons dashicons-warning"></span>
						<?php esc_html_e( 'Premium', 'stedb-forms' ); ?>
                    </strong>
                </a>
            </li>
		<?php endif; ?>
    </ul>
</div>