'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
	payone_checkout.js 2017-09-19
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Payone Checkout
 *
 * @module Widgets/payone_checkout
 */
gambio.widgets.module('payone_checkout', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        p1_debug = true,
        module = {};

    // ########## PAYONE FUNCTIONS ##########

    var _p1_payment_submit_handler = function _p1_payment_submit_handler(e) {
        var selected_payment = $('form#checkout_payment').get(0).elements.payment.value;
        if (selected_payment === 'payone_cc') {
            if (p1_debug) {
                console.log('payone cc check triggered');
            }
            e.preventDefault();
            p1cc_check();
        }
    };

    var _initOnlineTransfer = function _initOnlineTransfer() {
        $('select#otrans_type').on('change', function (e) {
            var selected_type = $(this).val();
            var $pd_table = $(this).closest('table.payone_otrans_data');
            var $datarows = $('tr.datarow', $pd_table);
            $datarows.hide();
            $('.for_' + selected_type).show();
            if (selected_type == 'pfefinance' || selected_type == 'pfcard') {
                $(this).closest('div.payment_item').addClass('data_valid');
                $(this).closest('div.payment_item').click();
            }
        });
        $('select#otrans_type').trigger('change');

        var otrans_input_handler = function otrans_input_handler(e) {
            var any_empty = false;
            $('.payone_otrans_data input[type="text"]:visible').each(function () {
                if ($(this).val() === '') {
                    any_empty = true;
                }
            });
            if (any_empty === true) {
                $('table.payone_otrans_data').addClass('payone_data_missing');
            } else {
                $('table.payone_otrans_data').removeClass('payone_data_missing');
            }
            $(this).closest('div.payment_item').removeClass('data_valid');
        };

        $('.payone_otrans_data input[type="text"]').keyup(otrans_input_handler);
        $('.payone_otrans_data input[type="text"]').change(otrans_input_handler);
    };

    var _initELV = function _initELV() {
        $('table.payone_elv_data select[name="p1_elv_country"]').on('change', function (e) {
            var selected_iso_2 = $(this).val();
            var only_de_rows = $('tr.only_de', $(this).closest('table'));
            if (selected_iso_2 == 'DE') {
                only_de_rows.show('fast');
            } else {
                only_de_rows.hide('fast');
            }
        });
        $('table.payone_elv_data select[name="p1_elv_country"]').trigger('change');

        $('.sepadata input').on('change', function (e) {
            var sepadata = '';
            $('.sepadata input').each(function () {
                sepadata += $(this).val();
            });
            if (sepadata.length === 0) {
                $('tr.only_de input').removeAttr('disabled');
            } else {
                $('tr.only_de input').attr('disabled', 'disabled');
            }
        });

        $('.only_de input').on('change', function (e) {
            var accountdata = '';
            $('.only_de input').each(function () {
                accountdata += $(this).val();
            });
            if (accountdata.length === 0) {
                $('tr.sepadata input').removeAttr('disabled');
            } else {
                $('tr.sepadata input').attr('disabled', 'disabled');
            }
        });

        var pg_callback_elv = function pg_callback_elv(response) {
            if (p1_debug) {
                console.log(response);
            }
            var current_block = $('div.module_option_checked');
            if (!response || (typeof response === 'undefined' ? 'undefined' : _typeof(response)) != 'object' || response.status != 'VALID') {
                // error occurred
                var errormessage = p1_payment_error;
                if (typeof response.customermessage == 'string') {
                    errormessage = response.customermessage;
                }
                $('p.p1_error', current_block).html(errormessage);
                $('p.p1_error', current_block).show();
                current_block.closest('div.payment_item').removeClass('data_valid');
                current_block.get(0).scrollIntoView();
            } else {
                pg_callback_elv_none();
                $('form#checkout_payment').trigger('submit');
            }
        };

        var pg_callback_elv_none = function pg_callback_elv_none() {
            var $checked_payment = $('input[name="payment"]:checked');
            $('p.p1_error', $checked_payment.closest('div.payment_item')).hide();
            $('table.payone_elv_data').hide();
            $('div.p1_finaldata_elv').show();
            $('td.final_elv_country').html($('select#p1_elv_country option').filter(':selected').html());
            $('td.final_elv_accountnumber').html($('input#p1_elv_accountnumber').val());
            $('td.final_elv_bankcode').html($('input#p1_elv_bankcode').val());
            $('td.final_elv_iban').html($('input#p1_elv_iban').val());
            $('td.final_elv_bic').html($('input#p1_elv_bic').val());
            $checked_payment.closest('div.payment_item').addClass('data_valid');
            $('table.payone_elv_data').removeClass('payone_paydata');
        };

        var payone_elv_checkdata = function payone_elv_checkdata(e) {
            var input_bankcountry = $('select[name="p1_elv_country"] option').filter(':selected').val();
            var input_accountnumber = $('input[name="p1_elv_accountnumber"]', $this).val();
            var input_bankcode = $('input[name="p1_elv_bankcode"]', $this).val();
            var input_iban = $('input[name="p1_elv_iban"]', $this).val();
            var input_bic = $('input[name="p1_elv_bic"]', $this).val();

            if (p1_elv_checkmode == 'none') {
                pg_callback_elv_none();
            } else {
                e.preventDefault(); // prevent submit
                var pg_config = p1_elv_config;
                var pg = new PAYONE.Gateway(pg_config, pg_callback_elv);
                var data = {};
                if (input_iban.length > 0) {
                    data = {
                        iban: input_iban,
                        bic: input_bic,
                        bankcountry: input_bankcountry
                    };
                } else {
                    data = {
                        bankaccount: input_accountnumber,
                        bankcode: input_bankcode,
                        bankcountry: input_bankcountry
                    };
                }

                if (p1_debug) {
                    console.log(data);
                }
                pg.call(data);
            }
        };

        $('form#checkout_payment').on('submit', function (e) {
            var $checked_payment = $('input[name="payment"]:checked');
            if ($checked_payment.val() === 'payone_elv') {
                if ($checked_payment.closest('div.payment_item').hasClass('data_valid') === false) {
                    payone_elv_checkdata(e);
                }
            }
        });
    };

    var _initSafeInv = function _initSafeInv() {
        var _safeInvDisplayAgreement = function _safeInvDisplayAgreement() {
            var safeInvType = $('#p1_safeinv_type').val();
            $('tr.p1-safeinv-agreement').not('.p1-show-for-' + safeInvType).hide();
            $('tr.p1-show-for-' + safeInvType).show();
        };
        $('select[name="safeinv_type"]').on('change', _safeInvDisplayAgreement);
        _safeInvDisplayAgreement();
    };

    // ########## INITIALIZATION ##########

    /**
     * Initialize Module
     * @constructor
     */
    module.init = function (done) {
        if (p1_debug) {
            console.log('payone_checkout module initializing, submodule ' + options.module);
        }
        if (options.module == 'cc') {
            $('form#checkout_payment').on('submit', _p1_payment_submit_handler);
        }
        if (options.module == 'otrans') {
            _initOnlineTransfer();
        }
        if (options.module == 'elv') {
            _initELV();
        }
        if (options.module == 'safeinv') {
            _initSafeInv();
        }
        done();
    };

    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcGF5b25lX2NoZWNrb3V0LmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwicDFfZGVidWciLCJfcDFfcGF5bWVudF9zdWJtaXRfaGFuZGxlciIsImUiLCJzZWxlY3RlZF9wYXltZW50IiwiZ2V0IiwiZWxlbWVudHMiLCJwYXltZW50IiwidmFsdWUiLCJjb25zb2xlIiwibG9nIiwicHJldmVudERlZmF1bHQiLCJwMWNjX2NoZWNrIiwiX2luaXRPbmxpbmVUcmFuc2ZlciIsIm9uIiwic2VsZWN0ZWRfdHlwZSIsInZhbCIsIiRwZF90YWJsZSIsImNsb3Nlc3QiLCIkZGF0YXJvd3MiLCJoaWRlIiwic2hvdyIsImFkZENsYXNzIiwiY2xpY2siLCJ0cmlnZ2VyIiwib3RyYW5zX2lucHV0X2hhbmRsZXIiLCJhbnlfZW1wdHkiLCJlYWNoIiwicmVtb3ZlQ2xhc3MiLCJrZXl1cCIsImNoYW5nZSIsIl9pbml0RUxWIiwic2VsZWN0ZWRfaXNvXzIiLCJvbmx5X2RlX3Jvd3MiLCJzZXBhZGF0YSIsImxlbmd0aCIsInJlbW92ZUF0dHIiLCJhdHRyIiwiYWNjb3VudGRhdGEiLCJwZ19jYWxsYmFja19lbHYiLCJyZXNwb25zZSIsImN1cnJlbnRfYmxvY2siLCJzdGF0dXMiLCJlcnJvcm1lc3NhZ2UiLCJwMV9wYXltZW50X2Vycm9yIiwiY3VzdG9tZXJtZXNzYWdlIiwiaHRtbCIsInNjcm9sbEludG9WaWV3IiwicGdfY2FsbGJhY2tfZWx2X25vbmUiLCIkY2hlY2tlZF9wYXltZW50IiwiZmlsdGVyIiwicGF5b25lX2Vsdl9jaGVja2RhdGEiLCJpbnB1dF9iYW5rY291bnRyeSIsImlucHV0X2FjY291bnRudW1iZXIiLCJpbnB1dF9iYW5rY29kZSIsImlucHV0X2liYW4iLCJpbnB1dF9iaWMiLCJwMV9lbHZfY2hlY2ttb2RlIiwicGdfY29uZmlnIiwicDFfZWx2X2NvbmZpZyIsInBnIiwiUEFZT05FIiwiR2F0ZXdheSIsImliYW4iLCJiaWMiLCJiYW5rY291bnRyeSIsImJhbmthY2NvdW50IiwiYmFua2NvZGUiLCJjYWxsIiwiaGFzQ2xhc3MiLCJfaW5pdFNhZmVJbnYiLCJfc2FmZUludkRpc3BsYXlBZ3JlZW1lbnQiLCJzYWZlSW52VHlwZSIsIm5vdCIsImluaXQiLCJkb25lIl0sIm1hcHBpbmdzIjoiOzs7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7QUFLQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksaUJBREosRUFHSSxFQUhKLEVBS0ksVUFBVUMsSUFBVixFQUFnQjs7QUFFWjs7QUFFQTs7QUFFQSxRQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0lDLFdBQVcsRUFEZjtBQUFBLFFBRUlDLFVBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBRmQ7QUFBQSxRQUdJTSxXQUFXLElBSGY7QUFBQSxRQUlJUCxTQUFTLEVBSmI7O0FBTUE7O0FBRUEsUUFBSVEsNkJBQTZCLFNBQTdCQSwwQkFBNkIsQ0FBVUMsQ0FBVixFQUFhO0FBQzFDLFlBQUlDLG1CQUFtQlAsRUFBRSx1QkFBRixFQUEyQlEsR0FBM0IsQ0FBK0IsQ0FBL0IsRUFBa0NDLFFBQWxDLENBQTJDQyxPQUEzQyxDQUFtREMsS0FBMUU7QUFDQSxZQUFJSixxQkFBcUIsV0FBekIsRUFBc0M7QUFDbEMsZ0JBQUlILFFBQUosRUFBYztBQUNWUSx3QkFBUUMsR0FBUixDQUFZLDJCQUFaO0FBQ0g7QUFDRFAsY0FBRVEsY0FBRjtBQUNBQztBQUNIO0FBQ0osS0FURDs7QUFXQSxRQUFJQyxzQkFBc0IsU0FBdEJBLG1CQUFzQixHQUFZO0FBQ2xDaEIsVUFBRSxvQkFBRixFQUF3QmlCLEVBQXhCLENBQTJCLFFBQTNCLEVBQXFDLFVBQVVYLENBQVYsRUFBYTtBQUM5QyxnQkFBSVksZ0JBQWdCbEIsRUFBRSxJQUFGLEVBQVFt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a291dC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdHBheW9uZV9jaGVja291dC5qcyAyMDE3LTA5LTE5XG5cdEdhbWJpbyBHbWJIXG5cdGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG5cdENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuXHRSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcblx0W2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG5cdC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4qL1xuXG4vKipcbiAqIFBheW9uZSBDaGVja291dFxuICpcbiAqIEBtb2R1bGUgV2lkZ2V0cy9wYXlvbmVfY2hlY2tvdXRcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdwYXlvbmVfY2hlY2tvdXQnLFxuXG4gICAgW10sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgZGVmYXVsdHMgPSB7fSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgcDFfZGVidWcgPSB0cnVlLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cbiAgICAgICAgLy8gIyMjIyMjIyMjIyBQQVlPTkUgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgX3AxX3BheW1lbnRfc3VibWl0X2hhbmRsZXIgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgdmFyIHNlbGVjdGVkX3BheW1lbnQgPSAkKCdmb3JtI2NoZWNrb3V0X3BheW1lbnQnKS5nZXQoMCkuZWxlbWVudHMucGF5bWVudC52YWx1ZTtcbiAgICAgICAgICAgIGlmIChzZWxlY3RlZF9wYXltZW50ID09PSAncGF5b25lX2NjJykge1xuICAgICAgICAgICAgICAgIGlmIChwMV9kZWJ1Zykge1xuICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygncGF5b25lIGNjIGNoZWNrIHRyaWdnZXJlZCcpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgcDFjY19jaGVjaygpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIHZhciBfaW5pdE9ubGluZVRyYW5zZmVyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgJCgnc2VsZWN0I290cmFuc190eXBlJykub24oJ2NoYW5nZScsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAgICAgdmFyIHNlbGVjdGVkX3R5cGUgPSAkKHRoaXMpLnZhbCgpO1xuICAgICAgICAgICAgICAgIHZhciAkcGRfdGFibGUgPSAkKHRoaXMpLmNsb3Nlc3QoJ3RhYmxlLnBheW9uZV9vdHJhbnNfZGF0YScpO1xuICAgICAgICAgICAgICAgIHZhciAkZGF0YXJvd3MgPSAkKCd0ci5kYXRhcm93JywgJHBkX3RhYmxlKTtcbiAgICAgICAgICAgICAgICAkZGF0YXJvd3MuaGlkZSgpO1xuICAgICAgICAgICAgICAgICQoJy5mb3JfJyArIHNlbGVjdGVkX3R5cGUpLnNob3coKTtcbiAgICAgICAgICAgICAgICBpZiAoc2VsZWN0ZWRfdHlwZSA9PSAncGZlZmluYW5jZScgfHwgc2VsZWN0ZWRfdHlwZSA9PSAncGZjYXJkJykge1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJ2Rpdi5wYXltZW50X2l0ZW0nKS5hZGRDbGFzcygnZGF0YV92YWxpZCcpO1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJ2Rpdi5wYXltZW50X2l0ZW0nKS5jbGljaygpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgJCgnc2VsZWN0I290cmFuc190eXBlJykudHJpZ2dlcignY2hhbmdlJyk7XG5cbiAgICAgICAgICAgIHZhciBvdHJhbnNfaW5wdXRfaGFuZGxlciA9IGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAgICAgdmFyIGFueV9lbXB0eSA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICQoJy5wYXlvbmVfb3RyYW5zX2RhdGEgaW5wdXRbdHlwZT1cInRleHRcIl06dmlzaWJsZScpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICBpZiAoJCh0aGlzKS52YWwoKSA9PT0gJycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGFueV9lbXB0eSA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBpZiAoYW55X2VtcHR5ID09PSB0cnVlKSB7XG4gICAgICAgICAgICAgICAgICAgICQoJ3RhYmxlLnBheW9uZV9vdHJhbnNfZGF0YScpLmFkZENsYXNzKCdwYXlvbmVfZGF0YV9taXNzaW5nJyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgJCgndGFibGUucGF5b25lX290cmFuc19kYXRhJykucmVtb3ZlQ2xhc3MoJ3BheW9uZV9kYXRhX21pc3NpbmcnKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5jbG9zZXN0KCdkaXYucGF5bWVudF9pdGVtJykucmVtb3ZlQ2xhc3MoJ2RhdGFfdmFsaWQnKTtcbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgICQoJy5wYXlvbmVfb3RyYW5zX2RhdGEgaW5wdXRbdHlwZT1cInRleHRcIl0nKS5rZXl1cChvdHJhbnNfaW5wdXRfaGFuZGxlcik7XG4gICAgICAgICAgICAkKCcucGF5b25lX290cmFuc19kYXRhIGlucHV0W3R5cGU9XCJ0ZXh0XCJdJykuY2hhbmdlKG90cmFuc19pbnB1dF9oYW5kbGVyKTtcbiAgICAgICAgfTtcblxuICAgICAgICB2YXIgX2luaXRFTFYgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAkKCd0YWJsZS5wYXlvbmVfZWx2X2RhdGEgc2VsZWN0W25hbWU9XCJwMV9lbHZfY291bnRyeVwiXScpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgICAgIHZhciBzZWxlY3RlZF9pc29fMiA9ICQodGhpcykudmFsKCk7XG4gICAgICAgICAgICAgICAgdmFyIG9ubHlfZGVfcm93cyA9ICQoJ3RyLm9ubHlfZGUnLCAkKHRoaXMpLmNsb3Nlc3QoJ3RhYmxlJykpO1xuICAgICAgICAgICAgICAgIGlmIChzZWxlY3RlZF9pc29fMiA9PSAnREUnKSB7XG4gICAgICAgICAgICAgICAgICAgIG9ubHlfZGVfcm93cy5zaG93KCdmYXN0Jyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgb25seV9kZV9yb3dzLmhpZGUoJ2Zhc3QnKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICQoJ3RhYmxlLnBheW9uZV9lbHZfZGF0YSBzZWxlY3RbbmFtZT1cInAxX2Vsdl9jb3VudHJ5XCJdJykudHJpZ2dlcignY2hhbmdlJyk7XG5cbiAgICAgICAgICAgICQoJy5zZXBhZGF0YSBpbnB1dCcpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgICAgIHZhciBzZXBhZGF0YSA9ICcnO1xuICAgICAgICAgICAgICAgICQoJy5zZXBhZGF0YSBpbnB1dCcpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICBzZXBhZGF0YSArPSAkKHRoaXMpLnZhbCgpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIGlmIChzZXBhZGF0YS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgICAgICAgICAgJCgndHIub25seV9kZSBpbnB1dCcpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgJCgndHIub25seV9kZSBpbnB1dCcpLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICQoJy5vbmx5X2RlIGlucHV0Jykub24oJ2NoYW5nZScsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAgICAgdmFyIGFjY291bnRkYXRhID0gJyc7XG4gICAgICAgICAgICAgICAgJCgnLm9ubHlfZGUgaW5wdXQnKS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgYWNjb3VudGRhdGEgKz0gJCh0aGlzKS52YWwoKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBpZiAoYWNjb3VudGRhdGEubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICAgICAgICAgICQoJ3RyLnNlcGFkYXRhIGlucHV0JykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAkKCd0ci5zZXBhZGF0YSBpbnB1dCcpLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHZhciBwZ19jYWxsYmFja19lbHYgPSBmdW5jdGlvbiAocmVzcG9uc2UpIHtcbiAgICAgICAgICAgICAgICBpZiAocDFfZGVidWcpIHtcbiAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2cocmVzcG9uc2UpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB2YXIgY3VycmVudF9ibG9jayA9ICQoJ2Rpdi5tb2R1bGVfb3B0aW9uX2NoZWNrZWQnKTtcbiAgICAgICAgICAgICAgICBpZiAoIXJlc3BvbnNlIHx8IHR5cGVvZiByZXNwb25zZSAhPSAnb2JqZWN0JyB8fCByZXNwb25zZS5zdGF0dXMgIT0gJ1ZBTElEJykge1xuICAgICAgICAgICAgICAgICAgICAvLyBlcnJvciBvY2N1cnJlZFxuICAgICAgICAgICAgICAgICAgICB2YXIgZXJyb3JtZXNzYWdlID0gcDFfcGF5bWVudF9lcnJvcjtcbiAgICAgICAgICAgICAgICAgICAgaWYgKHR5cGVvZiByZXNwb25zZS5jdXN0b21lcm1lc3NhZ2UgPT0gJ3N0cmluZycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGVycm9ybWVzc2FnZSA9IHJlc3BvbnNlLmN1c3RvbWVybWVzc2FnZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAkKCdwLnAxX2Vycm9yJywgY3VycmVudF9ibG9jaykuaHRtbChlcnJvcm1lc3NhZ2UpO1xuICAgICAgICAgICAgICAgICAgICAkKCdwLnAxX2Vycm9yJywgY3VycmVudF9ibG9jaykuc2hvdygpO1xuICAgICAgICAgICAgICAgICAgICBjdXJyZW50X2Jsb2NrLmNsb3Nlc3QoJ2Rpdi5wYXltZW50X2l0ZW0nKS5yZW1vdmVDbGFzcygnZGF0YV92YWxpZCcpO1xuICAgICAgICAgICAgICAgICAgICBjdXJyZW50X2Jsb2NrLmdldCgwKS5zY3JvbGxJbnRvVmlldygpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIHBnX2NhbGxiYWNrX2Vsdl9ub25lKCk7XG4gICAgICAgICAgICAgICAgICAgICQoJ2Zvcm0jY2hlY2tvdXRfcGF5bWVudCcpLnRyaWdnZXIoJ3N1Ym1pdCcpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgIHZhciBwZ19jYWxsYmFja19lbHZfbm9uZSA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB2YXIgJGNoZWNrZWRfcGF5bWVudCA9ICQoJ2lucHV0W25hbWU9XCJwYXltZW50XCJdOmNoZWNrZWQnKTtcbiAgICAgICAgICAgICAgICAkKCdwLnAxX2Vycm9yJywgJGNoZWNrZWRfcGF5bWVudC5jbG9zZXN0KCdkaXYucGF5bWVudF9pdGVtJykpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAkKCd0YWJsZS5wYXlvbmVfZWx2X2RhdGEnKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgJCgnZGl2LnAxX2ZpbmFsZGF0YV9lbHYnKS5zaG93KCk7XG4gICAgICAgICAgICAgICAgJCgndGQuZmluYWxfZWx2X2NvdW50cnknKS5odG1sKCQoJ3NlbGVjdCNwMV9lbHZfY291bnRyeSBvcHRpb24nKS5maWx0ZXIoJzpzZWxlY3RlZCcpLmh0bWwoKSk7XG4gICAgICAgICAgICAgICAgJCgndGQuZmluYWxfZWx2X2FjY291bnRudW1iZXInKS5odG1sKCQoJ2lucHV0I3AxX2Vsdl9hY2NvdW50bnVtYmVyJykudmFsKCkpO1xuICAgICAgICAgICAgICAgICQoJ3RkLmZpbmFsX2Vsdl9iYW5rY29kZScpLmh0bWwoJCgnaW5wdXQjcDFfZWx2X2Jhbmtjb2RlJykudmFsKCkpO1xuICAgICAgICAgICAgICAgICQoJ3RkLmZpbmFsX2Vsdl9pYmFuJykuaHRtbCgkKCdpbnB1dCNwMV9lbHZfaWJhbicpLnZhbCgpKTtcbiAgICAgICAgICAgICAgICAkKCd0ZC5maW5hbF9lbHZfYmljJykuaHRtbCgkKCdpbnB1dCNwMV9lbHZfYmljJykudmFsKCkpO1xuICAgICAgICAgICAgICAgICRjaGVja2VkX3BheW1lbnQuY2xvc2VzdCgnZGl2LnBheW1lbnRfaXRlbScpLmFkZENsYXNzKCdkYXRhX3ZhbGlkJyk7XG4gICAgICAgICAgICAgICAgJCgndGFibGUucGF5b25lX2Vsdl9kYXRhJykucmVtb3ZlQ2xhc3MoJ3BheW9uZV9wYXlkYXRhJyk7XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICB2YXIgcGF5b25lX2Vsdl9jaGVja2RhdGEgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgICAgIHZhciBpbnB1dF9iYW5rY291bnRyeSA9ICQoJ3NlbGVjdFtuYW1lPVwicDFfZWx2X2NvdW50cnlcIl0gb3B0aW9uJykuZmlsdGVyKCc6c2VsZWN0ZWQnKS52YWwoKTtcbiAgICAgICAgICAgICAgICB2YXIgaW5wdXRfYWNjb3VudG51bWJlciA9ICQoJ2lucHV0W25hbWU9XCJwMV9lbHZfYWNjb3VudG51bWJlclwiXScsICR0aGlzKS52YWwoKTtcbiAgICAgICAgICAgICAgICB2YXIgaW5wdXRfYmFua2NvZGUgPSAkKCdpbnB1dFtuYW1lPVwicDFfZWx2X2Jhbmtjb2RlXCJdJywgJHRoaXMpLnZhbCgpO1xuICAgICAgICAgICAgICAgIHZhciBpbnB1dF9pYmFuID0gJCgnaW5wdXRbbmFtZT1cInAxX2Vsdl9pYmFuXCJdJywgJHRoaXMpLnZhbCgpO1xuICAgICAgICAgICAgICAgIHZhciBpbnB1dF9iaWMgPSAkKCdpbnB1dFtuYW1lPVwicDFfZWx2X2JpY1wiXScsICR0aGlzKS52YWwoKTtcblxuXG4gICAgICAgICAgICAgICAgaWYgKHAxX2Vsdl9jaGVja21vZGUgPT0gJ25vbmUnKSB7XG4gICAgICAgICAgICAgICAgICAgIHBnX2NhbGxiYWNrX2Vsdl9ub25lKCk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpOyAvLyBwcmV2ZW50IHN1Ym1pdFxuICAgICAgICAgICAgICAgICAgICB2YXIgcGdfY29uZmlnID0gcDFfZWx2X2NvbmZpZztcbiAgICAgICAgICAgICAgICAgICAgdmFyIHBnID0gbmV3IFBBWU9ORS5HYXRld2F5KHBnX2NvbmZpZywgcGdfY2FsbGJhY2tfZWx2KTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIGRhdGEgPSB7fTtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGlucHV0X2liYW4ubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YSA9IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpYmFuOiBpbnB1dF9pYmFuLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJpYzogaW5wdXRfYmljLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJhbmtjb3VudHJ5OiBpbnB1dF9iYW5rY291bnRyeSxcbiAgICAgICAgICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBkYXRhID0ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJhbmthY2NvdW50OiBpbnB1dF9hY2NvdW50bnVtYmVyLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJhbmtjb2RlOiBpbnB1dF9iYW5rY29kZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBiYW5rY291bnRyeTogaW5wdXRfYmFua2NvdW50cnksXG4gICAgICAgICAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHAxX2RlYnVnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhkYXRhKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBwZy5jYWxsKGRhdGEpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgICQoJ2Zvcm0jY2hlY2tvdXRfcGF5bWVudCcpLm9uKCdzdWJtaXQnLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgICAgIHZhciAkY2hlY2tlZF9wYXltZW50ID0gJCgnaW5wdXRbbmFtZT1cInBheW1lbnRcIl06Y2hlY2tlZCcpO1xuICAgICAgICAgICAgICAgIGlmICgkY2hlY2tlZF9wYXltZW50LnZhbCgpID09PSAncGF5b25lX2VsdicpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCRjaGVja2VkX3BheW1lbnQuY2xvc2VzdCgnZGl2LnBheW1lbnRfaXRlbScpLmhhc0NsYXNzKCdkYXRhX3ZhbGlkJykgPT09IGZhbHNlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBwYXlvbmVfZWx2X2NoZWNrZGF0YShlKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG4gICAgICAgIHZhciBfaW5pdFNhZmVJbnYgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB2YXIgX3NhZmVJbnZEaXNwbGF5QWdyZWVtZW50ID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciBzYWZlSW52VHlwZSA9ICQoJyNwMV9zYWZlaW52X3R5cGUnKS52YWwoKTtcbiAgICAgICAgICAgICAgICAkKCd0ci5wMS1zYWZlaW52LWFncmVlbWVudCcpLm5vdCgnLnAxLXNob3ctZm9yLScgKyBzYWZlSW52VHlwZSkuaGlkZSgpO1xuICAgICAgICAgICAgICAgICQoJ3RyLnAxLXNob3ctZm9yLScgKyBzYWZlSW52VHlwZSkuc2hvdygpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgJCgnc2VsZWN0W25hbWU9XCJzYWZlaW52X3R5cGVcIl0nKS5vbignY2hhbmdlJywgX3NhZmVJbnZEaXNwbGF5QWdyZWVtZW50KTtcbiAgICAgICAgICAgIF9zYWZlSW52RGlzcGxheUFncmVlbWVudCgpO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXRpYWxpemUgTW9kdWxlXG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuICAgICAgICAgICAgaWYgKHAxX2RlYnVnKSB7XG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ3BheW9uZV9jaGVja291dCBtb2R1bGUgaW5pdGlhbGl6aW5nLCBzdWJtb2R1bGUgJyArIG9wdGlvbnMubW9kdWxlKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChvcHRpb25zLm1vZHVsZSA9PSAnY2MnKSB7XG4gICAgICAgICAgICAgICAgJCgnZm9ybSNjaGVja291dF9wYXltZW50Jykub24oJ3N1Ym1pdCcsIF9wMV9wYXltZW50X3N1Ym1pdF9oYW5kbGVyKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChvcHRpb25zLm1vZHVsZSA9PSAnb3RyYW5zJykge1xuICAgICAgICAgICAgICAgIF9pbml0T25saW5lVHJhbnNmZXIoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmIChvcHRpb25zLm1vZHVsZSA9PSAnZWx2Jykge1xuICAgICAgICAgICAgICAgIF9pbml0RUxWKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBpZiAob3B0aW9ucy5tb2R1bGUgPT0gJ3NhZmVpbnYnKSB7XG4gICAgICAgICAgICAgICAgX2luaXRTYWZlSW52KCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9XG4pO1xuIl19
