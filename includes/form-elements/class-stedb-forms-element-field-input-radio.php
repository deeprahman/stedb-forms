<?php

/**
 * Class STEDB_Forms_Element_Field_Input_Radio
 * input radio field
 */
if ( ! class_exists( 'STEDB_Forms_Element_Field_Input_Radio' ) ) {

	class STEDB_Forms_Element_Field_Input_Radio extends STEDB_Forms_Element_Field_Base {

		/**
		 * get type
		 * @return string
		 */
		public function get_type() {
			return 'input_radio';
		}

		/**
		 * get name
		 * @return string|void
		 */
		public function get_name() {
			return esc_html__( 'Radio', 'stedb-forms' );
		}

		/**
		 * @return string
		 */
		public function get_icon() {
			return 'radio';
		}

		/**
		 * render field for admin
		 */
		public function admin_render() {
			?>
            <script type="text/html" id="tmpl-stedb-forms-admin-element-field-input_radio-html">

                <div class="stedb-form-element-field" data-id="{{-data.fieldId}}" data-type="{{-data.fieldType}}">
					<?php echo $this->get_render_remove_button_html(); ?>

					<?php echo $this->get_render_type_input_html(); ?>

                    <div class="stedb-form-element-field-edit">
                        <input type="text"
                               name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][label]"
                               class="stedb-form-element-field-input-label"
                               value="{{-data.values.label}}"
                               placeholder="<?php esc_attr_e( 'Enter label', 'stedb-forms' ); ?>"
                               aria-label="<?php esc_attr_e( 'Radio label', 'stedb-forms' ); ?>">

                        <div class="field-input-radio-option-list-editor">
                            <div class="radio-option radio-option-first" data-id="0">
                                <input type="radio"
                                       name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][checked]"
                                       value="0"
                                       data-checked="{{-(data.checked == 0)}}"
                                       aria-label="<?php esc_attr_e( 'Radio option checked', 'stedb-forms' ); ?>">
                                <input type="text"
                                       name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][option_list][0][radio_label]"
                                       value="{{-data.values.option_list[0]['radio_label']}}"
                                       placeholder="<?php esc_attr_e( 'Enter option label', 'stedb-forms' ); ?>"
                                       aria-label="<?php esc_attr_e( 'Radio option label', 'stedb-forms' ); ?>">

                                <button type="button" class="stedb-btn-add-option add-radio">
									<?php esc_html_e( '+ Add', 'stedb-forms' ); ?>
                                </button>
                            </div>

                            {{ if(!_.isEmpty(data.values.option_list)){ }}
                            {{ _.each(data.values.option_list, function(option, optionId){ }}
                            {{ if (optionId == 0) return; }}

                            <div class="radio-option" data-id="{{-optionId}}">
                                <input type="radio"
                                       name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][checked]"
                                       value="{{-optionId}}"
                                       data-checked="{{-(data.checked == optionId)}}"
                                       aria-label="<?php esc_attr_e( 'Radio option checked', 'stedb-forms' ); ?>">
                                <input type="text"
                                       name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][option_list][{{-optionId}}][radio_label]"
                                       value="{{-option.radio_label}}"
                                       placeholder="<?php esc_attr_e( 'Enter option label', 'stedb-forms' ); ?>"
                                       aria-label="<?php esc_attr_e( 'Radio option label', 'stedb-forms' ); ?>">

                                <button type="button" class="stedb-btn-remove-option remove-radio">
                                    x
                                </button>
                            </div>

                            {{ }); }}
                            {{ } }}

                        </div>
                    </div>
                </div>
            </script>

            <script type="text/html" id="tmpl-stedb-forms-admin-element-field-input_radio-option-html">
                <div class="radio-option" data-id="{{-data.optionId}}">
                    <input type="radio"
                           name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][checked]"
                           value="{{-data.optionId}}"
                           aria-label="<?php esc_attr_e( 'Radio option checked', 'stedb-forms' ); ?>">
                    <input type="text"
                           name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][option_list][{{-data.optionId}}][radio_label]"
                           placeholder="<?php esc_attr_e( 'Enter option label', 'stedb-forms' ); ?>"
                           aria-label="<?php esc_attr_e( 'Radio option label', 'stedb-forms' ); ?>">

                    <button type="button" class="stedb-btn-remove-option remove-radio">
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
                var STEdbFormElementFieldInputRadio;

                (function ($) {

                    STEdbFormElementFieldInputRadio = function (field, stedbFormBuilder) {
                        this.parentPrototype = STEdbFormElementField.prototype;
                        this.stedbFormBuilder = stedbFormBuilder;

                        this.name = field.name;
                        this.selector = field.selector;

                        this.options = $.extend({}, field.options, {
                            templateDefaultData: {
                                values: {
                                    checked: null,
                                    option_list: [{
                                        radio_label: null
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
                    STEdbFormElementFieldInputRadio.prototype = $.extend({}, STEdbFormElementField.prototype, {
                        /**
                         * option
                         * @param $element
                         * @param id
                         */
                        $option: function ($element, id) {
                            $element = $element || this.$element();

                            return $element.find('.radio-option' + this.idSelector(id));
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
                                var $radio = $(option).find('input[type="radio"]');

                                if ($radio.data('checked')) {
                                    $radio.prop('checked', true);
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
                            this.on(this.$('.field-input-radio-option-list-editor', id), 'click', '.add-radio', 'onClickAddOption');
                        },
                        /**
                         * init field events
                         * @param $element
                         * @param id
                         */
                        initFieldOptionEvents: function ($element, id) {
                            this.on(this.$option($element, id), 'click', '.remove-radio', 'onClickRemoveOption');
                        },
                        /**
                         * add option
                         * @param $element
                         */
                        addOption: function ($element) {
                            var optionTemplate = this.template('input_radio-option-html'),
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

                            var optionList = $element.closest('.field-input-radio-option-list-editor');

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
                            $element.closest('.radio-option').remove();
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
					<?php foreach ( $data['values']['option_list'] as $option_id => $option ): ?>
                        <label class="stedb-forms-radio">
                            <input type="radio" class="stedb-forms-input-radio"
                                   id="stedb-forms-field-input-radio-<?php echo esc_attr( $option_id ); ?>"
                                   name="stedb_form[<?php echo esc_attr( $data['name'] ); ?>][]"
                                   value="<?php echo esc_attr( $option['radio_label'] ); ?>"
								<?php checked( isset( $data['checked'] ) ? $option_id : false ); ?>>
							<?php echo esc_html( $option['radio_label'] ); ?>
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