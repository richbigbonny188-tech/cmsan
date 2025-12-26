'use strict';

/* --------------------------------------------------------------
   amazon_loginpay.js 2018-11-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

gambio.widgets.module('amazon_loginpay', [], function (data) {

    'use strict';

    var $this = $(this),
        $body = $('body'),
        $countryNotAllowed = null,
        $button = null,
        $continue = null,
        defaults = {
        continueBtn: '.btn-continue',
        placeholderId: null,
        mode: 'addressbook',
        sellerId: null,
        clientId: null,
        widgetsSrc: null,
        readonlyAddressbook: null,
        readonlyWallet: null,
        requestURL: 'request_port.php?module=AmazonAdvPay',
        countrytxt: 'country not allowed'
    },
        orderReference = null,
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        $continue = $(options.continueBtn);

        window.onAmazonLoginReady = function () {
            amazon.Login.setClientId(options.clientId);
            amazon.Login.setUseCookie(true);
        };
        window.onAmazonPaymentsReady = function () {
            if (options.mode === 'addressbook') {
                new OffAmazonPayments.Widgets.AddressBook({
                    sellerId: options.sellerId,
                    design: {
                        designMode: 'responsive'
                    },
                    onOrderReferenceCreate: function onOrderReferenceCreate(amzOrderReference) {
                        console.log(amzOrderReference);
                        orderReference = amzOrderReference.getAmazonOrderReferenceId();
                        $('#amz-orderrefid').val(orderReference);
                    },
                    onAddressSelect: function onAddressSelect(addressBookWidget) {
                        var dataset = {
                            orderrefid: orderReference,
                            action: 'addressSelect'
                        };
                        $.post(options.requestURL, dataset).done(function (result) {
                            // Show / hide the "country not allowed" error message
                            if (result.country_allowed === 'false') {
                                $continue.hide();
                                $countryNotAllowed.show();
                            } else {
                                if (result.reload === 'true') {
                                    location.reload();
                                }
                                $continue.show();
                                $countryNotAllowed.hide();
                            }
                        }).fail(function (result) {
                            alert('ERROR');
                        });
                        console.log(orderReference);
                    },
                    onReady: function onReady(orderReference) {},
                    onError: function onError(error) {
                        console.log(error);
                    }
                }).bind(options.placeholderId);
            }

            if (options.mode === 'wallet') {
                var walletOptions = {
                    sellerId: options.sellerId,
                    design: {
                        designMode: 'responsive'
                    },
                    onPaymentSelect: function onPaymentSelect(orderReference) {},
                    onError: function onError(error) {
                        console.log(error);
                    }
                };
                if (options.orderReferenceId) {
                    walletOptions.amazonOrderReferenceId = options.orderReferenceId;
                } else {
                    walletOptions.onOrderReferenceCreate = function (amzOrderReference) {
                        console.log(amzOrderReference);
                        orderReference = amzOrderReference.getAmazonOrderReferenceId();
                        $('#amz-orderrefid').val(orderReference);
                    };
                }

                new OffAmazonPayments.Widgets.Wallet(walletOptions).bind(options.placeholderId);
            }

            if (options.mode === 'readonly') {
                if (null !== document.querySelector('#' + options.readonlyAddressbook)) {
                    new OffAmazonPayments.Widgets.AddressBook({
                        sellerId: options.sellerId,
                        design: {
                            designMode: 'responsive'
                        },
                        amazonOrderReferenceId: options.orderReferenceId,
                        displayMode: 'Read'
                    }).bind(options.readonlyAddressbook);
                }

                new OffAmazonPayments.Widgets.Wallet({
                    sellerId: options.sellerId,
                    design: {
                        designMode: 'responsive'
                    },
                    amazonOrderReferenceId: options.orderReferenceId,
                    displayMode: 'Read'
                }).bind(options.readonlyWallet);
            }

            $('#' + options.placeholderId).css('height', '25em').css('margin-bottom', '1em');
            if (options.readonlyAddressbook) {
                $('#' + options.readonlyAddressbook).css('height', '15em');
            }
            if (options.readonlyWallet) {
                $('#' + options.readonlyWallet).css('height', '15em');
            }
        };
        $('body').append($('<script src="' + options.widgetsSrc + '" async></script>'));

        if (options.mode === 'addressbook') {
            $countryNotAllowed = $('<div class="amzadvpay_countrynotallowed" style="display: none;">' + options.countrytxt + '</div>');
            $('#' + options.placeholderId).after($countryNotAllowed);
        }

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvYW1hem9uX2xvZ2lucGF5LmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGJvZHkiLCIkY291bnRyeU5vdEFsbG93ZWQiLCIkYnV0dG9uIiwiJGNvbnRpbnVlIiwiZGVmYXVsdHMiLCJjb250aW51ZUJ0biIsInBsYWNlaG9sZGVySWQiLCJtb2RlIiwic2VsbGVySWQiLCJjbGllbnRJZCIsIndpZGdldHNTcmMiLCJyZWFkb25seUFkZHJlc3Nib29rIiwicmVhZG9ubHlXYWxsZXQiLCJyZXF1ZXN0VVJMIiwiY291bnRyeXR4dCIsIm9yZGVyUmVmZXJlbmNlIiwib3B0aW9ucyIsImV4dGVuZCIsImluaXQiLCJkb25lIiwid2luZG93Iiwib25BbWF6b25Mb2dpblJlYWR5IiwiYW1hem9uIiwiTG9naW4iLCJzZXRDbGllbnRJZCIsInNldFVzZUNvb2tpZSIsIm9uQW1hem9uUGF5bWVudHNSZWFkeSIsIk9mZkFtYXpvblBheW1lbnRzIiwiV2lkZ2V0cyIsIkFkZHJlc3NCb29rIiwiZGVzaWduIiwiZGVzaWduTW9kZSIsIm9uT3JkZXJSZWZlcmVuY2VDcmVhdGUiLCJhbXpPcmRlclJlZmVyZW5jZSIsImNvbnNvbGUiLCJsb2ciLCJnZXRBbWF6b25PcmRlclJlZmVyZW5jZUlkIiwidmFsIiwib25BZGRyZXNzU2VsZWN0IiwiYWRkcmVzc0Jvb2tXaWRnZXQiLCJkYXRhc2V0Iiwib3JkZXJyZWZpZCIsImFjdGlvbiIsInBvc3QiLCJyZXN1bHQiLCJjb3VudHJ5X2FsbG93ZWQiLCJoaWRlIiwic2hvdyIsInJlbG9hZCIsImxvY2F0aW9uIiwiZmFpbCIsImFsZXJ0Iiwib25SZWFkeSIsIm9uRXJyb3IiLCJlcnJvciIsImJpbmQiLCJ3YWxsZXRPcHRpb25zIiwib25QYXltZW50U2VsZWN0Iiwib3JkZXJSZWZlcmVuY2VJZCIsImFtYXpvbk9yZGVyUmVmZXJlbmNlSWQiLCJXYWxsZXQiLCJkb2N1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QjtBQUNILEtBbEhEOztBQW9IQTtBQUNBLFdBQU92QixNQUFQO0FBQ0gsQ0F2SkQiLCJmaWxlIjoid2lkZ2V0cy9hbWF6b25fbG9naW5wYXkuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAgYW1hem9uX2xvZ2lucGF5LmpzIDIwMTgtMTEtMjhcbiAgIEdhbWJpbyBHbWJIXG4gICBodHRwOi8vd3d3LmdhbWJpby5kZVxuICAgQ29weXJpZ2h0IChjKSAyMDE3IEdhbWJpbyBHbWJIXG4gICBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiAgIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuICAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiovXG5cbmdhbWJpby53aWRnZXRzLm1vZHVsZSgnYW1hem9uX2xvZ2lucGF5JywgW10sIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAkYm9keSA9ICQoJ2JvZHknKSxcbiAgICAgICAgJGNvdW50cnlOb3RBbGxvd2VkID0gbnVsbCxcbiAgICAgICAgJGJ1dHRvbiA9IG51bGwsXG4gICAgICAgICRjb250aW51ZSA9IG51bGwsXG4gICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgY29udGludWVCdG46ICcuYnRuLWNvbnRpbnVlJyxcbiAgICAgICAgICAgIHBsYWNlaG9sZGVySWQ6IG51bGwsXG4gICAgICAgICAgICBtb2RlOiAnYWRkcmVzc2Jvb2snLFxuICAgICAgICAgICAgc2VsbGVySWQ6IG51bGwsXG4gICAgICAgICAgICBjbGllbnRJZDogbnVsbCxcbiAgICAgICAgICAgIHdpZGdldHNTcmM6IG51bGwsXG4gICAgICAgICAgICByZWFkb25seUFkZHJlc3Nib29rOiBudWxsLFxuICAgICAgICAgICAgcmVhZG9ubHlXYWxsZXQ6IG51bGwsXG4gICAgICAgICAgICByZXF1ZXN0VVJMOiAncmVxdWVzdF9wb3J0LnBocD9tb2R1bGU9QW1hem9uQWR2UGF5JyxcbiAgICAgICAgICAgIGNvdW50cnl0eHQ6ICdjb3VudHJ5IG5vdCBhbGxvd2VkJ1xuICAgICAgICB9LFxuICAgICAgICBvcmRlclJlZmVyZW5jZSA9IG51bGwsXG4gICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICBtb2R1bGUgPSB7fTtcblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuICAgIC8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgLyoqXG4gICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICogQGNvbnN0cnVjdG9yXG4gICAgICovXG4gICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuICAgICAgICAkY29udGludWUgPSAkKG9wdGlvbnMuY29udGludWVCdG4pO1xuXG4gICAgICAgIHdpbmRvdy5vbkFtYXpvbkxvZ2luUmVhZHkgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBhbWF6b24uTG9naW4uc2V0Q2xpZW50SWQob3B0aW9ucy5jbGllbnRJZCk7XG4gICAgICAgICAgICBhbWF6b24uTG9naW4uc2V0VXNlQ29va2llKHRydWUpO1xuICAgICAgICB9O1xuICAgICAgICB3aW5kb3cub25BbWF6b25QYXltZW50c1JlYWR5ID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgaWYgKG9wdGlvbnMubW9kZSA9PT0gJ2FkZHJlc3Nib29rJykge1xuICAgICAgICAgICAgICAgIG5ldyBPZmZBbWF6b25QYXltZW50cy5XaWRnZXRzLkFkZHJlc3NCb29rKHtcbiAgICAgICAgICAgICAgICAgICAgc2VsbGVySWQ6IG9wdGlvbnMuc2VsbGVySWQsXG4gICAgICAgICAgICAgICAgICAgIGRlc2lnbjoge1xuICAgICAgICAgICAgICAgICAgICAgICAgZGVzaWduTW9kZTogJ3Jlc3BvbnNpdmUnXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIG9uT3JkZXJSZWZlcmVuY2VDcmVhdGU6IGZ1bmN0aW9uIChhbXpPcmRlclJlZmVyZW5jZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coYW16T3JkZXJSZWZlcmVuY2UpO1xuICAgICAgICAgICAgICAgICAgICAgICAgb3JkZXJSZWZlcmVuY2UgPSBhbXpPcmRlclJlZmVyZW5jZS5nZXRBbWF6b25PcmRlclJlZmVyZW5jZUlkKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjYW16LW9yZGVycmVmaWQnKS52YWwob3JkZXJSZWZlcmVuY2UpO1xuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBvbkFkZHJlc3NTZWxlY3Q6IGZ1bmN0aW9uIChhZGRyZXNzQm9va1dpZGdldCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGRhdGFzZXQgPSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgb3JkZXJyZWZpZDogb3JkZXJSZWZlcmVuY2UsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYWN0aW9uOiAnYWRkcmVzc1NlbGVjdCdcbiAgICAgICAgICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICAgICAgICAgICAgICAkLnBvc3Qob3B0aW9ucy5yZXF1ZXN0VVJMLCBkYXRhc2V0KS5kb25lKGZ1bmN0aW9uIChyZXN1bHQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBTaG93IC8gaGlkZSB0aGUgXCJjb3VudHJ5IG5vdCBhbGxvd2VkXCIgZXJyb3IgbWVzc2FnZVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChyZXN1bHQuY291bnRyeV9hbGxvd2VkID09PSAnZmFsc2UnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRjb250aW51ZS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRjb3VudHJ5Tm90QWxsb3dlZC5zaG93KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHJlc3VsdC5yZWxvYWQgPT09ICd0cnVlJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbG9jYXRpb24ucmVsb2FkKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJGNvbnRpbnVlLnNob3coKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJGNvdW50cnlOb3RBbGxvd2VkLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pLmZhaWwoZnVuY3Rpb24gKHJlc3VsdCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGFsZXJ0KCdFUlJPUicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhvcmRlclJlZmVyZW5jZSk7XG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIG9uUmVhZHk6IGZ1bmN0aW9uIChvcmRlclJlZmVyZW5jZSkge1xuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBvbkVycm9yOiBmdW5jdGlvbiAoZXJyb3IpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGVycm9yKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pLmJpbmQob3B0aW9ucy5wbGFjZWhvbGRlcklkKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKG9wdGlvbnMubW9kZSA9PT0gJ3dhbGxldCcpIHtcbiAgICAgICAgICAgICAgICBsZXQgd2FsbGV0T3B0aW9ucyA9IHtcbiAgICAgICAgICAgICAgICAgICAgc2VsbGVySWQ6IG9wdGlvbnMuc2VsbGVySWQsXG4gICAgICAgICAgICAgICAgICAgIGRlc2lnbjoge1xuICAgICAgICAgICAgICAgICAgICAgICAgZGVzaWduTW9kZTogJ3Jlc3BvbnNpdmUnXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIG9uUGF5bWVudFNlbGVjdDogZnVuY3Rpb24gKG9yZGVyUmVmZXJlbmNlKSB7XG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIG9uRXJyb3I6IGZ1bmN0aW9uIChlcnJvcikge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coZXJyb3IpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgICAgICBpZiAob3B0aW9ucy5vcmRlclJlZmVyZW5jZUlkKSB7XG4gICAgICAgICAgICAgICAgICAgIHdhbGxldE9wdGlvbnMuYW1hem9uT3JkZXJSZWZlcmVuY2VJZCA9IG9wdGlvbnMub3JkZXJSZWZlcmVuY2VJZDtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICB3YWxsZXRPcHRpb25zLm9uT3JkZXJSZWZlcmVuY2VDcmVhdGUgPSBmdW5jdGlvbiAoYW16T3JkZXJSZWZlcmVuY2UpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGFtek9yZGVyUmVmZXJlbmNlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIG9yZGVyUmVmZXJlbmNlID0gYW16T3JkZXJSZWZlcmVuY2UuZ2V0QW1hem9uT3JkZXJSZWZlcmVuY2VJZCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2Ftei1vcmRlcnJlZmlkJykudmFsKG9yZGVyUmVmZXJlbmNlKTtcbiAgICAgICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBuZXcgT2ZmQW1hem9uUGF5bWVudHMuV2lkZ2V0cy5XYWxsZXQod2FsbGV0T3B0aW9ucykuYmluZChvcHRpb25zLnBsYWNlaG9sZGVySWQpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAob3B0aW9ucy5tb2RlID09PSAncmVhZG9ubHknKSB7XG4gICAgICAgICAgICAgICAgaWYgKG51bGwgIT09IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyMnICsgb3B0aW9ucy5yZWFkb25seUFkZHJlc3Nib29rKSkge1xuICAgICAgICAgICAgICAgICAgICBuZXcgT2ZmQW1hem9uUGF5bWVudHMuV2lkZ2V0cy5BZGRyZXNzQm9vayh7XG4gICAgICAgICAgICAgICAgICAgICAgICBzZWxsZXJJZDogb3B0aW9ucy5zZWxsZXJJZCxcbiAgICAgICAgICAgICAgICAgICAgICAgIGRlc2lnbjoge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRlc2lnbk1vZGU6ICdyZXNwb25zaXZlJ1xuICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGFtYXpvbk9yZGVyUmVmZXJlbmNlSWQ6IG9wdGlvbnMub3JkZXJSZWZlcmVuY2VJZCxcbiAgICAgICAgICAgICAgICAgICAgICAgIGRpc3BsYXlNb2RlOiAnUmVhZCdcbiAgICAgICAgICAgICAgICAgICAgfSkuYmluZChvcHRpb25zLnJlYWRvbmx5QWRkcmVzc2Jvb2spO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIG5ldyBPZmZBbWF6b25QYXltZW50cy5XaWRnZXRzLldhbGxldCh7XG4gICAgICAgICAgICAgICAgICAgIHNlbGxlcklkOiBvcHRpb25zLnNlbGxlcklkLFxuICAgICAgICAgICAgICAgICAgICBkZXNpZ246IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGRlc2lnbk1vZGU6ICdyZXNwb25zaXZlJ1xuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBhbWF6b25PcmRlclJlZmVyZW5jZUlkOiBvcHRpb25zLm9yZGVyUmVmZXJlbmNlSWQsXG4gICAgICAgICAgICAgICAgICAgIGRpc3BsYXlNb2RlOiAnUmVhZCdcbiAgICAgICAgICAgICAgICB9KS5iaW5kKG9wdGlvbnMucmVhZG9ubHlXYWxsZXQpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAkKCcjJyArIG9wdGlvbnMucGxhY2Vob2xkZXJJZCkuY3NzKCdoZWlnaHQnLCAnMjVlbScpLmNzcygnbWFyZ2luLWJvdHRvbScsICcxZW0nKTtcbiAgICAgICAgICAgIGlmIChvcHRpb25zLnJlYWRvbmx5QWRkcmVzc2Jvb2spIHtcbiAgICAgICAgICAgICAgICAkKCcjJyArIG9wdGlvbnMucmVhZG9ubHlBZGRyZXNzYm9vaykuY3NzKCdoZWlnaHQnLCAnMTVlbScpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYgKG9wdGlvbnMucmVhZG9ubHlXYWxsZXQpIHtcbiAgICAgICAgICAgICAgICAkKCcjJyArIG9wdGlvbnMucmVhZG9ubHlXYWxsZXQpLmNzcygnaGVpZ2h0JywgJzE1ZW0nKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuICAgICAgICAkKCdib2R5JykuYXBwZW5kKCQoJzxzY3JpcHQgc3JjPVwiJyArIG9wdGlvbnMud2lkZ2V0c1NyYyArICdcIiBhc3luYz48L3NjcmlwdD4nKSk7XG5cbiAgICAgICAgaWYgKG9wdGlvbnMubW9kZSA9PT0gJ2FkZHJlc3Nib29rJykge1xuICAgICAgICAgICAgJGNvdW50cnlOb3RBbGxvd2VkID0gJCgnPGRpdiBjbGFzcz1cImFtemFkdnBheV9jb3VudHJ5bm90YWxsb3dlZFwiIHN0eWxlPVwiZGlzcGxheTogbm9uZTtcIj4nICsgb3B0aW9ucy5jb3VudHJ5dHh0ICsgJzwvZGl2PicpO1xuICAgICAgICAgICAgJCgnIycgKyBvcHRpb25zLnBsYWNlaG9sZGVySWQpLmFmdGVyKCRjb3VudHJ5Tm90QWxsb3dlZCk7XG4gICAgICAgIH1cblxuICAgICAgICBkb25lKCk7XG4gICAgfTtcblxuICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICByZXR1cm4gbW9kdWxlO1xufSk7XG5cblxuIl19
