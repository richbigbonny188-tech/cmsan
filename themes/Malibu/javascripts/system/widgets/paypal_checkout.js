'use strict';

/* --------------------------------------------------------------
 paypal_checkout.js 2017-10-30
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals ppp, initPPP */

/**
 * PayPal Checkout
 *
 * Loads and handles the actions of the PayPal payment wall
 *
 * @module Widgets/paypal_checkout
 */
gambio.widgets.module('paypal_checkout', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var _checkPayPal3 = function _checkPayPal3() {
        var paypal3 = $('input[value="paypal3"]'),
            hub_paypal3 = $('input[data-module_code="PayPalHub"]');

        if (paypal3.get(0)) {
            return paypal3.get(0).checked;
        }

        return hub_paypal3.get(0).checked;
    };

    var $this = $(this),
        defaults = {
        thirdPartyPaymentsBlock: []
    },
        options = $.extend(true, {}, defaults, data),
        module = {},
        paypal3_checked = _checkPayPal3,
        continue_button_text = $('div.continue_button input').val(),
        ppplus_continue = $('<div id="ppplus_continue" class="col-xs-6 col-sm-4 col-sm-offset-4 col-md-3 ' + ' col-md-offset-6 text-right paypal_continue_button"><input type="submit" ' + ' class="btn btn-primary btn-block" value="' + continue_button_text + '"></div>');

    // ########## EVENT HANDLERS ##########

    var _paymentItemOnClick = function _paymentItemOnClick(e) {
        $('.order_payment #checkout_payment div.items div.payment_item').removeClass('module_option_selected');

        if ($('#ppplus', this).length > 0) {
            $(this).css('background-image', 'none');
            $(this).css('background-color', 'transparent');
            $('div.paypal_continue_button').show();
            $('div.continue_button').hide();
            paypal3_checked = true;
        } else {
            if (paypal3_checked) {
                paypal3_checked = false;
                console.log('3rd party payment selected ...');
                if (ppp.deselectPaymentMethod) {
                    console.log('... and deselectPaymentMethod() called.');
                    ppp.deselectPaymentMethod();
                } else {
                    console.log('... and pp+ widget re-initialized.');
                    initPPP(options.thirdPartyPaymentsBlock);
                }
            }
            $('div.paypal_continue_button').hide();
            $('div.continue_button').show();
            $(this).addClass('module_option_selected');
        }
    };

    var _ppplusContinueOnClick = function _ppplusContinueOnClick(e) {
        ppp.doContinue();
        return false;
    };

    // ########## INITIALIZATION ##########

    /**
     * Initialize Module
     * @constructor
     */
    module.init = function (done) {

        if ($('#ppplus').length > 0) {
            $('div.continue_button:first').before(ppplus_continue);

            $('input[name="payment"]:checked').closest('div.payment_item').addClass('module_option_selected');
            $('#ppplus').closest('div.payment_item').addClass('ppplus_payment_item');

            if ($('body').on) {
                $('div.payment_item_container').on('click', _paymentItemOnClick);
                $('div.paypal_continue_button').on('click', _ppplusContinueOnClick);
            } else {
                $('body').delegate('div.payment_item_container', 'click', _paymentItemOnClick);
                $('body').delegate('#ppplus_continue', 'click', _ppplusContinueOnClick);
            }

            $('div.payment_item input[value="paypal3"]').closest('div.payment_item').css('border-bottom', 'none');

            $('iframe').ready(function () {
                $('.list-group-item').each(function () {
                    $(this).css('display', 'block');
                });
            });

            if (initPPP) {
                initPPP(options.thirdPartyPaymentsBlock);
            }
        }

        done();
    };

    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcGF5cGFsX2NoZWNrb3V0LmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiX2NoZWNrUGF5UGFsMyIsInBheXBhbDMiLCIkIiwiaHViX3BheXBhbDMiLCJnZXQiLCJjaGVja2VkIiwiJHRoaXMiLCJkZWZhdWx0cyIsInRoaXJkUGFydHlQYXltZW50c0Jsb2NrIiwib3B0aW9ucyIsImV4dGVuZCIsInBheXBhbDNfY2hlY2tlZCIsImNvbnRpbnVlX2J1dHRvbl90ZXh0IiwidmFsIiwicHBwbHVzX2NvbnRpbnVlIiwiX3BheW1lbnRJdGVtT25DbGljayIsImUiLCJyZW1vdmVDbGFzcyIsImxlbmd0aCIsImNzcyIsInNob3ciLCJoaWRlIiwiY29uc29sZSIsImxvZyIsInBwcCIsImRlc2VsZWN0UGF5bWVudE1ldGhvZCIsImluaXRQUFAiLCJhZGRDbGFzcyIsIl9wcHBsdXNDb250aW51ZU9uQ2xpY2siLCJkb0NvbnRpbnVlIiwiaW5pdCIsImRvbmUiLCJiZWZvcmUiLCJjbG9zZXN0Iiwib24iLCJkZWxlZ2F0ZSIsInJlYWR5IiwiZWFjaCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOztBQUVBOzs7Ozs7O0FBT0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLGlCQURKLEVBR0ksRUFISixFQUtJLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRUE7O0FBRUEsUUFBSUMsZ0JBQWdCLFNBQWhCQSxhQUFnQixHQUFZO0FBQzVCLFlBQUlDLFVBQVVDLEVBQUUsd0JBQUYsQ0FBZDtBQUFBLFlBQ0lDLGNBQWNELEVBQUUscUNBQUYsQ0FEbEI7O0FBR0EsWUFBSUQsUUFBUUcsR0FBUixDQUFZLENBQVosQ0FBSixFQUFvQjtBQUNoQixtQkFBT0gsUUFBUUcsR0FBUixDQUFZLENBQVosRUFBZUMsT0FBdEI7QUFDSDs7QUFFRCxlQUFPRixZQUFZQyxHQUFaLENBQWdCLENBQWhCLEVBQW1CQyxPQUEx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QXFELGtCQUFyRCxFQUF5RWQsR0FBekUsQ0FBNkUsZUFBN0UsRUFBOEYsTUFBOUY7O0FBRUFqQixjQUFFLFFBQUYsRUFBWWtDLEtBQVosQ0FBa0IsWUFBWTtBQUMxQmxDLGtCQUFFLGtCQUFGLEVBQXNCbUMsSUFBdEIsQ0FBMkIsWUFBWTtBQUNuQ25DLHNCQUFFLElBQUYsRUFBUWlCLEdBQVIsQ0FBWSxTQUFaLEVBQXVCLE9BQXZCO0FBQ0gsaUJBRkQ7QUFHSCxhQUpEOztBQU1BLGdCQUFJTyxPQUFKLEVBQWE7QUFDVEEsd0JBQVFqQixRQUFRRCx1QkFBaEI7QUFDSDtBQUNKOztBQUVEdUI7QUFDSCxLQTlCRDs7QUFnQ0EsV0FBT2pDLE1BQVA7QUFDSCxDQTlHTCIsImZpbGUiOiJ3aWRnZXRzL3BheXBhbF9jaGVja291dC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcGF5cGFsX2NoZWNrb3V0LmpzIDIwMTctMTAtMzBcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE3IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKiBnbG9iYWxzIHBwcCwgaW5pdFBQUCAqL1xuXG4vKipcbiAqIFBheVBhbCBDaGVja291dFxuICpcbiAqIExvYWRzIGFuZCBoYW5kbGVzIHRoZSBhY3Rpb25zIG9mIHRoZSBQYXlQYWwgcGF5bWVudCB3YWxsXG4gKlxuICogQG1vZHVsZSBXaWRnZXRzL3BheXBhbF9jaGVja291dFxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG4gICAgJ3BheXBhbF9jaGVja291dCcsXG5cbiAgICBbXSxcblxuICAgIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAgICAgJ3VzZSBzdHJpY3QnO1xuXG4gICAgICAgIC8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIHZhciBfY2hlY2tQYXlQYWwzID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdmFyIHBheXBhbDMgPSAkKCdpbnB1dFt2YWx1ZT1cInBheXBhbDNcIl0nKSxcbiAgICAgICAgICAgICAgICBodWJfcGF5cGFsMyA9ICQoJ2lucHV0W2RhdGEtbW9kdWxlX2NvZGU9XCJQYXlQYWxIdWJcIl0nKTtcblxuICAgICAgICAgICAgaWYgKHBheXBhbDMuZ2V0KDApKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHBheXBhbDMuZ2V0KDApLmNoZWNrZWQ7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiBodWJfcGF5cGFsMy5nZXQoMCkuY2hlY2tlZDtcbiAgICAgICAgfTtcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgZGVmYXVsdHMgPSB7XG4gICAgICAgICAgICAgICAgdGhpcmRQYXJ0eVBheW1lbnRzQmxvY2s6IFtdXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fSxcblxuXG4gICAgICAgICAgICBwYXlwYWwzX2NoZWNrZWQgPSBfY2hlY2tQYXlQYWwzLFxuICAgICAgICAgICAgY29udGludWVfYnV0dG9uX3RleHQgPSAkKCdkaXYuY29udGludWVfYnV0dG9uIGlucHV0JykudmFsKCksXG4gICAgICAgICAgICBwcHBsdXNfY29udGludWUgPSAkKCc8ZGl2IGlkPVwicHBwbHVzX2NvbnRpbnVlXCIgY2xhc3M9XCJjb2wteHMtNiBjb2wtc20tNCBjb2wtc20tb2Zmc2V0LTQgY29sLW1kLTMgJ1xuICAgICAgICAgICAgICAgICsgJyBjb2wtbWQtb2Zmc2V0LTYgdGV4dC1yaWdodCBwYXlwYWxfY29udGludWVfYnV0dG9uXCI+PGlucHV0IHR5cGU9XCJzdWJtaXRcIiAnXG4gICAgICAgICAgICAgICAgKyAnIGNsYXNzPVwiYnRuIGJ0bi1wcmltYXJ5IGJ0bi1ibG9ja1wiIHZhbHVlPVwiJyArIGNvbnRpbnVlX2J1dHRvbl90ZXh0ICsgJ1wiPjwvZGl2PicpO1xuXG5cbiAgICAgICAgLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSUyAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyIF9wYXltZW50SXRlbU9uQ2xpY2sgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgJCgnLm9yZGVyX3BheW1lbnQgI2NoZWNrb3V0X3BheW1lbnQgZGl2Lml0ZW1zIGRpdi5wYXltZW50X2l0ZW0nKS5yZW1vdmVDbGFzcygnbW9kdWxlX29wdGlvbl9zZWxlY3RlZCcpO1xuXG4gICAgICAgICAgICBpZiAoJCgnI3BwcGx1cycsIHRoaXMpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmNzcygnYmFja2dyb3VuZC1pbWFnZScsICdub25lJyk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5jc3MoJ2JhY2tncm91bmQtY29sb3InLCAndHJhbnNwYXJlbnQnKTtcbiAgICAgICAgICAgICAgICAkKCdkaXYucGF5cGFsX2NvbnRpbnVlX2J1dHRvbicpLnNob3coKTtcbiAgICAgICAgICAgICAgICAkKCdkaXYuY29udGludWVfYnV0dG9uJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgIHBheXBhbDNfY2hlY2tlZCA9IHRydWU7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGlmIChwYXlwYWwzX2NoZWNrZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgcGF5cGFsM19jaGVja2VkID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCczcmQgcGFydHkgcGF5bWVudCBzZWxlY3RlZCAuLi4nKTtcbiAgICAgICAgICAgICAgICAgICAgaWYgKHBwcC5kZXNlbGVjdFBheW1lbnRNZXRob2QpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCcuLi4gYW5kIGRlc2VsZWN0UGF5bWVudE1ldGhvZCgpIGNhbGxlZC4nKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHBwcC5kZXNlbGVjdFBheW1lbnRNZXRob2QoKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCcuLi4gYW5kIHBwKyB3aWRnZXQgcmUtaW5pdGlhbGl6ZWQuJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBpbml0UFBQKG9wdGlvbnMudGhpcmRQYXJ0eVBheW1lbnRzQmxvY2spO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICQoJ2Rpdi5wYXlwYWxfY29udGludWVfYnV0dG9uJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICQoJ2Rpdi5jb250aW51ZV9idXR0b24nKS5zaG93KCk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnbW9kdWxlX29wdGlvbl9zZWxlY3RlZCcpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIHZhciBfcHBwbHVzQ29udGludWVPbkNsaWNrID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIHBwcC5kb0NvbnRpbnVlKCk7XG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXRpYWxpemUgTW9kdWxlXG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuXG4gICAgICAgICAgICBpZiAoJCgnI3BwcGx1cycpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICAgICAkKCdkaXYuY29udGludWVfYnV0dG9uOmZpcnN0JykuYmVmb3JlKHBwcGx1c19jb250aW51ZSk7XG5cbiAgICAgICAgICAgICAgICAkKCdpbnB1dFtuYW1lPVwicGF5bWVudFwiXTpjaGVja2VkJykuY2xvc2VzdCgnZGl2LnBheW1lbnRfaXRlbScpLmFkZENsYXNzKCdtb2R1bGVfb3B0aW9uX3NlbGVjdGVkJyk7XG4gICAgICAgICAgICAgICAgJCgnI3BwcGx1cycpLmNsb3Nlc3QoJ2Rpdi5wYXltZW50X2l0ZW0nKS5hZGRDbGFzcygncHBwbHVzX3BheW1lbnRfaXRlbScpO1xuXG4gICAgICAgICAgICAgICAgaWYgKCQoJ2JvZHknKS5vbikge1xuICAgICAgICAgICAgICAgICAgICAkKCdkaXYucGF5bWVudF9pdGVtX2NvbnRhaW5lcicpLm9uKCdjbGljaycsIF9wYXltZW50SXRlbU9uQ2xpY2spO1xuICAgICAgICAgICAgICAgICAgICAkKCdkaXYucGF5cGFsX2NvbnRpbnVlX2J1dHRvbicpLm9uKCdjbGljaycsIF9wcHBsdXNDb250aW51ZU9uQ2xpY2spO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICQoJ2JvZHknKS5kZWxlZ2F0ZSgnZGl2LnBheW1lbnRfaXRlbV9jb250YWluZXInLCAnY2xpY2snLCBfcGF5bWVudEl0ZW1PbkNsaWNrKTtcbiAgICAgICAgICAgICAgICAgICAgJCgnYm9keScpLmRlbGVnYXRlKCcjcHBwbHVzX2NvbnRpbnVlJywgJ2NsaWNrJywgX3BwcGx1c0NvbnRpbnVlT25DbGljayk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgJCgnZGl2LnBheW1lbnRfaXRlbSBpbnB1dFt2YWx1ZT1cInBheXBhbDNcIl0nKS5jbG9zZXN0KCdkaXYucGF5bWVudF9pdGVtJykuY3NzKCdib3JkZXItYm90dG9tJywgJ25vbmUnKTtcblxuICAgICAgICAgICAgICAgICQoJ2lmcmFtZScpLnJlYWR5KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgJCgnLmxpc3QtZ3JvdXAtaXRlbScpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5jc3MoJ2Rpc3BsYXknLCAnYmxvY2snKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICBpZiAoaW5pdFBQUCkge1xuICAgICAgICAgICAgICAgICAgICBpbml0UFBQKG9wdGlvbnMudGhpcmRQYXJ0eVBheW1lbnRzQmxvY2spO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIHJldHVybiBtb2R1bGU7XG4gICAgfSk7XG4iXX0=
