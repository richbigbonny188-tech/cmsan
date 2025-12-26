/* --------------------------------------------------------------
 account.js 2018-04-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Gambio Hub Account Configuration Controller
 *
 * This module transfers the shop data to the hub account registration/edit form and handles the incoming messages
 * sent via window.postMessage() from the hub.
 *
 * Add the "data-target" attribute to the #account-iframe-container element to set the iframe source URL.
 *
 * Add the "data-autostart" attribute to the #account-iframe-container element to start with  the initialization
 * once the DOM is loaded. Use the "hub:account:start" event to start the initialization manually.
 *
 * Add the "data-legacy-mode" attribute to the #account-iframe-container to enable compatibility with older shop pages.
 */
(function() {
	
	'use strict';
	
	/**
	 * Elements
	 *
	 * @type {Object}
	 */
	const elements = {
		container: document.querySelector('#account-iframe-container'),
		iframe: null
	};
	
	/**
	 * "postMessage" message types
	 *
	 * @type {Object}
	 */
	const messageTypes = {
		fromHub: {
			created: 'hub:account:created',
			updated: 'hub:account:updated',
			disconnected: 'hub:account:disconnected',
			payment_modules_redirect: 'hub:account:payment_modules:redirect'
		},
		toHub: {
			shopData: 'shop:transfer:shopData'
		},
	};
	
	/**
	 * URIs
	 *
	 * @type {Object}
	 */
	const uris = {
		translations: null,
		shopData: null,
	};
	
	/**
	 * Fetched Contents
	 *
	 * @type {Object}
	 */
	const contents = {
		translations: null,
		shopData: null
	};
	
	/**
	 * Is the browser an old Internet Explorer.
	 *
	 * @type {Boolean}
	 */
	const isOldInternetExplorer = (navigator.userAgent.indexOf('MSIE') !== -1);
	
	/**
	 * Is this an old shop version?
	 *
	 * @type {Boolean}
	 */
	let isLegacyShop;
	
	/**
	 * Handles incoming messages.
	 *
	 * @param {MessageEvent} event Triggered event.
	 */
	function onMessage(event) {
		if (!event.data.type) {
			return;
		}
		
		switch (event.data.type) {
			case messageTypes.fromHub.disconnected:
				$.ajax({
					type: 'POST',
					url: 'admin.php?do=HubConfigurationAjax/deleteClientKey'
				}).done(function(data) {
					setTimeout(() => window.location.reload(), 1500);
				});
				break;
			case messageTypes.fromHub.created:
			case messageTypes.fromHub.updated:
				setTimeout(() => window.location.reload(), 1500);
				break;
			case messageTypes.fromHub.payment_modules_redirect:
				setTimeout(() => {
					window.location.href = jse.core.config.get('appUrl')
						+ '/admin/admin.php?do=HubConfiguration/PaymentMethods&action=install:' + event.data.getParam
				}, 1500);
				break;
		}
	}
	
	/**
	 * Shows a message modal using jQuery UI or native alerts.
	 *
	 * @param {String} title The modal dialog title.
	 * @param {String} content The modal dialog content.
	 *
	 * {@link http://api.jqueryui.com/1.10/dialog/}
	 */
	function showMessage(title, content) {
		const hasJqueryUi = (window.$ && window.$.ui);
		
		if (hasJqueryUi) {
			const $modal = $('<div/>', {class: 'gambio-hub-modal gx-container', html: content});
			
			$modal.dialog({
				title,
				buttons: [{text: 'OK', click: () => $modal.dialog('close')}],
				close: () => $modal.remove()
			});
			
			$modal.parents('.ui-dialog').addClass('gx-container');
			
			const $closeIcon = $modal.parents('.ui-dialog').find('.ui-icon.ui-icon-closethick');
			
			if ($closeIcon.length) {
				$closeIcon.remove();
			}
		} else {
			alert(`${title} - ${content}`);
		}
	}
	
	/**
	 * Fetches the translations and saves the translations response for later reference.
	 *
	 * @return {Promise}
	 */
	function fetchTranslations() {
		const options = {credentials: 'include'};
		const request = new Request(uris.translations, options);
		
		return window.fetch(request)
			.then(response => response.json())
			.then(parsed => contents.translations = parsed)
			.catch(() => showMessage('Error', 'The page translations couldn\'t be loaded.'));
	}
	
	/**
	 * Fetches the shop data and saves the shop data response for later reference.
	 *
	 * @return {Promise}
	 */
	function fetchShopData() {
		const options = {credentials: 'include'};
		const request = new Request(uris.shopData, options);
		
		return window.fetch(request)
			.then(response => response.json())
			.then(parsed => contents.shopData = parsed)
			.catch(() => showMessage(contents.translations.ERROR, contents.translations.GET_SHOP_DATA_ERROR));
	}
	
	/**
	 * Sends the shop data or the hub client key to the iframe.
	 */
	function sendShopDataToIframe() {
		const message = {
			type: messageTypes.toHub.shopData,
			data: contents.shopData,
		};
		
		elements.iframe.contentWindow.postMessage(message, '*');
	}
	
	/**
	 * Handles the iframe's load event.
	 */
	function onIframeLoad() {
		window.iFrameResize({
			heightCalculationMethod: 'grow'
		}, '#account-iframe');
		
		fetchTranslations()
			.then(fetchShopData)
			.then(sendShopDataToIframe);
	}
	
	/**
	 * Handles the module initialization.
	 */
	function onInit() {
		isLegacyShop = elements.container.getAttribute('data-legacy-mode') !== null;
		
		uris.translations = isLegacyShop ?
		                    'request_port.php?module=HubConfiguration&action=get_translations' :
		                    'admin.php?do=HubConfigurationAjax/GetTranslations';
		
		uris.shopData = isLegacyShop ?
		                'request_port.php?module=HubConfiguration&action=get_shop_data' :
		                'admin.php?do=HubConfigurationAjax/GetShopData';
		
		elements.iframe = document.createElement('iframe');
		elements.iframe.setAttribute('src', elements.container.getAttribute('data-target'));
		elements.iframe.setAttribute('id', 'account-iframe');
		elements.iframe.classList.add('hub-iframe');
		elements.iframe.addEventListener('load', onIframeLoad);
		elements.container.appendChild(elements.iframe);
		elements.container.removeAttribute('data-target');
		elements.container.removeAttribute('data-autostart');
		elements.container.removeAttribute('data-legacy-mode');
	}
	
	const initEvent = elements.container.getAttribute('data-autostart') !== null
		? 'DOMContentLoaded' : 'hub:account:start';
	window.addEventListener(initEvent, onInit);
	window.addEventListener('message', onMessage);
})();
