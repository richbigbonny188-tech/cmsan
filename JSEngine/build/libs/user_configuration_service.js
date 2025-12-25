'use strict';

/* --------------------------------------------------------------
 user_configuration_service.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.user_configuration_service = jse.libs.user_configuration_service || {};

/**
 * ## User Configuration Library
 *
 * This library is an adapter for the UserConfigurationService of the shop's backend codebase. It will
 * perform AJAX requests for getting/setting user config data that is a robust way to store information
 * about a specific user.
 *
 *```js
 * var options= {
 *     data: {
 *         userId: 1,  // Current user ID
 *         configurationKey: 'recent_search_area', // Configuration key
 *         configurationValue: '', // Configuration value (only for posting)
 *     },
 *
 *     onSuccess: function (data) {}, // Callback function, that will be executed on successful request,
 *                                    // contains the response as argument.
 *
 *     onError: function (data) {},   // Callback function, that will be executed on failed request.
 * }
 *
 * jse.libs.user_configuration_service.set(options); // Set values
 *
 * jse.libs.user_configuration_service.get(options); // Get values
 * ```
 *
 * @module JSE/Libs/user_configuration_service
 * @exports jse.libs.user_configuration_service
 */
(function (exports) {

    'use strict';

    // ------------------------------------------------------------------------
    // DEFAULTS
    // ------------------------------------------------------------------------

    /**
     * Default Library Settings
     *
     * @type {object}
     */

    var defaults = {
        // URL
        baseUrl: 'admin.php?do=UserConfiguration',
        urlSet: '/set',
        urlGet: '/get'
    };

    // ------------------------------------------------------------------------
    // PRIVATE METHODS
    // ------------------------------------------------------------------------

    /**
     * Performs AJAX request
     *
     * @param {object} params Contains the request parameters.
     * @param {string} params.type - type of request
     * @param {function} params.onSuccess - callback on success
     * @param {function} params.onError - callback on success
     * @param {object} params.data - request parameter
     *
     * @throws Error
     *
     * @private
     */
    var _request = function _request(params) {
        $.ajax({
            url: [defaults.baseUrl, params.type === 'set' ? defaults.urlSet : defaults.urlGet].join(''),
            dataType: 'json',
            data: params.data,
            method: params.type === 'set' ? 'post' : 'get',
            success: function success(data) {
                if (params.type === 'get') {
                    // GET
                    _handleSuccess(data, params);
                } else {
                    // POST
                    if (data.success) {
                        _handleSuccess({}, params);
                    } else if (typeof params.onError === 'function') {
                        params.onError(data);
                    }
                }
            },
            error: function error(data) {
                if (typeof params.onError === 'function') {
                    params.onError(data);
                }
            }
        });
    };

    /**
     * Handles success requests.
     *
     * @param {object} data - Data returned from server
     * @param {object} params - Parameters
     *
     * @private
     */
    var _handleSuccess = function _handleSuccess(data, params) {
        var response = {};
        if (data.success && data.configurationValue) {
            response = data;
        }
        if (typeof params.onSuccess === 'function') {
            params.onSuccess(response);
        }
    };

    // ------------------------------------------------------------------------
    // PUBLIC METHODS
    // ------------------------------------------------------------------------

    /**
     * Returns the user configuration value.
     *
     * @param {object} options
     * @param {function} options.onSuccess - callback on success
     * @param {function} options.onError - callback on success
     * @param {object} options.data - request parameter
     */
    exports.get = function (options) {
        options.type = 'get';
        _request(options);
    };

    /**
     * Sets the user configuration value.
     *
     * @param {object} options
     * @param {function} options.onSuccess - callback on success
     * @param {function} options.onError - callback on success
     * @param {object} options.data - request parameter
     */
    exports.set = function (options) {
        options.type = 'set';
        _request(options);
    };
})(jse.libs.user_configuration_service);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlLmpzIl0sIm5hbWVzIjpbImpzZSIsImxpYnMiLCJ1c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZSIsImV4cG9ydHMiLCJkZWZhdWx0cyIsImJhc2VVcmwiLCJ1cmxTZXQiLCJ1cmxHZXQiLCJfcmVxdWVzdCIsInBhcmFtcyIsIiQiLCJhamF4IiwidXJsIiwidHlwZSIsImpvaW4iLCJkYXRhVHlwZSIsImRhdGEiLCJtZXRob2QiLCJzdWNjZXNzIiwiX2hhbmRsZVN1Y2Nlc3MiLCJvbkVycm9yIiwiZXJyb3IiLCJyZXNwb25zZSIsImNvbmZpZ3VyYXRpb25WYWx1ZSIsIm9uU3VjY2VzcyIsImdldCIsIm9wdGlvbnMiLCJzZXQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQywwQkFBVCxHQUFzQ0YsSUFBSUMsSUFBSixDQUFTQywwQkFBVCxJQUF1QyxFQUE3RTs7QUFFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUE2QkEsQ0FBQyxVQUFVQyxPQUFWLEVBQW1COztBQUVoQjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLFFBQUlDLFdBQVc7QUFDWDtBQUNBQyxpQkFBUyxnQ0FGRTtBQUdYQyxnQkFBUSxNQUhHO0FBSVhDLGdCQUFRO0FBSkcsS0FBZjs7QUFPQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7Ozs7Ozs7Ozs7QUFhQSxRQUFJQyxXQUFXLFNBQVhBLFFBQVcsQ0FBVUMsTUFBVixFQUFrQjtBQUM3QkMsVUFBRUMsSUFBRixDQUFPO0FBQ0hDLGlCQUFLLENBQ0RSLFNBQVNDLE9BRFIsRUFFQUksT0FBT0ksSUFBUCxLQUFnQixLQUFoQixHQUF3QlQsU0FBU0UsTUFBakMsR0FBMENGLFNBQVNHLE1BRm5ELEVBR0hPLElBSEcsQ0FHRSxFQUhGLENBREY7QUFLSEMsc0JBQVUsTUFMUDtBQU1IQyxrQkFBTVAsT0FBT08sSUFOVjtBQU9IQyxvQkFBU1IsT0FBT0ksSUFBUCxLQUFnQixLQUFoQixHQUF3QixNQUF4QixHQUFpQyxLQVB2QztBQVFISyxxQkFBUyxpQkFBVUYsSUFBVixFQUFnQjtBQUNyQixvQkFBSVAsT0FBT0ksSUFBUCxLQUFnQixLQUFwQixFQUEyQjtBQUFFO0FBQ3pCTSxtQ0FBZUgsSUFBZixFQUFxQlAsTUFBckI7QUFDSCxpQkFGRCxNQUVPO0FBQUU7QUFDTCx3QkFBSU8sS0FBS0UsT0FBVCxFQUFrQjtBQUNkQyx1Q0FBZSxFQUFmLEVBQW1CVixNQUFuQjtBQUNILHFCQUZELE1BRU8sSUFBSSxPQUFPQSxPQUFPVyxPQUFkLEtBQTBCLFVBQTlCLEVBQTBDO0FBQzdDWCwrQkFBT1csT0FBUCxDQUFlSixJQUFmO0FBQ0g7QUFDSjtBQUNKLGFBbEJFO0FBbUJISyxtQkFBTyxlQUFVTCxJQUFWLEVBQWdCO0FBQ25CLG9CQUFJLE9BQU9QLE9BQU9XLE9BQWQsS0FBMEIsVUFBOUIsRUFBMEM7QUFDdENYLDJCQUFPVyxPQUFQLENBQWVKLElBQWY7QUFDSDtBQUNKO0FBdkJFLFNBQVA7QUF5QkgsS0ExQkQ7O0FBNEJBOzs7Ozs7OztBQVFBLFFBQUlHLGlCQUFpQixTQUFqQkEsY0FBaUIsQ0FBVUgsSUFBVixFQUFnQlAsTUFBaEIsRUFBd0I7QUFDekMsWUFBSWEsV0FBVyxFQUFmO0FBQ0EsWUFBSU4sS0FBS0UsT0FBTCxJQUFnQkYsS0FBS08sa0JBQXpCLEVBQTZDO0FBQ3pDRCx1QkFBV04sSUFBWDtBQUNIO0FBQ0QsWUFBSSxPQUFPUCxPQUFPZSxTQUFkLEtBQTRCLFVBQWhDLEVBQTRDO0FBQ3hDZixtQkFBT2UsU0FBUCxDQUFpQkYsUUFBakI7QUFDSDtBQUNKLEtBUkQ7O0FBVUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7OztBQVFBbkIsWUFBUXNCLEdBQVIsR0FBYyxVQUFVQyxPQUFWLEVBQW1CO0FBQzdCQSxnQkFBUWIsSUFBUixHQUFlLEtBQWY7QUFDQUwsaUJBQVNrQixPQUFUO0FBQ0gsS0FIRDs7QUFLQTs7Ozs7Ozs7QUFRQXZCLFlBQVF3QixHQUFSLEdBQWMsVUFBVUQsT0FBVixFQUFtQjtBQUM3QkEsZ0JBQVFiLElBQVIsR0FBZSxLQUFmO0FBQ0FMLGlCQUFTa0IsT0FBVDtBQUNILEtBSEQ7QUFLSCxDQWpIRCxFQWlIRzFCLElBQUlDLElBQUosQ0FBU0MsMEJBakhaIiwiZmlsZSI6InVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiB1c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZS5qcyAyMDE2LTAyLTIzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMudXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UgPSBqc2UubGlicy51c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZSB8fCB7fTtcblxuLyoqXG4gKiAjIyBVc2VyIENvbmZpZ3VyYXRpb24gTGlicmFyeVxuICpcbiAqIFRoaXMgbGlicmFyeSBpcyBhbiBhZGFwdGVyIGZvciB0aGUgVXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlIG9mIHRoZSBzaG9wJ3MgYmFja2VuZCBjb2RlYmFzZS4gSXQgd2lsbFxuICogcGVyZm9ybSBBSkFYIHJlcXVlc3RzIGZvciBnZXR0aW5nL3NldHRpbmcgdXNlciBjb25maWcgZGF0YSB0aGF0IGlzIGEgcm9idXN0IHdheSB0byBzdG9yZSBpbmZvcm1hdGlvblxuICogYWJvdXQgYSBzcGVjaWZpYyB1c2VyLlxuICpcbiAqYGBganNcbiAqIHZhciBvcHRpb25zPSB7XG4gKiAgICAgZGF0YToge1xuICogICAgICAgICB1c2VySWQ6IDEsICAvLyBDdXJyZW50IHVzZXIgSURcbiAqICAgICAgICAgY29uZmlndXJhdGlvbktleTogJ3JlY2VudF9zZWFyY2hfYXJlYScsIC8vIENvbmZpZ3VyYXRpb24ga2V5XG4gKiAgICAgICAgIGNvbmZpZ3VyYXRpb25WYWx1ZTogJycsIC8vIENvbmZpZ3VyYXRpb24gdmFsdWUgKG9ubHkgZm9yIHBvc3RpbmcpXG4gKiAgICAgfSxcbiAqXG4gKiAgICAgb25TdWNjZXNzOiBmdW5jdGlvbiAoZGF0YSkge30sIC8vIENhbGxiYWNrIGZ1bmN0aW9uLCB0aGF0IHdpbGwgYmUgZXhlY3V0ZWQgb24gc3VjY2Vzc2Z1bCByZXF1ZXN0LFxuICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBjb250YWlucyB0aGUgcmVzcG9uc2UgYXMgYXJndW1lbnQuXG4gKlxuICogICAgIG9uRXJyb3I6IGZ1bmN0aW9uIChkYXRhKSB7fSwgICAvLyBDYWxsYmFjayBmdW5jdGlvbiwgdGhhdCB3aWxsIGJlIGV4ZWN1dGVkIG9uIGZhaWxlZCByZXF1ZXN0LlxuICogfVxuICpcbiAqIGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlLnNldChvcHRpb25zKTsgLy8gU2V0IHZhbHVlc1xuICpcbiAqIGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlLmdldChvcHRpb25zKTsgLy8gR2V0IHZhbHVlc1xuICogYGBgXG4gKlxuICogQG1vZHVsZSBKU0UvTGlicy91c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZVxuICogQGV4cG9ydHMganNlLmxpYnMudXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2VcbiAqL1xuKGZ1bmN0aW9uIChleHBvcnRzKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAgICAvLyBERUZBVUxUU1xuICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG4gICAgLyoqXG4gICAgICogRGVmYXVsdCBMaWJyYXJ5IFNldHRpbmdzXG4gICAgICpcbiAgICAgKiBAdHlwZSB7b2JqZWN0fVxuICAgICAqL1xuICAgIHZhciBkZWZhdWx0cyA9IHtcbiAgICAgICAgLy8gVVJMXG4gICAgICAgIGJhc2VVcmw6ICdhZG1pbi5waHA/ZG89VXNlckNvbmZpZ3VyYXRpb24nLFxuICAgICAgICB1cmxTZXQ6ICcvc2V0JyxcbiAgICAgICAgdXJsR2V0OiAnL2dldCdcbiAgICB9O1xuXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICAgLy8gUFJJVkFURSBNRVRIT0RTXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cbiAgICAvKipcbiAgICAgKiBQZXJmb3JtcyBBSkFYIHJlcXVlc3RcbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBwYXJhbXMgQ29udGFpbnMgdGhlIHJlcXVlc3QgcGFyYW1ldGVycy5cbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gcGFyYW1zLnR5cGUgLSB0eXBlIG9mIHJlcXVlc3RcbiAgICAgKiBAcGFyYW0ge2Z1bmN0aW9ufSBwYXJhbXMub25TdWNjZXNzIC0gY2FsbGJhY2sgb24gc3VjY2Vzc1xuICAgICAqIEBwYXJhbSB7ZnVuY3Rpb259IHBhcmFtcy5vbkVycm9yIC0gY2FsbGJhY2sgb24gc3VjY2Vzc1xuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBwYXJhbXMuZGF0YSAtIHJlcXVlc3QgcGFyYW1ldGVyXG4gICAgICpcbiAgICAgKiBAdGhyb3dzIEVycm9yXG4gICAgICpcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIHZhciBfcmVxdWVzdCA9IGZ1bmN0aW9uIChwYXJhbXMpIHtcbiAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgIHVybDogW1xuICAgICAgICAgICAgICAgIGRlZmF1bHRzLmJhc2VVcmwsXG4gICAgICAgICAgICAgICAgKHBhcmFtcy50eXBlID09PSAnc2V0JyA/IGRlZmF1bHRzLnVybFNldCA6IGRlZmF1bHRzLnVybEdldClcbiAgICAgICAgICAgIF0uam9pbignJyksXG4gICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgICAgICAgZGF0YTogcGFyYW1zLmRhdGEsXG4gICAgICAgICAgICBtZXRob2Q6IChwYXJhbXMudHlwZSA9PT0gJ3NldCcgPyAncG9zdCcgOiAnZ2V0JyksXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAoZGF0YSkge1xuICAgICAgICAgICAgICAgIGlmIChwYXJhbXMudHlwZSA9PT0gJ2dldCcpIHsgLy8gR0VUXG4gICAgICAgICAgICAgICAgICAgIF9oYW5kbGVTdWNjZXNzKGRhdGEsIHBhcmFtcyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHsgLy8gUE9TVFxuICAgICAgICAgICAgICAgICAgICBpZiAoZGF0YS5zdWNjZXNzKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBfaGFuZGxlU3VjY2Vzcyh7fSwgcGFyYW1zKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmICh0eXBlb2YgcGFyYW1zLm9uRXJyb3IgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHBhcmFtcy5vbkVycm9yKGRhdGEpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGVycm9yOiBmdW5jdGlvbiAoZGF0YSkge1xuICAgICAgICAgICAgICAgIGlmICh0eXBlb2YgcGFyYW1zLm9uRXJyb3IgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgICAgICAgICAgcGFyYW1zLm9uRXJyb3IoZGF0YSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogSGFuZGxlcyBzdWNjZXNzIHJlcXVlc3RzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtvYmplY3R9IGRhdGEgLSBEYXRhIHJldHVybmVkIGZyb20gc2VydmVyXG4gICAgICogQHBhcmFtIHtvYmplY3R9IHBhcmFtcyAtIFBhcmFtZXRlcnNcbiAgICAgKlxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgdmFyIF9oYW5kbGVTdWNjZXNzID0gZnVuY3Rpb24gKGRhdGEsIHBhcmFtcykge1xuICAgICAgICB2YXIgcmVzcG9uc2UgPSB7fTtcbiAgICAgICAgaWYgKGRhdGEuc3VjY2VzcyAmJiBkYXRhLmNvbmZpZ3VyYXRpb25WYWx1ZSkge1xuICAgICAgICAgICAgcmVzcG9uc2UgPSBkYXRhO1xuICAgICAgICB9XG4gICAgICAgIGlmICh0eXBlb2YgcGFyYW1zLm9uU3VjY2VzcyA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICAgICAgcGFyYW1zLm9uU3VjY2VzcyhyZXNwb25zZSk7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICAgLy8gUFVCTElDIE1FVEhPRFNcbiAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuICAgIC8qKlxuICAgICAqIFJldHVybnMgdGhlIHVzZXIgY29uZmlndXJhdGlvbiB2YWx1ZS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zXG4gICAgICogQHBhcmFtIHtmdW5jdGlvbn0gb3B0aW9ucy5vblN1Y2Nlc3MgLSBjYWxsYmFjayBvbiBzdWNjZXNzXG4gICAgICogQHBhcmFtIHtmdW5jdGlvbn0gb3B0aW9ucy5vbkVycm9yIC0gY2FsbGJhY2sgb24gc3VjY2Vzc1xuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zLmRhdGEgLSByZXF1ZXN0IHBhcmFtZXRlclxuICAgICAqL1xuICAgIGV4cG9ydHMuZ2V0ID0gZnVuY3Rpb24gKG9wdGlvbnMpIHtcbiAgICAgICAgb3B0aW9ucy50eXBlID0gJ2dldCc7XG4gICAgICAgIF9yZXF1ZXN0KG9wdGlvbnMpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBTZXRzIHRoZSB1c2VyIGNvbmZpZ3VyYXRpb24gdmFsdWUuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge29iamVjdH0gb3B0aW9uc1xuICAgICAqIEBwYXJhbSB7ZnVuY3Rpb259IG9wdGlvbnMub25TdWNjZXNzIC0gY2FsbGJhY2sgb24gc3VjY2Vzc1xuICAgICAqIEBwYXJhbSB7ZnVuY3Rpb259IG9wdGlvbnMub25FcnJvciAtIGNhbGxiYWNrIG9uIHN1Y2Nlc3NcbiAgICAgKiBAcGFyYW0ge29iamVjdH0gb3B0aW9ucy5kYXRhIC0gcmVxdWVzdCBwYXJhbWV0ZXJcbiAgICAgKi9cbiAgICBleHBvcnRzLnNldCA9IGZ1bmN0aW9uIChvcHRpb25zKSB7XG4gICAgICAgIG9wdGlvbnMudHlwZSA9ICdzZXQnO1xuICAgICAgICBfcmVxdWVzdChvcHRpb25zKTtcbiAgICB9O1xuXG59KShqc2UubGlicy51c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZSk7XG4iXX0=
