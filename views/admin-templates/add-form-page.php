<?php defined( 'ABSPATH' ) or die(); ?>

<div class="stedb-forms-content wrap">
    <h2><?php esc_html_e( 'Add New Form', 'stedb-forms' ); ?></h2>

    <form id="stedb-form-builder" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

        <input type="hidden" name="action" value="stedb_forms_add_form">
		<?php wp_nonce_field( 'stedb_forms_add_form' ); ?>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="stedb-forms-grid-row stedb-forms--m-n15 stedb-forms--mb-30">
                        <div class="stedb-forms-grid-col stedb-forms--p-15">
                            <label class="stedb-forms-label" for="stedb-forms-from-name">
								<?php esc_html_e( 'From Name:', 'stedb-forms' ); ?>
                            </label>
                            <input type="text" name="stedb_forms_from_name" id="stedb-forms-from-name"
                                   class="large-text"
                                   placeholder="<?php esc_attr_e( 'Type a name for your form', 'stedb-forms' ); ?>"
                                   required>
                        </div>
                        <div class="stedb-forms-grid-col stedb-forms--p-15">
                            <label class="stedb-forms-label" for="stedb-forms-receiver">
								<?php esc_html_e( 'Receiver (Email):', 'stedb-forms' ); ?>
                            </label>
                            <input type="email" name="stedb_forms_receiver" id="stedb-forms-receiver"
                                   class="large-text"
                                   placeholder="<?php esc_attr_e( 'Type an email to get a copy of the data', 'stedb-forms' ); ?>"
                                   required>
                        </div>
                    </div>

                    <div class="stedb-forms-grid-row">
                        <div class="stedb-forms-grid-col">
                            <div class="stedb-form-element-rows-container-help">
                                <i class="stedb-icon stedb-icon-add"></i>
                                <h3>
									<?php esc_html_e( 'Drag and Drop "Row" item from the Form Elements', 'stedb-forms' ); ?>
                                </h3>
                            </div>
                            <div class="stedb-form-element-rows-container"></div>
                        </div>
                    </div>

                    <div class="stedb-forms-grid-row stedb-forms--mt-30 stedb-forms--mb-30">
                        <div class="stedb-forms-grid-col--half stedb-forms-grid-col-mobile--full">
                            <label class="stedb-forms-label" for="stedb-forms-from-type">
								<?php esc_html_e( 'From Type:', 'stedb-forms' ); ?>
                            </label>
                            <select name="stedb_forms_from_type" id="stedb-forms-from-type"
                                    class="stedb-forms-widefat widefat">
                                <option value="">
									<?php esc_html_e( 'Default', 'stedb-forms' ); ?>
                                </option>
                                <option value="popup-default">
									<?php esc_html_e( 'Popup - Default style', 'stedb-forms' ); ?>
                                </option>
                                <option value="popup-sidebar">
									<?php esc_html_e( 'Popup - Sidebar style', 'stedb-forms' ); ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="postbox-container-1" class="postbox-container">

                    <div class="stedb-forms-draggable-elements">

                        <label class="stedb-forms-label">
							<?php esc_html_e( 'Form Elements', 'stedb-forms' ); ?>
                        </label>

                        <div class="stedb-forms-grid-row">
                            <div class="stedb-forms-grid-col">
                                <div class="stedb-form-element-row-add-container">
                                    <div class="stedb-form-element-row-add">
                                        <div class="stedb-form-element-row-icon stedb-icon-row-add"></div>
                                        <div class="stedb-form-element-row-text">
											<?php esc_html_e( 'Row', 'stedb-forms' ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="stedb-form-element-fields-add stedb-forms-grid-row">
							<?php if ( ! empty( $this->form_element_fields ) ): ?>
								<?php foreach ( $this->form_element_fields as $form_element_field ): ?>
                                    <div class="stedb-forms-grid-col--half">
                                        <div class="stedb-form-element-field-add-container">
                                            <div class="stedb-form-element-field-add"
                                                 data-type="<?php echo esc_attr( $form_element_field->get_type() ); ?>">
                                                <div class="stedb-form-element-field-icon stedb-icon-<?php echo esc_attr( $form_element_field->get_icon() ); ?>"></div>

                                                <div class="stedb-form-element-field-text">
													<?php echo esc_html( $form_element_field->get_name() ); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
								<?php endforeach; ?>
							<?php endif; ?>
                        </div>

                        <div class="stedb-form-element-social-fields-add stedb-forms-grid-row">
							<?php if ( ! empty( $this->form_element_social_fields ) ): ?>
								<?php foreach ( $this->form_element_social_fields as $form_element_social_field ): ?>
                                    <div class="stedb-forms-grid-col">
                                        <div class="stedb-form-element-social-field-add-container">
                                            <div class="stedb-form-element-field-add stedb-form-element-social-field-add"
                                                 data-type="<?php echo esc_attr( $form_element_social_field->get_type() ); ?>">
                                                <div class="stedb-form-element-social-field-icon">
                                                    <img src="<?php echo esc_url( STEDB_FORMS_DIR_URL . '/assets/img/icon-social-' . $form_element_social_field->get_icon() ); ?>.png"
                                                         alt="<?php echo esc_attr( $form_element_social_field->get_name() ); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
								<?php endforeach; ?>
							<?php endif; ?>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="clear"></div>

        <p class="submit">
            <button type="submit" class="button-primary">
                <span class="stedb-icon stedb-icon-code"></span>
				<?php esc_html_e( 'Save Form', 'stedb-forms' ); ?>
            </button>
            <button type="button" class="button stedb-form-preview">
                <span class="stedb-icon stedb-icon-view"></span>
				<?php esc_html_e( 'Preview', 'stedb-forms' ); ?>
            </button>
            <button type="button" class="button stedb-form-templates">
                <span class="stedb-icon stedb-icon-add"></span>
				<?php esc_html_e( 'Templates', 'stedb-forms' ); ?>
            </button>
        </p>
    </form>
</div>
