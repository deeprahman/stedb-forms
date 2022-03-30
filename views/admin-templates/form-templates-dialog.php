<?php defined( 'ABSPATH' ) or die(); ?>

<div id="stedb-forms-form-templates-dialog" class="hidden"
     data-title="<?php esc_attr_e( 'Form Templates', 'stedb-forms' ); ?>">

	<?php if ( $this->form_templates ): ?>

		<?php /** @var STEDB_Forms_Template_Base $form_template */ ?>
		<?php foreach ( $this->form_templates as $form_template ): ?>
            <div class="stedb-form-template-add"
                 data-form-builder-content="<?php echo esc_attr( json_encode( $form_template->form_builder_content() ) ); ?>">

                <div class="stedb-form-template-icon">
                    <img src="<?php echo esc_url( $form_template->get_icon() ); ?>"
                         alt="<?php echo esc_attr( $form_template->get_name() ); ?>">
                </div>

                <h4 class="stedb-form-template-name">
					<?php echo esc_html( $form_template->get_name() ); ?>
                </h4>
            </div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>