'use strict';

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

(function (exports) {
    // Prefix for hook collection keys provided by the shop system
    var KEY_PREFIX = 'GX';

    // Hook collection keys mapping
    var keys = {
        shop: {
            cart: {
                add: KEY_PREFIX + '_SHOP_CART_ADD',
                change: KEY_PREFIX + '_SHOP_CART_CHANGE',
                checkout: KEY_PREFIX + '_SHOP_CART_CHECKOUT'
            },
            product: {
                listing: {
                    hover: KEY_PREFIX + '_SHOP_PRODUCT_LISTING'
                }
            }
        }
    };

    // Hook collection
    var hooks = new Map();

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
        return new Promise(function (resolve) {
            var isFinished = false;

            Promise.all(promises).then(function (results) {
                isFinished = true;
                resolve(results);
            });

            setTimeout(function () {
                if (!isFinished) {
                    resolve();
                }
            }, timeout);
        });
    }

    /**
     * Perform all hooks
     * @param {string} key Hook collection key
     * @param {*} [parameters] Execution context parameters
     * @param {Number} [timeout] Timeout window (ms) for execution (0 for no timeout)
     * @return {Promise}
     */
    function execute(key, parameters) {
        var timeout = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 0;

        var promises = [];

        if (!hooks.has(key) || !hooks.get(key).size) {
            return Promise.resolve();
        }

        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
            for (var _iterator = hooks.get(key).values()[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                var hook = _step.value;

                promises.push(hook(parameters));
            }
        } catch (err) {
            _didIteratorError = true;
            _iteratorError = err;
        } finally {
            try {
                if (!_iteratorNormalCompletion && _iterator.return) {
                    _iterator.return();
                }
            } finally {
                if (_didIteratorError) {
                    throw _iteratorError;
                }
            }
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImhvb2tzLmpzIl0sIm5hbWVzIjpbImpzZSIsImxpYnMiLCJob29rcyIsIktFWV9QUkVGSVgiLCJrZXlzIiwic2hvcCIsImNhcnQiLCJhZGQiLCJjaGFuZ2UiLCJjaGVja291dCIsInByb2R1Y3QiLCJsaXN0aW5nIiwiaG92ZXIiLCJNYXAiLCJrZXkiLCJob29rIiwiaGFzIiwic2V0IiwiU2V0IiwiZ2V0IiwiZXhlY3V0ZVdpdGhpblRpbWVvdXRXaW5kb3ciLCJwcm9taXNlcyIsInRpbWVvdXQiLCJQcm9taXNlIiwiaXNGaW5pc2hlZCIsImFsbCIsInRoZW4iLCJyZXNvbHZlIiwicmVzdWx0cyIsInNldFRpbWVvdXQiLCJleGVjdXRlIiwicGFyYW1ldGVycyIsInNpemUiLCJ2YWx1ZXMiLCJwdXNoIiwiY2xlYXIiLCJOdW1iZXIiLCJpc0ludGVnZXIiLCJleHBvcnRzIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLElBQUlDLElBQUosQ0FBU0MsS0FBVCxHQUFpQkYsSUFBSUMsSUFBSixDQUFTQyxLQUFULElBQWtCLEVBQW5DOztBQUVBLENBQUMsbUJBQVc7QUFDUjtBQUNBLFFBQU1DLGFBQWEsSUFBbkI7O0FBRUE7QUFDQSxRQUFNQyxPQUFPO0FBQ1RDLGNBQU07QUFDRkMsa0JBQU07QUFDRkMscUJBQVFKLFVBQVIsbUJBREU7QUFFRkssd0JBQVdMLFVBQVgsc0JBRkU7QUFHRk0sMEJBQWFOLFVBQWI7QUFIRSxhQURKO0FBTUZPLHFCQUFTO0FBQ0xDLHlCQUFTO0FBQ0xDLDJCQUFVVCxVQUFWO0FBREs7QUFESjtBQU5QO0FBREcsS0FBYjs7QUFlQTtBQUNBLFFBQU1ELFFBQVEsSUFBSVcsR0FBSixFQUFkOztBQUVBOzs7OztBQUtBLGFBQVNOLEdBQVQsQ0FBYU8sR0FBYixFQUFrQkMsSUFBbEIsRUFBd0I7QUFDcEIsWUFBSSxDQUFDYixNQUFNYyxHQUFOLENBQVVGLEdBQVYsQ0FBTCxFQUFxQjtBQUNqQlosa0JBQU1lLEdBQU4sQ0FBVUgsR0FBVixFQUFlLElBQUlJLEdBQUosRUFBZjtBQUNIOztBQUVEaEIsY0FBTWlCLEdBQU4sQ0FBVUwsR0FBVixFQUFlUCxHQUFmLENBQW1CUSxJQUFuQjtBQUNIOztBQUVEOzs7Ozs7O0FBT0EsYUFBU0ssMEJBQVQsQ0FBb0NDLFFBQXBDLEVBQThDQyxPQUE5QyxFQUF1RDtBQUNuRCxlQUFPLElBQUlDLE9BQUosQ0FBWSxtQkFBVztBQUMxQixnQkFBSUMsYUFBYSxLQUFqQjs7QUFFQUQsb0JBQVFFLEdBQVIsQ0FBWUosUUFBWixFQUFzQkssSUFBdEIsQ0FBMkIsbUJBQVc7QUFDbENGLDZCQUFhLElBQWI7QUFDQUcsd0JBQVFDLE9BQVI7QUFDSCxhQUhEOztBQUtBQyx1QkFBVyxZQUFNO0FBQ2Isb0JBQUksQ0FBQ0wsVUFBTCxFQUFpQjtBQUNiRztBQUNIO0FBQ0osYUFKRCxFQUlHTCxPQUpIO0FBS0gsU0FiTSxDQUFQO0FBY0g7O0FBRUQ7Ozs7Ozs7QUFPQSxhQUFTUSxPQUFULENBQWlCaEIsR0FBakIsRUFBc0JpQixVQUF0QixFQUErQztBQUFBLFlBQWJULE9BQWEsdUVBQUgsQ0FBRzs7QUFDM0MsWUFBTUQsV0FBVyxFQUFqQjs7QUFFQSxZQUFJLENBQUNuQixNQUFNYyxHQUFOLENBQVVGLEdBQVYsQ0FBRCxJQUFtQixDQUFDWixNQUFNaUIsR0FBTixDQUFVTCxHQUFWLEVBQWVrQixJQUF2QyxFQUE2QztBQUN6QyxtQkFBT1QsUUFBUUksT0FBUixFQUFQO0FBQ0g7O0FBTDBDO0FBQUE7QUFBQTs7QUFBQTtBQU8zQyxpQ0FBbUJ6QixNQUFNaUIsR0FBTixDQUFVTCxHQUFWLEVBQWVtQixNQUFmLEVBQW5CLDhIQUE0QztBQUFBLG9CQUFqQ2xCLElBQWlDOztBQUN4Q00seUJBQVNhLElBQVQsQ0FBY25CLEtBQUtnQixVQUFMLENBQWQ7QUFDSDtBQVQwQztBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBOztBQVczQzdCLGNBQU1pQixHQUFOLENBQVVMLEdBQVYsRUFBZXFCLEtBQWY7O0FBRUEsWUFBSUMsT0FBT0MsU0FBUCxDQUFpQmYsT0FBakIsS0FBNkJBLFVBQVUsQ0FBM0MsRUFBOEM7QUFDMUMsbUJBQU9GLDJCQUEyQkMsUUFBM0IsRUFBcUNDLE9BQXJDLENBQVA7QUFDSDs7QUFFRCxlQUFPQyxRQUFRRSxHQUFSLENBQVlKLFFBQVosQ0FBUDtBQUNIOztBQUVEO0FBQ0FpQixZQUFRbEMsSUFBUixHQUFlQSxJQUFmO0FBQ0FrQyxZQUFRL0IsR0FBUixHQUFjQSxHQUFkO0FBQ0ErQixZQUFRUixPQUFSLEdBQWtCQSxPQUFsQjtBQUNILENBM0ZELEVBMkZHOUIsSUFBSUMsSUFBSixDQUFTQyxLQTNGWiIsImZpbGUiOiJob29rcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gaG9va3MuanMgMjAxOC0wNC0xMlxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5saWJzLmhvb2tzID0ganNlLmxpYnMuaG9va3MgfHwge307XG5cbihleHBvcnRzID0+IHtcbiAgICAvLyBQcmVmaXggZm9yIGhvb2sgY29sbGVjdGlvbiBrZXlzIHByb3ZpZGVkIGJ5IHRoZSBzaG9wIHN5c3RlbVxuICAgIGNvbnN0IEtFWV9QUkVGSVggPSAnR1gnO1xuXG4gICAgLy8gSG9vayBjb2xsZWN0aW9uIGtleXMgbWFwcGluZ1xuICAgIGNvbnN0IGtleXMgPSB7XG4gICAgICAgIHNob3A6IHtcbiAgICAgICAgICAgIGNhcnQ6IHtcbiAgICAgICAgICAgICAgICBhZGQ6IGAke0tFWV9QUkVGSVh9X1NIT1BfQ0FSVF9BRERgLFxuICAgICAgICAgICAgICAgIGNoYW5nZTogYCR7S0VZX1BSRUZJWH1fU0hPUF9DQVJUX0NIQU5HRWAsXG4gICAgICAgICAgICAgICAgY2hlY2tvdXQ6IGAke0tFWV9QUkVGSVh9X1NIT1BfQ0FSVF9DSEVDS09VVGAsXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgcHJvZHVjdDoge1xuICAgICAgICAgICAgICAgIGxpc3Rpbmc6IHtcbiAgICAgICAgICAgICAgICAgICAgaG92ZXI6IGAke0tFWV9QUkVGSVh9X1NIT1BfUFJPRFVDVF9MSVNUSU5HYCxcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgfSxcbiAgICAgICAgfSxcbiAgICB9O1xuXG4gICAgLy8gSG9vayBjb2xsZWN0aW9uXG4gICAgY29uc3QgaG9va3MgPSBuZXcgTWFwKCk7XG5cbiAgICAvKipcbiAgICAgKiBBZGQgYSBob29rXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGtleSBIb29rIGNvbGxlY3Rpb24ga2V5XG4gICAgICogQHBhcmFtIHtGdW5jdGlvbn0gaG9vayBIb29rIGZ1bmN0aW9uLCB0aGF0IHJldHVybnMgYSBQcm9taXNlXG4gICAgICovXG4gICAgZnVuY3Rpb24gYWRkKGtleSwgaG9vaykge1xuICAgICAgICBpZiAoIWhvb2tzLmhhcyhrZXkpKSB7XG4gICAgICAgICAgICBob29rcy5zZXQoa2V5LCBuZXcgU2V0KCkpO1xuICAgICAgICB9XG5cbiAgICAgICAgaG9va3MuZ2V0KGtleSkuYWRkKGhvb2spO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEV4ZWN1dGUgdGhlIHByb21pc2VzIHdpdGhpbiBnaXZlbiB0aW1lIHdpbmRvd1xuICAgICAqIEBwYXJhbSB7UHJvbWlzZVtdfSBwcm9taXNlc1xuICAgICAqIEBwYXJhbSB7TnVtYmVyfSB0aW1lb3V0IFRpbWVvdXQgKG1zKVxuICAgICAqIEByZXR1cm4ge1Byb21pc2V9IFByb21pc2UgdGhhdCBpcyByZXNvbHZlZCBieSBmdWxmaWxsbWVudCBvZiBhbGwgcHJvbWlzZXNcbiAgICAgKiBvciByZWplY3RlZCBieSBleGNlZWRpbmcgdGhlIHRpbWVvdXQgd2luZG93XG4gICAgICovXG4gICAgZnVuY3Rpb24gZXhlY3V0ZVdpdGhpblRpbWVvdXRXaW5kb3cocHJvbWlzZXMsIHRpbWVvdXQpIHtcbiAgICAgICAgcmV0dXJuIG5ldyBQcm9taXNlKHJlc29sdmUgPT4ge1xuICAgICAgICAgICAgbGV0IGlzRmluaXNoZWQgPSBmYWxzZTtcblxuICAgICAgICAgICAgUHJvbWlzZS5hbGwocHJvbWlzZXMpLnRoZW4ocmVzdWx0cyA9PiB7XG4gICAgICAgICAgICAgICAgaXNGaW5pc2hlZCA9IHRydWU7XG4gICAgICAgICAgICAgICAgcmVzb2x2ZShyZXN1bHRzKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgICAgICAgICAgICBpZiAoIWlzRmluaXNoZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgcmVzb2x2ZSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sIHRpbWVvdXQpO1xuICAgICAgICB9KVxuICAgIH1cblxuICAgIC8qKlxuICAgICAqIFBlcmZvcm0gYWxsIGhvb2tzXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGtleSBIb29rIGNvbGxlY3Rpb24ga2V5XG4gICAgICogQHBhcmFtIHsqfSBbcGFyYW1ldGVyc10gRXhlY3V0aW9uIGNvbnRleHQgcGFyYW1ldGVyc1xuICAgICAqIEBwYXJhbSB7TnVtYmVyfSBbdGltZW91dF0gVGltZW91dCB3aW5kb3cgKG1zKSBmb3IgZXhlY3V0aW9uICgwIGZvciBubyB0aW1lb3V0KVxuICAgICAqIEByZXR1cm4ge1Byb21pc2V9XG4gICAgICovXG4gICAgZnVuY3Rpb24gZXhlY3V0ZShrZXksIHBhcmFtZXRlcnMsIHRpbWVvdXQgPSAwKSB7XG4gICAgICAgIGNvbnN0IHByb21pc2VzID0gW107XG5cbiAgICAgICAgaWYgKCFob29rcy5oYXMoa2V5KSB8fCAhaG9va3MuZ2V0KGtleSkuc2l6ZSkge1xuICAgICAgICAgICAgcmV0dXJuIFByb21pc2UucmVzb2x2ZSgpO1xuICAgICAgICB9XG5cbiAgICAgICAgZm9yIChjb25zdCBob29rIG9mIGhvb2tzLmdldChrZXkpLnZhbHVlcygpKSB7XG4gICAgICAgICAgICBwcm9taXNlcy5wdXNoKGhvb2socGFyYW1ldGVycykpO1xuICAgICAgICB9XG5cbiAgICAgICAgaG9va3MuZ2V0KGtleSkuY2xlYXIoKTtcblxuICAgICAgICBpZiAoTnVtYmVyLmlzSW50ZWdlcih0aW1lb3V0KSAmJiB0aW1lb3V0ID4gMCkge1xuICAgICAgICAgICAgcmV0dXJuIGV4ZWN1dGVXaXRoaW5UaW1lb3V0V2luZG93KHByb21pc2VzLCB0aW1lb3V0KTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBQcm9taXNlLmFsbChwcm9taXNlcyk7XG4gICAgfVxuXG4gICAgLy8gUHVibGljIHByb3BlcnRpZXMgYW5kIG1ldGhvZHNcbiAgICBleHBvcnRzLmtleXMgPSBrZXlzO1xuICAgIGV4cG9ydHMuYWRkID0gYWRkO1xuICAgIGV4cG9ydHMuZXhlY3V0ZSA9IGV4ZWN1dGU7XG59KShqc2UubGlicy5ob29rcyk7Il19
