/* --------------------------------------------------------------
   EasyCreditLoader.js 2020-04-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

(function() {
	if (window.NodeList && !NodeList.prototype.forEach) {
		NodeList.prototype.forEach = Array.prototype.forEach;
	}
	
	let currentScript = document.currentScript;

	let pluginLoaded = function() {
		let placeholderElement = document.querySelector('div.easycredit-rr-container');
		if(!placeholderElement) {
			return;
		}
		
		let styleSrc = 'https://ratenkauf.easycredit.de/ratenkauf/js/ratenrechner/v1/ratenrechner.css';
		let pluginStyle = document.createElement('link');
		pluginStyle.rel = 'stylesheet';
		pluginStyle.type = 'text/css';
		pluginStyle.href = styleSrc;
		if(currentScript) {
			currentScript.parentNode.insertBefore(pluginStyle, currentScript);
		} else {
			document.querySelector('script:last-of-type').parentNode.appendChild(pluginStyle);
		}
		
		rkPlugin.anzeige(placeholderElement.id, {
			webshopId: easyCreditParameters.shopId,
			finanzierungsbetrag: easyCreditParameters.finanzierungsbetrag,
			euro: easyCreditParameters.euro,
			textVariante: easyCreditParameters.textVariante,
			linkText: easyCreditParameters.linkText,
			fromText: easyCreditParameters.fromText,
			installmentTemplate: easyCreditParameters.installmentTemplate,
			widgetTemplate: easyCreditParameters.widgetTemplate
		});
	}
	
	let readyCallback = function() {
		if (typeof(window.rkPlugin) === 'undefined') {
			let pluginSrc = 'https://ratenkauf.easycredit.de/ratenkauf/js/ratenrechner/v1/ratenrechner.js';
			let pluginScript = document.createElement('script');
			pluginScript.src = pluginSrc;
			pluginScript.onload = pluginLoaded;
			
			if(currentScript) {
				currentScript.parentNode.insertBefore(pluginScript, currentScript);
			} else {
				document.querySelector('script:last-of-type').parentNode.appendChild(pluginScript);
			}
		}
	};
	
	if(document.readyState === 'complete' || (document.readyState !== 'loading' && !document.documentElement.doScroll)) {
		readyCallback();
	} else {
		document.addEventListener('DOMContentLoaded', readyCallback);
	}
}());
