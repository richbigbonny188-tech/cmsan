/* --------------------------------------------------------------
 loading-spinner.js 2021-05-05
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Checkout loading spinner extension
 * This file is loaded through the HTML component
 */
(function (exports) {
    'use strict';

    const SECOND_MULTIPLICATOR = 1000;
    const DEFAULT_TIMEOUT = 0;

    const methodName = 'showCheckoutLoadingSpinner';
    const template = document.querySelector('#checkout-loading-spinner-container-template');
    const timeout = Number(document.currentScript.dataset.timeout * SECOND_MULTIPLICATOR) || DEFAULT_TIMEOUT;

    function showLoadingSpinner() {
        document.querySelector('.checkout-loading-spinner-container-backdrop').classList.add('loading');
    }

    if (methodName in exports) {
        return;
    }

    document.body.insertBefore(
        document.importNode(template.content, true),
        document.body.firstChild
    );

    exports[methodName] = function () {
        setTimeout(showLoadingSpinner, timeout);
    };
})(window);


