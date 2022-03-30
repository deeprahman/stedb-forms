<?php
defined( 'ABSPATH' ) or die();

/** STEdb API */
$stedb_forms_api_client = new STEDB_Forms_Api_Client();

/** api get plan */
$plan = $stedb_forms_api_client->get_plans( intval( wp_unslash( $_GET['id'] ) ) );
?>

<div class="stedb-forms-content wrap">

    <h1 class="wp-heading">
		<?php esc_html_e( 'Premium', 'stedb-forms' ); ?>
    </h1>

	<?php /** check plan is not wp error */ ?>
	<?php if ( ! is_wp_error( $plan ) ): ?>

        <h2 class="title">
			<?php echo sprintf( '#%d - %s', esc_html( $plan['plan_id'] ), esc_html( $plan['name'] ) ); ?>
        </h2>
	<?php endif; ?>

    <ul class="subsubsub">
        <li class="premium-plans">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-premium.php&subpage=plans' ) ); ?>">
				<?php esc_html_e( 'Plans', 'stedb-forms' ); ?>
            </a> |
        </li>
        <li class="premium-plan">
            <a href="<?php echo esc_url( admin_url( add_query_arg( array(
				'page'    => 'stedb-forms-premium.php',
				'subpage' => 'plan',
				'id'      => absint( wp_unslash( $_GET['id'] ) ),
			), 'admin.php' ) ) ); ?>"
               class="current" aria-current="page">
				<?php esc_html_e( 'Plan', 'stedb-forms' ); ?>
            </a> |
        </li>
        <li class="premium-payments">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=stedb-forms-premium.php&subpage=payments' ) ); ?>">
				<?php esc_html_e( 'Payments', 'stedb-forms' ); ?>
            </a>
        </li>
    </ul>
    <br/>

	<?php /** check plan is not wp error */ ?>
	<?php if ( ! is_wp_error( $plan ) ): ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label>
						<?php esc_html_e( 'ID', 'stedb-forms' ); ?>
                    </label>
                </th>
                <td>
                    <p>
						<?php echo esc_html( '#' . $plan['plan_id'] ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
						<?php esc_html_e( 'Name', 'stedb-forms' ); ?>
                    </label>
                </th>
                <td>
                    <p>
						<?php echo esc_html( $plan['name'] ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
						<?php esc_html_e( 'Price', 'stedb-forms' ); ?>
                    </label>
                </th>
                <td>
                    <p>
						<?php echo esc_html( sprintf( __( '%.2f USD/month', 'stedb-forms' ), $plan['price'] ) ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
						<?php esc_html_e( 'Processing Fee', 'stedb-forms' ); ?>
                    </label>
                </th>
                <td>
                    <p>
						<?php echo esc_html( sprintf( __( '%.2f USD', 'stedb-forms' ), $plan['processing_fee'] ) ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
						<?php esc_html_e( 'Charge for Upgrade', 'stedb-forms' ); ?>
                    </label>
                </th>
                <td>
                    <p>
						<?php echo esc_html( sprintf( __( '%.2f USD', 'stedb-forms' ), $plan['charge_for_upgrade'] ) ); ?>
                    </p>
                </td>
            </tr>
        </table>
	<?php else: ?>

        <h2>
			<?php _e( 'Error', 'stedb-forms' ); ?>
        </h2>
        <p>
			<?php _e( 'Sorry, the plan cannot be found', 'stedb-forms' ); ?>
        </p>

	<?php endif; ?>
</div>