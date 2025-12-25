/* --------------------------------------------------------------
 hooks.js 2018-04-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.hooks = jse.libs.hooks || {};

(exports => {
    // Prefix for hook collection keys provided by the shop system
    const KEY_PREFIX = 'GX';

    // Hook collection keys mapping
    const keys = {
        shop: {
            cart: {
                add: `${KEY_PREFIX}_SHOP_CART_ADD`,
                change: `${KEY_PREFIX}_SHOP_CART_CHANGE`,
                checkout: `${KEY_PREFIX}_SHOP_CART_CHECKOUT`,
            },
            product: {
                listing: {
                    hover: `${KEY_PREFIX}_SHOP_PRODUCT_LISTING`,
                },
            },
        },
    };

    // Hook collection
    const hooks = new Map();

    /**
     * Add a hook
     * @param {string} key Hook collection key
     * @param {Function} hook Hook function, that returns a Promise
     */
    function add(key, hook) {
        if (!hooks.has(key)) {
            hooks.set(key, new Set());
        }

        hooks.get(key).add(hook);
    }

    /**
     * Execute the promises within given time window
     * @param {Promise[]} promises
     * @param {Number} timeout Timeout (ms)
     * @return {Promise} Promise that is resolved by fulfillment of all promises
     * or rejected by exceeding the timeout window
     */
    function executeWithinTimeoutWindow(promises, timeout) {
        return new Promise(resolve => {
            let isFinished = false;

            Promise.all(promises).then(results => {
                isFinished = true;
                resolve(results);
            });

            setTimeout(() => {
                if (!isFinished) {
                    resolve();
                }
            }, timeout);
        })
    }

    /**
     * Perform all hooks
     * @param {string} key Hook collection key
     * @param {*} [parameters] Execution context parameters
     * @param {Number} [timeout] Timeout window (ms) for execution (0 for no timeout)
     * @return {Promise}
     */
    function execute(key, parameters, timeout = 0) {
        const promises = [];

        if (!hooks.has(key) || !hooks.get(key).size) {
            return Promise.resolve();
        }

        for (const hook of hooks.get(key).values()) {
            promises.push(hook(parameters));
        }

        hooks.get(key).clear();

        if (Number.isInteger(timeout) && timeout > 0) {
            return executeWithinTimeoutWindow(promises, timeout);
        }

        return Promise.all(promises);
    }

    // Public properties and methods
    exports.keys = keys;
    exports.add = add;
    exports.execute = execute;
})(jse.libs.hooks);