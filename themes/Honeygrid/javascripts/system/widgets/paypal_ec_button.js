'use strict';

/* --------------------------------------------------------------
 paypal_ec_button.js 2016-02-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * PayPal Express Checkout Button
 *
 * This widget handles the "PayPal Express Checkout" button functionality.
 *
 * It needs the following options:
 *
 * - data-paypal_ec_button-page >> (string) The current page of the widget instance will alter its behavior.
 * - data-paypal_ec_button-redirect >> (bool) Whether to redirect to PayPal directly upon widget initialization.
 * - data-paypal_ec_button-display-cart >> (bool) Whether the app will automatically navigate to the shopping cart page
 *   after a product was added to the cart. This settings comes from the admin section.
 *
 * @module Widgets/paypal_ec_button
 */
gambio.widgets.module('paypal_ec_button', [], function (data) {

    'use strict';

    var $this = $(this),
        module = {};

    /**
     * Redirect the browser to the PayPal controller.
     *
     * @private
     */
    var _redirectToPayPal = function _redirectToPayPal() {
        var paypalUrl = jse.core.config.get('appUrl').replace(/\/$/, '') + '/shop.php?do=PayPal/PrepareECS';
        window.location.href = paypalUrl;
    };

    /**
     * On PayPal Button Click
     *
     * This event handle will react differently according to the current page. If we are on the
     * shopping cart page there is only a redirect to the PayPal/PrepareECS page. But if we are
     * on the product details page then we first have to make a get request to the PayPal/CartECS
     * that will prepare.
     *
     * @private
     */
    var _onPayPalButtonClick = function _onPayPalButtonClick() {
        if (data.page === 'cart') {
            _redirectToPayPal();
        } else if (data.page === 'product') {
            var activateUrl = jse.core.config.get('appUrl') + '/shop.php?do=PayPal/CartECS';

            $.get(activateUrl, function () {
                // Click the "Add to Cart" button.
                $('input[name="btn-add-to-cart"]').click();

                if (data.displayCart === false) {
                    // Wait until the cart is display which means that the product was successfully inserted in the
                    // shopping cart and then navigate to PayPal page. If the cart is not displayed after 10 seconds
                    // that means that the item was not added to the shopping cart.
                    var currentDate = new Date(),
                        timeout = 10; // seconds

                    var interval = setInterval(function () {
                        if ($('.cart-dropdown:visible').length > 0) {
                            clearInterval(interval);
                            _redirectToPayPal();
                        }

                        if ((new Date().getTime() - currentDate.getTime()) / 1000 > timeout) {
                            clearInterval(interval); // Check has timed out.
                        }
                    }, 100);
                }
            });
        } else {
            throw new Error('Invalid page attribute provided: ' + data.page);
        }
    };

    /**
     * Initialize Module
     */
    module.init = function (done) {
        // If the "redirect" option is enabled then navigate directly to PayPal page. This option is necessary when
        // the DISPLAY_CART is enabled which means that after a product has been added to the cart the app will
        // automatically redirect to the shopping cart page.
        if (data.redirect === true) {
            _redirectToPayPal();
        }

        // Bind the button event handler.
        $this.on('click', _onPayPalButtonClick);

        done();
    };

    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcGF5cGFsX2VjX2J1dHRvbi5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIl9yZWRpcmVjdFRvUGF5UGFsIiwicGF5cGFsVXJsIiwianNlIiwiY29yZSIsImNvbmZpZyIsImdldCIsInJlcGxhY2UiLCJ3aW5kb3ciLCJsb2NhdGlvbiIsImhyZWYiLCJfb25QYXlQYWxCdXR0b25DbGljayIsInBhZ2UiLCJhY3RpdmF0ZVVybCIsImNsaWNrIiwiZGlzcGxheUNhcnQiLCJjdXJyZW50RGF0ZSIsIkRhdGUiLCJ0aW1lb3V0IiwiaW50ZXJ2YWwiLCJzZXRJbnRlcnZhbCIsImxlbmd0aCIsImNsZWFySW50ZXJ2YWwiLCJnZXRUaW1lIiwiRXJyb3IiLCJpbml0IiwiZG9uZSIsInJlZGlyZWN0Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7QUFjQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQXNCLGtCQUF0QixFQUEwQyxFQUExQyxFQUE4QyxVQUFVQyxJQUFWLEVBQWdCOztBQUUxRDs7QUFFQSxRQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0lILFNBQVMsRUFEYjs7QUFHQTs7Ozs7QUFLQSxRQUFJSSxvQkFBb0IsU0FBcEJBLGlCQUFvQixHQUFZO0FBQ2hDLFlBQUlDLFlBQVlDLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsRUFBOEJDLE9BQTlCLENBQXNDLEtBQXRDLEVBQTZDLEVBQTdDLElBQW1ELGdDQUFuRTtBQUNBQyxlQUFPQyxRQUFQLENBQWdCQyxJQUFoQixHQUF1QlIsU0FBdkI7QUFDSCxLQUhEOztBQU1BOzs7Ozs7Ozs7O0FBVUEsUUFBSVMsdUJBQXVCLFNBQXZCQSxvQkFBdUIsR0FBWTtBQUNuQyxZQUFJYixLQUFLYyxJQUFMLEtBQWMsTUFBbEIsRUFBMEI7QUFDdEJYO0FBQ0gsU0FGRCxNQUVPLElBQUlILEtBQUtjLElBQUwsS0FBYyxTQUFsQixFQUE2QjtBQUNoQyxnQkFBSUMsY0FBY1YsSUFBSUMsSUFBSixDQUFTQyxNQUFULENBQWdCQyxHQUFoQixDQUFvQixRQUFwQixJQUFnQyw2QkFBbEQ7O0FBRUFOLGNBQUVNLEdBQUYsQ0FBTU8sV0FBTixFQUFtQixZQUFZO0FBQzNCO0FBQ0FiLGtCQUFFLCtCQUFGLEVBQW1DYyxLQUFuQzs7QUFFQSxvQkFBSWhCLEtBQUtpQixXQUFMLEtBQXFCLEtBQXpCLEVBQWdDO0FBQzVCO0FBQ0E7QUFDQTtBQUNBLHdCQUFJQyxjQUFjLElBQUlDLElBQUosRUFBbEI7QUFBQSx3QkFDSUMsVUFBVSxFQURkLENBSjRCLENBS1Y7O0FBRWxCLHdCQUFJQyxXQUFXQyxZQUFZLFlBQVk7QUFDbkMsNEJBQUlwQixFQUFFLHdCQUFGLEVBQTRCcUIsTUFBNUIsR0FBcUMsQ0FBekMsRUFBNEM7QUFDeENDLDBDQUFjSCxRQUFkO0FBQ0FsQjtBQUNIOztBQUVELDRCQUFJLENBQUMsSUFBSWdCLElBQUosR0FBV00sT0FBWCxLQUF1QlAsWUFBWU8sT0FBWixFQUF4QixJQUFpRCxJQUFqRCxHQUF3REwsT0FBNUQsRUFBcUU7QUFDakVJLDBDQUFjSCxRQUFkLEVBRGlFLENBQ3hDO0FBQzVCO0FBQ0oscUJBVGMsRUFTWixHQVRZLENBQWY7QUFVSDtBQUNKLGFBdEJEO0FBdUJILFNBMUJNLE1BMEJBO0FBQ0gsa0JBQU0sSUFBSUssS0FBSixDQUFVLHNDQUFzQzFCLEtBQUtjLElBQXJELENBQU47QUFDSDtBQUNKLEtBaENEOztBQW1DQTs7O0FBR0FmLFdBQU80QixJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjtBQUMxQjtBQUNBO0FBQ0E7QUFDQSxZQUFJNUIsS0FBSzZCLFFBQUwsS0FBa0IsSUFBdEIsRUFBNEI7QUFDeEIxQjtBQUNIOztBQUVEO0FBQ0FGLGNBQU02QixFQUFOLENBQVMsT0FBVCxFQUFrQmpCLG9CQUFsQjs7QUFFQWU7QUFDSCxLQVpEOztBQWNBLFdBQU83QixNQUFQO0FBQ0gsQ0FqRkQiLCJmaWxlIjoid2lkZ2V0cy9wYXlwYWxfZWNfYnV0dG9uLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBwYXlwYWxfZWNfYnV0dG9uLmpzIDIwMTYtMDItMjZcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFBheVBhbCBFeHByZXNzIENoZWNrb3V0IEJ1dHRvblxuICpcbiAqIFRoaXMgd2lkZ2V0IGhhbmRsZXMgdGhlIFwiUGF5UGFsIEV4cHJlc3MgQ2hlY2tvdXRcIiBidXR0b24gZnVuY3Rpb25hbGl0eS5cbiAqXG4gKiBJdCBuZWVkcyB0aGUgZm9sbG93aW5nIG9wdGlvbnM6XG4gKlxuICogLSBkYXRhLXBheXBhbF9lY19idXR0b24tcGFnZSA+PiAoc3RyaW5nKSBUaGUgY3VycmVudCBwYWdlIG9mIHRoZSB3aWRnZXQgaW5zdGFuY2Ugd2lsbCBhbHRlciBpdHMgYmVoYXZpb3IuXG4gKiAtIGRhdGEtcGF5cGFsX2VjX2J1dHRvbi1yZWRpcmVjdCA+PiAoYm9vbCkgV2hldGhlciB0byByZWRpcmVjdCB0byBQYXlQYWwgZGlyZWN0bHkgdXBvbiB3aWRnZXQgaW5pdGlhbGl6YXRpb24uXG4gKiAtIGRhdGEtcGF5cGFsX2VjX2J1dHRvbi1kaXNwbGF5LWNhcnQgPj4gKGJvb2wpIFdoZXRoZXIgdGhlIGFwcCB3aWxsIGF1dG9tYXRpY2FsbHkgbmF2aWdhdGUgdG8gdGhlIHNob3BwaW5nIGNhcnQgcGFnZVxuICogICBhZnRlciBhIHByb2R1Y3Qgd2FzIGFkZGVkIHRvIHRoZSBjYXJ0LiBUaGlzIHNldHRpbmdzIGNvbWVzIGZyb20gdGhlIGFkbWluIHNlY3Rpb24uXG4gKlxuICogQG1vZHVsZSBXaWRnZXRzL3BheXBhbF9lY19idXR0b25cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKCdwYXlwYWxfZWNfYnV0dG9uJywgW10sIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICBtb2R1bGUgPSB7fTtcblxuICAgIC8qKlxuICAgICAqIFJlZGlyZWN0IHRoZSBicm93c2VyIHRvIHRoZSBQYXlQYWwgY29udHJvbGxlci5cbiAgICAgKlxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgdmFyIF9yZWRpcmVjdFRvUGF5UGFsID0gZnVuY3Rpb24gKCkge1xuICAgICAgICB2YXIgcGF5cGFsVXJsID0ganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykucmVwbGFjZSgvXFwvJC8sICcnKSArICcvc2hvcC5waHA/ZG89UGF5UGFsL1ByZXBhcmVFQ1MnO1xuICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IHBheXBhbFVybDtcbiAgICB9O1xuXG5cbiAgICAvKipcbiAgICAgKiBPbiBQYXlQYWwgQnV0dG9uIENsaWNrXG4gICAgICpcbiAgICAgKiBUaGlzIGV2ZW50IGhhbmRsZSB3aWxsIHJlYWN0IGRpZmZlcmVudGx5IGFjY29yZGluZyB0byB0aGUgY3VycmVudCBwYWdlLiBJZiB3ZSBhcmUgb24gdGhlXG4gICAgICogc2hvcHBpbmcgY2FydCBwYWdlIHRoZXJlIGlzIG9ubHkgYSByZWRpcmVjdCB0byB0aGUgUGF5UGFsL1ByZXBhcmVFQ1MgcGFnZS4gQnV0IGlmIHdlIGFyZVxuICAgICAqIG9uIHRoZSBwcm9kdWN0IGRldGFpbHMgcGFnZSB0aGVuIHdlIGZpcnN0IGhhdmUgdG8gbWFrZSBhIGdldCByZXF1ZXN0IHRvIHRoZSBQYXlQYWwvQ2FydEVDU1xuICAgICAqIHRoYXQgd2lsbCBwcmVwYXJlLlxuICAgICAqXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICB2YXIgX29uUGF5UGFsQnV0dG9uQ2xpY2sgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgIGlmIChkYXRhLnBhZ2UgPT09ICdjYXJ0Jykge1xuICAgICAgICAgICAgX3JlZGlyZWN0VG9QYXlQYWwoKTtcbiAgICAgICAgfSBlbHNlIGlmIChkYXRhLnBhZ2UgPT09ICdwcm9kdWN0Jykge1xuICAgICAgICAgICAgdmFyIGFjdGl2YXRlVXJsID0ganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykgKyAnL3Nob3AucGhwP2RvPVBheVBhbC9DYXJ0RUNTJztcblxuICAgICAgICAgICAgJC5nZXQoYWN0aXZhdGVVcmwsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAvLyBDbGljayB0aGUgXCJBZGQgdG8gQ2FydFwiIGJ1dHRvbi5cbiAgICAgICAgICAgICAgICAkKCdpbnB1dFtuYW1lPVwiYnRuLWFkZC10by1jYXJ0XCJdJykuY2xpY2soKTtcblxuICAgICAgICAgICAgICAgIGlmIChkYXRhLmRpc3BsYXlDYXJ0ID09PSBmYWxzZSkge1xuICAgICAgICAgICAgICAgICAgICAvLyBXYWl0IHVudGlsIHRoZSBjYXJ0IGlzIGRpc3BsYXkgd2hpY2ggbWVhbnMgdGhhdCB0aGUgcHJvZHVjdCB3YXMgc3VjY2Vzc2Z1bGx5IGluc2VydGVkIGluIHRoZVxuICAgICAgICAgICAgICAgICAgICAvLyBzaG9wcGluZyBjYXJ0IGFuZCB0aGVuIG5hdmlnYXRlIHRvIFBheVBhbCBwYWdlLiBJZiB0aGUgY2FydCBpcyBub3QgZGlzcGxheWVkIGFmdGVyIDEwIHNlY29uZHNcbiAgICAgICAgICAgICAgICAgICAgLy8gdGhhdCBtZWFucyB0aGF0IHRoZSBpdGVtIHdhcyBub3QgYWRkZWQgdG8gdGhlIHNob3BwaW5nIGNhcnQuXG4gICAgICAgICAgICAgICAgICAgIHZhciBjdXJyZW50RGF0ZSA9IG5ldyBEYXRlKCksXG4gICAgICAgICAgICAgICAgICAgICAgICB0aW1lb3V0ID0gMTA7IC8vIHNlY29uZHNcblxuICAgICAgICAgICAgICAgICAgICB2YXIgaW50ZXJ2YWwgPSBzZXRJbnRlcnZhbChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoJCgnLmNhcnQtZHJvcGRvd246dmlzaWJsZScpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjbGVhckludGVydmFsKGludGVydmFsKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBfcmVkaXJlY3RUb1BheVBhbCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoKG5ldyBEYXRlKCkuZ2V0VGltZSgpIC0gY3VycmVudERhdGUuZ2V0VGltZSgpKSAvIDEwMDAgPiB0aW1lb3V0KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY2xlYXJJbnRlcnZhbChpbnRlcnZhbCk7IC8vIENoZWNrIGhhcyB0aW1lZCBvdXQuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0sIDEwMCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ0ludmFsaWQgcGFnZSBhdHRyaWJ1dGUgcHJvdmlkZWQ6ICcgKyBkYXRhLnBhZ2UpO1xuICAgICAgICB9XG4gICAgfTtcblxuXG4gICAgLyoqXG4gICAgICogSW5pdGlhbGl6ZSBNb2R1bGVcbiAgICAgKi9cbiAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG4gICAgICAgIC8vIElmIHRoZSBcInJlZGlyZWN0XCIgb3B0aW9uIGlzIGVuYWJsZWQgdGhlbiBuYXZpZ2F0ZSBkaXJlY3RseSB0byBQYXlQYWwgcGFnZS4gVGhpcyBvcHRpb24gaXMgbmVjZXNzYXJ5IHdoZW5cbiAgICAgICAgLy8gdGhlIERJU1BMQVlfQ0FSVCBpcyBlbmFibGVkIHdoaWNoIG1lYW5zIHRoYXQgYWZ0ZXIgYSBwcm9kdWN0IGhhcyBiZWVuIGFkZGVkIHRvIHRoZSBjYXJ0IHRoZSBhcHAgd2lsbFxuICAgICAgICAvLyBhdXRvbWF0aWNhbGx5IHJlZGlyZWN0IHRvIHRoZSBzaG9wcGluZyBjYXJ0IHBhZ2UuXG4gICAgICAgIGlmIChkYXRhLnJlZGlyZWN0ID09PSB0cnVlKSB7XG4gICAgICAgICAgICBfcmVkaXJlY3RUb1BheVBhbCgpO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gQmluZCB0aGUgYnV0dG9uIGV2ZW50IGhhbmRsZXIuXG4gICAgICAgICR0aGlzLm9uKCdjbGljaycsIF9vblBheVBhbEJ1dHRvbkNsaWNrKTtcblxuICAgICAgICBkb25lKCk7XG4gICAgfTtcblxuICAgIHJldHVybiBtb2R1bGU7XG59KTtcbiJdfQ==
