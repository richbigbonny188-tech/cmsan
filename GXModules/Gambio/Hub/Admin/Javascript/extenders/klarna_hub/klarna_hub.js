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
(function() {
	'use strict';
	
	/**
	 * KlarnaHub event callback functions.
	 *
	 * @type {Object}
	 */
	const callbacks = {
		ready: [],
		module: []
	};
	
	/**
	 * App URL.
	 *
	 * @type {String}
	 */
	let appUrl;
	
	/**
	 * Shop order number/ID.
	 *
	 * @type {String|null}
	 */
	let orderNumber;
	
	/**
	 * Gambio Hub module code.
	 *
	 * @type {String|null}
	 */
	let moduleCode;
	
	/**
	 * Register KlarnaHub event callback.
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
	 * Trigger KlarnaHub event callbacks.
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
	 * Get current script URL.
	 *
	 * @return {String} Returns the "src" attribute of the current script.
	 *
	 * @private
	 */
	const getScriptUrl = () => {
		const $script = $('script').filter(function(index, script) {
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
	const init = () => {
		const scriptUrl = getScriptUrl();
		appUrl = KlarnaHub.Lib.getUrlParameter('appUrl', scriptUrl);
		orderNumber = KlarnaHub.Lib.getUrlParameter('orderNumber', scriptUrl);
		moduleCode = KlarnaHub.Lib.getUrlParameter('moduleCode', scriptUrl);
		
		KlarnaHub.Api.getConfiguration(appUrl, orderNumber, moduleCode)
			.then(configuration => {
				window.KlarnaHub.Config = configuration;
				
				if (!configuration.clientKey) {
					return; // The client is not connected to Hub any more. 
				}
				
				trigger('ready', configuration);
			});
	};
	
	// Export 
	window.KlarnaHub = Object.assign({}, {on, trigger}, window.KlarnaHub);
	
	// Initialize
	document.addEventListener('JSENGINE_INIT_FINISHED', () => init());
})();