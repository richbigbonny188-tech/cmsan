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
            parameters.data.page_token = ($pageToken.length) ? $pageToken.val() : '';
        }

        var options = $.extend({}, defaultAjaxOptions, parameters),
            deferred = $.Deferred(),
            promise = deferred.promise();

        /**
         * Default fail handler
         *
         * @param {Object} result The failure response
         */
        var _failHandler = function (result) {
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
        return exports.ajax($.extend({}, {type: 'get'}, parameters), ignoreFail);
    };

    /**
     * Wrapper function for the jquery "post" method.
     *
     * @param {object} parameters AJAX-config object which gets merged with the default settings from config.
     *
     * @return {object} Returns an ajax compatible promise object.
     */
    exports.post = function (parameters, ignoreFail) {
        return exports.ajax($.extend({}, {type: 'post'}, parameters), ignoreFail);
    };

    /**
     * Wrapper function for the jquery "post" method.
     *
     * @param {object} parameters AJAX-config object which gets merged with the default settings from config.
     *
     * @return {object} Returns an ajax compatible promise object.
     */
    exports.delete = function (parameters, ignoreFail) {
        return exports.ajax($.extend({}, {type: 'delete'}, parameters), ignoreFail);
    };

    /**
     * Wrapper function for the jquery "post" method.
     *
     * @param {object} parameters AJAX-config object which gets merged with the default settings from config.
     *
     * @return {object} Returns an ajax compatible promise object.
     */
    exports.put = function (parameters, ignoreFail) {
        return exports.ajax($.extend({}, {type: 'put'}, parameters), ignoreFail);
    };

})(jse.libs.xhr);
