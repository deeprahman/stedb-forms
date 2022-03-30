/**
 * wp localize variables
 * @typedef {object} stedb_forms
 * @typedef {object} stedb_forms_l10n
 * @typedef {object} stedb_forms_ajax
 */

/** Create jQuery anonymous function */
(function ($) {

    /** account id visibility */
    $('.stedb-forms-account-user-id-visibility').on('click', function () {

        var $accountUserId = $('#stedb-forms-account-user-id');

        /** password type */
        if ($accountUserId.attr('type') === 'password') {
            $accountUserId.attr('type', 'text');
            $(this).find('.stedb-forms-show-text').hide();
            $(this).find('.stedb-forms-hide-text').show();

            return false;
        }

        /** text type */
        if ($accountUserId.attr('type') === 'text') {
            $accountUserId.attr('type', 'password');
            $(this).find('.stedb-forms-show-text').show();
            $(this).find('.stedb-forms-hide-text').hide();

            return false;
        }
    });

    /** account secret visibility */
    $('.stedb-forms-account-secret-visibility').on('click', function () {

        var $accountSecret = $('#stedb-forms-account-secret');

        /** password type */
        if ($accountSecret.attr('type') === 'password') {
            $accountSecret.attr('type', 'text');
            $(this).find('.stedb-forms-show-text').hide();
            $(this).find('.stedb-forms-hide-text').show();

            return false;
        }

        /** text type */
        if ($accountSecret.attr('type') === 'text') {
            $accountSecret.attr('type', 'password');
            $(this).find('.stedb-forms-show-text').show();
            $(this).find('.stedb-forms-hide-text').hide();

            return false;
        }
    });

    /**
     * STEdb Forms
     * auth error dialog
     */
    var $stedbFormsAuthErrorDialog = $('#stedb-forms-auth-error-dialog');

    /** init dialog */
    $stedbFormsAuthErrorDialog.dialog({
        title: $stedbFormsAuthErrorDialog.data('title'),
        dialogClass: 'wp-dialog',
        autoOpen: true,
        draggable: false,
        width: 'auto',
        modal: true,
        resizable: false,
        closeOnEscape: true,
        position: {
            my: 'center',
            at: 'center',
            of: window
        },
        buttons: [
            {
                text: stedb_forms_l10n['close'],
                class: 'button',
                click: function () {
                    $(this).dialog('close');
                }
            },
            {
                text: stedb_forms_l10n['wordpress_settings'],
                class: 'button-primary',
                click: function () {
                    window.location.replace(stedb_forms['options_general_url']);
                }
            },
            {
                text: stedb_forms_l10n['stedb_forms_settings'],
                class: 'button-primary',
                click: function () {
                    window.location.replace(stedb_forms['stedb_forms_settings_url']);
                }
            }
        ],
        open: function () {
            /** close dialog by clicking the overlay behind it */
            $('.ui-widget-overlay').bind('click', function () {
                $('#stedb-forms-auth-error-dialog').dialog('close');
            });
        }
    });

    /**
     * STEdb Forms
     * campaign from email verify dialog
     */
    var $stedbFormsCampaignFromEmailVerifyDialog = $('#stedb-forms-campaign-from-email-verify-dialog');

    /** init dialog */
    $stedbFormsCampaignFromEmailVerifyDialog.dialog({
        title: $stedbFormsCampaignFromEmailVerifyDialog.data('title'),
        dialogClass: 'wp-dialog',
        autoOpen: false,
        draggable: false,
        width: 'auto',
        modal: true,
        resizable: false,
        closeOnEscape: true,
        position: {
            my: 'center',
            at: 'center',
            of: window
        },
        buttons: [
            {
                text: stedb_forms_l10n['close'],
                class: 'button',
                click: function () {
                    $(this).dialog('close');
                }
            },
            {
                text: stedb_forms_l10n['verify'],
                class: 'button-primary',
                click: function () {

                    $.post(
                        ajaxurl,
                        {
                            action: 'stedb_forms_get_campaigns_check_from_email_with_code',
                            _ajax_nonce: stedb_forms_ajax['get_campaigns_check_from_email_with_code_nonce'],
                            email: $('#stedb-forms-campaign-from-email').val(),
                            code: $(this).find('input[name="stedb_forms_code[]"]').map(function (id, n) {
                                return $(n).val();
                            }).get().join('')
                        },
                        function (response) {

                            /** success */
                            if (true === response['success']) {
                                $stedbFormsCampaignFromEmailVerifyDialog.dialog('close');

                                /** add confirmed class to button */
                                var $buttonVerifyFromEmail = $('.button.stedb-forms-campaign-verify-from-email');

                                if (!$buttonVerifyFromEmail.hasClass('is-confirmed')) {
                                    $buttonVerifyFromEmail.addClass('is-confirmed');
                                }
                            }

                            /** error */
                            if (false === response['success']) {
                                alert(response['data']['error']);
                            }
                        },
                        'json'
                    );
                }
            }
        ]
    });

    /** campaign verify from email button */
    $('body.stedb-forms_page_stedb-forms-add-campaign .button.stedb-forms-campaign-verify-from-email').on('click', function () {

        var $buttonVerifyFromEmail = $(this);

        /** add loading */
        $buttonVerifyFromEmail.addClass('disabled');
        $buttonVerifyFromEmail.find('.spinner').addClass('is-active');

        $.post(
            ajaxurl,
            {
                action: 'stedb_forms_set_campaigns_check_from_email',
                _ajax_nonce: stedb_forms_ajax['set_campaigns_check_from_email_nonce'],
                email: $('#stedb-forms-campaign-from-email').val()
            },
            function (response) {

                /** success */
                if (true === response['success']) {
                    $stedbFormsCampaignFromEmailVerifyDialog.dialog('open');
                }

                /** error */
                if (false === response['success']) {
                    alert(response['data']['error']);
                }
            },
            'json'
        ).always(function () {

            /** remove loading */
            $buttonVerifyFromEmail.removeClass('disabled');
            $buttonVerifyFromEmail.find('.spinner').removeClass('is-active');
        });

        return false;
    });

    /** campaign verify from email input change */
    $('body.stedb-forms_page_stedb-forms-add-campaign #stedb-forms-campaign-from-email').on('change', function () {

        var $buttonVerifyFromEmail = $('.button.stedb-forms-campaign-verify-from-email');

        /** add loading */
        $buttonVerifyFromEmail.addClass('disabled');
        $buttonVerifyFromEmail.find('.spinner').addClass('is-active');

        $.post(
            ajaxurl,
            {
                action: 'stedb_forms_get_campaigns_check_from_email',
                _ajax_nonce: stedb_forms_ajax['get_campaigns_check_from_email_nonce'],
                email: $(this).val()
            },
            function (response) {

                /** success */
                if (true === response['success']) {
                    if (!$buttonVerifyFromEmail.hasClass('is-confirmed')) {
                        $buttonVerifyFromEmail.addClass('is-confirmed');
                    }
                }

                /** error */
                if (false === response['success']) {

                    switch (response['data']['error_code']) {
                        /** unconfirmed address */
                        case 'stedb_forms_api_error_400':
                            $buttonVerifyFromEmail.removeClass('is-confirmed');
                            break;
                        /** not found */
                        case 'stedb_forms_api_error_404':
                            $buttonVerifyFromEmail.removeClass('is-confirmed');
                            break;
                        default:
                            $buttonVerifyFromEmail.removeClass('is-confirmed');
                            alert(response['data']['error_code'] + ': ' + response['data']['error']);
                    }
                }
            },
            'json'
        ).always(function () {

            /** remove loading */
            $buttonVerifyFromEmail.removeClass('disabled');
            $buttonVerifyFromEmail.find('.spinner').removeClass('is-active');
        });
    });

    /** confirm delete form */
    $('.wp-list-table.stedb-forms-lists .row-actions .delete').on('click', function () {

        /** confirm delete form */
        if (!confirm(stedb_forms_l10n['confirm_delete_form_message'])) {
            return false;
        }
    });

    /** confirm delete entry */
    $('.wp-list-table.stedb-forms-entries .row-actions .delete').on('click', function () {

        /** confirm delete form */
        if (!confirm(stedb_forms_l10n['confirm_delete_entry_message'])) {
            return false;
        }
    });

    /** confirm delete campaign */
    $('.wp-list-table.stedb-forms-campaigns .row-actions .delete').on('click', function () {

        /** confirm delete form */
        if (!confirm(stedb_forms_l10n['confirm_delete_campaign_message'])) {
            return false;
        }
    });

    /** campaign reports */
    if ($('body.stedb-forms_page_stedb-forms-reports #stedb-forms-campaign-reports-table')) {

        /** date start */
        $('#stedb-forms-campaigns-report-date-start').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'mm-dd-yy'
        });

        /** date end */
        $('#stedb-forms-campaigns-report-date-end').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'mm-dd-yy'
        });
    }

}(jQuery));