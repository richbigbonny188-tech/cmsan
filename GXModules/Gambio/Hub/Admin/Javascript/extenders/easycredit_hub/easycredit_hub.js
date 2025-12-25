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
(function() {
	'use strict';
	
	/**
	 * EasyCreditHub event callback functions.
	 *
	 * @type {Object}
	 */
	const callbacks = {
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
	const on = (event, callback) => {
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
	const trigger = (event, data) => {
		if (!callbacks[event]) {
			throw new Error('Invalid event type provided: ' + event);
		}
		
		callbacks[event].forEach(callback => callback(data));
	};
	
	/**
	 * Initializes the EasyCreditHub module.
	 *
	 * Call this method once the DOM is ready or the JSEngine has finished loading the modules.
	 *
	 * @private
	 */
	const init = () => {
		window.EasyCreditHub.trigger('ready', {});
	};
	
	// Export 
	window.EasyCreditHub = Object.assign({}, {on, trigger}, window.EasyCreditHub);
	
	// Initialize
	document.addEventListener('JSENGINE_INIT_FINISHED', () => init());
})();
