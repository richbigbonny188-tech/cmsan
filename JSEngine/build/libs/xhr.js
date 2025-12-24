'use strict';

/* --------------------------------------------------------------
 xhr.js 2020-03-05
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.xhr = jse.libs.xhr || {};

/**
 * ## AJAX Library
 *
 * This library contains wrapper-methods for the original jquery AJAX methods ('ajax', 'post', 'get').
 *
 * @module JSE/Libs/xhr
 * @exports jse.libs.xhr
 */
(function (exports) {

    'use strict';

    /**
     * Default AJAX Options
     *
     * @type {object}
     */

    var defaultAjaxOptions = {
        type: 'post',
        dataType: 'json',
        cache: false,
        async: true
    };

    /**
     * Wrapper for the jquery "ajax" method.
     *
     * @param {object} parameters AJAX-config object which gets merged with the default settings from config.
     *
     * @return {object} Returns an ajax compatible promise object.
     */
    exports.ajax = function (parameters, ignoreFail) {

        var $pageToken = $('input[name="page_token"]');

        parameters = parameters || {};
        parameters.data = parameters.data || {};

        // If no page token was provided try to use the existing one.
        if (!parameters.data.page_token) {
            parameters.data.page_token = $pageToken.length ? $pageToken.val() : '';
        }

        var options = $.extend({}, defaultAjaxOptions, parameters),
            deferred = $.Deferred(),
            promise = deferred.promise();

        /**
         * Default fail handler
         *
         * @param {Object} result The failure response
         */
        var _failHandler = function _failHandler(result) {
            if (result === null) {
                deferred.reject();
            } else {
                result.message = result.message || 'JavaScript AJAX Error';
                deferred.reject(result);
            }
        };

        // The ajax call
        var ajax = $.ajax(options).done(function (result) {
            // Check if it is an JSON-compatible result, if so, check the success message.
            if (result.success !== undefined && result.success === false && !ignoreFail) {
                _failHandler(result);
            } else {
                // set new page_token
                if (result.page_token !== undefined) {
                    $pageToken.val(result.page_token);
                }
                deferred.resolve(result);
            }
        }).fail(function () {
            if (!ignoreFail) {
                _failHandler(null);
            } else {
                deferred.reject({});
            }
        });

        // Add an ajax abort method to the promise, for cases where we need to abort the AJAX request.
        promise.abort = function () {
            ajax.abort();
        };

        return promise;
    };

    /**
     * Wrapper function for the jquery "get" method.
     *
     * @param {object} parameters AJAX-config object which will be merged with the default settings from config.
     *
     * @return {object} Returns an ajax compatible promise object.
     */
    exports.get = function (parameters, ignoreFail) {
        return exports.ajax($.extend({}, { type: 'get' }, parameters), ignoreFail);
    };

    /**
     * Wrapper function for the jquery "post" method.
     *
     * @param {object} parameters AJAX-config object which gets merged with the default settings from config.
     *
     * @return {object} Returns an ajax compatible promise object.
     */
    exports.post = function (parameters, ignoreFail) {
        return exports.ajax($.extend({}, { type: 'post' }, parameters), ignoreFail);
    };

    /**
     * Wrapper function for the jquery "post" method.
     *
     * @param {object} parameters AJAX-config object which gets merged with the default settings from config.
     *
     * @return {object} Returns an ajax compatible promise object.
     */
    exports.delete = function (parameters, ignoreFail) {
        return exports.ajax($.extend({}, { type: 'delete' }, parameters), ignoreFail);
    };

    /**
     * Wrapper function for the jquery "post" method.
     *
     * @param {object} parameters AJAX-config object which gets merged with the default settings from config.
     *
     * @return {object} Returns an ajax compatible promise object.
     */
    exports.put = function (parameters, ignoreFail) {
        return exports.ajax($.extend({}, { type: 'put' }, parameters), ignoreFail);
    };
})(jse.libs.xhr);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInhoci5qcyJdLCJuYW1lcyI6WyJqc2UiLCJsaWJzIiwieGhyIiwiZXhwb3J0cyIsImRlZmF1bHRBamF4T3B0aW9ucyIsInR5cGUiLCJkYXRhVHlwZSIsImNhY2hlIiwiYXN5bmMiLCJhamF4IiwicGFyYW1ldGVycyIsImlnbm9yZUZhaWwiLCIkcGFnZVRva2VuIiwiJCIsImRhdGEiLCJwYWdlX3Rva2VuIiwibGVuZ3RoIiwidmFsIiwib3B0aW9ucyIsImV4dGVuZCIsImRlZmVycmVkIiwiRGVmZXJyZWQiLCJwcm9taXNlIiwiX2ZhaWxIYW5kbGVyIiwicmVzdWx0IiwicmVqZWN0IiwibWVzc2FnZSIsImRvbmUiLCJzdWNjZXNzIiwidW5kZWZpbmVkIiwicmVzb2x2ZSIsImZhaWwiLCJhYm9ydCIsImdldCIsInBvc3QiLCJkZWxldGUiLCJwdXQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQyxHQUFULEdBQWVGLElBQUlDLElBQUosQ0FBU0MsR0FBVCxJQUFnQixFQUEvQjs7QUFFQTs7Ozs7Ozs7QUFRQSxDQUFDLFVBQVVDLE9BQVYsRUFBbUI7O0FBRWhCOztBQUVBOzs7Ozs7QUFLQSxRQUFJQyxxQkFBcUI7QUFDckJDLGNBQU0sTUFEZTtBQUVyQkMsa0JBQVUsTUFGVztBQUdyQkMsZUFBTyxLQUhjO0FBSXJCQyxlQUFPO0FBSmMsS0FBekI7O0FBT0E7Ozs7Ozs7QUFPQUwsWUFBUU0sSUFBUixHQUFlLFVBQVVDLFVBQVYsRUFBc0JDLFVBQXRCLEVBQWtDOztBQUU3QyxZQUFJQyxhQUFhQyxFQUFFLDBCQUFGLENBQWpCOztBQUVBSCxxQkFBYUEsY0FBYyxFQUEzQjtBQUNBQSxtQkFBV0ksSUFBWCxHQUFrQkosV0FBV0ksSUFBWCxJQUFtQixFQUFyQzs7QUFFQTtBQUNBLFlBQUksQ0FBQ0osV0FBV0ksSUFBWCxDQUFnQkMsVUFBckIsRUFBaUM7QUFDN0JMLHVCQUFXSSxJQUFYLENBQWdCQyxVQUFoQixHQUE4Qkgs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dGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHhoci5qcyAyMDIwLTAzLTA1XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAyMCBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMueGhyID0ganNlLmxpYnMueGhyIHx8IHt9O1xuXG4vKipcbiAqICMjIEFKQVggTGlicmFyeVxuICpcbiAqIFRoaXMgbGlicmFyeSBjb250YWlucyB3cmFwcGVyLW1ldGhvZHMgZm9yIHRoZSBvcmlnaW5hbCBqcXVlcnkgQUpBWCBtZXRob2RzICgnYWpheCcsICdwb3N0JywgJ2dldCcpLlxuICpcbiAqIEBtb2R1bGUgSlNFL0xpYnMveGhyXG4gKiBAZXhwb3J0cyBqc2UubGlicy54aHJcbiAqL1xuKGZ1bmN0aW9uIChleHBvcnRzKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvKipcbiAgICAgKiBEZWZhdWx0IEFKQVggT3B0aW9uc1xuICAgICAqXG4gICAgICogQHR5cGUge29iamVjdH1cbiAgICAgKi9cbiAgICB2YXIgZGVmYXVsdEFqYXhPcHRpb25zID0ge1xuICAgICAgICB0eXBlOiAncG9zdCcsXG4gICAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICAgIGNhY2hlOiBmYWxzZSxcbiAgICAgICAgYXN5bmM6IHRydWVcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogV3JhcHBlciBmb3IgdGhlIGpxdWVyeSBcImFqYXhcIiBtZXRob2QuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge29iamVjdH0gcGFyYW1ldGVycyBBSkFYLWNvbmZpZyBvYmplY3Qgd2hpY2ggZ2V0cyBtZXJnZWQgd2l0aCB0aGUgZGVmYXVsdCBzZXR0aW5ncyBmcm9tIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhbiBhamF4IGNvbXBhdGlibGUgcHJvbWlzZSBvYmplY3QuXG4gICAgICovXG4gICAgZXhwb3J0cy5hamF4ID0gZnVuY3Rpb24gKHBhcmFtZXRlcnMsIGlnbm9yZUZhaWwpIHtcblxuICAgICAgICB2YXIgJHBhZ2VUb2tlbiA9ICQoJ2lucHV0W25hbWU9XCJwYWdlX3Rva2VuXCJdJyk7XG5cbiAgICAgICAgcGFyYW1ldGVycyA9IHBhcmFtZXRlcnMgfHwge307XG4gICAgICAgIHBhcmFtZXRlcnMuZGF0YSA9IHBhcmFtZXRlcnMuZGF0YSB8fCB7fTtcblxuICAgICAgICAvLyBJZiBubyBwYWdlIHRva2VuIHdhcyBwcm92aWRlZCB0cnkgdG8gdXNlIHRoZSBleGlzdGluZyBvbmUuXG4gICAgICAgIGlmICghcGFyYW1ldGVycy5kYXRhLnBhZ2VfdG9rZW4pIHtcbiAgICAgICAgICAgIHBhcmFtZXRlcnMuZGF0YS5wYWdlX3Rva2VuID0gKCRwYWdlVG9rZW4ubGVuZ3RoKSA/ICRwYWdlVG9rZW4udmFsKCkgOiAnJztcbiAgICAgICAgfVxuXG4gICAgICAgIHZhciBvcHRpb25zID0gJC5leHRlbmQoe30sIGRlZmF1bHRBamF4T3B0aW9ucywgcGFyYW1ldGVycyksXG4gICAgICAgICAgICBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKSxcbiAgICAgICAgICAgIHByb21pc2UgPSBkZWZlcnJlZC5wcm9taXNlKCk7XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIERlZmF1bHQgZmFpbCBoYW5kbGVyXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwYXJhbSB7T2JqZWN0fSByZXN1bHQgVGhlIGZhaWx1cmUgcmVzcG9uc2VcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfZmFpbEhhbmRsZXIgPSBmdW5jdGlvbiAocmVzdWx0KSB7XG4gICAgICAgIFx0aWYgKHJlc3VsdCA9PT0gbnVsbCkge1xuICAgICAgICBcdFx0ZGVmZXJyZWQucmVqZWN0KCk7XG5cdCAgICAgICAgfSBlbHNlIHtcblx0XHQgICAgICAgIHJlc3VsdC5tZXNzYWdlID0gcmVzdWx0Lm1lc3NhZ2UgfHwgJ0phdmFTY3JpcHQgQUpBWCBFcnJvcic7XG5cdFx0ICAgICAgICBkZWZlcnJlZC5yZWplY3QocmVzdWx0KTtcblx0ICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gVGhlIGFqYXggY2FsbFxuICAgICAgICB2YXIgYWpheCA9ICQuYWpheChvcHRpb25zKS5kb25lKGZ1bmN0aW9uIChyZXN1bHQpIHtcbiAgICAgICAgICAgIC8vIENoZWNrIGlmIGl0IGlzIGFuIEpTT04tY29tcGF0aWJsZSByZXN1bHQsIGlmIHNvLCBjaGVjayB0aGUgc3VjY2VzcyBtZXNzYWdlLlxuICAgICAgICAgICAgaWYgKHJlc3VsdC5zdWNjZXNzICE9PSB1bmRlZmluZWQgJiYgcmVzdWx0LnN1Y2Nlc3MgPT09IGZhbHNlICYmICFpZ25vcmVGYWlsKSB7XG4gICAgICAgICAgICAgICAgX2ZhaWxIYW5kbGVyKHJlc3VsdCk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIC8vIHNldCBuZXcgcGFnZV90b2tlblxuICAgICAgICAgICAgICAgIGlmIChyZXN1bHQucGFnZV90b2tlbiAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICAgICAgICAgICRwYWdlVG9rZW4udmFsKHJlc3VsdC5wYWdlX3Rva2VuKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgZGVmZXJyZWQucmVzb2x2ZShyZXN1bHQpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KS5mYWlsKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGlmICghaWdub3JlRmFpbCkge1xuICAgICAgICAgICAgICAgIF9mYWlsSGFuZGxlcihudWxsKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgZGVmZXJyZWQucmVqZWN0KHt9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy8gQWRkIGFuIGFqYXggYWJvcnQgbWV0aG9kIHRvIHRoZSBwcm9taXNlLCBmb3IgY2FzZXMgd2hlcmUgd2UgbmVlZCB0byBhYm9ydCB0aGUgQUpBWCByZXF1ZXN0LlxuICAgICAgICBwcm9taXNlLmFib3J0ID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgYWpheC5hYm9ydCgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIHJldHVybiBwcm9taXNlO1xuICAgIH07XG5cblxuICAgIC8qKlxuICAgICAqIFdyYXBwZXIgZnVuY3Rpb24gZm9yIHRoZSBqcXVlcnkgXCJnZXRcIiBtZXRob2QuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge29iamVjdH0gcGFyYW1ldGVycyBBSkFYLWNvbmZpZyBvYmplY3Qgd2hpY2ggd2lsbCBiZSBtZXJnZWQgd2l0aCB0aGUgZGVmYXVsdCBzZXR0aW5ncyBmcm9tIGNvbmZpZy5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhbiBhamF4IGNvbXBhdGlibGUgcHJvbWlzZSBvYmplY3QuXG4gICAgICovXG4gICAgZXhwb3J0cy5nZXQgPSBmdW5jdGlvbiAocGFyYW1ldGVycywgaWdub3JlRmFpbCkge1xuICAgICAgICByZXR1cm4gZXhwb3J0cy5hamF4KCQuZXh0ZW5kKHt9LCB7dHlwZTogJ2dldCd9LCBwYXJhbWV0ZXJzKSwgaWdub3JlRmFpbCk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIFdyYXBwZXIgZnVuY3Rpb24gZm9yIHRoZSBqcXVlcnkgXCJwb3N0XCIgbWV0aG9kLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtvYmplY3R9IHBhcmFtZXRlcnMgQUpBWC1jb25maWcgb2JqZWN0IHdoaWNoIGdldHMgbWVyZ2VkIHdpdGggdGhlIGRlZmF1bHQgc2V0dGluZ3MgZnJvbSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgYW4gYWpheCBjb21wYXRpYmxlIHByb21pc2Ugb2JqZWN0LlxuICAgICAqL1xuICAgIGV4cG9ydHMucG9zdCA9IGZ1bmN0aW9uIChwYXJhbWV0ZXJzLCBpZ25vcmVGYWlsKSB7XG4gICAgICAgIHJldHVybiBleHBvcnRzLmFqYXgoJC5leHRlbmQoe30sIHt0eXBlOiAncG9zdCd9LCBwYXJhbWV0ZXJzKSwgaWdub3JlRmFpbCk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIFdyYXBwZXIgZnVuY3Rpb24gZm9yIHRoZSBqcXVlcnkgXCJwb3N0XCIgbWV0aG9kLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtvYmplY3R9IHBhcmFtZXRlcnMgQUpBWC1jb25maWcgb2JqZWN0IHdoaWNoIGdldHMgbWVyZ2VkIHdpdGggdGhlIGRlZmF1bHQgc2V0dGluZ3MgZnJvbSBjb25maWcuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgYW4gYWpheCBjb21wYXRpYmxlIHByb21pc2Ugb2JqZWN0LlxuICAgICAqL1xuICAgIGV4cG9ydHMuZGVsZXRlID0gZnVuY3Rpb24gKHBhcmFtZXRlcnMsIGlnbm9yZUZhaWwpIHtcbiAgICAgICAgcmV0dXJuIGV4cG9ydHMuYWpheCgkLmV4dGVuZCh7fSwge3R5cGU6ICdkZWxldGUnfSwgcGFyYW1ldGVycyksIGlnbm9yZUZhaWwpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBXcmFwcGVyIGZ1bmN0aW9uIGZvciB0aGUganF1ZXJ5IFwicG9zdFwiIG1ldGhvZC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBwYXJhbWV0ZXJzIEFKQVgtY29uZmlnIG9iamVjdCB3aGljaCBnZXRzIG1lcmdlZCB3aXRoIHRoZSBkZWZhdWx0IHNldHRpbmdzIGZyb20gY29uZmlnLlxuICAgICAqXG4gICAgICogQHJldHVybiB7b2JqZWN0fSBSZXR1cm5zIGFuIGFqYXggY29tcGF0aWJsZSBwcm9taXNlIG9iamVjdC5cbiAgICAgKi9cbiAgICBleHBvcnRzLnB1dCA9IGZ1bmN0aW9uIChwYXJhbWV0ZXJzLCBpZ25vcmVGYWlsKSB7XG4gICAgICAgIHJldHVybiBleHBvcnRzLmFqYXgoJC5leHRlbmQoe30sIHt0eXBlOiAncHV0J30sIHBhcmFtZXRlcnMpLCBpZ25vcmVGYWlsKTtcbiAgICB9O1xuXG59KShqc2UubGlicy54aHIpO1xuIl19
