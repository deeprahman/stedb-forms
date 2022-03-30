<?php defined( 'ABSPATH' ) or die(); ?>

<?php /**  @var bool $stedb_forms_auth */ ?>

<div class="stedb-forms-content wrap">

    <h1 class="wp-heading-inline">
		<?php esc_html_e( 'STEdb Forms', 'stedb-forms' ); ?>
    </h1>

    <div class="stedb-forms-welcome-panel">
        <div class="stedb-forms-welcome-panel-content">
            <h2>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec non eros elit.
            </h2>
            <p>
                Nullam in imperdiet purus. Vestibulum a lectus quis tellus posuere pellentesque. Cras molestie urna est.
                Quisque convallis nisi id diam consectetur, at consectetur dui finibus. In hac habitasse platea
                dictumst. Vivamus eu diam id dui iaculis ultrices.
            </p>
            <p>
                Donec eu porttitor urna, vitae rhoncus lectus. Donec id risus ac purus feugiat faucibus porttitor vel
                mauris. Mauris cursus, lectus eget vulputate sollicitudin, orci nulla rhoncus ligula, at venenatis
                tellus nisl sed metus. Integer sit amet nisl nec augue molestie condimentum. Nulla vitae scelerisque
                urna, eu tempor nunc. Quisque congue, erat non vulputate volutpat, nunc eros mattis leo, at porttitor
                odio nibh eget purus. Mauris ut rhoncus erat.
            </p>

            <div class="stedb-forms-welcome-panel-column-container">

				<?php if ( $stedb_forms_auth ): ?>
                    <div class="stedb-forms-welcome-panel-column">
                        <h3><?php esc_html_e( 'Forms', 'stedb-forms' ); ?></h3>
                        <ul>
                            <li>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-lists.php' ) ); ?>">
									<?php esc_html_e( 'All Forms', 'stedb-forms' ); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-add-form.php' ) ); ?>">
									<?php esc_html_e( 'Add New Form', 'stedb-forms' ); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
				<?php endif; ?>

				<?php if ( $stedb_forms_auth ): ?>
                    <div class="stedb-forms-welcome-panel-column stedb-forms-welcome-panel-last">
                        <h3><?php esc_html_e( 'Campaigns', 'stedb-forms' ); ?></h3>
                        <ul>
                            <li>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-campaigns.php' ) ); ?>">
									<?php esc_html_e( 'All Campaigns', 'stedb-forms' ); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-add-campaign.php' ) ); ?>">
									<?php esc_html_e( 'Add New Campaign', 'stedb-forms' ); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
				<?php endif; ?>

                <div class="stedb-forms-welcome-panel-column stedb-forms-welcome-panel-last">
                    <h3><?php esc_html_e( 'API', 'stedb-forms' ); ?></h3>
                    <p>
						<?php echo sprintf( esc_html__( 'Status: %s', 'stedb-forms' ), esc_html( $this->has_account() ? __( 'User is set', 'stedb-forms' ) : __( 'No user set', 'stedb-forms' ) ) ); ?>
                    </p>
                </div>
            </div>

        </div>
    </div>


    <div id="dashboard-widgets-wrap">
        <div id="dashboard-widgets" class="metabox-holder">
            <div id="postbox-container-1" class="postbox-container">
                <div class="meta-box-sortables">
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2 class="hndle"><?php esc_html_e( 'Forms', 'stedb-forms' ); ?></h2>
                        </div>
                        <div class="inside">
                            <h3>Aenean eget arcu hendrerit</h3>
                            <p>
                                Sed et erat et leo dictum venenatis sit amet ac nulla. Donec auctor hendrerit ex, quis
                                interdum magna aliquet eget. Orci varius natoque penatibus et magnis dis parturient
                                montes, nascetur ridiculus mus.
                            </p>
                            <h3>Suspendisse vitae volutpat turpis</h3>
                            <p>
                                Sed vehicula orci nec sodales rutrum. Pellentesque dignissim metus non massa efficitur
                                vehicula. Vivamus ut eros vel urna venenatis fringilla id vel mauris.
                            </p>

                            <p class="button-container">
                                <a class="button button-primary" href="">
									<?php esc_html_e( 'Learn more about Forms', 'stedb-forms' ); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="postbox-container-2" class="postbox-container">
                <div class="meta-box-sortables">
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2 class="hndle"><?php esc_html_e( 'Campaigns', 'stedb-forms' ); ?></h2>
                        </div>
                        <div class="inside">
                            <h3><?php esc_html_e( 'Regular Email', 'stedb-forms' ); ?></h3>
                            <p>

                                Duis vitae orci eget nunc iaculis feugiat. Suspendisse id fermentum dolor. Cras sed
                                libero urna. Aliquam erat volutpat. Vivamus consectetur enim massa, sit amet dictum
                                dolor pellentesque vel.
                            </p>
                            <h3><?php esc_html_e( 'Autoresponder', 'stedb-forms' ); ?></h3>
                            <p>
                                Proin magna est, pharetra at dui vel, suscipit pharetra velit. Morbi luctus arcu a
                                sapien aliquet, in bibendum sapien blandit. Cras mollis ultrices ultrices.
                            </p>

                            <p class="button-container">
                                <a class="button button-primary" href="">
									<?php esc_html_e( 'Learn more about Campaigns', 'stedb-forms' ); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>