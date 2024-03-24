// noinspection JSVoidFunctionReturnValueUsed

/**
 * wp localize variables
 * @typedef {object} stedb_form_builder_content
 */

/**
 * STEdb Form Builder
 * variables
 */
var STEdbFormBuilder;
var STEdbFormElement;
var STEdbFormElementRow;
var STEdbFormElementField;
debugger;
/** Create jQuery anonymous function */
(function ($) {

    /**
     * STEdb Form Builder
     */
    var STEdbFormBuilderDefaultOptions = {

        /** underscore.js template options */
        underscoreTemplate: {
            evaluate: /{{([\s\S]+?)}}/g,
            interpolate: /{{=([\s\S]+?)}}/g,
            escape: /{{-([\s\S]+?)}}/g,
            variable: 'data'
        },
        /**
         * wp localize variables
         * @typedef {object} stedb_form_builder_l10n
         * @typedef {object} stedb_form_builder_ajax
         */
        wp: {
            l10n: stedb_form_builder_l10n,
            ajax: stedb_form_builder_ajax
        },
        wpFormElements: {
            fields: null,
            socialFields: null
        }
    };

    /**
     * wp form elements localize variables
     * @typedef {object} stedb_form_element_fields
     * @typedef {object} stedb_form_element_social_fields
     */
    if (typeof stedb_form_element_fields !== 'undefined') {
        STEdbFormBuilderDefaultOptions.wpFormElements.fields = stedb_form_element_fields;
    }

    if (typeof stedb_form_element_social_fields !== 'undefined') {
        STEdbFormBuilderDefaultOptions.wpFormElements.socialFields = stedb_form_element_social_fields;
    }

    /**
     * STEdbFormBuilder
     * @param options
     * @constructor
     */
    STEdbFormBuilder = function (options) {
        this.options = $.extend({}, STEdbFormBuilderDefaultOptions, options);

        this.elements = {
            row: new STEdbFormElementRow(this),
            field: new STEdbFormElementField(this)
        };

        var socialElements = {};

        $.each(this.options.wpFormElements.socialFields, function (fieldName) {
            socialElements[fieldName] = [];
        });

        this.socialElements = socialElements;

        this.init();
    };

    /**
     * STEdb Form Builder
     * prototype
     */
    STEdbFormBuilder.prototype = {
        /**
         * element selector
         */
        selector: '#stedb-form-builder',
        /**
         * element
         * @returns {*|HTMLElement}
         */
        $element: function () {
            return $(this.selector);
        },
        /**
         * nonce field
         * @returns {*}
         */
        $nonceField: function () {
            return this.$('#stedb_forms_add_form_nonce_field');
        },
        /**
         * form preview dialog
         * @returns {*}
         */
        $dialogFormPreview: function () {
            return $('#stedb-forms-form-preview-dialog');
        },
        /**
         * form templates dialog
         * @returns {*}
         */
        $dialogFormTemplates: function () {
            return $('#stedb-forms-form-templates-dialog');
        },
        /**
         * get jQuery element
         * @param selector
         */
        $: function (selector) {
            return this.$element().find(selector);
        },
        /**
         * Underscore template
         * with STEdb Forms options
         *
         * @param id
         * @returns {Function}
         */
        template: function (id) {
            
            return _.template($('#tmpl-stedb-forms-' + id).html(), this.options.underscoreTemplate);
        },
        /**
         * main
         * init
         */
        init: function () {

            /** check form builder content */
            if (typeof stedb_form_builder_content !== 'undefined') {
                this.initContent(stedb_form_builder_content);
            }

            this.initElements();

            this.initFormPreviewDialog();
            this.initFormTemplatesDialog();
        },
        /**
         * init form preview dialog
         */
        initFormPreviewDialog: function () {

            /** init dialog */
            this.$dialogFormPreview().dialog({
                title: this.$dialogFormPreview().data('title'),
                dialogClass: 'wp-dialog',
                autoOpen: false,
                draggable: true,
                minWidth: 800,
                maxHeight: ($(window).height() * 0.8),
                modal: true,
                resizable: true,
                closeOnEscape: true,
                position: {
                    my: 'center top+10%',
                    at: 'center top+10%',
                    of: window
                },
                open: this.proxyEvent(function () {

                    /** ajax get stedb form preview */
                    $.post(
                        ajaxurl,
                        {
                            action: 'stedb_forms_get_stedb_form_preview',
                            _ajax_nonce: this.options.wp.ajax['get_stedb_form_preview_nonce'],
                            form: this.$element().serialize()
                        },
                        this.proxy(function (html) {

                            /** set html */
                            this.$dialogFormPreview().find('.stedb-form-preview').html(html);

                            /** remove social links from preview */
                            this.$dialogFormPreview().find('.stedb-form-preview').find('.stedb-form').find('a.stedb-forms-social-field').attr('href', '#').on('click', function () {
                                return false;
                            });
                        }),
                        'html'
                    );

                    /** close dialog by clicking the overlay behind it */
                    $('.ui-widget-overlay').bind('click', this.proxy(function () {
                        this.$dialogFormPreview().dialog('close');
                    }));
                })
            });

            /** on click preview button */
            this.on(this.$('.button.stedb-form-preview'), 'click', this.proxy(function () {
                this.$dialogFormPreview().dialog('open');
            }));
        },
        /**
         * init form templates dialog
         */
        initFormTemplatesDialog: function () {

            /** init dialog */
            this.$dialogFormTemplates().dialog({
                title: this.$dialogFormTemplates().data('title'),
                dialogClass: 'wp-dialog',
                autoOpen: false,
                draggable: true,
                minWidth: 400,
                maxHeight: ($(window).height() * 0.8),
                modal: true,
                resizable: false,
                closeOnEscape: true,
                position: {
                    my: 'center top+10%',
                    at: 'center top+10%',
                    of: window
                },
                buttons: [
                    {
                        text: this.options.wp.l10n['close'],
                        class: 'button',
                        click: function () {
                            $(this).dialog('close');
                        }
                    }
                ],
                open: this.proxyEvent(function () {
                    /** close dialog by clicking the overlay behind it */
                    $('.ui-widget-overlay').bind('click', this.proxy(function () {
                        this.$dialogFormTemplates().dialog('close');
                    }));
                })
            });

            /** on click templates button */
            this.on(this.$('.button.stedb-form-templates'), 'click', this.proxyEvent(function () {
                /** open dialog */
                this.$dialogFormTemplates().dialog('open');
            }));

            /** on click form template add */
            this.on(this.$dialogFormTemplates().find('.stedb-form-template-add'), 'click', this.proxyEvent(function (e, $element) {

                /** check has children */
                if (this.elements.row.$container().children().length) {

                    /** confirm add template */
                    if (!confirm(this.options.wp.l10n['confirm_add_template_message'])) {
                        return;
                    }
                }

                /** init content from template */
                this.initContent($element.data('form-builder-content'));

                /** init elements */
                this.initElements();

                /** close dialog */
                this.$dialogFormTemplates().dialog('close');
            }));
        },
        /**
         * init content
         * @param form_builder_content
         */
        initContent: function (form_builder_content) {

            var rowTemplate = this.elements.row.template('html'),
                fieldTemplate,
                rowIds = [0], fieldIds = [0];

            /** check content */
            if (!form_builder_content) {
                return;
            }

            /** check rows */
            if (!form_builder_content.hasOwnProperty('rows')) {
                return;
            }

            /** rows */
            var rows = '';
            $.each(form_builder_content['rows'], this.proxy(function (rowId, row) {

                /** fields */
                var fields = '';
                $.each(row['fields'], this.proxy(function (fieldIndex, field) {
                    var fieldId = field['id'];
                    fieldTemplate = this.elements.field.template(field['type'] + '-html');

                    /** add social element */
                    if (field['type'] in this.socialElements) {
                        this.socialElements[field['type']].push(parseInt(fieldId));
                    }

                    /** set field id from index */
                    if (typeof fieldId == 'undefined') {
                        fieldId = fieldIndex;
                    }

                    fields += fieldTemplate($.extend({}, {
                        rowId: rowId,
                        fieldId: fieldId,
                        fieldType: field['type']
                    }, field));
                    fieldIds.push(parseInt(fieldId) + 1);
                }));

                rows += rowTemplate({
                    rowId: rowId,
                    fields: fields
                });
                rowIds.push(parseInt(rowId) + 1);
            }));

            /** set ids */
            this.elements.row.options.id = Math.max.apply(null, rowIds);
            this.elements.field.options.id = Math.max.apply(null, fieldIds);

            /** add html content to form builder */
            this.$(this.elements.row.options.containerSelector).empty().append(rows);

            /** hide row container help */
            this.elements.row.hideContainerHelp();
        },
        /**
         * init elements
         */
        initElements: function () {

            this.elements.row.init();
            this.elements.field.init();
        },
        /**
         * on action
         * @param a1
         * @param a2
         * @param a3
         * @param a4
         */
        on: function (a1, a2, a3, a4) {
            var $element, event, selector, callback, args;

            /** find args */
            if (a1 instanceof jQuery) {

                /** 1. args( $el, event, selector, callback ) */
                if (a4) {
                    $element = a1;
                    event = a2;
                    selector = a3;
                    callback = a4;

                    /** 2. args( $el, event, callback ) */
                } else {
                    $element = a1;
                    event = a2;
                    callback = a3;
                }
            } else {

                /** 3. args( event, selector, callback ) */
                if (a3) {
                    event = a1;
                    selector = a2;
                    callback = a3;

                    /** 4. args( event, callback ) */
                } else {
                    event = a1;
                    callback = a2;
                }
            }

            /** element */
            $element = this.getEventTarget($element);

            /** modify callback */
            if (typeof callback === 'string') {
                callback = this.proxyEvent(this[callback]);
            }

            /** args */
            if (selector) {
                args = [event, selector, callback];
            } else {
                args = [event, callback];
            }

            /** on() */
            $element.on.apply($element, args);
        },
        /**
         *  get event target
         * @param $element
         * @returns {*|HTMLElement}
         */
        getEventTarget: function ($element) {
            return $element || this.$element() || $(document);
        },
        /**
         * proxy event
         * @param callback
         * @returns {*}
         */
        proxyEvent: function (callback) {

            return this.proxy(function (e) {
                var args, extraArgs, eventArgs;

                args = Array.prototype.slice.call(arguments);
                extraArgs = args.slice(1);
                eventArgs = [e, $(e.currentTarget)].concat(extraArgs);

                callback.apply(this, eventArgs);
            });
        },
        /**
         * proxy
         * with this model
         * @param callback
         * @returns {*}
         */
        proxy: function (callback) {
            return $.proxy(callback, this);
        }
    }

    /**
     * STEdb Form Element
     * default options
     * @type {{}}
     */
    var STEdbFormElementDefaultOptions = {
        id: 0,
        parentElement: null,
        childElement: null,
        wp: {},
        removable: false,
        sortable: false,
        sortableOptions: {
            placeholder: 'ui-state-highlight',
            scrollSensitivity: 30
        },
        sortableSetOptions: {},
        draggable: false,
        draggableOptions: {}
    };

    /**
     * STEdb Form Element
     * @param name
     * @param selector
     * @param options
     * @constructor
     */
    STEdbFormElement = function (name, selector, options) {
        this.name = name;
        this.selector = selector;

        /** set options */
        this.options = $.extend({}, STEdbFormElementDefaultOptions, options, {
            elementName: this.name,
            containerSelector: this.selector + 's-container',
            shortSingularName: this.name.replace('stedb', '').replace('_', ''),
            shortPluralName: this.name.replace('stedb', '').replace('_', '') + 's'
        });

        /** set sortable options */
        this.options.sortableOptions = $.extend({}, STEdbFormElementDefaultOptions.sortableOptions, this.options.sortableOptions);

        /** set sortable set options */
        this.options.sortableSetOptions = $.extend({}, STEdbFormElementDefaultOptions.sortableSetOptions, this.options.sortableSetOptions, {
            items: this.selector,
            connectWith: this.stedbFormBuilder.selector + ' ' + this.options.containerSelector
        });

        /** set draggable options */
        this.options.draggableOptions = $.extend({}, STEdbFormElementDefaultOptions.draggableOptions, this.options.draggableOptions, {
            connectToSortable: this.stedbFormBuilder.selector + ' ' + this.options.containerSelector,
            helper: this.proxy(function (e) {
                return this.draggableHelper(e);
            })
        });

        /** set wp ajax options */
        this.options.wp.ajax = $.extend({}, this.stedbFormBuilder.options.wp.ajax[this.name], this.options.wp.ajax);

        /** set wp l10n options */
        this.options.wp.l10n = $.extend({}, this.stedbFormBuilder.options.wp.l10n[this.name], this.options.wp.l10n);
    };

    /**
     * STEdb Form Element
     * prototype
     */
    STEdbFormElement.prototype = {
        /**
         * id selector
         * @param id
         * @returns {string}
         */
        idSelector: function (id) {
            /** element selector */
            if (typeof id !== 'undefined') {
                return '[data-id=' + id + ']'
            }

            return '';
        },
        /**
         * element
         * @param id
         */
        $element: function (id) {
            return $(this.selector + this.idSelector(id));
        },
        /**
         * container
         * @param id
         */
        $container: function (id) {
            /** element container */
            if (typeof id !== 'undefined') {
                return this.$element(id).closest(this.options.containerSelector);
            }

            return this.stedbFormBuilder.$(this.options.containerSelector);
        },
        /**
         * input
         * @param $element
         */
        $input: function ($element) {
            $element = $element || this.$element();

            return $element.find('input[name^="stedb_form_elements"]');
        },
        /**
         * get jQuery element
         * @param selector
         * @param id
         */
        $: function (selector, id) {
            return this.$element(id).find(selector);
        },
        /**
         * Underscore template
         * with STEdb Forms options
         *
         * @param id
         * @returns {Function}
         */
        template: function (id) {

            return _.template($('#tmpl-stedb-forms-admin-element-' + this.options.templateName + '-' + id).html(), this.stedbFormBuilder.options.underscoreTemplate);
        },
        /**
         * main
         * init
         */
        init: function () {
            this.initParentElement();
            this.initChildElement();

            this.initEvents();
            this.initChildEvents();

            this.addSortable(this.$container());
            this.addDraggable(this.stedbFormBuilder.$(this.selector + '-add'));
        },
        /**
         * init child events
         */
        initChildEvents: function (id) {

            /** check child element */
            if (null === this.options.childElement) {
                return;
            }

            /** init parent events */
            this.options.childElement.initParentEvents(id);

            /** init sortable */
            this.options.childElement.addSortable(this.$(this.options.childElement.options.containerSelector, id));
        },
        /**
         * init parent events
         */
        initParentEvents: function (id, $element) {
            /** add the function to the subclass */
        },
        /**
         * init events
         * @param id
         */
        initEvents: function (id) {

            /** remove element */
            if (this.options.removable) {
                this.on(this.$element(id), 'click', this.selector + '-remove', 'onClickRemove');
            }
        },
        /**
         * init parent element
         */
        initParentElement: function () {

            /** set parent element */
            if (typeof this.options.parentElement !== 'object') {
                this.options.parentElement = this.getObjectProperty(this.stedbFormBuilder.elements, this.options.parentElement);
            }
        },
        /**
         * init child element
         */
        initChildElement: function () {

            /** set child element */
            if (typeof this.options.childElement !== 'object') {
                this.options.childElement = this.getObjectProperty(this.stedbFormBuilder.elements, this.options.childElement);
            }
        },
        /**
         * add element
         * @param id
         * @returns {*}
         */
        addElement: function (id) {
            this.initEvents(id);
            this.initChildEvents(id);

            /** return added element id */
            return id;
        },
        /**
         * change element id recursive
         * @param $element
         */
        changeElementId: function ($element) {
            var element = this, parentElement, parentNameRegex, $parentElement;

            /** recursive replace id in element */
            while (null !== element) {

                /** element inputs */
                element.$input($element).each((function (element, that) {
                    return that.proxy(function (i, input) {
                        parentElement = element.getObjectProperty(element, 'options', 'parentElement');

                        /** recursive replace parent id in element input name */
                        while (null !== parentElement) {
                            parentNameRegex = new RegExp('\\[' + parentElement.options.shortPluralName + '\\]\\[(.*?)\\]');
                            $parentElement = $(input).closest(parentElement.selector);

                            /** change input name */
                            $(input).attr('name', $(input).attr('name').replace(parentNameRegex, '[' + parentElement.options.shortPluralName + '][' + $parentElement.data('id') + ']'));

                            parentElement = element.getObjectProperty(parentElement, 'options', 'parentElement');
                        }
                    });
                }(element, this)));

                element = this.getObjectProperty(element, 'options', 'childElement');
            }
        },
        /**
         * add sortable to element
         * @param $element
         */
        addSortable: function ($element) {

            /** check is sortable */
            if (!this.options.sortable) {
                return;
            }

            /** check is already sortable */
            if ($element.hasClass('ui-sortable')) {
                return;
            }

            /** add sortable */
            $element.sortable(this.options.sortableOptions);

            /** add sortable events */
            this.on($element, 'sortstart', 'onSortableStart');
            this.on($element, 'sortstop', 'onSortableStop');
            this.on($element, 'sortupdate', 'onSortableUpdate');
            this.on($element, 'sortreceive', 'onSortableReceive');
        },
        /**
         * on sortable start
         */
        onSortableStart: function () {
            /** add the function to the subclass */
        },
        /**
         * on sortable stop
         */
        onSortableStop: function () {
            /** add the function to the subclass */
        },
        /**
         * on sortable update
         */
        onSortableUpdate: function () {
            /** add the function to the subclass */
        },
        /**
         * on sortable receive
         */
        onSortableReceive: function () {
            /** add the function to the subclass */
        },
        /**
         * add draggable to element
         * @param $element
         */
        addDraggable: function ($element) {

            /** check is draggable */
            if (!this.options.draggable) {
                return;
            }

            /** check is already draggable */
            if ($element.hasClass('ui-draggable')) {
                return;
            }

            /** add draggable */
            $element.draggable(this.options.draggableOptions);

            /** add draggable events */
            this.on($element, 'dragstart', 'onDraggableStart');
            this.on($element, 'dragstop', 'onDraggableStop');
        },
        /**
         * on draggable start
         */
        onDraggableStart: function () {
            /** add the function to the subclass */
        },
        /**
         * on draggable stop
         */
        onDraggableStop: function () {
            /** add the function to the subclass */
        },
        /**
         * draggable helper
         * @param e
         * @param $element
         * @returns {string}
         */
        draggableHelper: function (e, $element) {
            /** add the function to the subclass */
        },
        /**
         * on click remove
         * @param e
         * @param $closeElement
         */
        onClickRemove: function (e, $closeElement) {
            var $element = $closeElement.closest(this.selector);

            e.preventDefault();

            /** check removable */
            if (!this.options.removable) {
                return;
            }

            this.removeElement($element);
        },
        /**
         * remove element
         * @param $element
         */
        removeElement: function ($element) {

            /** if social element, remove from it */
            if ($element.data('type') in this.stedbFormBuilder.socialElements) {
                this.stedbFormBuilder.socialElements[$element.data('type')] = _.without(this.stedbFormBuilder.socialElements[$element.data('type')], parseInt($element.data('id')));
            }

            /** remove element */
            $element.remove();
        },
        /**
         * on action
         * @param a1
         * @param a2
         * @param a3
         * @param a4
         */
        on: function (a1, a2, a3, a4) {
            var $element, event, selector, callback, args;

            /** find args */
            if (a1 instanceof jQuery) {

                /** 1. args( $el, event, selector, callback ) */
                if (a4) {
                    $element = a1;
                    event = a2;
                    selector = a3;
                    callback = a4;

                    /** 2. args( $el, event, callback ) */
                } else {
                    $element = a1;
                    event = a2;
                    callback = a3;
                }
            } else {

                /** 3. args( event, selector, callback ) */
                if (a3) {
                    event = a1;
                    selector = a2;
                    callback = a3;

                    /** 4. args( event, callback ) */
                } else {
                    event = a1;
                    callback = a2;
                }
            }

            /** element */
            $element = this.getEventTarget($element);

            /** modify callback */
            if (typeof callback === 'string') {
                callback = this.proxyEvent(this[callback]);
            }

            /** args */
            if (selector) {
                args = [event, selector, callback];
            } else {
                args = [event, callback];
            }

            /** on() */
            $element.on.apply($element, args);
        },
        /**
         *  get event target
         * @param $element
         * @returns {*|HTMLElement}
         */
        getEventTarget: function ($element) {
            return $element || this.$element() || $(document);
        },
        /**
         * proxy event
         * @param callback
         * @returns {*}
         */
        proxyEvent: function (callback) {
            return this.proxy(function (e) {
                var args, extraArgs, eventArgs;

                args = Array.prototype.slice.call(arguments);
                extraArgs = args.slice(1);
                eventArgs = [e, $(e.currentTarget)].concat(extraArgs);

                callback.apply(this, eventArgs);
            });
        },
        /**
         * proxy
         * with this model
         * @param callback
         * @returns {*}
         */
        proxy: function (callback) {
            return $.proxy(callback, this);
        },
        /**
         * call parent function
         * @param functionName
         */
        parent: function (functionName) {
            var args = Array.prototype.slice.call(arguments).slice(1);
            return this.parentPrototype[functionName].apply(this, args);
        },
        /**
         * parse object args
         * @param args
         * @param defaults
         * @returns {*}
         */
        parseObjectArgs: function (args, defaults) {
            if (typeof args !== 'object') args = {};
            if (typeof defaults !== 'object') defaults = {};
            return $.extend({}, defaults, args);
        },
        /**
         * get object property
         * @param obj
         * @returns {*}
         */
        getObjectProperty: function (obj) {
            for (var i = 1; i < arguments.length; i++) {
                if (!obj.hasOwnProperty(arguments[i])) {
                    return null;
                }
                obj = obj[arguments[i]];
            }
            return obj;
        }
    };

    /**
     * STEdb Forms - Element Row
     * @param stedbFormBuilder
     * @constructor
     */
    STEdbFormElementRow = function (stedbFormBuilder) {
        this.parentPrototype = STEdbFormElement.prototype;
        this.stedbFormBuilder = stedbFormBuilder;

        STEdbFormElement.call(this, 'row', '.stedb-form-element-row', {
            elementName: 'row',
            templateName: 'row',
            parentElement: null,
            childElement: 'field',
            removable: true,
            sortable: true,
            sortableOptions: {
                axis: 'y',
                distance: 5,
                cursor: 'move',
                cursorAt: {
                    top: 20,
                    left: 0
                }
            },
            draggable: true,
            draggableOptions: {}
        });
    };

    /**
     * STEdb Forms - Element Row
     * prototype
     */
    STEdbFormElementRow.prototype = $.extend({}, STEdbFormElement.prototype, {
        /**
         * element container help
         */
        $containerHelp: function () {
            return this.stedbFormBuilder.$(this.options.containerSelector + '-help');
        },
        /**
         * hide container help
         */
        hideContainerHelp: function () {

            /** check container has children */
            if (this.$container().children().length) {
                this.$containerHelp().hide();
            }
        },
        /**
         * show container help
         */
        showContainerHelp: function () {

            /** check container has not children */
            if (!this.$container().children().length) {
                /** show container help */
                this.$containerHelp().show();
            }
        },
        /**
         * add element
         * @param $element
         * @returns {*}
         */
        addElement: function ($element) {
            var rowId = this.options.id++;

            /** set id */
            $element.attr('data-id', rowId);

            /** call parent function */
            this.parent('addElement', rowId, $element);

            /** hide container help */
            this.hideContainerHelp();
        },
        /**
         * remove element
         * @param $element
         * @returns {*}
         */
        removeElement: function ($element) {

            /** call parent function */
            this.parent('removeElement', $element);

            /** show container help */
            this.showContainerHelp();
        },
        /**
         * add sortable to element
         * @param $element
         */
        addSortable: function ($element) {

            /** check is sortable */
            if (!this.options.sortable) {
                return;
            }

            /** check is already sortable */
            if ($element.hasClass('ui-sortable')) {
                return;
            }

            /** call parent function */
            this.parent('addSortable', $element);

            /** add sortable */
            $element.sortable('option', 'items', this.options.sortableSetOptions.items);
            $element.sortable('option', 'connectWith', this.options.sortableSetOptions.connectWith);
        },
        /**
         * on sortable receive
         */
        onSortableReceive: function (e, element, ui) {

            /** check target is container */
            if ($(e.target).is(this.options.containerSelector)) {

                /** check dragging from addable */
                if (ui.sender.is(this.selector + '-add') && ui.sender.hasClass('ui-draggable')) {

                    /** add element */
                    this.addElement(ui.helper);
                }
            }

            /** call parent function */
            this.parent('onSortableReceive', e, element, ui);
        },
        /**
         * draggable helper
         * @param e
         * @returns {string}
         */
        draggableHelper: function (e) {

            var htmlTemplate = this.template('html');

            return htmlTemplate({});
        }
    });

    /**
     * STEdb Forms - Element Field
     * @param stedbFormBuilder
     * @constructor
     */
    STEdbFormElementField = function (stedbFormBuilder) {
        this.parentPrototype = STEdbFormElement.prototype;
        this.stedbFormBuilder = stedbFormBuilder;

        STEdbFormElement.call(this, 'field', '.stedb-form-element-field', {
            elementName: 'field',
            templateName: 'field',
            templateDefaultData: {
                values: {}
            },
            parentElement: 'row',
            childElement: null,
            removable: true,
            sortable: true,
            sortableOptions: {
                axis: ['x', 'y'],
                distance: 5,
                cursor: 'move',
                cursorAt: {
                    top: 20,
                    left: 0
                }
            },
            draggable: true,
            draggableOptions: {}
        });
    };

    /**
     * STEdb Forms - Element Field
     * prototype
     */
    STEdbFormElementField.prototype = $.extend({}, STEdbFormElement.prototype, {
        /**
         * init events
         */
        initEvents: function (id) {

            /** init field types */
            this.initFieldTypes(id);

            /** call parent function */
            this.parent('initEvents', id);
        },
        /**
         * init field types
         */
        initFieldTypes: function (id) {

            $.each(this.$element(id), this.proxy(function (fieldId, field) {

                /** get  field type element */
                var fieldElement = this.getFieldTypeElement($(field).data('type'));

                /** init field */
                fieldElement.initField($(field).data('id'));
            }));
        },
        /**
         * init field
         * @param id
         */
        initField: function (id) {
            this.initFieldEvents(id);
        },
        /**
         * init field events
         */
        initFieldEvents: function () {
            /** add the function to the subclass */
        },
        /**
         * get field type element
         */
        getFieldTypeElement: function (fieldType) {
            var elementFieldTypeClassName = 'STEdbFormElementField' + this.toProperCase(fieldType);

            /** check element field type */
            if (!window.hasOwnProperty(elementFieldTypeClassName)) {

                /** return default */
                return this;
            }

            /** return field element */
            return new window[elementFieldTypeClassName](this, this.stedbFormBuilder);
        },
        /**
         * add element
         * @param $element
         * @param $containerElement
         * @returns {*}
         */
        addElement: function ($element, $containerElement) {

            var elements = this.stedbFormBuilder.elements,
                rowId = $containerElement.closest(elements.row.selector).data('id'),
                fieldId = this.options.id++,
                fieldElement = this.getFieldTypeElement($element.data('type')),
                htmlTemplate = this.template($element.data('type') + '-html');

            debugger;
            var dbg =htmlTemplate($.extend({}, {
                rowId: rowId,
                fieldId: fieldId,
                fieldType: $element.data('type')
            }, fieldElement.options.templateDefaultData));

            /** set html */
            $element.replaceWith($(htmlTemplate($.extend({}, {
                rowId: rowId,
                fieldId: fieldId,
                fieldType: $element.data('type')
            }, fieldElement.options.templateDefaultData))));

            /** add social element */
            this.addSocialElement(fieldId, $element);

            /** call parent function */
            this.parent('addElement', fieldId, $element);
        },
        /**
         * add social element
         * @param fieldId
         * @param $element
         */
        addSocialElement: function (fieldId, $element) {

            /** check is social element */
            if ($element.data('type') in this.stedbFormBuilder.socialElements) {

                /** add social element */
                this.stedbFormBuilder.socialElements[$element.data('type')].push(parseInt(fieldId));
            }
        },
        /**
         * add sortable to element
         * @param $element
         */
        addSortable: function ($element) {

            /** check is sortable */
            if (!this.options.sortable) {
                return;
            }

            /** check is already sortable */
            if ($element.hasClass('ui-sortable')) {
                return;
            }

            /** call parent function */
            this.parent('addSortable', $element);

            /** add sortable */
            $element.sortable('option', 'items', $element.sortable('option', 'items') + ', ' + this.options.sortableSetOptions.items);
            $element.sortable('option', 'connectWith', $element.sortable('option', 'connectWith') + ', ' + this.options.sortableSetOptions.connectWith);
        },
        /**
         * on sortable receive
         *
         * @param e
         * @param element
         * @param ui
         */
        onSortableReceive: function (e, element, ui) {

            /** check dragging from addable */
            if (ui.sender.is(this.selector + '-add') && ui.sender.hasClass('ui-draggable')) {

                /** add element */
                this.addElement(ui.helper, element);
            }

            /** check dragging other container */
            if (ui.sender.is(this.options.containerSelector)) {
                this.changeElementId(ui.item);
            }

            /** disable social element draggable */
            this.disableSocialElementDraggable(ui.sender);

            /** call parent function */
            this.parent('onSortableReceive', e, element, ui);
        },
        /**
         * add draggable to element
         * @param $element
         */
        addDraggable: function ($element) {

            /** call parent function */
            this.parent('addDraggable', $element);

            /** each field */
            $.each($element, this.proxy(function (i, field) {
                this.disableSocialElementDraggable($(field));
            }));
        },
        /**
         * disable social element draggable
         * @param $field
         */
        disableSocialElementDraggable: function ($field) {

            /** check is social element */
            if ($field.data('type') in this.stedbFormBuilder.socialElements) {

                /** check if social element already exists in form builder */
                if (this.stedbFormBuilder.socialElements[$field.data('type')].length) {

                    /** disable draggable */
                    $field.draggable('option', 'disabled', true);

                    /** add disabled message */
                    $field.find('.stedb-form-element-social-field-icon img').attr('title', this.stedbFormBuilder.options.wp.l10n['disabled_only_one_social_field_can_be_added']);
                }
            }
        },
        /**
         * on draggable start
         * @param e
         */
        onDraggableStart: function (e) {

            /** check has rows in form builder */
            if (!this.stedbFormBuilder.elements.row.$container().children().length) {

                /** add covering bg */
                this.stedbFormBuilder.$('.stedb-forms-draggable-elements').prepend(
                    $('<div>').addClass('stedb-forms-covering-background')
                );

                /** add highlight arrow */
                this.stedbFormBuilder.$(this.stedbFormBuilder.elements.row.selector + '-add-container').prepend(
                    $('<div>').addClass('stedb-form-element-row-highlight-arrow')
                );

                /** add highlight */
                this.stedbFormBuilder.$(this.stedbFormBuilder.elements.row.selector + '-add')
                    .addClass('stedb-form-element-row-highlight');
            }

            /** call parent function */
            this.parent('onDraggableStart', e);
        },
        /**
         * on draggable stop
         * @param e
         */
        onDraggableStop: function (e) {

            /** remove covering bg */
            this.stedbFormBuilder.$('.stedb-forms-draggable-elements').find('.stedb-forms-covering-background').remove();

            /** remove highlight arrow */
            this.stedbFormBuilder.$(this.stedbFormBuilder.elements.row.selector + '-add-container').find('.stedb-form-element-row-highlight-arrow').remove();

            /** remove highlight */
            this.stedbFormBuilder.$(this.stedbFormBuilder.elements.row.selector + '-add').removeClass('stedb-form-element-row-highlight');

            /** call parent function */
            this.parent('onDraggableStop', e);
        },
        /**
         * draggable helper
         * @param e
         * @returns {string}
         */
        draggableHelper: function (e) {
            var $element = $(e.currentTarget),
                fieldElement = this.getFieldTypeElement($element.data('type')),
                htmlTemplate = this.template($element.data('type') + '-html');

               
            return htmlTemplate($.extend({}, {
                fieldType: $element.data('type')
            }, fieldElement.options.templateDefaultData));
        },
        /**
         * remove element
         * @param $element
         * @returns {*}
         */
        removeElement: function ($element) {

            this.removeSocialElement($element);

            /** call parent function */
            this.parent('removeElement', $element);
        },
        /**
         * remove element
         * @param $element
         * @returns {*}
         */
        removeSocialElement: function ($element) {

            /** check is social element */
            if (!($element.data('type') in this.stedbFormBuilder.socialElements)) {
                return;
            }

            /** remove from social elements array */
            this.stedbFormBuilder.socialElements[$element.data('type')] = _.without(this.stedbFormBuilder.socialElements[$element.data('type')], parseInt($element.data('id')));

            var $addField = $(this.stedbFormBuilder.$(this.selector + '-add' + '[data-type=' + $element.data('type') + ']'));

            /** enable draggable */
            $addField.draggable('option', 'disabled', false);

            /** remove disabled message */
            $addField.find('.stedb-form-element-social-field-icon img').attr('title', '');
        },
        /**
         * to poper case
         * @param str
         * @returns {*}
         */
        toProperCase: function (str) {
            return str.replace(/-/g, ' ').replace(/_/g, ' ').replace(/\w\S*/g, function (txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            }).replace(/ /g, '');
        }
    });

}(jQuery));
