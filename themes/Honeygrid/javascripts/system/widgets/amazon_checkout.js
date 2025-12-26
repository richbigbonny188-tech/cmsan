'use strict';

/* --------------------------------------------------------------
 amazon_checkout.js 2018-11-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals OffAmazonPayments */

/**
 * Widget that performs all actions of the amazon paymend method
 * at the checkout process
 */
gambio.widgets.module('amazon_checkout', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $body = $('body'),
        $countryNotAllowed = null,
        $button = null,
        $continue = null,
        defaults = {
        // The amazon seller id
        sellerId: null,
        // The order reference id
        orderReference: null,
        // The size for the generated boxes
        size: { width: '600px', height: '400px' },
        // The size for the generated red onlay boxes
        sizeReadOnly: { width: '400px', height: '185px' },
        // Error message shown if the country isn't allowed
        countryTxt: '',
        // Text that is shown inside the signout button
        buttonTxt: '',
        // Selector for the continue button
        continueBtn: '.btn-continue',
        // Class set to error messages
        errorClass: 'amzadvpay_countrynotallowed',
        // ID set to the signout button
        buttonAClass: 'btn btn-default btn-block amazonadvpay_signout',
        // Class set to the signout button
        buttonClass: 'col-xs-6 col-sm-6 col-md-4 col-md-offset-1 amazonadvpay_signoutbutton',
        // Append the signout button after this selector
        buttonAppendAfter: '.btn-back',
        // URL the POST sends the data to
        requestURL: 'request_port.php?module=AmazonAdvPay',
        // URL the page gets redirected to after an error on signout
        signoutErrorUrl: 'shopping_cart.php?error=apa_signout'
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########


    /**
     * Event handler that is performed on address selection
     * or clicking on the signout button. Both actions perform
     * almost the same steps except the dataset that is deliverd
     * to the server
     * @param {object} d Contains the jQuery event object or the order reference (depending on the emitting action).
     * @private
     */
    var _onAction = function _onAction(d) {

        var dataset = d && d.data && d.data.action ? d.data : {
            orderrefid: options.orderReference,
            action: 'addressSelect'
        };

        $.post(options.requestURL, dataset).done(function (result) {

            // Reload page
            if (result.reload === 'true') {
                window.location.reload();
            }

            // Redirect to an other page
            if (result.redirect_url && dataset.action === 'signOut') {
                window.location = result.redirect_url;
            }

            // Show / hide the "country not allowed" error message
            if (result.country_allowed === 'false') {
                $continue.hide();
                $this.after($countryNotAllowed);
            } else if (dataset.action !== 'signOut') {
                $continue.show();
                $this.next('.' + options.errorClass).remove();
            }
        }).fail(function (result) {
            // If an error occurs on signout redirect page
            if (dataset.action === 'signOut') {
                window.location = options.signoutErrorUrl;
            }
        });
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        if (!$body.hasClass('amazon-payment-initialized')) {
            $body.addClass('amazon-payment-initialized');

            // Generate markup and select elements
            $countryNotAllowed = $('<div class="' + options.errorClass + '">' + options.countryTxt + '</div>');
            $button = $('<div class="' + options.buttonClass + '"><a class="' + options.buttonAClass + '">' + options.buttonTxt + '</div></div>');
            $continue = $(options.continueBtn);

            // Enable signout button
            $button.on('click', { orderrefid: 'n/a', action: 'signOut' }, _onAction);
            $(options.buttonAppendAfter).after($button);

            // Start the amazon widgets
            try {

                // default configuration for all widgets
                var settings = {
                    sellerId: options.sellerId,
                    amazonOrderReferenceId: options.orderReference,
                    design: {
                        designMode: 'responsive'
                    },
                    onAddressSelect: _onAction
                };

                if (null !== document.querySelector('#addressBookWidgetDiv')) {
                    new OffAmazonPayments.Widgets.AddressBook(settings).bind('addressBookWidgetDiv');
                }
                if (null !== document.querySelector('#walletWidgetDiv')) {
                    new OffAmazonPayments.Widgets.Wallet(settings).bind('walletWidgetDiv');
                    document.querySelector('#walletWidgetDiv').style.height = '25em';
                }

                var roAddressWidget = document.querySelector('#readOnlyAddressBookWidgetDiv'),
                    roWalletWidget = document.querySelector('#readOnlyWalletWidgetDiv');
                if (null !== roAddressWidget && null !== roWalletWidget) {
                    roAddressWidget.style.height = '12em';
                    roWalletWidget.style.height = '12em';
                    roWalletWidget.style['margin-bottom'] = '3em';
                    $.extend(settings, { displayMode: 'Read' });
                    new OffAmazonPayments.Widgets.AddressBook(settings).bind('readOnlyAddressBookWidgetDiv');
                    new OffAmazonPayments.Widgets.Wallet(settings).bind('readOnlyWalletWidgetDiv');
                }
            } catch (ignore) {}
        }

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvYW1hem9uX2NoZWNrb3V0LmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGJvZHkiLCIkY291bnRyeU5vdEFsbG93ZWQiLCIkYnV0dG9uIiwiJGNvbnRpbnVlIiwiZGVmYXVsdHMiLCJzZWxsZXJJZCIsIm9yZGVyUmVmZXJlbmNlIiwic2l6ZSIsIndpZHRoIiwiaGVpZ2h0Iiwic2l6ZVJlYWRPbmx5IiwiY291bnRyeVR4dCIsImJ1dHRvblR4dCIsImNvbnRpbnVlQnRuIiwiZXJyb3JDbGFzcyIsImJ1dHRvbkFDbGFzcyIsImJ1dHRvbkNsYXNzIiwiYnV0dG9uQXBwZW5kQWZ0ZXIiLCJyZXF1ZXN0VVJMIiwic2lnbm91dEVycm9yVXJsIiwib3B0aW9ucyIsImV4dGVuZCIsIl9vbkFjdGlvbiIsImQiLCJkYXRhc2V0IiwiYWN0aW9uIiwib3JkZXJyZWZpZCIsInBvc3QiLCJkb25lIiwicmVzdWx0IiwicmVsb2FkIiwid2luZG93IiwibG9jYXRpb24iLCJyZWRpcmVjdF91cmwiLCJjb3VudHJ5X2FsbG93ZWQiLCJoaWRlIiwiYWZ0ZXIiLCJzaG93IiwibmV4dCIsInJlbW92ZSIsImZhaWwiLCJpbml0IiwiaGFzQ2xhc3MiLCJhZGRDbGFzcyIsIm9uIiwic2V0dGluZ3MiLCJhbWF6b25PcmRlclJlZmVyZW5jZUlkIiwiZGVzaWduIiwiZGVzaWduTW9kZSIsIm9uQWRkcmVzc1NlbGVjdCIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvciIsIk9mZkFtYXpvblBheW1lbnRzIiwiV2lkZ2V0cyIsIkFkZHJlc3NCb29rIiwiYmluZCIsIldhbGxldCIsInN0eWxlIiwicm9BZGRyZXNzV2lkZ2V0Iiwicm9XYWxsZXRXaWRnZXQiLCJkaXNwbGF5TW9kZSIsImlnbm9yZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOztBQUVBOzs7O0FBSUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFB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LENBQXVCLDBCQUF2QixDQURyQjtBQUVBLG9CQUFJLFNBQVNPLGVBQVQsSUFBNEIsU0FBU0MsY0FBekMsRUFBeUQ7QUFDckRELG9DQUFnQkQsS0FBaEIsQ0FBc0JoRCxNQUF0QixHQUErQixNQUEvQjtBQUNBa0QsbUNBQWVGLEtBQWYsQ0FBcUJoRCxNQUFyQixHQUE4QixNQUE5QjtBQUNBa0QsbUNBQWVGLEtBQWYsQ0FBcUIsZUFBckIsSUFBd0MsS0FBeEM7QUFDQTFELHNCQUFFc0IsTUFBRixDQUFTd0IsUUFBVCxFQUFtQixFQUFDZSxhQUFhLE1BQWQsRUFBbkI7QUFDQSx3QkFBSVIsa0JBQWtCQyxPQUFsQixDQUEwQkMsV0FBOUIsQ0FBMENULFFBQTFDLEVBQW9EVSxJQUFwRCxDQUF5RCw4QkFBekQ7QUFDQSx3QkFBSUgsa0JBQWtCQyxPQUFsQixDQUEwQkcsTUFBOUIsQ0FBcUNYLFFBQXJDLEVBQStDVSxJQUEvQyxDQUFvRCx5QkFBcEQ7QUFDSDtBQUVKLGFBL0JELENBK0JFLE9BQU9NLE1BQVAsRUFBZSxDQUNoQjtBQUNKOztBQUVEakM7QUFDSCxLQXBERDs7QUFzREE7QUFDQSxXQUFPaEMsTUFBUDtBQUNILENBekpEIiwiZmlsZSI6IndpZGdldHMvYW1hem9uX2NoZWNrb3V0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBhbWF6b25fY2hlY2tvdXQuanMgMjAxOC0xMS0xNlxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qIGdsb2JhbHMgT2ZmQW1hem9uUGF5bWVudHMgKi9cblxuLyoqXG4gKiBXaWRnZXQgdGhhdCBwZXJmb3JtcyBhbGwgYWN0aW9ucyBvZiB0aGUgYW1hem9uIHBheW1lbmQgbWV0aG9kXG4gKiBhdCB0aGUgY2hlY2tvdXQgcHJvY2Vzc1xuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoJ2FtYXpvbl9jaGVja291dCcsIFtdLCBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgIHZhciAkdGhpcyA9ICQodGhpcyksXG4gICAgICAgICRib2R5ID0gJCgnYm9keScpLFxuICAgICAgICAkY291bnRyeU5vdEFsbG93ZWQgPSBudWxsLFxuICAgICAgICAkYnV0dG9uID0gbnVsbCxcbiAgICAgICAgJGNvbnRpbnVlID0gbnVsbCxcbiAgICAgICAgZGVmYXVsdHMgPSB7XG4gICAgICAgICAgICAvLyBUaGUgYW1hem9uIHNlbGxlciBpZFxuICAgICAgICAgICAgc2VsbGVySWQ6IG51bGwsXG4gICAgICAgICAgICAvLyBUaGUgb3JkZXIgcmVmZXJlbmNlIGlkXG4gICAgICAgICAgICBvcmRlclJlZmVyZW5jZTogbnVsbCxcbiAgICAgICAgICAgIC8vIFRoZSBzaXplIGZvciB0aGUgZ2VuZXJhdGVkIGJveGVzXG4gICAgICAgICAgICBzaXplOiB7d2lkdGg6ICc2MDBweCcsIGhlaWdodDogJzQwMHB4J30sXG4gICAgICAgICAgICAvLyBUaGUgc2l6ZSBmb3IgdGhlIGdlbmVyYXRlZCByZWQgb25sYXkgYm94ZXNcbiAgICAgICAgICAgIHNpemVSZWFkT25seToge3dpZHRoOiAnNDAwcHgnLCBoZWlnaHQ6ICcxODVweCd9LFxuICAgICAgICAgICAgLy8gRXJyb3IgbWVzc2FnZSBzaG93biBpZiB0aGUgY291bnRyeSBpc24ndCBhbGxvd2VkXG4gICAgICAgICAgICBjb3VudHJ5VHh0OiAnJyxcbiAgICAgICAgICAgIC8vIFRleHQgdGhhdCBpcyBzaG93biBpbnNpZGUgdGhlIHNpZ25vdXQgYnV0dG9uXG4gICAgICAgICAgICBidXR0b25UeHQ6ICcnLFxuICAgICAgICAgICAgLy8gU2VsZWN0b3IgZm9yIHRoZSBjb250aW51ZSBidXR0b25cbiAgICAgICAgICAgIGNvbnRpbnVlQnRuOiAnLmJ0bi1jb250aW51ZScsXG4gICAgICAgICAgICAvLyBDbGFzcyBzZXQgdG8gZXJyb3IgbWVzc2FnZXNcbiAgICAgICAgICAgIGVycm9yQ2xhc3M6ICdhbXphZHZwYXlfY291bnRyeW5vdGFsbG93ZWQnLFxuICAgICAgICAgICAgLy8gSUQgc2V0IHRvIHRoZSBzaWdub3V0IGJ1dHRvblxuICAgICAgICAgICAgYnV0dG9uQUNsYXNzOiAnYnRuIGJ0bi1kZWZhdWx0IGJ0bi1ibG9jayBhbWF6b25hZHZwYXlfc2lnbm91dCcsXG4gICAgICAgICAgICAvLyBDbGFzcyBzZXQgdG8gdGhlIHNpZ25vdXQgYnV0dG9uXG4gICAgICAgICAgICBidXR0b25DbGFzczogJ2NvbC14cy02IGNvbC1zbS02IGNvbC1tZC00IGNvbC1tZC1vZmZzZXQtMSBhbWF6b25hZHZwYXlfc2lnbm91dGJ1dHRvbicsXG4gICAgICAgICAgICAvLyBBcHBlbmQgdGhlIHNpZ25vdXQgYnV0dG9uIGFmdGVyIHRoaXMgc2VsZWN0b3JcbiAgICAgICAgICAgIGJ1dHRvbkFwcGVuZEFmdGVyOiAnLmJ0bi1iYWNrJyxcbiAgICAgICAgICAgIC8vIFVSTCB0aGUgUE9TVCBzZW5kcyB0aGUgZGF0YSB0b1xuICAgICAgICAgICAgcmVxdWVzdFVSTDogJ3JlcXVlc3RfcG9ydC5waHA/bW9kdWxlPUFtYXpvbkFkdlBheScsXG4gICAgICAgICAgICAvLyBVUkwgdGhlIHBhZ2UgZ2V0cyByZWRpcmVjdGVkIHRvIGFmdGVyIGFuIGVycm9yIG9uIHNpZ25vdXRcbiAgICAgICAgICAgIHNpZ25vdXRFcnJvclVybDogJ3Nob3BwaW5nX2NhcnQucGhwP2Vycm9yPWFwYV9zaWdub3V0J1xuICAgICAgICB9LFxuICAgICAgICBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcbiAgICAgICAgbW9kdWxlID0ge307XG5cbi8vICMjIyMjIyMjIyMgSEVMUEVSIEZVTkNUSU9OUyAjIyMjIyMjIyMjXG5cblxuICAgIC8qKlxuICAgICAqIEV2ZW50IGhhbmRsZXIgdGhhdCBpcyBwZXJmb3JtZWQgb24gYWRkcmVzcyBzZWxlY3Rpb25cbiAgICAgKiBvciBjbGlja2luZyBvbiB0aGUgc2lnbm91dCBidXR0b24uIEJvdGggYWN0aW9ucyBwZXJmb3JtXG4gICAgICogYWxtb3N0IHRoZSBzYW1lIHN0ZXBzIGV4Y2VwdCB0aGUgZGF0YXNldCB0aGF0IGlzIGRlbGl2ZXJkXG4gICAgICogdG8gdGhlIHNlcnZlclxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBkIENvbnRhaW5zIHRoZSBqUXVlcnkgZXZlbnQgb2JqZWN0IG9yIHRoZSBvcmRlciByZWZlcmVuY2UgKGRlcGVuZGluZyBvbiB0aGUgZW1pdHRpbmcgYWN0aW9uKS5cbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIHZhciBfb25BY3Rpb24gPSBmdW5jdGlvbiAoZCkge1xuXG4gICAgICAgIHZhciBkYXRhc2V0ID0gKGQgJiYgZC5kYXRhICYmIGQuZGF0YS5hY3Rpb24pID8gZC5kYXRhIDoge1xuICAgICAgICAgICAgb3JkZXJyZWZpZDogb3B0aW9ucy5vcmRlclJlZmVyZW5jZSxcbiAgICAgICAgICAgIGFjdGlvbjogJ2FkZHJlc3NTZWxlY3QnXG4gICAgICAgIH07XG5cbiAgICAgICAgJC5wb3N0KG9wdGlvbnMucmVxdWVzdFVSTCwgZGF0YXNldCkuZG9uZShmdW5jdGlvbiAocmVzdWx0KSB7XG5cbiAgICAgICAgICAgIC8vIFJlbG9hZCBwYWdlXG4gICAgICAgICAgICBpZiAocmVzdWx0LnJlbG9hZCA9PT0gJ3RydWUnKSB7XG4gICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLnJlbG9hZCgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBSZWRpcmVjdCB0byBhbiBvdGhlciBwYWdlXG4gICAgICAgICAgICBpZiAocmVzdWx0LnJlZGlyZWN0X3VybCAmJiBkYXRhc2V0LmFjdGlvbiA9PT0gJ3NpZ25PdXQnKSB7XG4gICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uID0gcmVzdWx0LnJlZGlyZWN0X3VybDtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gU2hvdyAvIGhpZGUgdGhlIFwiY291bnRyeSBub3QgYWxsb3dlZFwiIGVycm9yIG1lc3NhZ2VcbiAgICAgICAgICAgIGlmIChyZXN1bHQuY291bnRyeV9hbGxvd2VkID09PSAnZmFsc2UnKSB7XG4gICAgICAgICAgICAgICAgJGNvbnRpbnVlLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAkdGhpcy5hZnRlcigkY291bnRyeU5vdEFsbG93ZWQpO1xuICAgICAgICAgICAgfSBlbHNlIGlmIChkYXRhc2V0LmFjdGlvbiAhPT0gJ3NpZ25PdXQnKSB7XG4gICAgICAgICAgICAgICAgJGNvbnRpbnVlLnNob3coKTtcbiAgICAgICAgICAgICAgICAkdGhpc1xuICAgICAgICAgICAgICAgICAgICAubmV4dCgnLicgKyBvcHRpb25zLmVycm9yQ2xhc3MpXG4gICAgICAgICAgICAgICAgICAgIC5yZW1vdmUoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9KS5mYWlsKGZ1bmN0aW9uIChyZXN1bHQpIHtcbiAgICAgICAgICAgIC8vIElmIGFuIGVycm9yIG9jY3VycyBvbiBzaWdub3V0IHJlZGlyZWN0IHBhZ2VcbiAgICAgICAgICAgIGlmIChkYXRhc2V0LmFjdGlvbiA9PT0gJ3NpZ25PdXQnKSB7XG4gICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uID0gb3B0aW9ucy5zaWdub3V0RXJyb3JVcmw7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH07XG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgLyoqXG4gICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICogQGNvbnN0cnVjdG9yXG4gICAgICovXG4gICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuXG4gICAgICAgIGlmICghJGJvZHkuaGFzQ2xhc3MoJ2FtYXpvbi1wYXltZW50LWluaXRpYWxpemVkJykpIHtcbiAgICAgICAgICAgICRib2R5LmFkZENsYXNzKCdhbWF6b24tcGF5bWVudC1pbml0aWFsaXplZCcpO1xuXG4gICAgICAgICAgICAvLyBHZW5lcmF0ZSBtYXJrdXAgYW5kIHNlbGVjdCBlbGVtZW50c1xuICAgICAgICAgICAgJGNvdW50cnlOb3RBbGxvd2VkID0gJCgnPGRpdiBjbGFzcz1cIicgKyBvcHRpb25zLmVycm9yQ2xhc3MgKyAnXCI+JyArIG9wdGlvbnMuY291bnRyeVR4dCArICc8L2Rpdj4nKTtcbiAgICAgICAgICAgICRidXR0b24gPSAkKCc8ZGl2IGNsYXNzPVwiJyArIG9wdGlvbnMuYnV0dG9uQ2xhc3MgKyAnXCI+PGEgY2xhc3M9XCInICsgb3B0aW9ucy5idXR0b25BQ2xhc3MgKyAnXCI+J1xuICAgICAgICAgICAgICAgICsgb3B0aW9ucy5idXR0b25UeHQgKyAnPC9kaXY+PC9kaXY+Jyk7XG4gICAgICAgICAgICAkY29udGludWUgPSAkKG9wdGlvbnMuY29udGludWVCdG4pO1xuXG4gICAgICAgICAgICAvLyBFbmFibGUgc2lnbm91dCBidXR0b25cbiAgICAgICAgICAgICRidXR0b24ub24oJ2NsaWNrJywge29yZGVycmVmaWQ6ICduL2EnLCBhY3Rpb246ICdzaWduT3V0J30sIF9vbkFjdGlvbik7XG4gICAgICAgICAgICAkKG9wdGlvbnMuYnV0dG9uQXBwZW5kQWZ0ZXIpLmFmdGVyKCRidXR0b24pO1xuXG4gICAgICAgICAgICAvLyBTdGFydCB0aGUgYW1hem9uIHdpZGdldHNcbiAgICAgICAgICAgIHRyeSB7XG5cbiAgICAgICAgICAgICAgICAvLyBkZWZhdWx0IGNvbmZpZ3VyYXRpb24gZm9yIGFsbCB3aWRnZXRzXG4gICAgICAgICAgICAgICAgdmFyIHNldHRpbmdzID0ge1xuICAgICAgICAgICAgICAgICAgICBzZWxsZXJJZDogb3B0aW9ucy5zZWxsZXJJZCxcbiAgICAgICAgICAgICAgICAgICAgYW1hem9uT3JkZXJSZWZlcmVuY2VJZDogb3B0aW9ucy5vcmRlclJlZmVyZW5jZSxcbiAgICAgICAgICAgICAgICAgICAgZGVzaWduOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBkZXNpZ25Nb2RlOiAncmVzcG9uc2l2ZSdcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgb25BZGRyZXNzU2VsZWN0OiBfb25BY3Rpb25cbiAgICAgICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAgICAgaWYgKG51bGwgIT09IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNhZGRyZXNzQm9va1dpZGdldERpdicpKSB7XG4gICAgICAgICAgICAgICAgICAgIG5ldyBPZmZBbWF6b25QYXltZW50cy5XaWRnZXRzLkFkZHJlc3NCb29rKHNldHRpbmdzKS5iaW5kKCdhZGRyZXNzQm9va1dpZGdldERpdicpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBpZiAobnVsbCAhPT0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI3dhbGxldFdpZGdldERpdicpKSB7XG4gICAgICAgICAgICAgICAgICAgIG5ldyBPZmZBbWF6b25QYXltZW50cy5XaWRnZXRzLldhbGxldChzZXR0aW5ncykuYmluZCgnd2FsbGV0V2lkZ2V0RGl2Jyk7XG4gICAgICAgICAgICAgICAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyN3YWxsZXRXaWRnZXREaXYnKS5zdHlsZS5oZWlnaHQgPSAnMjVlbSc7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgbGV0IHJvQWRkcmVzc1dpZGdldCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNyZWFkT25seUFkZHJlc3NCb29rV2lkZ2V0RGl2JyksXG4gICAgICAgICAgICAgICAgICAgIHJvV2FsbGV0V2lkZ2V0ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI3JlYWRPbmx5V2FsbGV0V2lkZ2V0RGl2Jyk7XG4gICAgICAgICAgICAgICAgaWYgKG51bGwgIT09IHJvQWRkcmVzc1dpZGdldCAmJiBudWxsICE9PSByb1dhbGxldFdpZGdldCkge1xuICAgICAgICAgICAgICAgICAgICByb0FkZHJlc3NXaWRnZXQuc3R5bGUuaGVpZ2h0ID0gJzEyZW0nO1xuICAgICAgICAgICAgICAgICAgICByb1dhbGxldFdpZGdldC5zdHlsZS5oZWlnaHQgPSAnMTJlbSc7XG4gICAgICAgICAgICAgICAgICAgIHJvV2FsbGV0V2lkZ2V0LnN0eWxlWydtYXJnaW4tYm90dG9tJ10gPSAnM2VtJztcbiAgICAgICAgICAgICAgICAgICAgJC5leHRlbmQoc2V0dGluZ3MsIHtkaXNwbGF5TW9kZTogJ1JlYWQnfSk7XG4gICAgICAgICAgICAgICAgICAgIG5ldyBPZmZBbWF6b25QYXltZW50cy5XaWRnZXRzLkFkZHJlc3NCb29rKHNldHRpbmdzKS5iaW5kKCdyZWFkT25seUFkZHJlc3NCb29rV2lkZ2V0RGl2Jyk7XG4gICAgICAgICAgICAgICAgICAgIG5ldyBPZmZBbWF6b25QYXltZW50cy5XaWRnZXRzLldhbGxldChzZXR0aW5ncykuYmluZCgncmVhZE9ubHlXYWxsZXRXaWRnZXREaXYnKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0gY2F0Y2ggKGlnbm9yZSkge1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgZG9uZSgpO1xuICAgIH07XG5cbiAgICAvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG4gICAgcmV0dXJuIG1vZHVsZTtcbn0pO1xuIl19
