'use strict';

/* --------------------------------------------------------------
 klarna_hub_lib.js 2018-10-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * KlarnaHub API Module
 *
 * AJAX client for the KlarnaHubController class.
 *
 * @module KlarnaHub.Api
 */
(function () {
    'use strict';

    /**
     * Returns the KlarnaHubController request URL based on the provided action.
     *
     * @param {String} action KlarnaHubController action to be executed.
     *
     * @return {String}
     *
     * @private
     */

    var getUrl = function getUrl(action) {
        return KlarnaHub.Config.appUrl + 'admin/admin.php?do=KlarnaHub/' + action + ('&orderNumber=' + KlarnaHub.Config.orderNumber + '&moduleCode=' + KlarnaHub.Config.moduleCode);
    };

    /**
     * Performs a new AJAX request to the server.
     *
     * This method wraps the "fetch" functionality and adds custom error handling.
     *
     * @param {String} url Request URL.
     * @param {Object} options Request options (use valid fetch API options).
     *
     * @return {Promise} Returns a promise that will be resolved with the response object.
     *
     * @private
     */
    var request = function request(url, options) {
        return new Promise(function (resolve, reject) {
            fetch(url, options).then(function (response) {
                if (!response.ok) {
                    response.json().then(console.error);
                    throw new Error(response.statusText);
                }

                return response.json();
            }).then(function (json) {
                if (json.exception) {
                    throw new Error(json);
                }

                if (json.error_code) {
                    var message = [KlarnaHub.Config.lang.KLARNA_COMMUNICATION_FAILED, '<strong>' + KlarnaHub.Config.lang.ERROR + '</strong>:', json.error_messages.join('<br>'), '<strong>' + KlarnaHub.Config.lang.ERROR_CODE + '</strong>:', json.error_code, '<strong>' + KlarnaHub.Config.lang.CORRELATION_ID + '</strong>:', json.correlation_id];

                    KlarnaHub.Lib.showMessage('Klarna', message.join('<br><br>'));

                    return;
                }

                resolve(json);
            }).catch(function (error) {
                KlarnaHub.Lib.handleError(error);
                reject(error);
            });
        });
    };

    /**
     * Gets KlarnaHub configuration information.
     *
     * @param {String} appUrl Base URL of the app, used for reaching KlarnaHubController.
     * @param {String} [orderNumber] Shop order number/ID.
     * @param {String} [moduleCode] Gambio Hub module code.
     *
     * @return {Promise} Returns a promise that will be resolved with the response object.
     *
     * @public
     */
    var getConfiguration = function getConfiguration(appUrl, orderNumber, moduleCode) {
        if (!appUrl) {
            throw new Error('App URL argument cannot be empty.');
        }

        var parameters = ['do=KlarnaHub/GetConfiguration', 'appUrl=' + appUrl];

        if (orderNumber) {
            parameters.push('orderNumber=' + orderNumber);
        }

        if (moduleCode) {
            parameters.push('moduleCode=' + moduleCode);
        }

        var url = appUrl + 'admin/admin.php?' + parameters.join('&');

        var options = { credentials: 'include' };

        return request(url, options);
    };

    /**
     * Executes the full capture action.
     *
     * @return {Promise} Returns a promise that will be resolved with the response object.
     *
     * @public
     */
    var executeFullCapture = function executeFullCapture() {
        var url = getUrl('ExecuteFullCapture');

        var options = {
            method: 'POST',
            credentials: 'include'
        };

        return request(url, options);
    };

    /**
     * Executes the capture action.
     *
     * @param {Object[]} orderLines Order lines to be sent included in the capture.
     *
     * @return {Promise} Returns a promise that will be resolved with the response object.
     *
     * @public
     */
    var executeCapture = function executeCapture(orderLines) {
        var url = getUrl('ExecuteCapture');

        var options = {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderLines)
        };

        return request(url, options);
    };

    /**
     * Executes the add tracking code action.
     *
     * @param {String} shippingCompany Shipping company.
     * @param {String} trackingNumber Tracking number.
     *
     * @return {Promise} Returns a promise that will be resolved with the response object.
     *
     * @public
     */
    var executeAddTrackingCode = function executeAddTrackingCode(shippingCompany, trackingNumber) {
        var url = getUrl('ExecuteAddTrackingCode');

        var options = {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                shippingCompany: shippingCompany.trim(),
                trackingNumber: trackingNumber.trim()
            })
        };

        return request(url, options);
    };

    /**
     * Executes the cancel order action.
     *
     * @return {Promise} Returns a promise that will be resolved with the response object.
     *
     * @public
     */
    var executeCancelOrder = function executeCancelOrder() {
        var url = getUrl('ExecuteCancelOrder');

        var options = {
            method: 'POST',
            credentials: 'include'
        };

        return request(url, options);
    };

    /**
     * Activates the recalculation flag of the current order.
     *
     * @param {Number} newCouponAmount New coupon amount.
     * @param {Number} newDiscountAmount New discount amount.
     * @param {Number} newVoucherAmount New voucher amount.
     * @param {Number} newShippingCosts New shipping costs.
     *
     * @return {Promise} Returns a promise that will be resolved with the response object.
     *
     * @public
     */
    var activateRecalculationFlag = function activateRecalculationFlag(newCouponAmount, newDiscountAmount, newVoucherAmount, newShippingCosts) {
        var url = getUrl('ActivateRecalculationFlag');

        var options = {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                newCouponAmount: parseFloat(newCouponAmount),
                newDiscountAmount: parseFloat(newDiscountAmount),
                newVoucherAmount: parseFloat(newVoucherAmount),
                newShippingCosts: parseFloat(newShippingCosts)
            })
        };

        return request(url, options);
    };

    // Export
    window.KlarnaHub = window.KlarnaHub || {};
    window.KlarnaHub.Api = Object.assign({}, {
        getConfiguration: getConfiguration,
        executeFullCapture: executeFullCapture,
        executeCapture: executeCapture,
        executeAddTrackingCode: executeAddTrackingCode,
        executeCancelOrder: executeCancelOrder,
        activateRecalculationFlag: activateRecalculationFlag
    }, window.KlarnaHub.Api);
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2tsYXJuYV9odWIva2xhcm5hX2h1Yl9hcGkuanMiXSwibmFtZXMiOlsiZ2V0VXJsIiwiYWN0aW9uIiwiS2xhcm5hSHViIiwiQ29uZmlnIiwiYXBwVXJsIiwib3JkZXJOdW1iZXIiLCJtb2R1bGVDb2RlIiwicmVxdWVzdCIsInVybCIsIm9wdGlvbnMiLCJQcm9taXNlIiwicmVzb2x2ZSIsInJlamVjdCIsImZldGNoIiwidGhlbiIsInJlc3BvbnNlIiwib2siLCJqc29uIiwiY29uc29sZSIsImVycm9yIiwiRXJyb3IiLCJzdGF0dXNUZXh0IiwiZXhjZXB0aW9uIiwiZXJyb3JfY29kZSIsIm1lc3NhZ2UiLCJsYW5nIiwiS0xBUk5BX0NPTU1VTklDQVRJT05fRkFJTEVEIiwiRVJST1IiLCJlcnJvcl9tZXNzYWdlcyIsImpvaW4iLCJFUlJPUl9DT0RFIiwiQ09SUkVMQVRJT05fSUQiLCJjb3JyZWxhdGlvbl9pZCIsIkxpYiIsInNob3dNZXNzYWdlIiwiY2F0Y2giLCJoYW5kbGVFcnJvciIsImdldENvbmZpZ3VyYXRpb24iLCJwYXJhbWV0ZXJzIiwicHVzaCIsImNyZWRlbnRpYWxzIiwiZXhlY3V0ZUZ1bGxDYXB0dXJlIiwibWV0aG9kIiwiZXhlY3V0ZUNhcHR1cmUiLCJvcmRlckxpbmVzIiwiaGVhZGVycyIsImJvZHkiLCJKU09OIiwic3RyaW5naWZ5IiwiZXhlY3V0ZUFkZFRyYWNraW5nQ29kZSIsInNoaXBwaW5nQ29tcGFueSIsInRyYWNraW5nTnVtYmVyIiwidHJpbSIsImV4ZWN1dGVDYW5jZWxPcmRlciIsImFjdGl2YXRlUmVjYWxjdWxhdGlvbkZsYWciLCJuZXdDb3Vwb25BbW91bnQiLCJuZXdEaXNjb3VudEFtb3VudCIsIm5ld1ZvdWNoZXJBbW91bnQiLCJuZXdTaGlwcGluZ0Nvc3RzIiwicGFyc2VGbG9hdCIsIndpbmRvdyIsIkFwaSIsIk9iamVjdCIsImFzc2lnbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7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QWhCOztBQUtBLGVBQU9qQyxRQUFRQyxHQUFSLEVBQWFDLE9BQWIsQ0FBUDtBQUNILEtBVEQ7O0FBV0E7Ozs7Ozs7Ozs7OztBQVlBLFFBQU02Qyw0QkFBNEIsU0FBNUJBLHlCQUE0QixDQUFDQyxlQUFELEVBQWtCQyxpQkFBbEIsRUFBcUNDLGdCQUFyQyxFQUF1REMsZ0JBQXZELEVBQTRFO0FBQzFHLFlBQU1sRCxNQUFNUixPQUFPLDJCQUFQLENBQVo7O0FBRUEsWUFBTVMsVUFBVTtBQUNaaUMsb0JBQVEsTUFESTtBQUVaRix5QkFBYSxTQUZEO0FBR1pLLHFCQUFTO0FBQ0wsZ0NBQWdCO0FBRFgsYUFIRztBQU1aQyxrQkFBTUMsS0FBS0MsU0FBTCxDQUFlO0FBQ2pCTyxpQ0FBaUJJLFdBQVdKLGVBQVgsQ0FEQTtBQUVqQkMsbUNBQW1CRyxXQUFXSCxpQkFBWCxDQUZGO0FBR2pCQyxrQ0FBa0JFLFdBQVdGLGdCQUFYLENBSEQ7QUFJakJDLGtDQUFrQkMsV0FBV0QsZ0JBQVg7QUFKRCxhQUFmO0FBTk0sU0FBaEI7O0FBY0EsZUFBT25ELFFBQVFDLEdBQVIsRUFBYUMsT0FBYixDQUFQO0FBQ0gsS0FsQkQ7O0FBcUJBO0FBQ0FtRCxXQUFPMUQsU0FBUCxHQUFtQjBELE9BQU8xRCxTQUFQLElBQW9CLEVBQXZDO0FBQ0EwRCxXQUFPMUQsU0FBUCxDQUFpQjJELEdBQWpCLEdBQXVCQyxPQUFPQyxNQUFQLENBQWMsRUFBZCxFQUFrQjtBQUNyQzFCLDBDQURxQztBQUVyQ0ksOENBRnFDO0FBR3JDRSxzQ0FIcUM7QUFJckNNLHNEQUpxQztBQUtyQ0ksOENBTHFDO0FBTXJDQztBQU5xQyxLQUFsQixFQU9wQk0sT0FBTzFELFNBQVAsQ0FBaUIyRCxHQVBHLENBQXZCO0FBUUgsQ0E3T0QiLCJmaWxlIjoiQWRtaW4vSmF2YXNjcmlwdC9leHRlbmRlcnMva2xhcm5hX2h1Yi9rbGFybmFfaHViX2FwaS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4ga2xhcm5hX2h1Yl9saWIuanMgMjAxOC0xMC0yNlxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTggR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogS2xhcm5hSHViIEFQSSBNb2R1bGVcbiAqXG4gKiBBSkFYIGNsaWVudCBmb3IgdGhlIEtsYXJuYUh1YkNvbnRyb2xsZXIgY2xhc3MuXG4gKlxuICogQG1vZHVsZSBLbGFybmFIdWIuQXBpXG4gKi9cbihmdW5jdGlvbigpIHtcbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm5zIHRoZSBLbGFybmFIdWJDb250cm9sbGVyIHJlcXVlc3QgVVJMIGJhc2VkIG9uIHRoZSBwcm92aWRlZCBhY3Rpb24uXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gYWN0aW9uIEtsYXJuYUh1YkNvbnRyb2xsZXIgYWN0aW9uIHRvIGJlIGV4ZWN1dGVkLlxuICAgICAqXG4gICAgICogQHJldHVybiB7U3RyaW5nfVxuICAgICAqXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBjb25zdCBnZXRVcmwgPSAoYWN0aW9uKSA9PiB7XG4gICAgICAgIHJldHVybiBgJHtLbGFybmFIdWIuQ29uZmlnLmFwcFVybH1hZG1pbi9hZG1pbi5waHA/ZG89S2xhcm5hSHViLyR7YWN0aW9ufWBcbiAgICAgICAgICAgICsgYCZvcmRlck51bWJlcj0ke0tsYXJuYUh1Yi5Db25maWcub3JkZXJOdW1iZXJ9Jm1vZHVsZUNvZGU9JHtLbGFybmFIdWIuQ29uZmlnLm1vZHVsZUNvZGV9YFxuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBQZXJmb3JtcyBhIG5ldyBBSkFYIHJlcXVlc3QgdG8gdGhlIHNlcnZlci5cbiAgICAgKlxuICAgICAqIFRoaXMgbWV0aG9kIHdyYXBzIHRoZSBcImZldGNoXCIgZnVuY3Rpb25hbGl0eSBhbmQgYWRkcyBjdXN0b20gZXJyb3IgaGFuZGxpbmcuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gdXJsIFJlcXVlc3QgVVJMLlxuICAgICAqIEBwYXJhbSB7T2JqZWN0fSBvcHRpb25zIFJlcXVlc3Qgb3B0aW9ucyAodXNlIHZhbGlkIGZldGNoIEFQSSBvcHRpb25zKS5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge1Byb21pc2V9IFJldHVybnMgYSBwcm9taXNlIHRoYXQgd2lsbCBiZSByZXNvbHZlZCB3aXRoIHRoZSByZXNwb25zZSBvYmplY3QuXG4gICAgICpcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIGNvbnN0IHJlcXVlc3QgPSAodXJsLCBvcHRpb25zKSA9PiB7XG4gICAgICAgIHJldHVybiBuZXcgUHJvbWlzZSgocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG4gICAgICAgICAgICBmZXRjaCh1cmwsIG9wdGlvbnMpXG4gICAgICAgICAgICAgICAgLnRoZW4ocmVzcG9uc2UgPT4ge1xuICAgICAgICAgICAgICAgICAgICBpZiAoIXJlc3BvbnNlLm9rKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXNwb25zZS5qc29uKCkudGhlbihjb25zb2xlLmVycm9yKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihyZXNwb25zZS5zdGF0dXNUZXh0KTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiByZXNwb25zZS5qc29uKCk7XG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAudGhlbihqc29uID0+IHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGpzb24uZXhjZXB0aW9uKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoanNvbik7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBpZiAoanNvbi5lcnJvcl9jb2RlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zdCBtZXNzYWdlID0gW1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIEtsYXJuYUh1Yi5Db25maWcubGFuZy5LTEFSTkFfQ09NTVVOSUNBVElPTl9GQUlMRUQsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYDxzdHJvbmc+JHtLbGFybmFIdWIuQ29uZmlnLmxhbmcuRVJST1J9PC9zdHJvbmc+OmAsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAganNvbi5lcnJvcl9tZXNzYWdlcy5qb2luKCc8YnI+JyksXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYDxzdHJvbmc+JHtLbGFybmFIdWIuQ29uZmlnLmxhbmcuRVJST1JfQ09ERX08L3N0cm9uZz46YCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBqc29uLmVycm9yX2NvZGUsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYDxzdHJvbmc+JHtLbGFybmFIdWIuQ29uZmlnLmxhbmcuQ09SUkVMQVRJT05fSUR9PC9zdHJvbmc+OmAsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAganNvbi5jb3JyZWxhdGlvbl9pZFxuICAgICAgICAgICAgICAgICAgICAgICAgXTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgS2xhcm5hSHViLkxpYi5zaG93TWVzc2FnZSgnS2xhcm5hJywgbWVzc2FnZS5qb2luKCc8YnI+PGJyPicpKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZShqc29uKTtcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgIC5jYXRjaChlcnJvciA9PiB7XG4gICAgICAgICAgICAgICAgICAgIEtsYXJuYUh1Yi5MaWIuaGFuZGxlRXJyb3IoZXJyb3IpO1xuICAgICAgICAgICAgICAgICAgICByZWplY3QoZXJyb3IpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogR2V0cyBLbGFybmFIdWIgY29uZmlndXJhdGlvbiBpbmZvcm1hdGlvbi5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBhcHBVcmwgQmFzZSBVUkwgb2YgdGhlIGFwcCwgdXNlZCBmb3IgcmVhY2hpbmcgS2xhcm5hSHViQ29udHJvbGxlci5cbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gW29yZGVyTnVtYmVyXSBTaG9wIG9yZGVyIG51bWJlci9JRC5cbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gW21vZHVsZUNvZGVdIEdhbWJpbyBIdWIgbW9kdWxlIGNvZGUuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtQcm9taXNlfSBSZXR1cm5zIGEgcHJvbWlzZSB0aGF0IHdpbGwgYmUgcmVzb2x2ZWQgd2l0aCB0aGUgcmVzcG9uc2Ugb2JqZWN0LlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNvbnN0IGdldENvbmZpZ3VyYXRpb24gPSAoYXBwVXJsLCBvcmRlck51bWJlciwgbW9kdWxlQ29kZSkgPT4ge1xuICAgICAgICBpZiAoIWFwcFVybCkge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKCdBcHAgVVJMIGFyZ3VtZW50IGNhbm5vdCBiZSBlbXB0eS4nKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IHBhcmFtZXRlcnMgPSBbXG4gICAgICAgICAgICAnZG89S2xhcm5hSHViL0dldENvbmZpZ3VyYXRpb24nLFxuICAgICAgICAgICAgYGFwcFVybD0ke2FwcFVybH1gXG4gICAgICAgIF07XG5cbiAgICAgICAgaWYgKG9yZGVyTnVtYmVyKSB7XG4gICAgICAgICAgICBwYXJhbWV0ZXJzLnB1c2goYG9yZGVyTnVtYmVyPSR7b3JkZXJOdW1iZXJ9YCk7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAobW9kdWxlQ29kZSkge1xuICAgICAgICAgICAgcGFyYW1ldGVycy5wdXNoKGBtb2R1bGVDb2RlPSR7bW9kdWxlQ29kZX1gKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IHVybCA9IGAke2FwcFVybH1hZG1pbi9hZG1pbi5waHA/JHtwYXJhbWV0ZXJzLmpvaW4oJyYnKX1gO1xuXG4gICAgICAgIGNvbnN0IG9wdGlvbnMgPSB7Y3JlZGVudGlhbHM6ICdpbmNsdWRlJ307XG5cbiAgICAgICAgcmV0dXJuIHJlcXVlc3QodXJsLCBvcHRpb25zKVxuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBFeGVjdXRlcyB0aGUgZnVsbCBjYXB0dXJlIGFjdGlvbi5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge1Byb21pc2V9IFJldHVybnMgYSBwcm9taXNlIHRoYXQgd2lsbCBiZSByZXNvbHZlZCB3aXRoIHRoZSByZXNwb25zZSBvYmplY3QuXG4gICAgICpcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY29uc3QgZXhlY3V0ZUZ1bGxDYXB0dXJlID0gKCkgPT4ge1xuICAgICAgICBjb25zdCB1cmwgPSBnZXRVcmwoJ0V4ZWN1dGVGdWxsQ2FwdHVyZScpO1xuXG4gICAgICAgIGNvbnN0IG9wdGlvbnMgPSB7XG4gICAgICAgICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICAgICAgICAgIGNyZWRlbnRpYWxzOiAnaW5jbHVkZSdcbiAgICAgICAgfTtcblxuICAgICAgICByZXR1cm4gcmVxdWVzdCh1cmwsIG9wdGlvbnMpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBFeGVjdXRlcyB0aGUgY2FwdHVyZSBhY3Rpb24uXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge09iamVjdFtdfSBvcmRlckxpbmVzIE9yZGVyIGxpbmVzIHRvIGJlIHNlbnQgaW5jbHVkZWQgaW4gdGhlIGNhcHR1cmUuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtQcm9taXNlfSBSZXR1cm5zIGEgcHJvbWlzZSB0aGF0IHdpbGwgYmUgcmVzb2x2ZWQgd2l0aCB0aGUgcmVzcG9uc2Ugb2JqZWN0LlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNvbnN0IGV4ZWN1dGVDYXB0dXJlID0gKG9yZGVyTGluZXMpID0+IHtcbiAgICAgICAgY29uc3QgdXJsID0gZ2V0VXJsKCdFeGVjdXRlQ2FwdHVyZScpO1xuXG4gICAgICAgIGNvbnN0IG9wdGlvbnMgPSB7XG4gICAgICAgICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICAgICAgICAgIGNyZWRlbnRpYWxzOiAnaW5jbHVkZScsXG4gICAgICAgICAgICBoZWFkZXJzOiB7XG4gICAgICAgICAgICAgICAgJ0NvbnRlbnQtVHlwZSc6ICdhcHBsaWNhdGlvbi9qc29uJ1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGJvZHk6IEpTT04uc3RyaW5naWZ5KG9yZGVyTGluZXMpXG4gICAgICAgIH07XG5cbiAgICAgICAgcmV0dXJuIHJlcXVlc3QodXJsLCBvcHRpb25zKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogRXhlY3V0ZXMgdGhlIGFkZCB0cmFja2luZyBjb2RlIGFjdGlvbi5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBzaGlwcGluZ0NvbXBhbnkgU2hpcHBpbmcgY29tcGFueS5cbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gdHJhY2tpbmdOdW1iZXIgVHJhY2tpbmcgbnVtYmVyLlxuICAgICAqXG4gICAgICogQHJldHVybiB7UHJvbWlzZX0gUmV0dXJucyBhIHByb21pc2UgdGhhdCB3aWxsIGJlIHJlc29sdmVkIHdpdGggdGhlIHJlc3BvbnNlIG9iamVjdC5cbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBjb25zdCBleGVjdXRlQWRkVHJhY2tpbmdDb2RlID0gKHNoaXBwaW5nQ29tcGFueSwgdHJhY2tpbmdOdW1iZXIpID0+IHtcbiAgICAgICAgY29uc3QgdXJsID0gZ2V0VXJsKCdFeGVjdXRlQWRkVHJhY2tpbmdDb2RlJylcblxuICAgICAgICBjb25zdCBvcHRpb25zID0ge1xuICAgICAgICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICAgICAgICBjcmVkZW50aWFsczogJ2luY2x1ZGUnLFxuICAgICAgICAgICAgaGVhZGVyczoge1xuICAgICAgICAgICAgICAgICdDb250ZW50LVR5cGUnOiAnYXBwbGljYXRpb24vanNvbidcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBib2R5OiBKU09OLnN0cmluZ2lmeSh7XG4gICAgICAgICAgICAgICAgc2hpcHBpbmdDb21wYW55OiBzaGlwcGluZ0NvbXBhbnkudHJpbSgpLFxuICAgICAgICAgICAgICAgIHRyYWNraW5nTnVtYmVyOiB0cmFja2luZ051bWJlci50cmltKClcbiAgICAgICAgICAgIH0pXG4gICAgICAgIH07XG5cbiAgICAgICAgcmV0dXJuIHJlcXVlc3QodXJsLCBvcHRpb25zKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogRXhlY3V0ZXMgdGhlIGNhbmNlbCBvcmRlciBhY3Rpb24uXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtQcm9taXNlfSBSZXR1cm5zIGEgcHJvbWlzZSB0aGF0IHdpbGwgYmUgcmVzb2x2ZWQgd2l0aCB0aGUgcmVzcG9uc2Ugb2JqZWN0LlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGNvbnN0IGV4ZWN1dGVDYW5jZWxPcmRlciA9ICgpID0+IHtcbiAgICAgICAgY29uc3QgdXJsID0gZ2V0VXJsKCdFeGVjdXRlQ2FuY2VsT3JkZXInKTtcblxuICAgICAgICBjb25zdCBvcHRpb25zID0ge1xuICAgICAgICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICAgICAgICBjcmVkZW50aWFsczogJ2luY2x1ZGUnXG4gICAgICAgIH07XG5cbiAgICAgICAgcmV0dXJuIHJlcXVlc3QodXJsLCBvcHRpb25zKVxuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBBY3RpdmF0ZXMgdGhlIHJlY2FsY3VsYXRpb24gZmxhZyBvZiB0aGUgY3VycmVudCBvcmRlci5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7TnVtYmVyfSBuZXdDb3Vwb25BbW91bnQgTmV3IGNvdXBvbiBhbW91bnQuXG4gICAgICogQHBhcmFtIHtOdW1iZXJ9IG5ld0Rpc2NvdW50QW1vdW50IE5ldyBkaXNjb3VudCBhbW91bnQuXG4gICAgICogQHBhcmFtIHtOdW1iZXJ9IG5ld1ZvdWNoZXJBbW91bnQgTmV3IHZvdWNoZXIgYW1vdW50LlxuICAgICAqIEBwYXJhbSB7TnVtYmVyfSBuZXdTaGlwcGluZ0Nvc3RzIE5ldyBzaGlwcGluZyBjb3N0cy5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge1Byb21pc2V9IFJldHVybnMgYSBwcm9taXNlIHRoYXQgd2lsbCBiZSByZXNvbHZlZCB3aXRoIHRoZSByZXNwb25zZSBvYmplY3QuXG4gICAgICpcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgY29uc3QgYWN0aXZhdGVSZWNhbGN1bGF0aW9uRmxhZyA9IChuZXdDb3Vwb25BbW91bnQsIG5ld0Rpc2NvdW50QW1vdW50LCBuZXdWb3VjaGVyQW1vdW50LCBuZXdTaGlwcGluZ0Nvc3RzKSA9PiB7XG4gICAgICAgIGNvbnN0IHVybCA9IGdldFVybCgnQWN0aXZhdGVSZWNhbGN1bGF0aW9uRmxhZycpO1xuXG4gICAgICAgIGNvbnN0IG9wdGlvbnMgPSB7XG4gICAgICAgICAgICBtZXRob2Q6ICdQT1NUJyxcbiAgICAgICAgICAgIGNyZWRlbnRpYWxzOiAnaW5jbHVkZScsXG4gICAgICAgICAgICBoZWFkZXJzOiB7XG4gICAgICAgICAgICAgICAgJ0NvbnRlbnQtVHlwZSc6ICdhcHBsaWNhdGlvbi9qc29uJ1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGJvZHk6IEpTT04uc3RyaW5naWZ5KHtcbiAgICAgICAgICAgICAgICBuZXdDb3Vwb25BbW91bnQ6IHBhcnNlRmxvYXQobmV3Q291cG9uQW1vdW50KSxcbiAgICAgICAgICAgICAgICBuZXdEaXNjb3VudEFtb3VudDogcGFyc2VGbG9hdChuZXdEaXNjb3VudEFtb3VudCksXG4gICAgICAgICAgICAgICAgbmV3Vm91Y2hlckFtb3VudDogcGFyc2VGbG9hdChuZXdWb3VjaGVyQW1vdW50KSxcbiAgICAgICAgICAgICAgICBuZXdTaGlwcGluZ0Nvc3RzOiBwYXJzZUZsb2F0KG5ld1NoaXBwaW5nQ29zdHMpXG4gICAgICAgICAgICB9KVxuICAgICAgICB9O1xuXG4gICAgICAgIHJldHVybiByZXF1ZXN0KHVybCwgb3B0aW9ucyk7XG4gICAgfTtcblxuXG4gICAgLy8gRXhwb3J0XG4gICAgd2luZG93LktsYXJuYUh1YiA9IHdpbmRvdy5LbGFybmFIdWIgfHwge307XG4gICAgd2luZG93LktsYXJuYUh1Yi5BcGkgPSBPYmplY3QuYXNzaWduKHt9LCB7XG4gICAgICAgIGdldENvbmZpZ3VyYXRpb24sXG4gICAgICAgIGV4ZWN1dGVGdWxsQ2FwdHVyZSxcbiAgICAgICAgZXhlY3V0ZUNhcHR1cmUsXG4gICAgICAgIGV4ZWN1dGVBZGRUcmFja2luZ0NvZGUsXG4gICAgICAgIGV4ZWN1dGVDYW5jZWxPcmRlcixcbiAgICAgICAgYWN0aXZhdGVSZWNhbGN1bGF0aW9uRmxhZ1xuICAgIH0sIHdpbmRvdy5LbGFybmFIdWIuQXBpKTtcbn0pKCk7XG4iXX0=
