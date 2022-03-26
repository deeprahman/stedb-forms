<?php

/**
 * Class STEDB_Forms_Element_Field_Select
 * select field
 */
if ( ! class_exists( 'STEDB_Forms_Element_Field_Select' ) ) {

	class STEDB_Forms_Element_Field_Select extends STEDB_Forms_Element_Field_Base {

		/**
		 * get type
		 * @return string
		 */
		public function get_type() {
			return 'select';
		}

		/**
		 * get name
		 * @return string|void
		 */
		public function get_name() {
			return esc_html__( 'Select', 'stedb-forms' );
		}

		/**
		 * @return string
		 */
		public function get_icon() {
			return 'dropdown';
		}

		/**
		 * render field for admin
		 */
		public function admin_render() {
			?>
            <script type="text/html" id="tmpl-stedb-forms-admin-element-field-select-html">

                <div class="stedb-form-element-field" data-id="{{-data.fieldId}}" data-type="{{-data.fieldType}}">
					<?php echo $this->get_render_remove_button_html(); ?>

					<?php echo $this->get_render_type_input_html(); ?>

                    <div class="stedb-form-element-field-edit">
                        <input type="text"
                               name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][label]"
                               class="stedb-form-element-field-input-label"
                               value="{{-data.values.label}}"
                               placeholder="<?php esc_attr_e( 'Enter label', 'stedb-forms' ); ?>"
                               aria-label="<?php esc_attr_e( 'Select label', 'stedb-forms' ); ?>">

                        <div class="field-select-option-list-editor-container">

                            <div class="select-preview-container">
                                <select class="select-preview"
                                        name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][selected]"
                                        data-selected="{{-data.selected}}"
                                        aria-label="<?php esc_attr_e( 'Select options', 'stedb-forms' ); ?>">
                                    <option data-id="0" value="{{-data.values.option_list[0]['select_label']}}">
                                        {{-data.values.option_list[0]['select_label']}}
                                    </option>

                                    {{ if(!_.isEmpty(data.values.option_list)){ }}
                                    {{ _.each(data.values.option_list, function(option, optionId){ }}
                                    {{ if (optionId == 0) return; }}

                                    <option data-id="{{-optionId}}" value="{{-option.select_label}}">
                                        {{-option.select_label}}
                                    </option>

                                    {{ }); }}
                                    {{ } }}
                                </select>

                                <button type="button" class="stedb-btn-add-option add-select-option">
									<?php esc_html_e( '+ Add', 'stedb-forms' ); ?>
                                </button>
                            </div>

                            <div class="field-select-option-list-editor">

                                <div class="select-option" data-id="0">
                                    <input type="text"
                                           name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][option_list][{{-data.optionId}}][select_label]"
                                           placeholder="<?php esc_attr_e( 'Enter option label', 'stedb-forms' ); ?>"
                                           value="{{-data.values.option_list[0]['select_label']}}"
                                           aria-label="<?php esc_attr_e( 'Select option label', 'stedb-forms' ); ?>">
                                </div>

                                {{ if(!_.isEmpty(data.values.option_list)){ }}
                                {{ _.each(data.values.option_list, function(option, optionId){ }}
                                {{ if (optionId == 0) return; }}

                                <div class="select-option" data-id="{{-optionId}}">
                                    <input type="text"
                                           name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][option_list][{{-optionId}}][select_label]"
                                           value="{{-option.select_label}}"
                                           placeholder="<?php esc_attr_e( 'Enter option label', 'stedb-forms' ); ?>"
                                           aria-label="<?php esc_attr_e( 'Select option label', 'stedb-forms' ); ?>">

                                    <button type="button" class="stedb-btn-remove-option remove-select-option">
                                        x
                                    </button>
                                </div>

                                {{ }); }}
                                {{ } }}

                            </div>
                        </div>
                    </div>
                </div>
            </script>

            <script type="text/html" id="tmpl-stedb-forms-admin-element-field-select-option-html">
                <div class="select-option" data-id="{{-data.optionId}}">
                    <input type="text"
                           name="stedb_form_elements[rows][{{-data.rowId}}][fields][{{-data.fieldId}}][values][option_list][{{-data.optionId}}][select_label]"
                           placeholder="<?php esc_attr_e( 'Enter option label', 'stedb-forms' ); ?>"
                           aria-label="<?php esc_attr_e( 'Select option label', 'stedb-forms' ); ?>">

                    <button type="button" class="stedb-btn-remove-option remove-select-option">
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
                var STEdbFormElementFieldSelect;

                (function ($) {

                    STEdbFormElementFieldSelect = function (field, stedbFormBuilder) {
                        this.parentPrototype = STEdbFormElementField.prototype;
                        this.stedbFormBuilder = stedbFormBuilder;

                        this.name = field.name;
                        this.selector = field.selector;

                        this.options = $.extend({}, field.options, {
                            templateDefaultData: {
                                values: {
                                    option_list: [{
                                        select_label: null
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
                    STEdbFormElementFieldSelect.prototype = $.extend({}, STEdbFormElementField.prototype, {
                        /**
                         * select preview
                         * @param $element
                         */
                        $selectPreview: function ($element) {
                            $element = $element || this.$element();

                            return $element.find('select.select-preview');
                        },
                        /**
                         * option
                         * @param $element
                         * @param id
                         */
                        $option: function ($element, id) {
                            $element = $element || this.$element();

                            return $element.find('.select-option' + this.idSelector(id));
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

                            /** set selected from data selected */
                            this.$selectPreview().find('option[value="' + this.$selectPreview().data('selected') + '"]').prop('selected', true);

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
                            this.on(this.$('.field-select-option-list-editor-container', id), 'click', '.add-select-option', 'onClickAddOption');
                        },
                        /**
                         * init field events
                         * @param $element
                         * @param id
                         */
                        initFieldOptionEvents: function ($element, id) {

                            this.on(this.$option($element, id).find('input'), 'keyup input', 'onChangeOption');
                            this.on(this.$option($element, id), 'click', '.remove-select-option', 'onClickRemoveOption');
                        },
                        /**
                         * add option
                         * @param $element
                         */
                        addOption: function ($element) {
                            var optionTemplate = this.template('select-option-html'),
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

                            /** add option html to select preview */
                            this.$selectPreview().append(
                                $('<option>').attr('data-id', optionId)
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

                            var optionList = $element.closest('.stedb-form-element-field-edit').find('.field-select-option-list-editor');

                            /** add option to list */
                            this.addOption(optionList);
                        },
                        /**
                         * on change option
                         * @param e
                         * @param $element
                         */
                        onChangeOption: function (e, $element) {
                            e.preventDefault();

                            var $selectOption = $element.closest('.select-option');

                            /** change option in preview */
                            this.$selectPreview().find('option[data-id="' + $selectOption.data('id') + '"]').text($element.val());
                        },
                        /**
                         * on click remove option
                         * @param e
                         * @param $element
                         */
                        onClickRemoveOption: function (e, $element) {
                            e.preventDefault();

                            /** remove option */
                            $element.closest('.select-option').remove();
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

                <select id="stedb-forms-field-input-<?php echo esc_attr( $id ); ?>" class="stedb-forms-select"
                        name="stedb_form[<?php echo esc_html( $data['name'] ); ?>]">
					<?php if ( $data['values']['option_list'] ): ?>
						<?php foreach ( $data['values']['option_list'] as $option ): ?>
                            <option value="<?php echo esc_attr( $option['select_label'] ); ?>"
								<?php selected( $data['selected'] == $option['select_label'] ); ?>>
								<?php echo esc_html( $option['select_label'] ); ?>
                            </option>
						<?php endforeach; ?>
					<?php endif; ?>
                </select>
            </div>

			<?php
			$html = ob_get_clean();

			return $html;
		}
	}
}