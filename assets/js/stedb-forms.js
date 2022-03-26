/** Create jQuery anonymous function */
(function ($) {

    $(document).on('click', 'form.stedb-form .stedb-forms-social-field', function (e) {
        e.preventDefault();

        var $form = $(this).closest('form.stedb-form'),
            $smProviderName = $form.find('input[name="sm_provider_name"]');

        $smProviderName.attr('value', $(this).data('sm_provider_name'));

        $(this).closest('form.stedb-form').submit();

        return false;
    });

    /**
     * stedb form
     * magnific popup
     */
    var stedbFormsPopupCounter = 0;

    $(document).on('mouseleave', function (e) {
        if (e.clientY < 0) {
            if (!stedbFormsPopupCounter) {

                $('form.stedb-form.stedb-forms-type--popup').each(function (i, form) {
                    $.magnificPopup.open({
                        items: {
                            src: form,
                            type: 'inline'
                        },
                        callbacks: {
                            beforeOpen: function () {
                                if ($(this.items[0].src).hasClass('stedb-forms-type--popup-sidebar-right')) {
                                    this.st.mainClass = 'mfp-move-from-right';
                                }
                            }
                        },
                        closeBtnInside: true
                    });
                });

                stedbFormsPopupCounter++;
            }
        }
    });

}(jQuery));