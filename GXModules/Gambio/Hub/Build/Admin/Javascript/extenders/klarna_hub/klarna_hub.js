'use strict';

/* --------------------------------------------------------------
 klarna_hub.js 2018-10-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * KlarnaHub Module
 *
 * Important:
 *
 * This script must explicitly be called with the "appUrl" GET parameter and optionally with the "orderNumber" and
 * "moduleCode" GET parameters.
 *
 * Example:
 *
 * http://shop.de/../../klarna_hub.js?appUrl=shop.de/&orderNumber=10&moduleCode=KlarnaPaynowHub
 *
 * Events:
 *
 * - ready Called whenever the document and KlarnaHub configuration are loaded.
 *
 * @module KlarnaHub
 */
(function () {
	'use strict';

	/**
  * KlarnaHub event callback functions.
  *
  * @type {Object}
  */

	var callbacks = {
		ready: [],
		module: []
	};

	/**
  * App URL.
  *
  * @type {String}
  */
	var appUrl = void 0;

	/**
  * Shop order number/ID.
  *
  * @type {String|null}
  */
	var orderNumber = void 0;

	/**
  * Gambio Hub module code.
  *
  * @type {String|null}
  */
	var moduleCode = void 0;

	/**
  * Register KlarnaHub event callback.
  *
  * @param {String} event Event name.
  * @param {Function} callback Event callback function.
  *
  * @public
  */
	var on = function on(event, callback) {
		if (!callbacks[event]) {
			throw new Error('Invalid event type provided: ' + event);
		}

		callbacks[event].push(callback);
	};

	/**
  * Trigger KlarnaHub event callbacks.
  *
  * @param {String} event Event name.
  * @param {Object} data Event data.
  */
	var trigger = function trigger(event, data) {
		if (!callbacks[event]) {
			throw new Error('Invalid event type provided: ' + event);
		}

		callbacks[event].forEach(function (callback) {
			return callback(data);
		});
	};

	/**
  * Get current script URL.
  *
  * @return {String} Returns the "src" attribute of the current script.
  *
  * @private
  */
	var getScriptUrl = function getScriptUrl() {
		var $script = $('script').filter(function (index, script) {
			return script.src.includes('appUrl');
		});

		return $script.attr('src');
	};

	/**
  * Initializes the KlarnaHub module.
  *
  * Call this method once the DOM is ready or the JSEngine has finished loading the modules.
  *
  * @private
  */
	var init = function init() {
		var scriptUrl = getScriptUrl();
		appUrl = KlarnaHub.Lib.getUrlParameter('appUrl', scriptUrl);
		orderNumber = KlarnaHub.Lib.getUrlParameter('orderNumber', scriptUrl);
		moduleCode = KlarnaHub.Lib.getUrlParameter('moduleCode', scriptUrl);

		KlarnaHub.Api.getConfiguration(appUrl, orderNumber, moduleCode).then(function (configuration) {
			window.KlarnaHub.Config = configuration;

			if (!configuration.clientKey) {
				return; // The client is not connected to Hub any more. 
			}

			trigger('ready', configuration);
		});
	};

	// Export 
	window.KlarnaHub = Object.assign({}, { on: on, trigger: trigger }, window.KlarnaHub);

	// Initialize
	document.addEventListener('JSENGINE_INIT_FINISHED', function () {
		return init();
	});
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2tsYXJuYV9odWIva2xhcm5hX2h1Yi5qcyJdLCJuYW1lcyI6WyJjYWxsYmFja3MiLCJyZWFkeSIsIm1vZHVsZSIsImFwcFVybCIsIm9yZGVyTnVtYmVyIiwibW9kdWxlQ29kZSIsIm9uIiwiZXZlbnQiLCJjYWxsYmFjayIsIkVycm9yIiwicHVzaCIsInRyaWdnZXIiLCJkYXRhIiwiZm9yRWFjaCIsImdldFNjcmlwdFVybCIsIiRzY3JpcHQiLCIkIiwiZmlsdGVyIiwiaW5kZXgiLCJzY3JpcHQiLCJzcmMiLCJpbmNsdWRlcyIsImF0dHIiLCJpbml0Iiwic2NyaXB0VXJsIiwiS2xhcm5hSHViIiwiTGliIiwiZ2V0VXJsUGFyYW1ldGVyIiwiQXBpIiwiZ2V0Q29uZmlndXJhdGlvbiIsInRoZW4iLCJ3aW5kb3ciLCJDb25maWciLCJjb25maWd1cmF0aW9uIiwiY2xpZW50S2V5IiwiT2JqZWN0IiwiYXNzaWduIiwiZG9jdW1lbnQiLCJhZGRFdmVudExpc3RlbmVyIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQWtCQSxDQUFDLFlBQVc7QUFDWDs7QUFFQTs7Ozs7O0FBS0EsS0FBTUEsWUFBWTtBQUNqQkMsU0FBTyxFQURVO0FBRWpCQyxVQUFRO0FBRlMsRUFBbEI7O0FBS0E7Ozs7O0FBS0EsS0FBSUMsZUFBSjs7QUFFQTs7Ozs7QUFLQSxLQUFJQyxvQkFBSjs7QUFFQTs7Ozs7QUFLQSxLQUFJQyxtQkFBSjs7QUFFQTs7Ozs7Ozs7QUFRQSxLQUFNQyxLQUFLLFNBQUxBLEVBQUssQ0FBQ0MsS0FBRCxFQUFRQyxRQUFSLEVBQXFCO0FBQy9CLE1BQUksQ0FBQ1IsVUFBVU8sS0FBVixDQUFMLEVBQXVCO0FBQ3RCLFNBQU0sSUFBSUUsS0FBSixDQUFVLGtDQUFrQ0YsS0FBNUMsQ0FBTjtBQUNBOztBQUVEUCxZQUFVTyxLQUFWLEVBQWlCRyxJQUFqQixDQUFzQkYsUUFBdEI7QUFDQSxFQU5EOztBQVFBOzs7Ozs7QUFNQSxLQUFNRyxVQUFVLFNBQVZBLE9BQVUsQ0FBQ0osS0FBRCxFQUFRSyxJQUFSLEVBQWlCO0FBQ2hDLE1BQUksQ0FBQ1osVUFBVU8sS0FBVixDQUFMLEVBQXVCO0FBQ3RCLFNBQU0sSUFBSUUsS0FBSixDQUFVLGtDQUFrQ0YsS0FBNUMsQ0FBTjtBQUNBOztBQUVEUCxZQUFVTyxLQUFWLEVBQWlCTSxPQUFqQixDQUF5QjtBQUFBLFVBQVlMLFNBQVNJLElBQVQsQ0FBWjtBQUFBLEdBQXpCO0FBQ0EsRUFORDs7QUFRQTs7Ozs7OztBQU9BLEtBQU1FLGVBQWUsU0FBZkEsWUFBZSxHQUFNO0FBQzFCLE1BQU1DLFVBQVVDLEVBQUUsUUFBRixFQUFZQyxNQUFaLENBQW1CLFVBQVNDLEtBQVQsRUFBZ0JDLE1BQWhCLEVBQXdCO0FBQzFELFVBQU9BLE9BQU9DLEdBQVAsQ0FBV0MsUUFBWCxDQUFvQixRQUFwQixDQUFQO0FBQ0EsR0FGZSxDQUFoQjs7QUFJQSxTQUFPTixRQUFRTyxJQUFSLENBQWEsS0FBYixDQUFQO0FBQ0EsRUFORDs7QUFRQTs7Ozs7OztBQU9BLEtBQU1DLE9BQU8sU0FBUEEsSUFBTyxHQUFNO0FBQ2xCLE1BQU1DLFlBQVlWLGNBQWxCO0FBQ0FYLFdBQVNzQixVQUFVQyxHQUFWLENBQWNDLGVBQWQsQ0FBOEIsUUFBOUIsRUFBd0NILFNBQXhDLENBQVQ7QUFDQXBCLGdCQUFjcUIsVUFBVUMsR0FBVixDQUFjQyxlQUFkLENBQThCLGFBQTlCLEVBQTZDSCxTQUE3QyxDQUFkO0FBQ0FuQixlQUFhb0IsVUFBVUMsR0FBVixDQUFjQyxlQUFkLENBQThCLFlBQTlCLEVBQTRDSCxTQUE1QyxDQUFiOztBQUVBQyxZQUFVRyxHQUFWLENBQWNDLGdCQUFkLENBQStCMUIsTUFBL0IsRUFBdUNDLFdBQXZDLEVBQW9EQyxVQUFwRCxFQUNFeUIsSUFERixDQUNPLHlCQUFpQjtBQUN0QkMsVUFBT04sU0FBUCxDQUFpQk8sTUFBakIsR0FBMEJDLGFBQTFCOztBQUVBLE9BQUksQ0FBQ0EsY0FBY0MsU0FBbkIsRUFBOEI7QUFDN0IsV0FENkIsQ0FDckI7QUFDUjs7QUFFRHZCLFdBQVEsT0FBUixFQUFpQnNCLGFBQWpCO0FBQ0EsR0FURjtBQVVBLEVBaEJEOztBQWtCQTtBQUNBRixRQUFPTixTQUFQLEdBQW1CVSxPQUFPQyxNQUFQLENBQWMsRUFBZCxFQUFrQixFQUFDOUIsTUFBRCxFQUFLSyxnQkFBTCxFQUFsQixFQUFpQ29CLE9BQU9OLFNBQXhDLENBQW5COztBQUVBO0FBQ0FZLFVBQVNDLGdCQUFULENBQTBCLHdCQUExQixFQUFvRDtBQUFBLFNBQU1mLE1BQU47QUFBQSxFQUFwRDtBQUNBLENBN0dEIiwiZmlsZSI6IkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2tsYXJuYV9odWIva2xhcm5hX2h1Yi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4ga2xhcm5hX2h1Yi5qcyAyMDE4LTEwLTI2XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxOCBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBLbGFybmFIdWIgTW9kdWxlXG4gKlxuICogSW1wb3J0YW50OlxuICpcbiAqIFRoaXMgc2NyaXB0IG11c3QgZXhwbGljaXRseSBiZSBjYWxsZWQgd2l0aCB0aGUgXCJhcHBVcmxcIiBHRVQgcGFyYW1ldGVyIGFuZCBvcHRpb25hbGx5IHdpdGggdGhlIFwib3JkZXJOdW1iZXJcIiBhbmRcbiAqIFwibW9kdWxlQ29kZVwiIEdFVCBwYXJhbWV0ZXJzLlxuICpcbiAqIEV4YW1wbGU6XG4gKlxuICogaHR0cDovL3Nob3AuZGUvLi4vLi4va2xhcm5hX2h1Yi5qcz9hcHBVcmw9c2hvcC5kZS8mb3JkZXJOdW1iZXI9MTAmbW9kdWxlQ29kZT1LbGFybmFQYXlub3dIdWJcbiAqXG4gKiBFdmVudHM6XG4gKlxuICogLSByZWFkeSBDYWxsZWQgd2hlbmV2ZXIgdGhlIGRvY3VtZW50IGFuZCBLbGFybmFIdWIgY29uZmlndXJhdGlvbiBhcmUgbG9hZGVkLlxuICpcbiAqIEBtb2R1bGUgS2xhcm5hSHViXG4gKi9cbihmdW5jdGlvbigpIHtcblx0J3VzZSBzdHJpY3QnO1xuXHRcblx0LyoqXG5cdCAqIEtsYXJuYUh1YiBldmVudCBjYWxsYmFjayBmdW5jdGlvbnMuXG5cdCAqXG5cdCAqIEB0eXBlIHtPYmplY3R9XG5cdCAqL1xuXHRjb25zdCBjYWxsYmFja3MgPSB7XG5cdFx0cmVhZHk6IFtdLFxuXHRcdG1vZHVsZTogW11cblx0fTtcblx0XG5cdC8qKlxuXHQgKiBBcHAgVVJMLlxuXHQgKlxuXHQgKiBAdHlwZSB7U3RyaW5nfVxuXHQgKi9cblx0bGV0IGFwcFVybDtcblx0XG5cdC8qKlxuXHQgKiBTaG9wIG9yZGVyIG51bWJlci9JRC5cblx0ICpcblx0ICogQHR5cGUge1N0cmluZ3xudWxsfVxuXHQgKi9cblx0bGV0IG9yZGVyTnVtYmVyO1xuXHRcblx0LyoqXG5cdCAqIEdhbWJpbyBIdWIgbW9kdWxlIGNvZGUuXG5cdCAqXG5cdCAqIEB0eXBlIHtTdHJpbmd8bnVsbH1cblx0ICovXG5cdGxldCBtb2R1bGVDb2RlO1xuXHRcblx0LyoqXG5cdCAqIFJlZ2lzdGVyIEtsYXJuYUh1YiBldmVudCBjYWxsYmFjay5cblx0ICpcblx0ICogQHBhcmFtIHtTdHJpbmd9IGV2ZW50IEV2ZW50IG5hbWUuXG5cdCAqIEBwYXJhbSB7RnVuY3Rpb259IGNhbGxiYWNrIEV2ZW50IGNhbGxiYWNrIGZ1bmN0aW9uLlxuXHQgKlxuXHQgKiBAcHVibGljXG5cdCAqL1xuXHRjb25zdCBvbiA9IChldmVudCwgY2FsbGJhY2spID0+IHtcblx0XHRpZiAoIWNhbGxiYWNrc1tldmVudF0pIHtcblx0XHRcdHRocm93IG5ldyBFcnJvcignSW52YWxpZCBldmVudCB0eXBlIHByb3ZpZGVkOiAnICsgZXZlbnQpO1xuXHRcdH1cblx0XHRcblx0XHRjYWxsYmFja3NbZXZlbnRdLnB1c2goY2FsbGJhY2spO1xuXHR9O1xuXHRcblx0LyoqXG5cdCAqIFRyaWdnZXIgS2xhcm5hSHViIGV2ZW50IGNhbGxiYWNrcy5cblx0ICpcblx0ICogQHBhcmFtIHtTdHJpbmd9IGV2ZW50IEV2ZW50IG5hbWUuXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIEV2ZW50IGRhdGEuXG5cdCAqL1xuXHRjb25zdCB0cmlnZ2VyID0gKGV2ZW50LCBkYXRhKSA9PiB7XG5cdFx0aWYgKCFjYWxsYmFja3NbZXZlbnRdKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ0ludmFsaWQgZXZlbnQgdHlwZSBwcm92aWRlZDogJyArIGV2ZW50KTtcblx0XHR9XG5cdFx0XG5cdFx0Y2FsbGJhY2tzW2V2ZW50XS5mb3JFYWNoKGNhbGxiYWNrID0+IGNhbGxiYWNrKGRhdGEpKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBHZXQgY3VycmVudCBzY3JpcHQgVVJMLlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtTdHJpbmd9IFJldHVybnMgdGhlIFwic3JjXCIgYXR0cmlidXRlIG9mIHRoZSBjdXJyZW50IHNjcmlwdC5cblx0ICpcblx0ICogQHByaXZhdGVcblx0ICovXG5cdGNvbnN0IGdldFNjcmlwdFVybCA9ICgpID0+IHtcblx0XHRjb25zdCAkc2NyaXB0ID0gJCgnc2NyaXB0JykuZmlsdGVyKGZ1bmN0aW9uKGluZGV4LCBzY3JpcHQpIHtcblx0XHRcdHJldHVybiBzY3JpcHQuc3JjLmluY2x1ZGVzKCdhcHBVcmwnKTtcblx0XHR9KTtcblx0XHRcblx0XHRyZXR1cm4gJHNjcmlwdC5hdHRyKCdzcmMnKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBJbml0aWFsaXplcyB0aGUgS2xhcm5hSHViIG1vZHVsZS5cblx0ICpcblx0ICogQ2FsbCB0aGlzIG1ldGhvZCBvbmNlIHRoZSBET00gaXMgcmVhZHkgb3IgdGhlIEpTRW5naW5lIGhhcyBmaW5pc2hlZCBsb2FkaW5nIHRoZSBtb2R1bGVzLlxuXHQgKlxuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0Y29uc3QgaW5pdCA9ICgpID0+IHtcblx0XHRjb25zdCBzY3JpcHRVcmwgPSBnZXRTY3JpcHRVcmwoKTtcblx0XHRhcHBVcmwgPSBLbGFybmFIdWIuTGliLmdldFVybFBhcmFtZXRlcignYXBwVXJsJywgc2NyaXB0VXJsKTtcblx0XHRvcmRlck51bWJlciA9IEtsYXJuYUh1Yi5MaWIuZ2V0VXJsUGFyYW1ldGVyKCdvcmRlck51bWJlcicsIHNjcmlwdFVybCk7XG5cdFx0bW9kdWxlQ29kZSA9IEtsYXJuYUh1Yi5MaWIuZ2V0VXJsUGFyYW1ldGVyKCdtb2R1bGVDb2RlJywgc2NyaXB0VXJsKTtcblx0XHRcblx0XHRLbGFybmFIdWIuQXBpLmdldENvbmZpZ3VyYXRpb24oYXBwVXJsLCBvcmRlck51bWJlciwgbW9kdWxlQ29kZSlcblx0XHRcdC50aGVuKGNvbmZpZ3VyYXRpb24gPT4ge1xuXHRcdFx0XHR3aW5kb3cuS2xhcm5hSHViLkNvbmZpZyA9IGNvbmZpZ3VyYXRpb247XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAoIWNvbmZpZ3VyYXRpb24uY2xpZW50S2V5KSB7XG5cdFx0XHRcdFx0cmV0dXJuOyAvLyBUaGUgY2xpZW50IGlzIG5vdCBjb25uZWN0ZWQgdG8gSHViIGFueSBtb3JlLiBcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0dHJpZ2dlcigncmVhZHknLCBjb25maWd1cmF0aW9uKTtcblx0XHRcdH0pO1xuXHR9O1xuXHRcblx0Ly8gRXhwb3J0IFxuXHR3aW5kb3cuS2xhcm5hSHViID0gT2JqZWN0LmFzc2lnbih7fSwge29uLCB0cmlnZ2VyfSwgd2luZG93LktsYXJuYUh1Yik7XG5cdFxuXHQvLyBJbml0aWFsaXplXG5cdGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0pTRU5HSU5FX0lOSVRfRklOSVNIRUQnLCAoKSA9PiBpbml0KCkpO1xufSkoKTsiXX0=
