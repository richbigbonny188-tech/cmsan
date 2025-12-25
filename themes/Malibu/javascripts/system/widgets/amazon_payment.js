'use strict';

/* --------------------------------------------------------------
 amazon_payment.js 2016-01-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals OffAmazonPayments */

/**
 * Widget to enable the Amazon payment button @ the checkout
 */
gambio.widgets.module('amazon_payment', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        id = null,
        defaults = {
        // The url at which the amazon oder information is send
        url: 'request_port.php?module=AmazonAdvPay',
        // If amazon payment is successfull procced checkout to this url
        target: 'checkout_shipping.php',
        // The Amazon Payment seller ID
        sellerId: null,
        // Use the Amazon address book?
        addressBook: false
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    /**
     * Helper function that add a class to the button
     * if the amazon payment was chosen in the checkout
     * process and the user gets back to the cart page
     * @private
     */
    var _highlightButton = function _highlightButton() {
        if (location.hash === '#amazonlogin') {
            $this.on('transitionend', function () {
                $this.removeClass('paywithamazonbtn_highlight');
            }).addClass('paywithamazonbtn_highlight');
        }
    };

    // ########## EVENT HANDLER ##########

    /**
     * Submit the "Amazon Order Reference" to
     * the shop system and proceed the checkout
     * @param       {object}        orderReference          The "Amazon Order Reference"
     * @private
     */
    var _signInHandler = function _signInHandler(orderReference) {
        var settings = {
            orderrefid: orderReference.getAmazonOrderReferenceId(),
            action: 'signIn'
        };

        $.post(options.url, settings).done(function (result) {
            if (result.continue === 'true') {
                window.location = options.target;
            }
        });
    };

    /**
     * Basic error handling if
     * something went wrong
     * @private
     */
    var _errorHandler = function _errorHandler() {
        // ToDo: proper error handling
        alert('ERROR in Amazon Payments');
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        id = $this.attr('id');

        try {
            new OffAmazonPayments.Widgets.Button({
                sellerId: options.sellerId,
                useAmazonAddressBook: options.addressBook,
                onSignIn: _signInHandler,
                onError: _errorHandler
            }).bind(id);
        } catch (ignore) {}

        _highlightButton();

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvYW1hem9uX3BheW1lbnQuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJpZCIsImRlZmF1bHRzIiwidXJsIiwidGFyZ2V0Iiwic2VsbGVySWQiLCJhZGRyZXNzQm9vayIsIm9wdGlvbnMiLCJleHRlbmQiLCJfaGlnaGxpZ2h0QnV0dG9uIiwibG9jYXRpb24iLCJoYXNoIiwib24iLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiX3NpZ25JbkhhbmRsZXIiLCJvcmRlclJlZmVyZW5jZSIsInNldHRpbmdzIiwib3JkZXJyZWZpZCIsImdldEFtYXpvbk9yZGVyUmVmZXJlbmNlSWQiLCJhY3Rpb24iLCJwb3N0IiwiZG9uZSIsInJlc3VsdCIsImNvbnRpbnVlIiwid2luZG93IiwiX2Vycm9ySGFuZGxlciIsImFsZXJ0IiwiaW5pdCIsImF0dHIiLCJPZmZBbWF6b25QYXltZW50cyIsIldpZGdldHMiLCJCdXR0b24iLCJ1c2VBbWF6b25BZGRyZXNzQm9vayIsIm9uU2lnbkluIiwib25FcnJvciIsImJpbmQiLCJpZ25vcmUiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7QUFFQTs7O0FBR0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUFzQixnQkFBdEIsRUFBd0MsRUFBeEMsRUFBNEMsVUFBVUMsSUFBVixFQUFnQjs7QUFFeEQ7O0FBRUo7O0FBRUksUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxLQUFLLElBRFQ7QUFBQSxRQUVJQyxXQUFXO0FBQ1A7QUFDQUMsYUFBSyxzQ0FGRTtBQUdQO0FBQ0FDLGdCQUFRLHVCQUpEO0FBS1A7QUFDQUMsa0JBQVUsSUFOSDtBQU9QO0FBQ0FDLHFCQUFhO0FBUk4sS0FGZjtBQUFBLFFBWUlDLFVBQVVQLEVBQUVRLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQk4sUUFBbkIsRUFBNkJKLElBQTdCLENBWmQ7QUFBQSxRQWFJRCxTQUFTLEVBYmI7O0FBZUo7O0FBRUk7Ozs7OztBQU1BLFFBQUlZLG1CQUFtQixTQUFuQkEsZ0JBQW1CLEdBQVk7QUFDL0IsWUFBSUMsU0FBU0MsSUFBVCxLQUFrQixjQUF0QixFQUFzQztBQUNsQ1osa0JBQ0thLEVBREwsQ0FDUSxlQURSLEVBQ3lCLFlBQVk7QUFDN0JiLHNCQUFNYyxXQUFOLENBQWtCLDRCQUFsQjtBQUNILGFBSEwsRUFJS0MsUUFKTCxDQUljLDRCQUpkO0FBS0g7QUFDSixLQVJEOztBQVVKOztBQUVJOzs7Ozs7QUFNQSxRQUFJQyxpQkFBaUIsU0FBakJBLGNBQWlCLENBQVVDLGNBQVYsRUFBMEI7QUFDM0MsWUFBSUMsV0FBVztBQUNYQyx3QkFBWUYsZUFBZUcseUJBQWYsRUFERDtBQUVYQyxvQkFBUTtBQUZHLFNBQWY7O0FBS0FwQixVQUFFcUIsSUFBRixDQUFPZCxRQUFRSixHQUFmLEVBQW9CYyxRQUFwQixFQUNLSyxJQURMLENBQ1UsVUFBVUMsTUFBVixFQUFrQjtBQUNwQixnQkFBSUEsT0FBT0MsUUFBUCxLQUFvQixNQUF4QixFQUFnQztBQUM1QkMsdUJBQU9mLFFBQVAsR0FBa0JILFFBQVFILE1BQTFCO0FBQ0g7QUFDSixTQUxMO0FBTUgsS0FaRDs7QUFjQTs7Ozs7QUFLQSxRQUFJc0IsZ0JBQWdCLFNBQWhCQSxhQUFnQixHQUFZO0FBQzVCO0FBQ0FDLGNBQU0sMEJBQU47QUFDSCxLQUhEOztBQU1KOztBQUVJOzs7O0FBSUE5QixXQUFPK0IsSUFBUCxHQUFjLFVBQVVOLElBQVYsRUFBZ0I7O0FBRTFCckIsYUFBS0YsTUFBTThCLElBQU4sQ0FBVyxJQUFYLENBQUw7O0FBRUEsWUFBSTtBQUNBLGdCQUFJQyxrQkFDQ0MsT0FERCxDQUVDQyxNQUZMLENBRVk7QUFDSjNCLDBCQUFVRSxRQUFRRixRQURkO0FBRUo0QixzQ0FBc0IxQixRQUFRRCxXQUYxQjtBQUdKNEIsMEJBQVVuQixjQUhOO0FBSUpvQix5QkFBU1Q7QUFKTCxhQUZaLEVBUUtVLElBUkwsQ0FRVW5DLEVBUlY7QUFTSCxTQVZELENBVUUsT0FBT29DLE1BQVAsRUFBZSxDQUNoQjs7QUFFRDVCOztBQUVBYTtBQUNILEtBcEJEOztBQXNCQTtBQUNBLFdBQU96QixNQUFQO0FBQ0gsQ0F0R0QiLCJmaWxlIjoid2lkZ2V0cy9hbWF6b25fcGF5bWVudC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gYW1hem9uX3BheW1lbnQuanMgMjAxNi0wMS0yMFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qIGdsb2JhbHMgT2ZmQW1hem9uUGF5bWVudHMgKi9cblxuLyoqXG4gKiBXaWRnZXQgdG8gZW5hYmxlIHRoZSBBbWF6b24gcGF5bWVudCBidXR0b24gQCB0aGUgY2hlY2tvdXRcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKCdhbWF6b25fcGF5bWVudCcsIFtdLCBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgIHZhciAkdGhpcyA9ICQodGhpcyksXG4gICAgICAgIGlkID0gbnVsbCxcbiAgICAgICAgZGVmYXVsdHMgPSB7XG4gICAgICAgICAgICAvLyBUaGUgdXJsIGF0IHdoaWNoIHRoZSBhbWF6b24gb2RlciBpbmZvcm1hdGlvbiBpcyBzZW5kXG4gICAgICAgICAgICB1cmw6ICdyZXF1ZXN0X3BvcnQucGhwP21vZHVsZT1BbWF6b25BZHZQYXknLFxuICAgICAgICAgICAgLy8gSWYgYW1hem9uIHBheW1lbnQgaXMgc3VjY2Vzc2Z1bGwgcHJvY2NlZCBjaGVja291dCB0byB0aGlzIHVybFxuICAgICAgICAgICAgdGFyZ2V0OiAnY2hlY2tvdXRfc2hpcHBpbmcucGhwJyxcbiAgICAgICAgICAgIC8vIFRoZSBBbWF6b24gUGF5bWVudCBzZWxsZXIgSURcbiAgICAgICAgICAgIHNlbGxlcklkOiBudWxsLFxuICAgICAgICAgICAgLy8gVXNlIHRoZSBBbWF6b24gYWRkcmVzcyBib29rP1xuICAgICAgICAgICAgYWRkcmVzc0Jvb2s6IGZhbHNlXG4gICAgICAgIH0sXG4gICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICBtb2R1bGUgPSB7fTtcblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuICAgIC8qKlxuICAgICAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IGFkZCBhIGNsYXNzIHRvIHRoZSBidXR0b25cbiAgICAgKiBpZiB0aGUgYW1hem9uIHBheW1lbnQgd2FzIGNob3NlbiBpbiB0aGUgY2hlY2tvdXRcbiAgICAgKiBwcm9jZXNzIGFuZCB0aGUgdXNlciBnZXRzIGJhY2sgdG8gdGhlIGNhcnQgcGFnZVxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgdmFyIF9oaWdobGlnaHRCdXR0b24gPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgIGlmIChsb2NhdGlvbi5oYXNoID09PSAnI2FtYXpvbmxvZ2luJykge1xuICAgICAgICAgICAgJHRoaXNcbiAgICAgICAgICAgICAgICAub24oJ3RyYW5zaXRpb25lbmQnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICR0aGlzLnJlbW92ZUNsYXNzKCdwYXl3aXRoYW1hem9uYnRuX2hpZ2hsaWdodCcpO1xuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgLmFkZENsYXNzKCdwYXl3aXRoYW1hem9uYnRuX2hpZ2hsaWdodCcpO1xuICAgICAgICB9XG4gICAgfTtcblxuLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuICAgIC8qKlxuICAgICAqIFN1Ym1pdCB0aGUgXCJBbWF6b24gT3JkZXIgUmVmZXJlbmNlXCIgdG9cbiAgICAgKiB0aGUgc2hvcCBzeXN0ZW0gYW5kIHByb2NlZWQgdGhlIGNoZWNrb3V0XG4gICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBvcmRlclJlZmVyZW5jZSAgICAgICAgICBUaGUgXCJBbWF6b24gT3JkZXIgUmVmZXJlbmNlXCJcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIHZhciBfc2lnbkluSGFuZGxlciA9IGZ1bmN0aW9uIChvcmRlclJlZmVyZW5jZSkge1xuICAgICAgICB2YXIgc2V0dGluZ3MgPSB7XG4gICAgICAgICAgICBvcmRlcnJlZmlkOiBvcmRlclJlZmVyZW5jZS5nZXRBbWF6b25PcmRlclJlZmVyZW5jZUlkKCksXG4gICAgICAgICAgICBhY3Rpb246ICdzaWduSW4nXG4gICAgICAgIH07XG5cbiAgICAgICAgJC5wb3N0KG9wdGlvbnMudXJsLCBzZXR0aW5ncylcbiAgICAgICAgICAgIC5kb25lKGZ1bmN0aW9uIChyZXN1bHQpIHtcbiAgICAgICAgICAgICAgICBpZiAocmVzdWx0LmNvbnRpbnVlID09PSAndHJ1ZScpIHtcbiAgICAgICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uID0gb3B0aW9ucy50YXJnZXQ7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIEJhc2ljIGVycm9yIGhhbmRsaW5nIGlmXG4gICAgICogc29tZXRoaW5nIHdlbnQgd3JvbmdcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIHZhciBfZXJyb3JIYW5kbGVyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAvLyBUb0RvOiBwcm9wZXIgZXJyb3IgaGFuZGxpbmdcbiAgICAgICAgYWxlcnQoJ0VSUk9SIGluIEFtYXpvbiBQYXltZW50cycpO1xuICAgIH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAvKipcbiAgICAgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcbiAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgKi9cbiAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG5cbiAgICAgICAgaWQgPSAkdGhpcy5hdHRyKCdpZCcpO1xuXG4gICAgICAgIHRyeSB7XG4gICAgICAgICAgICBuZXcgT2ZmQW1hem9uUGF5bWVudHNcbiAgICAgICAgICAgICAgICAuV2lkZ2V0c1xuICAgICAgICAgICAgICAgIC5CdXR0b24oe1xuICAgICAgICAgICAgICAgICAgICBzZWxsZXJJZDogb3B0aW9ucy5zZWxsZXJJZCxcbiAgICAgICAgICAgICAgICAgICAgdXNlQW1hem9uQWRkcmVzc0Jvb2s6IG9wdGlvbnMuYWRkcmVzc0Jvb2ssXG4gICAgICAgICAgICAgICAgICAgIG9uU2lnbkluOiBfc2lnbkluSGFuZGxlcixcbiAgICAgICAgICAgICAgICAgICAgb25FcnJvcjogX2Vycm9ySGFuZGxlclxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgLmJpbmQoaWQpO1xuICAgICAgICB9IGNhdGNoIChpZ25vcmUpIHtcbiAgICAgICAgfVxuXG4gICAgICAgIF9oaWdobGlnaHRCdXR0b24oKTtcblxuICAgICAgICBkb25lKCk7XG4gICAgfTtcblxuICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICByZXR1cm4gbW9kdWxlO1xufSk7XG4iXX0=
