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
(function() {
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
    const getUrl = (action) => {
        return `${KlarnaHub.Config.appUrl}admin/admin.php?do=KlarnaHub/${action}`
            + `&orderNumber=${KlarnaHub.Config.orderNumber}&moduleCode=${KlarnaHub.Config.moduleCode}`
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
    const request = (url, options) => {
        return new Promise((resolve, reject) => {
            fetch(url, options)
                .then(response => {
                    if (!response.ok) {
                        response.json().then(console.error);
                        throw new Error(response.statusText);
                    }

                    return response.json();
                })
                .then(json => {
                    if (json.exception) {
                        throw new Error(json);
                    }

                    if (json.error_code) {
                        const message = [
                            KlarnaHub.Config.lang.KLARNA_COMMUNICATION_FAILED,
                            `<strong>${KlarnaHub.Config.lang.ERROR}</strong>:`,
                            json.error_messages.join('<br>'),
                            `<strong>${KlarnaHub.Config.lang.ERROR_CODE}</strong>:`,
                            json.error_code,
                            `<strong>${KlarnaHub.Config.lang.CORRELATION_ID}</strong>:`,
                            json.correlation_id
                        ];

                        KlarnaHub.Lib.showMessage('Klarna', message.join('<br><br>'));

                        return;
                    }

                    resolve(json);
                })
                .catch(error => {
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
    const getConfiguration = (appUrl, orderNumber, moduleCode) => {
        if (!appUrl) {
            throw new Error('App URL argument cannot be empty.');
        }

        const parameters = [
            'do=KlarnaHub/GetConfiguration',
            `appUrl=${appUrl}`
        ];

        if (orderNumber) {
            parameters.push(`orderNumber=${orderNumber}`);
        }

        if (moduleCode) {
            parameters.push(`moduleCode=${moduleCode}`);
        }

        const url = `${appUrl}admin/admin.php?${parameters.join('&')}`;

        const options = {credentials: 'include'};

        return request(url, options)
    };

    /**
     * Executes the full capture action.
     *
     * @return {Promise} Returns a promise that will be resolved with the response object.
     *
     * @public
     */
    const executeFullCapture = () => {
        const url = getUrl('ExecuteFullCapture');

        const options = {
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
    const executeCapture = (orderLines) => {
        const url = getUrl('ExecuteCapture');

        const options = {
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
    const executeAddTrackingCode = (shippingCompany, trackingNumber) => {
        const url = getUrl('ExecuteAddTrackingCode')

        const options = {
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
    const executeCancelOrder = () => {
        const url = getUrl('ExecuteCancelOrder');

        const options = {
            method: 'POST',
            credentials: 'include'
        };

        return request(url, options)
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
    const activateRecalculationFlag = (newCouponAmount, newDiscountAmount, newVoucherAmount, newShippingCosts) => {
        const url = getUrl('ActivateRecalculationFlag');

        const options = {
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
        getConfiguration,
        executeFullCapture,
        executeCapture,
        executeAddTrackingCode,
        executeCancelOrder,
        activateRecalculationFlag
    }, window.KlarnaHub.Api);
})();
