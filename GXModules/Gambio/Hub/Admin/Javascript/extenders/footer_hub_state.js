/* --------------------------------------------------------------
 footer_hub_state.js 2018-04-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Footer Hub State Extender
 *
 * This module creates a Gambio Hub state badge in the admin footer in order to display the connection state of
 * Hub.
 *
 * Add a "data-connected" attribute on the <script/> tag if there's an active connection with Gambio Hub.
 *
 * Add a "data-lecacy-mode" attribute on the <script/> tag to enable compatibility with older shop pages.
 *
 * Add a "data-text" attribute on the <script/> tag to change the displayed text.
 *
 * Example:
 *
 * <script src="http://shop.de/admin/assets/html/javascript/modules/gambio_hub/extenders/footer_hub_state.js"
 *     data-connected data-legacy-mode data-text="HUB Connected"></script>
 */
(function() {
	
	'use strict';
	
	/**
	 * Parse options from script's data attributes.
	 *
	 * @param {HTMLElement} script Element that loaded current script.
	 *
	 * @return {{isLegacy: boolean, isConnected: boolean, text: (string|*)}}
	 */
	function parseDataOptions(script) {
		return {
			isLegacy: script.getAttribute('data-legacy-mode') !== null,
			isConnected: script.getAttribute('data-connected') !== null,
			text: script.getAttribute('data-text')
		}
	}
	
	/**
	 * Creates Gambio Hub footer badge for legacy pages.
	 *
	 * @param {Object} options Contains the extender options.
	 *
	 * @return {HTMLElement} Returns the badge element.
	 */
	function createLegacyBadge(options) {
		const style = [
			'padding: 6px',
			'margin-top: -4px',
			'margin-left: 42px',
			'font-size: 11px',
			'color: #fff',
			'text-decoration: none'
		];
		
		const badge = document.createElement('a');
		badge.setAttribute('href', 'admin.php?do=HubConfiguration/account');
		badge.setAttribute('style', style.join('; '));
		badge.className = 'badge ' + (options.isConnected ? 'connected badge-success' : 'disconnected badge-danger');
		
		const icon = document.createElement('i');
		icon.className = 'fa fa-share-alt-square';
		badge.appendChild(icon);
		
		const text = document.createTextNode(' ' + options.text);
		badge.appendChild(text);
		
		const targetContainer = document.querySelector('.main-bottom-footer .shop-key-link').parentNode;
		targetContainer.style.marginRight = '10px';
		targetContainer.appendChild(badge);
		
		return badge;
	}
	
	/**
	 * Creates Gambio Hub footer badge for modern pages.
	 *
	 * @param {Object} options Contains the extender options.
	 *
	 * @return {HTMLElement} Returns the badge element.
	 */
	function createModernBadge(options) {
		const style = [
			'padding: 6px',
			'margin-top: -4px',
			'font-size: 11px',
		];
		
		const badgeContainer = document.createElement('div');
		badgeContainer.setAttribute('style', 'min-width: 150px; display: inline-block; margin-left: 24px;');
		badgeContainer.className = 'hub-connection-state';
		
		const badge = document.createElement('a');
		badge.setAttribute('href', 'admin.php?do=HubConfiguration/account');
		badge.setAttribute('style', style.join('; '));
		badge.className = 'label ' + (options.isConnected ? 'connected label-success' : 'disconnected label-danger');
		
		const icon = document.createElement('i');
		icon.className = 'fa fa-share-alt-square';
		icon.style.marginLeft = '0';
		badge.appendChild(icon);
		
		const text = document.createTextNode(' ' + options.text);
		badge.appendChild(text);
		
		badgeContainer.appendChild(badge);
		
		let targetContainer = document.querySelector('#main-footer .info .version');
		targetContainer.appendChild(badgeContainer);
		
		const languageSelection = document.querySelector('#main-footer .info .language-selection');
		languageSelection.style.marginLeft = '30px';
		
		return badgeContainer;
	}
	
	/**
	 * Creates Gambio Hub Badge
	 *
	 * @param {Object} options Contains the extender options.
	 *
	 * @return {HTMLElement} Returns the badge element.
	 */
	function createBadge(options) {
		return options.isLegacy ? createLegacyBadge(options) : createModernBadge(options);
	}
	
	
	/**
	 * Initializes Gambio Hub footer badge extender.
	 *
	 * This method will create a badge in the admin footer section which indicates the Gambio Hub connection state
	 * of the shop.
	 */
	function initialize(script) {
		const options = parseDataOptions(script);
		
		createBadge(options);
	}
	
	const script = document.currentScript;
	
	if (document.readyState != 'loading') {
		initialize(script);
	} else {
		document.addEventListener('DOMContentLoaded', () => initialize(script));
	}
	
})();
