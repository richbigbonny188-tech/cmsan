'use strict';

/* --------------------------------------------------------------
 storage.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals Storage */

jse.libs.storage = jse.libs.storage || {};

/**
 * ## Browser Storage API Library
 *
 * This library handles the HTML storage functionality. You can either store information in the session or the local
 * storage of the browser.
 *
 * @deprecated Will be removed with JS Engine v1.7.
 *
 * @module JSE/Libs/storage
 * @exports jse.libs.storage
 * @ignore
 */
(function (exports) {

    'use strict';

    /**
     * JavaScript Storage Object
     *
     * @type {boolean}
     */

    var webStorage = Storage !== undefined ? true : false;

    /**
     * Stores a value in the browser storage.
     *
     * @param {object} store Storage handler object.
     * @param {boolean} overwrite Whether to overwrite an existing storage value.
     * @param {string} value String defining the value key name to be stored.
     * @param {object} dataset Contains the information to be stored.
     * @param {number} userId User id will be used to identify stored information of a specific user.
     *
     * @return {boolean} Returns the operation result.
     *
     * @private
     */
    var _store = function _store(store, overwrite, value, dataset, userId) {

        var dataCache = null,
            result = null;

        if (webStorage) {
            dataCache = store.getItem('user_' + userId);
            dataCache = dataCache || '{}';
            dataCache = $.parseJSON(dataCache);

            if (overwrite || dataCache[value] === undefined) {
                dataCache[value] = dataset;
            } else {
                dataCache[value] = $.extend({}, dataCache[value], dataset);
            }

            result = JSON.stringify(dataCache);
            store.setItem('user_' + userId, result);
            return true;
        }
        return false;
    };

    /**
     * Restores data from the browser storage.
     *
     * @param {object} store Storage handler object.
     * @param {string} value Value key name to be retrieved.
     * @param {number} userId User id that owns the value.
     *
     * @return {object} Returns the value if exists or an empty object if not.
     *
     * @private
     */
    var _restore = function _restore(store, value, userId) {

        var dataCache = null;

        if (webStorage) {
            dataCache = store.getItem('user_' + userId);
            dataCache = dataCache || '{}';
            dataCache = $.parseJSON(dataCache);
            return dataCache[value] || {};
        }
        return {};
    };

    /**
     * Stores data in the browser storage.
     *
     * @param {array} destinations Array containing where to store the data (session, local).
     * @param {object} dataset Data to be stored.
     * @param {boolean} overwrite Whether to overwrite existing values.
     *
     * @return {object} Returns a promise object.
     */
    exports.store = function (destinations, dataset, overwrite) {

        var userID = $('body').data().userId,
            resultObject = {},
            promises = [];

        $.each(destinations, function (dest, value) {
            var localDeferred = $.Deferred();
            promises.push(localDeferred);

            switch (dest) {
                case 'session':
                    resultObject.session = _store(sessionStorage, overwrite, value, dataset, userID);
                    localDeferred.resolve(resultObject);
                    break;
                case 'local':
                    resultObject.local = _store(localStorage, overwrite, value, dataset, userID);
                    localDeferred.resolve(resultObject);
                    break;
                default:
                    break;
            }
        });

        return $.when.apply(undefined, promises).promise();
    };

    /**
     * Restores data from the browser storage.
     *
     * @param {array} sources Defines the source of the data to be retrieved (session, local).
     *
     * @return {object} Returns a promise object.
     */
    exports.restore = function (sources) {
        var userID = $('body').data().userId,
            resultObject = {},
            promises = [];

        $.each(sources, function (src, value) {
            var localDeferred = $.Deferred();
            promises.push(localDeferred);

            switch (src) {
                case 'session':
                    resultObject.session = _restore(sessionStorage, value, userID);
                    localDeferred.resolve(resultObject);
                    break;
                case 'local':
                    resultObject.local = _restore(localStorage, value, userID);
                    localDeferred.resolve(resultObject);
                    break;
                default:
                    break;
            }
        });

        return $.when.apply(undefined, promises).then(function (result) {
            return $.extend(true, {}, result.local || {}, result.session || {}, result.server || {});
        }).promise();
    };
})(jse.libs.storage);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInN0b3JhZ2UuanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsInN0b3JhZ2UiLCJleHBvcnRzIiwid2ViU3RvcmFnZSIsIlN0b3JhZ2UiLCJ1bmRlZmluZWQiLCJfc3RvcmUiLCJzdG9yZSIsIm92ZXJ3cml0ZSIsInZhbHVlIiwiZGF0YXNldCIsInVzZXJJZCIsImRhdGFDYWNoZSIsInJlc3VsdCIsImdldEl0ZW0iLCIkIiwicGFyc2VKU09OIiwiZXh0ZW5kIiwiSlNPTiIsInN0cmluZ2lmeSIsInNldEl0ZW0iLCJfcmVzdG9yZSIsImRlc3RpbmF0aW9ucyIsInVzZXJJRCIsImRhdGEiLCJyZXN1bHRPYmplY3QiLCJwcm9taXNlcyIsImVhY2giLCJkZXN0IiwibG9jYWxEZWZlcnJlZCIsIkRlZmVycmVkIiwicHVzaCIsInNlc3Npb24iLCJzZXNzaW9uU3RvcmFnZSIsInJlc29sdmUiLCJsb2NhbCIsImxvY2FsU3RvcmFnZSIsIndoZW4iLCJhcHBseSIsInByb21pc2UiLCJyZXN0b3JlIiwic291cmNlcyIsInNyYyIsInRoZW4iLCJzZXJ2ZXIiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7QUFFQUEsSUFBSUMsSUFBSixDQUFTQyxPQUFULEdBQW1CRixJQUFJQyxJQUFKLENBQVNDLE9BQVQsSUFBb0IsRUFBdkM7O0FBRUE7Ozs7Ozs7Ozs7OztBQVlDLFdBQVVDLE9BQVYsRUFBbUI7O0FBRWhCOztBQUVBOzs7Ozs7QUFLQSxRQUFJQyxhQUFjQyxZQUFZQyxTQUFiLEdBQTBCLElBQTFCLEdBQWlDLEtBQWxEOztBQUVBOzs7Ozs7Ozs7Ozs7O0FBYUEsUUFBSUMsU0FBUyxTQUFUQSxNQUFTLENBQVVDLEtBQVYsRUFBaUJDLFNBQWpCLEVBQTRCQyxLQUE1QixFQUFtQ0MsT0FBbkMsRUFBNENDLE1BQTVDLEVBQW9EOztBQUU3RCxZQUFJQyxZQUFZLElBQWhCO0FBQUEsWUFDSUMsU0FBUyxJQURiOztBQUdBLFlBQUlWLFVBQUosRUFBZ0I7QUFDWlMs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SWpCLGlDQUFhTyxPQUFiLEdBQXVCWCxTQUFTWSxjQUFULEVBQXlCeEIsS0FBekIsRUFBZ0NjLE1BQWhDLENBQXZCO0FBQ0FNLGtDQUFjSyxPQUFkLENBQXNCVCxZQUF0QjtBQUNBO0FBQ0oscUJBQUssT0FBTDtBQUNJQSxpQ0FBYVUsS0FBYixHQUFxQmQsU0FBU2UsWUFBVCxFQUF1QjNCLEtBQXZCLEVBQThCYyxNQUE5QixDQUFyQjtBQUNBTSxrQ0FBY0ssT0FBZCxDQUFzQlQsWUFBdEI7QUFDQTtBQUNKO0FBQ0k7QUFWUjtBQVlILFNBaEJEOztBQWtCQSxlQUFPVixFQUFFc0IsSUFBRixDQUNGQyxLQURFLENBQ0lqQyxTQURKLEVBQ2VxQixRQURmLEVBRUZpQixJQUZFLENBRUcsVUFBVTlCLE1BQVYsRUFBa0I7QUFDcEIsbUJBQU9FLEVBQUVFLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkosT0FBT3NCLEtBQVAsSUFBZ0IsRUFBbkMsRUFBdUN0QixPQUFPbUIsT0FBUCxJQUFrQixFQUF6RCxFQUE2RG5CLE9BQU8rQixNQUFQLElBQWlCLEVBQTlFLENBQVA7QUFDSCxTQUpFLEVBS0ZMLE9BTEUsRUFBUDtBQU1ILEtBN0JEO0FBK0JILENBbEpBLEVBa0pDeEMsSUFBSUMsSUFBSixDQUFTQyxPQWxKVixDQUFEIiwiZmlsZSI6InN0b3JhZ2UuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHN0b3JhZ2UuanMgMjAxNi0wMi0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qIGdsb2JhbHMgU3RvcmFnZSAqL1xuXG5qc2UubGlicy5zdG9yYWdlID0ganNlLmxpYnMuc3RvcmFnZSB8fCB7fTtcblxuLyoqXG4gKiAjIyBCcm93c2VyIFN0b3JhZ2UgQVBJIExpYnJhcnlcbiAqXG4gKiBUaGlzIGxpYnJhcnkgaGFuZGxlcyB0aGUgSFRNTCBzdG9yYWdlIGZ1bmN0aW9uYWxpdHkuIFlvdSBjYW4gZWl0aGVyIHN0b3JlIGluZm9ybWF0aW9uIGluIHRoZSBzZXNzaW9uIG9yIHRoZSBsb2NhbFxuICogc3RvcmFnZSBvZiB0aGUgYnJvd3Nlci5cbiAqXG4gKiBAZGVwcmVjYXRlZCBXaWxsIGJlIHJlbW92ZWQgd2l0aCBKUyBFbmdpbmUgdjEuNy5cbiAqXG4gKiBAbW9kdWxlIEpTRS9MaWJzL3N0b3JhZ2VcbiAqIEBleHBvcnRzIGpzZS5saWJzLnN0b3JhZ2VcbiAqIEBpZ25vcmVcbiAqL1xuKGZ1bmN0aW9uIChleHBvcnRzKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvKipcbiAgICAgKiBKYXZhU2NyaXB0IFN0b3JhZ2UgT2JqZWN0XG4gICAgICpcbiAgICAgKiBAdHlwZSB7Ym9vbGVhbn1cbiAgICAgKi9cbiAgICB2YXIgd2ViU3RvcmFnZSA9IChTdG9yYWdlICE9PSB1bmRlZmluZWQpID8gdHJ1ZSA6IGZhbHNlO1xuXG4gICAgLyoqXG4gICAgICogU3RvcmVzIGEgdmFsdWUgaW4gdGhlIGJyb3dzZXIgc3RvcmFnZS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBzdG9yZSBTdG9yYWdlIGhhbmRsZXIgb2JqZWN0LlxuICAgICAqIEBwYXJhbSB7Ym9vbGVhbn0gb3ZlcndyaXRlIFdoZXRoZXIgdG8gb3ZlcndyaXRlIGFuIGV4aXN0aW5nIHN0b3JhZ2UgdmFsdWUuXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IHZhbHVlIFN0cmluZyBkZWZpbmluZyB0aGUgdmFsdWUga2V5IG5hbWUgdG8gYmUgc3RvcmVkLlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBkYXRhc2V0IENvbnRhaW5zIHRoZSBpbmZvcm1hdGlvbiB0byBiZSBzdG9yZWQuXG4gICAgICogQHBhcmFtIHtudW1iZXJ9IHVzZXJJZCBVc2VyIGlkIHdpbGwgYmUgdXNlZCB0byBpZGVudGlmeSBzdG9yZWQgaW5mb3JtYXRpb24gb2YgYSBzcGVjaWZpYyB1c2VyLlxuICAgICAqXG4gICAgICogQHJldHVybiB7Ym9vbGVhbn0gUmV0dXJucyB0aGUgb3BlcmF0aW9uIHJlc3VsdC5cbiAgICAgKlxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgdmFyIF9zdG9yZSA9IGZ1bmN0aW9uIChzdG9yZSwgb3ZlcndyaXRlLCB2YWx1ZSwgZGF0YXNldCwgdXNlcklkKSB7XG5cbiAgICAgICAgdmFyIGRhdGFDYWNoZSA9IG51bGwsXG4gICAgICAgICAgICByZXN1bHQgPSBudWxsO1xuXG4gICAgICAgIGlmICh3ZWJTdG9yYWdlKSB7XG4gICAgICAgICAgICBkYXRhQ2FjaGUgPSBzdG9yZS5nZXRJdGVtKCd1c2VyXycgKyB1c2VySWQpO1xuICAgICAgICAgICAgZGF0YUNhY2hlID0gZGF0YUNhY2hlIHx8ICd7fSc7XG4gICAgICAgICAgICBkYXRhQ2FjaGUgPSAkLnBhcnNlSlNPTihkYXRhQ2FjaGUpO1xuXG4gICAgICAgICAgICBpZiAob3ZlcndyaXRlIHx8IGRhdGFDYWNoZVt2YWx1ZV0gPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgIGRhdGFDYWNoZVt2YWx1ZV0gPSBkYXRhc2V0O1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBkYXRhQ2FjaGVbdmFsdWVdID0gJC5leHRlbmQoe30sIGRhdGFDYWNoZVt2YWx1ZV0sIGRhdGFzZXQpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXN1bHQgPSBKU09OLnN0cmluZ2lmeShkYXRhQ2FjaGUpO1xuICAgICAgICAgICAgc3RvcmUuc2V0SXRlbSgndXNlcl8nICsgdXNlcklkLCByZXN1bHQpO1xuICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgIH1cbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBSZXN0b3JlcyBkYXRhIGZyb20gdGhlIGJyb3dzZXIgc3RvcmFnZS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBzdG9yZSBTdG9yYWdlIGhhbmRsZXIgb2JqZWN0LlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSB2YWx1ZSBWYWx1ZSBrZXkgbmFtZSB0byBiZSByZXRyaWV2ZWQuXG4gICAgICogQHBhcmFtIHtudW1iZXJ9IHVzZXJJZCBVc2VyIGlkIHRoYXQgb3ducyB0aGUgdmFsdWUuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgdGhlIHZhbHVlIGlmIGV4aXN0cyBvciBhbiBlbXB0eSBvYmplY3QgaWYgbm90LlxuICAgICAqXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICB2YXIgX3Jlc3RvcmUgPSBmdW5jdGlvbiAoc3RvcmUsIHZhbHVlLCB1c2VySWQpIHtcblxuICAgICAgICB2YXIgZGF0YUNhY2hlID0gbnVsbDtcblxuICAgICAgICBpZiAod2ViU3RvcmFnZSkge1xuICAgICAgICAgICAgZGF0YUNhY2hlID0gc3RvcmUuZ2V0SXRlbSgndXNlcl8nICsgdXNlcklkKTtcbiAgICAgICAgICAgIGRhdGFDYWNoZSA9IGRhdGFDYWNoZSB8fCAne30nO1xuICAgICAgICAgICAgZGF0YUNhY2hlID0gJC5wYXJzZUpTT04oZGF0YUNhY2hlKTtcbiAgICAgICAgICAgIHJldHVybiBkYXRhQ2FjaGVbdmFsdWVdIHx8IHt9O1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiB7fTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogU3RvcmVzIGRhdGEgaW4gdGhlIGJyb3dzZXIgc3RvcmFnZS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7YXJyYXl9IGRlc3RpbmF0aW9ucyBBcnJheSBjb250YWluaW5nIHdoZXJlIHRvIHN0b3JlIHRoZSBkYXRhIChzZXNzaW9uLCBsb2NhbCkuXG4gICAgICogQHBhcmFtIHtvYmplY3R9IGRhdGFzZXQgRGF0YSB0byBiZSBzdG9yZWQuXG4gICAgICogQHBhcmFtIHtib29sZWFufSBvdmVyd3JpdGUgV2hldGhlciB0byBvdmVyd3JpdGUgZXhpc3RpbmcgdmFsdWVzLlxuICAgICAqXG4gICAgICogQHJldHVybiB7b2JqZWN0fSBSZXR1cm5zIGEgcHJvbWlzZSBvYmplY3QuXG4gICAgICovXG4gICAgZXhwb3J0cy5zdG9yZSA9IGZ1bmN0aW9uIChkZXN0aW5hdGlvbnMsIGRhdGFzZXQsIG92ZXJ3cml0ZSkge1xuXG4gICAgICAgIHZhciB1c2VySUQgPSAkKCdib2R5JykuZGF0YSgpLnVzZXJJZCxcbiAgICAgICAgICAgIHJlc3VsdE9iamVjdCA9IHt9LFxuICAgICAgICAgICAgcHJvbWlzZXMgPSBbXTtcblxuICAgICAgICAkLmVhY2goZGVzdGluYXRpb25zLCBmdW5jdGlvbiAoZGVzdCwgdmFsdWUpIHtcbiAgICAgICAgICAgIHZhciBsb2NhbERlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuICAgICAgICAgICAgcHJvbWlzZXMucHVzaChsb2NhbERlZmVycmVkKTtcblxuICAgICAgICAgICAgc3dpdGNoIChkZXN0KSB7XG4gICAgICAgICAgICAgICAgY2FzZSAnc2Vzc2lvbic6XG4gICAgICAgICAgICAgICAgICAgIHJlc3VsdE9iamVjdC5zZXNzaW9uID0gX3N0b3JlKHNlc3Npb25TdG9yYWdlLCBvdmVyd3JpdGUsIHZhbHVlLCBkYXRhc2V0LCB1c2VySUQpO1xuICAgICAgICAgICAgICAgICAgICBsb2NhbERlZmVycmVkLnJlc29sdmUocmVzdWx0T2JqZWN0KTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSAnbG9jYWwnOlxuICAgICAgICAgICAgICAgICAgICByZXN1bHRPYmplY3QubG9jYWwgPSBfc3RvcmUobG9jYWxTdG9yYWdlLCBvdmVyd3JpdGUsIHZhbHVlLCBkYXRhc2V0LCB1c2VySUQpO1xuICAgICAgICAgICAgICAgICAgICBsb2NhbERlZmVycmVkLnJlc29sdmUocmVzdWx0T2JqZWN0KTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgZGVmYXVsdDpcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJldHVybiAkLndoZW4uYXBwbHkodW5kZWZpbmVkLCBwcm9taXNlcykucHJvbWlzZSgpO1xuXG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIFJlc3RvcmVzIGRhdGEgZnJvbSB0aGUgYnJvd3NlciBzdG9yYWdlLlxuICAgICAqXG4gICAgICogQHBhcmFtIHthcnJheX0gc291cmNlcyBEZWZpbmVzIHRoZSBzb3VyY2Ugb2YgdGhlIGRhdGEgdG8gYmUgcmV0cmlldmVkIChzZXNzaW9uLCBsb2NhbCkuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgYSBwcm9taXNlIG9iamVjdC5cbiAgICAgKi9cbiAgICBleHBvcnRzLnJlc3RvcmUgPSBmdW5jdGlvbiAoc291cmNlcykge1xuICAgICAgICB2YXIgdXNlcklEID0gJCgnYm9keScpLmRhdGEoKS51c2VySWQsXG4gICAgICAgICAgICByZXN1bHRPYmplY3QgPSB7fSxcbiAgICAgICAgICAgIHByb21pc2VzID0gW107XG5cbiAgICAgICAgJC5lYWNoKHNvdXJjZXMsIGZ1bmN0aW9uIChzcmMsIHZhbHVlKSB7XG4gICAgICAgICAgICB2YXIgbG9jYWxEZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcbiAgICAgICAgICAgIHByb21pc2VzLnB1c2gobG9jYWxEZWZlcnJlZCk7XG5cbiAgICAgICAgICAgIHN3aXRjaCAoc3JjKSB7XG4gICAgICAgICAgICAgICAgY2FzZSAnc2Vzc2lvbic6XG4gICAgICAgICAgICAgICAgICAgIHJlc3VsdE9iamVjdC5zZXNzaW9uID0gX3Jlc3RvcmUoc2Vzc2lvblN0b3JhZ2UsIHZhbHVlLCB1c2VySUQpO1xuICAgICAgICAgICAgICAgICAgICBsb2NhbERlZmVycmVkLnJlc29sdmUocmVzdWx0T2JqZWN0KTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSAnbG9jYWwnOlxuICAgICAgICAgICAgICAgICAgICByZXN1bHRPYmplY3QubG9jYWwgPSBfcmVzdG9yZShsb2NhbFN0b3JhZ2UsIHZhbHVlLCB1c2VySUQpO1xuICAgICAgICAgICAgICAgICAgICBsb2NhbERlZmVycmVkLnJlc29sdmUocmVzdWx0T2JqZWN0KTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgZGVmYXVsdDpcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJldHVybiAkLndoZW5cbiAgICAgICAgICAgIC5hcHBseSh1bmRlZmluZWQsIHByb21pc2VzKVxuICAgICAgICAgICAgLnRoZW4oZnVuY3Rpb24gKHJlc3VsdCkge1xuICAgICAgICAgICAgICAgIHJldHVybiAkLmV4dGVuZCh0cnVlLCB7fSwgcmVzdWx0LmxvY2FsIHx8IHt9LCByZXN1bHQuc2Vzc2lvbiB8fCB7fSwgcmVzdWx0LnNlcnZlciB8fCB7fSk7XG4gICAgICAgICAgICB9KVxuICAgICAgICAgICAgLnByb21pc2UoKTtcbiAgICB9O1xuXG59KGpzZS5saWJzLnN0b3JhZ2UpKTtcbiJdfQ==
