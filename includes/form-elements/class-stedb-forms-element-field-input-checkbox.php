<?php

/**
 * Class STEDB_Forms_Element_Field_Input_Checkbox
 * input checkbox field
 */
if ( ! class_exists( 'STEDB_Forms_Element_Field_Input_Checkbox' ) ) {

	class STEDB_Forms_Element_Field_Input_Checkbox extends STEDB_Forms_Element_Field_Base {

		/**
		 * get type
		 * @return string
		 */
		public function get_type() {
			return 'input_checkbox';
		}

		/**
		 * get name
		 * @return string|void
		 */
		public function get_name() {
			return esc_html__( 'Checkbox', 'stedb-forms' );
		}

		/**
		 * @return string
		 */
		public function get_icon() {
			return 'checkbox';
		}

		/**
		 * render field for admin
		 */
		public function admin_render() {
			?>
            <script type="text/html" id="tmpl-stedb-forms-admin-element-field-input_checkbox-html">

                <div class="stedb-form-element-field" data-id="{{-data.fieldId}}" data-type="{{-data.fieldType}}">
					<?php echo $this->get_render_remove_button_html(); ?>

					<?php echo $this->get_render_type_input_html(); ?>

                    <div class="stedb-form-element-field-edit">
                        <input type="text"
                               name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][label]"
                               class="stedb-form-element-field-input-label"
                               value="{{-data.values.label}}"
                               placeholder="<?php esc_attr_e( 'Enter label', 'stedb-forms' ); ?>"
                               aria-label="<?php esc_attr_e( 'Checkbox label', 'stedb-forms' ); ?>">

                        <div class="field-input-checkbox-option-list-editor">
                            <div class="checkbox-option checkbox-option-first" data-id="0">
                                <input type="checkbox"
                                       name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][option_list][0][checked]"
                                       data-checked="{{-data.values.option_list[0]['checked']}}"
                                       aria-label="<?php esc_attr_e( 'Checkbox option checked', 'stedb-forms' ); ?>">
                                <input type="text"
                                       name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][option_list][0][checkbox_label]"
                                       value="{{-data.values.option_list[0]['checkbox_label']}}"
                                       placeholder="<?php esc_attr_e( 'Enter option label', 'stedb-forms' ); ?>"
                                       aria-label="<?php esc_attr_e( 'Checkbox option label', 'stedb-forms' ); ?>">

                                <button type="button" class="stedb-btn-add-option add-checkbox">
									<?php esc_html_e( '+ Add', 'stedb-forms' ); ?>
                                </button>
                            </div>

                            {{ if(!_.isEmpty(data.values.option_list)){ }}
                            {{ _.each(data.values.option_list, function(option, optionId){ }}
                            {{ if (optionId == 0) return; }}

                            <div class="checkbox-option" data-id="{{-optionId}}">
                                <input type="checkbox"
                                       name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][option_list][{{-optionId}}][checked]"
                                       value="1"
                                       data-checked="{{-option.checked}}"
                                       aria-label="<?php esc_attr_e( 'Checkbox option checked', 'stedb-forms' ); ?>">
                                <input type="text"
                                       name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][option_list][{{-optionId}}][checkbox_label]"
                                       value="{{-option.checkbox_label}}"
                                       placeholder="<?php esc_attr_e( 'Enter option label', 'stedb-forms' ); ?>"
                                       aria-label="<?php esc_attr_e( 'Checkbox option label', 'stedb-forms' ); ?>">

                                <button type="button" class="stedb-btn-remove-option remove-checkbox">
                                    x
                                </button>
                            </div>

                            {{ }); }}
                            {{ } }}
                        </div>
                    </div>
                </div>
            </script>

            <script type="text/html" id="tmpl-stedb-forms-admin-element-field-input_checkbox-option-html">
                <div class="checkbox-option" data-id="{{-data.optionId}}">
                    <input type="checkbox"
                           name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][option_list][{{-data.optionId}}][checked]"
                           value="1"
                           aria-label="<?php esc_attr_e( 'Checkbox option checked', 'stedb-forms' ); ?>">
                    <input type="text"
                           name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][option_list][{{-data.optionId}}][checkbox_label]"
                           placeholder="<?php esc_attr_e( 'Enter option label', 'stedb-forms' ); ?>"
                           aria-label="<?php esc_attr_e( 'Checkbox option label', 'stedb-forms' ); ?>">

                    <button type="button" class="stedb-btn-remove-option remove-checkbox">
                        x
                    </button>
                </div>
            </script>
			<?php
		}

		/**
		 * render field js for admin
		 */
		public function admin_render_js() {
			?>
            <script type="text/javascript">
                var STEdbFormElementFieldInputCheckbox;

                (function ($) {

                    STEdbFormElementFieldInputCheckbox = function (field, stedbFormBuilder) {
                        this.parentPrototype = STEdbFormElementField.prototype;
                        this.stedbFormBuilder = stedbFormBuilder;

                        this.name = field.name;
                        this.selector = field.selector;

                        this.options = $.extend({}, field.options, {
                            templateDefaultData: {
                                values: {
                                    option_list: [{
                                        checked: null,
                                        checkbox_label: null
                                    }]
                                }
                            }
                        });

                        this.options.optionId = 1;
                    }

                    /**
                     * STEdb Forms - Element Field
                     * prototype extend
                     */
                    STEdbFormElementFieldInputCheckbox.prototype = $.extend({}, STEdbFormElementField.prototype, {
                        /**
                         * option
                         * @param $element
                         * @param id
                         */
                        $option: function ($element, id) {
                            $element = $element || this.$element();

                            return $element.find('.checkbox-option' + this.idSelector(id));
                        },
                        /**
                         * init field
                         * @param id
                         */
                        initField: function (id) {

                            /** get option list (filter data id) */
                            var $optionList = this.$option(this.$element(id)).filter(function (id, element) {
                                return $.isNumeric($(element).data('id'));
                            });

                            /** check option list */
                            if ($optionList.length) {

                                /** set option id from option list max */
                                this.options.optionId = Math.max.apply(null, $optionList.toArray().map(function (element) {
                                    return $(element).data('id');
                                })) + 1;
                            }

                            /** set checked from data checked */
                            $.each(this.$option(this.$element(id)), this.proxy(function (optionId, option) {
                                var $checkbox = $(option).find('input[type="checkbox"]');

                                if ($checkbox.data('checked')) {
                                    $checkbox.prop('checked', true);
                                }
                            }));

                            /** init field option events */
                            this.initFieldOptionEvents(this.$element(id));

                            /** call parent function */
                            this.parent('initField', id);
                        },
                        /**
                         * init field events
                         * @param id
                         */
                        initFieldEvents: function (id) {
                            this.on(this.$('.field-input-checkbox-option-list-editor', id), 'click', '.add-checkbox', 'onClickAddOption');
                        },
                        /**
                         * init field events
                         * @param $element
                         * @param id
                         */
                        initFieldOptionEvents: function ($element, id) {
                            this.on(this.$option($element, id), 'click', '.remove-checkbox', 'onClickRemoveOption');
                        },
                        /**
                         * add option
                         * @param $element
                         */
                        addOption: function ($element) {
                            var optionTemplate = this.template('input_checkbox-option-html'),
                                elements = this.stedbFormBuilder.elements,
                                rowId = $element.closest(elements.row.selector).data('id'),
                                fieldId = $element.closest(elements.field.selector).data('id'),
                                optionId = this.options.optionId++;

                            /** add option html */
                            $element.append(
                                optionTemplate({
                                    rowId: rowId,
                                    fieldId: fieldId,
                                    optionId: optionId
                                })
                            );

                            /** init field option events */
                            this.initFieldOptionEvents($element, optionId);
                        },
                        /**
                         * on click add option
                         * @param e
                         * @param $element
                         */
                        onClickAddOption: function (e, $element) {
                            e.preventDefault();

                            var optionList = $element.closest('.field-input-checkbox-option-list-editor');

                            /** add option to list */
                            this.addOption(optionList);
                        },
                        /**
                         * on click remove option
                         * @param e
                         * @param $element
                         */
                        onClickRemoveOption: function (e, $element) {
                            e.preventDefault();

                            /** remove option */
                            $element.closest('.checkbox-option').remove();
                        }
                    });

                }(jQuery));
            </script>
			<?php
		}

		/**
		 * render field
		 *
		 * @param $id
		 * @param $data
		 *
		 * @return false|string
		 */
		public function render( $id, $data ) {
			ob_start();
			?>

            <div class="stedb-forms-field stedb-forms-field-<?php echo esc_attr( $this->get_type() ); ?> stedb-forms-field-<?php echo esc_attr( $id ); ?>">
                <label for="stedb-forms-field-input-<?php echo esc_attr( $id ); ?>">
					<?php echo esc_html( $data['values']['label'] ); ?>
                </label>

				<?php if ( $data['values']['option_list'] ): ?>
					<?php foreach ( $data['values']['option_list'] as $option ): ?>
                        <label class="stedb-forms-checkbox">
                            <input type="checkbox" class="stedb-forms-input-checkbox"
                                   id="stedb-forms-field-input-checkbox-<?php echo esc_attr( $id ); ?>"
                                   name="stedb_form[<?php echo esc_attr( $data['name'] ); ?>][]"
                                   value="<?php echo esc_attr( $option['checkbox_label'] ); ?>"
								<?php checked( isset( $option['checked'] ) ? $option['checked'] : false ); ?>>
							<?php echo esc_html( $option['checkbox_label'] ); ?>
                        </label>
					<?php endforeach; ?>
				<?php endif; ?>
            </div>

			<?php
			$html = ob_get_clean();

			return $html;
		}
	}
}