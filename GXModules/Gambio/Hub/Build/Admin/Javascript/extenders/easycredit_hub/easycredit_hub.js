'use strict';

/* --------------------------------------------------------------
 easycredit_hub.js 2020-06-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * EasyCreditHub Module
 *
 * @module EasyCreditHub
 */
(function () {
	'use strict';

	/**
  * EasyCreditHub event callback functions.
  *
  * @type {Object}
  */

	var callbacks = {
		ready: [],
		module: []
	};

	/**
  * Register EasyCreditHub event callback.
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
  * Trigger EasyCreditHub event callbacks.
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
  * Initializes the EasyCreditHub module.
  *
  * Call this method once the DOM is ready or the JSEngine has finished loading the modules.
  *
  * @private
  */
	var init = function init() {
		window.EasyCreditHub.trigger('ready', {});
	};

	// Export 
	window.EasyCreditHub = Object.assign({}, { on: on, trigger: trigger }, window.EasyCreditHub);

	// Initialize
	document.addEventListener('JSENGINE_INIT_FINISHED', function () {
		return init();
	});
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2Vhc3ljcmVkaXRfaHViL2Vhc3ljcmVkaXRfaHViLmpzIl0sIm5hbWVzIjpbImNhbGxiYWNrcyIsInJlYWR5IiwibW9kdWxlIiwib24iLCJldmVudCIsImNhbGxiYWNrIiwiRXJyb3IiLCJwdXNoIiwidHJpZ2dlciIsImRhdGEiLCJmb3JFYWNoIiwiaW5pdCIsIndpbmRvdyIsIkVhc3lDcmVkaXRIdWIiLCJPYmplY3QiLCJhc3NpZ24iLCJkb2N1bWVudCIsImFkZEV2ZW50TGlzdGVuZXIiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7QUFLQSxDQUFDLFlBQVc7QUFDWDs7QUFFQTs7Ozs7O0FBS0EsS0FBTUEsWUFBWTtBQUNqQkMsU0FBTyxFQURVO0FBRWpCQyxVQUFRO0FBRlMsRUFBbEI7O0FBS0E7Ozs7Ozs7O0FBUUEsS0FBTUMsS0FBSyxTQUFMQSxFQUFLLENBQUNDLEtBQUQsRUFBUUMsUUFBUixFQUFxQjtBQUMvQixNQUFJLENBQUNMLFVBQVVJLEtBQVYsQ0FBTCxFQUF1QjtBQUN0QixTQUFNLElBQUlFLEtBQUosQ0FBVSxrQ0FBa0NGLEtBQTVDLENBQU47QUFDQTs7QUFFREosWUFBVUksS0FBVixFQUFpQkcsSUFBakIsQ0FBc0JGLFFBQXRCO0FBQ0EsRUFORDs7QUFRQTs7Ozs7O0FBTUEsS0FBTUcsVUFBVSxTQUFWQSxPQUFVLENBQUNKLEtBQUQsRUFBUUssSUFBUixFQUFpQjtBQUNoQyxNQUFJLENBQUNULFVBQVVJLEtBQVYsQ0FBTCxFQUF1QjtBQUN0QixTQUFNLElBQUlFLEtBQUosQ0FBVSxrQ0FBa0NGLEtBQTVDLENBQU47QUFDQTs7QUFFREosWUFBVUksS0FBVixFQUFpQk0sT0FBakIsQ0FBeUI7QUFBQSxVQUFZTCxTQUFTSSxJQUFULENBQVo7QUFBQSxHQUF6QjtBQUNBLEVBTkQ7O0FBUUE7Ozs7Ozs7QUFPQSxLQUFNRSxPQUFPLFNBQVBBLElBQU8sR0FBTTtBQUNsQkMsU0FBT0MsYUFBUCxDQUFxQkwsT0FBckIsQ0FBNkIsT0FBN0IsRUFBc0MsRUFBdEM7QUFDQSxFQUZEOztBQUlBO0FBQ0FJLFFBQU9DLGFBQVAsR0FBdUJDLE9BQU9DLE1BQVAsQ0FBYyxFQUFkLEVBQWtCLEVBQUNaLE1BQUQsRUFBS0ssZ0JBQUwsRUFBbEIsRUFBaUNJLE9BQU9DLGFBQXhDLENBQXZCOztBQUVBO0FBQ0FHLFVBQVNDLGdCQUFULENBQTBCLHdCQUExQixFQUFvRDtBQUFBLFNBQU1OLE1BQU47QUFBQSxFQUFwRDtBQUNBLENBM0REIiwiZmlsZSI6IkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2Vhc3ljcmVkaXRfaHViL2Vhc3ljcmVkaXRfaHViLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBlYXN5Y3JlZGl0X2h1Yi5qcyAyMDIwLTA2LTE2XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAyMCBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBFYXN5Q3JlZGl0SHViIE1vZHVsZVxuICpcbiAqIEBtb2R1bGUgRWFzeUNyZWRpdEh1YlxuICovXG4oZnVuY3Rpb24oKSB7XG5cdCd1c2Ugc3RyaWN0Jztcblx0XG5cdC8qKlxuXHQgKiBFYXN5Q3JlZGl0SHViIGV2ZW50IGNhbGxiYWNrIGZ1bmN0aW9ucy5cblx0ICpcblx0ICogQHR5cGUge09iamVjdH1cblx0ICovXG5cdGNvbnN0IGNhbGxiYWNrcyA9IHtcblx0XHRyZWFkeTogW10sXG5cdFx0bW9kdWxlOiBbXVxuXHR9O1xuXHRcblx0LyoqXG5cdCAqIFJlZ2lzdGVyIEVhc3lDcmVkaXRIdWIgZXZlbnQgY2FsbGJhY2suXG5cdCAqXG5cdCAqIEBwYXJhbSB7U3RyaW5nfSBldmVudCBFdmVudCBuYW1lLlxuXHQgKiBAcGFyYW0ge0Z1bmN0aW9ufSBjYWxsYmFjayBFdmVudCBjYWxsYmFjayBmdW5jdGlvbi5cblx0ICpcblx0ICogQHB1YmxpY1xuXHQgKi9cblx0Y29uc3Qgb24gPSAoZXZlbnQsIGNhbGxiYWNrKSA9PiB7XG5cdFx0aWYgKCFjYWxsYmFja3NbZXZlbnRdKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ0ludmFsaWQgZXZlbnQgdHlwZSBwcm92aWRlZDogJyArIGV2ZW50KTtcblx0XHR9XG5cdFx0XG5cdFx0Y2FsbGJhY2tzW2V2ZW50XS5wdXNoKGNhbGxiYWNrKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBUcmlnZ2VyIEVhc3lDcmVkaXRIdWIgZXZlbnQgY2FsbGJhY2tzLlxuXHQgKlxuXHQgKiBAcGFyYW0ge1N0cmluZ30gZXZlbnQgRXZlbnQgbmFtZS5cblx0ICogQHBhcmFtIHtPYmplY3R9IGRhdGEgRXZlbnQgZGF0YS5cblx0ICovXG5cdGNvbnN0IHRyaWdnZXIgPSAoZXZlbnQsIGRhdGEpID0+IHtcblx0XHRpZiAoIWNhbGxiYWNrc1tldmVudF0pIHtcblx0XHRcdHRocm93IG5ldyBFcnJvcignSW52YWxpZCBldmVudCB0eXBlIHByb3ZpZGVkOiAnICsgZXZlbnQpO1xuXHRcdH1cblx0XHRcblx0XHRjYWxsYmFja3NbZXZlbnRdLmZvckVhY2goY2FsbGJhY2sgPT4gY2FsbGJhY2soZGF0YSkpO1xuXHR9O1xuXHRcblx0LyoqXG5cdCAqIEluaXRpYWxpemVzIHRoZSBFYXN5Q3JlZGl0SHViIG1vZHVsZS5cblx0ICpcblx0ICogQ2FsbCB0aGlzIG1ldGhvZCBvbmNlIHRoZSBET00gaXMgcmVhZHkgb3IgdGhlIEpTRW5naW5lIGhhcyBmaW5pc2hlZCBsb2FkaW5nIHRoZSBtb2R1bGVzLlxuXHQgKlxuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0Y29uc3QgaW5pdCA9ICgpID0+IHtcblx0XHR3aW5kb3cuRWFzeUNyZWRpdEh1Yi50cmlnZ2VyKCdyZWFkeScsIHt9KTtcblx0fTtcblx0XG5cdC8vIEV4cG9ydCBcblx0d2luZG93LkVhc3lDcmVkaXRIdWIgPSBPYmplY3QuYXNzaWduKHt9LCB7b24sIHRyaWdnZXJ9LCB3aW5kb3cuRWFzeUNyZWRpdEh1Yik7XG5cdFxuXHQvLyBJbml0aWFsaXplXG5cdGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0pTRU5HSU5FX0lOSVRfRklOSVNIRUQnLCAoKSA9PiBpbml0KCkpO1xufSkoKTtcbiJdfQ==
