/* --------------------------------------------------------------
 extend_order_edit_form.js 2018-11-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Extends the order edit form.
 *
 * If the "recalculate" checkbox is checked the order will be marked with a recalculation flag that will later be
 * used to forward the amount changes to Klarna.
 */
(function() {
    'use strict';

    /**
     * Form Submit Buttons Selector
     *
     * @type {String}
     */
    const formSubmitButtonsSelector = '.bottom-save-bar input:button, form[name="save_order"] input:submit';

    /**
     * Initializes the module.
     *
     * @private
     */
    const init = () => {
        const $form = $('[name="save_order"]');
        const $checkbox = $('[name="recalculate"]');

        // Create additional amounts dialog.
        let html = [];

        if (KlarnaHub.Config.giftSystem) {
            html = html.concat(createNewValueFieldFor(
                'ot_coupon',
                KlarnaHub.Config.lang.OLD_COUPON_AMOUNT,
                KlarnaHub.Config.lang.NEW_COUPON_AMOUNT,
                'new-coupon-amount'
            ));
            html = html.concat(createNewValueFieldFor(
                'ot_discount',
                KlarnaHub.Config.lang.OLD_DISCOUNT_AMOUNT,
                KlarnaHub.Config.lang.NEW_DISCOUNT_AMOUNT,
                'new-discount-amount'
            ));
            html = html.concat(createNewValueFieldFor(
                'ot_gv',
                KlarnaHub.Config.lang.OLD_VOUCHER_AMOUNT,
                KlarnaHub.Config.lang.NEW_VOUCHER_AMOUNT,
                'new-voucher-amount'
            ));
        }

        html = html.concat(createNewValueFieldFor(
            'ot_shipping',
            KlarnaHub.Config.lang.OLD_SHIPPING_COSTS,
            KlarnaHub.Config.lang.NEW_SHIPPING_COSTS,
            'new-shipping-costs'
        ));

        const $dialog = $('<div/>', {html}).appendTo('body');

        $dialog.dialog({
            autoOpen: false,
            width: 500,
            height: 'auto',
            resizable: false,
            modal: true,
            title: 'Klarna',
            dialogClass: 'gx-container additional-costs-dialog ',
            buttons: [
                {
                    text: KlarnaHub.Config.lang.CLOSE,
                    click: () => $dialog.dialog('close')
                },
                {
                    text: KlarnaHub.Config.lang.NOTIFY_KLARNA,
                    click: () => {
                        $dialog.find('.validate').remove('validate error');

                        const getNewValueOrFallbackToOldValue = (newValueClass, oldOrderReference) => {

                            const referenceOrderLine = KlarnaHub.Config.klarnaOrder.order_lines.find(orderLine => orderLine.reference
                                === oldOrderReference);

                            let oldReferenceAmount = referenceOrderLine ? Math.abs(referenceOrderLine.total_amount)
                                / 100 : 0;

                            const $newReferenceAmount = $dialog.find('.' + newValueClass);

                            const newReferenceAmount = $newReferenceAmount.length ? $newReferenceAmount.val()
                                .replace(',', '.') : oldReferenceAmount;

                            if (newReferenceAmount && isNaN(newReferenceAmount)) {
                                $newReferenceAmount.addClass('validate error');
                            }

                            return newReferenceAmount;
                        }

                        const newCouponAmount = getNewValueOrFallbackToOldValue('new-coupon-amount', 'ot_coupon');
                        const newDiscountAmount = getNewValueOrFallbackToOldValue('new-discount-amount', 'ot_discount');
                        const newVoucherAmount = getNewValueOrFallbackToOldValue('new-voucher-amount', 'ot_gv');

                        const newShippingCosts = getNewValueOrFallbackToOldValue('new-shipping-costs', 'ot_shipping');

                        if ($dialog.find('.validate.error').length) {
                            return;
                        }

                        // Activate the recalculation flag for this order.
                        KlarnaHub.Api.activateRecalculationFlag(
                            parseFloat(newCouponAmount),
                            parseFloat(newDiscountAmount),
                            parseFloat(newVoucherAmount),
                            parseFloat(newShippingCosts)
                        ).then(() => $form[0].submit());
                    }
                }
            ],
            open: () => $('.additional-costs-dialog .ui-dialog-buttonset button:last').addClass('btn btn-primary')
        });

        $form.on('submit', (event) => {
            event.preventDefault();

            if (!$checkbox.prop('checked')) {
                $form[0].submit();
                return;
            }

            if (!html.length) {
                KlarnaHub.Api.activateRecalculationFlag(0, 0).then(() => $form[0].submit());
            } else {
                $dialog.dialog('open');
            }
        });

        $(formSubmitButtonsSelector).prop('disabled', false);
    };

    const createNewValueFieldFor = (orderReference, oldAmountLabel, newAmountLabel, newAmountClass) => {

        const referenceOrderLine = KlarnaHub.Config.klarnaOrder.order_lines.find(orderLine => orderLine.reference
            === orderReference);

        let oldReferenceAmount = referenceOrderLine ? Math.abs(referenceOrderLine.total_amount) / 100 : 0;

        if (referenceOrderLine) {

            // Check if some of the amount was refunded already.
            KlarnaHub.Config.klarnaOrder.refunds.forEach((refund) => {
                refund.order_lines.forEach((refundedOrderLine) => {
                    if (refundedOrderLine.reference === orderReference) {
                        oldReferenceAmount = Math.abs(refundedOrderLine.total_amount / 100); // Replace original value.
                    }
                });
            });
        }

        const oldReferenceAmountDisplay = oldReferenceAmount.toLocaleString(KlarnaHub.Config.klarnaOrder.locale, {
            style: 'currency',
            currency: KlarnaHub.Config.klarnaOrder.purchase_currency,
            currencyDisplay: 'code',
            useGrouping: false
        }).replace(',', '.');

        let elements = [];

        elements.push(
            $('<div/>', {
                'class': 'control-group',
                'html': [
                    $('<label/>', {
                        'text': oldAmountLabel,
                        'style': 'width:70%'
                    }),
                    $('<span/>', {
                        'text': oldReferenceAmountDisplay
                    })
                ]
            })
        );

        elements.push(
            $('<div/>', {
                'class': 'control-group',
                'html': [
                    $('<label/>', {
                        'text': newAmountLabel,
                        'style': 'width:70%'
                    }),
                    $('<div/>', {
                        'class': 'input-group',
                        'html': [
                            $('<input/>', {
                                'class': newAmountClass,
                                'value': oldReferenceAmountDisplay.replace(/[^\d.-]/g, '')
                            }),
                            $('<div/>', {
                                'class': 'input-group-addon',
                                'text': oldReferenceAmountDisplay.replace(/[\d.-]/g, '')
                            })
                        ]
                    })
                ]
            })
        );
        return elements;
    }

    // Pre-check the recalculation checkbox.
    $(document).on('JSENGINE_INIT_FINISHED', () => $('[name="recalculate"]').prop('checked', true).trigger('change'));

    // Disable the close button until KlarnaHub is ready.
    $(document).on('ready', () => $(formSubmitButtonsSelector).prop('disabled', true));

    KlarnaHub.on('ready', () => init());
})();
