/* Shared date picker for admin forms. Add data-native-date="true" only when a
 * browser-native date input is explicitly required by a third-party component. */
(function ($) {
    'use strict';

    function replaceMissingMaterialIcons(root) {
        $(root).find('.material-icons').each(function () {
            const icon = $(this);
            const name = icon.text().trim();
            if (name === 'chevron_left') icon.text('‹');
            if (name === 'chevron_right') icon.text('›');
        });
    }

    function decorateCalendar(root) {
        if (!window.moment) return;

        const today = window.moment();
        $(root).find('.dtp').addBack('.dtp').each(function () {
            const picker = $(this);
            const monthLabel = picker.find('.dtp-picker-month').text().trim();
            const isCurrentMonth = monthLabel === today.format('MMMM YYYY');

            picker.find('td[data-date]').each(function () {
                const day = Number($(this).data('date'));
                $(this).toggleClass('premium-datepicker-today', isCurrentMonth && day === today.date());
            });
        });
    }

    function initializeDatePicker(input) {
        const $input = $(input);
        if ($input.data('premiumDatePickerReady') || !$.fn.bootstrapMaterialDatePicker) return;

        let currentVal = ($input.val() || '').trim();
        if (currentVal && /^\d{4}-\d{2}-\d{2}$/.test(currentVal)) {
            const parts = currentVal.split('-');
            currentVal = parts[2] + '-' + parts[1] + '-' + parts[0];
            $input.val(currentVal);
        }

        $input.data('premiumDatePickerReady', true)
              .attr('type', 'text')
              .addClass('premium-global-datepicker')
              .attr('autocomplete', 'off');

        $input.bootstrapMaterialDatePicker({
            time: false,
            format: 'DD-MM-YYYY',
            clearButton: true,
            cancelText: 'Cancel',
            okText: 'Select'
        });
    }

    $(function () {
        $('input[type="date"], .premium-datepicker, .attendance-datepicker, .staff-premium-datepicker')
            .not('[data-native-date="true"]')
            .each(function () { initializeDatePicker(this); });

        replaceMissingMaterialIcons(document);
        decorateCalendar(document);

        new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === 1) {
                        $(node).find('input[type="date"], .premium-datepicker, .attendance-datepicker, .staff-premium-datepicker')
                            .not('[data-native-date="true"]')
                            .each(function () { initializeDatePicker(this); });
                        replaceMissingMaterialIcons(node);
                        decorateCalendar(node);
                    }
                });
            });
        }).observe(document.body, { childList: true, subtree: true });
    });
})(jQuery);
