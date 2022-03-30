<?php
defined( 'ABSPATH' ) or die();
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
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-premium.php&subpage=payments' ) ); ?>">
				<?php esc_html_e( 'Payments', 'stedb-forms' ); ?>
            </a> |
        </li>
        <li class="premium-payment">
            <a href="<?php echo esc_url( admin_url( add_query_arg( array(
				'page'    => 'stedb-forms-premium.php',
				'subpage' => 'payment',
				'id'      => absint( wp_unslash( $_GET['id'] ) ),
			), 'admin.php' ) ) ); ?>"
               class="current" aria-current="page">
				<?php esc_html_e( 'Payment', 'stedb-forms' ); ?>
            </a>
        </li>
    </ul>
    <br/>


</div>