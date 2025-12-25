/* --------------------------------------------------------------
 payment_methods.js 2018-04-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Release under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Gambio Hub Payment Methods Configuration Controller
 *
 * This module transfers the shop data to the hub payment controller and handles the incoming messages sent via
 * window.postMessage() from the Hub.
 *
 * Add the "data-target" attribute to the #payment-methods-iframe-container element to set the iframe source URL.
 *
 * Add the "data-autostart" attribute to the #payment-methods-iframe-container element to start with  the initialization
 * once the DOM is loaded. Use the "hub:payment_methods:start" event to start the initialization manually.
 *
 * Add the "data-legacy-mode" attribute to the #payment-methods-iframe-container to enable compatibility with older
 * shop pages.
 */
(function() {
	
	'use strict';
	
	/**
	 * Elements
	 *
	 * @type {Object}
	 */
	const elements = {
		container: document.querySelector('#payment-methods-iframe-container'),
		iframe: null, // Dynamically generated 
		accountContainer: document.querySelector('#account-iframe-container'),
		accountIframe: null, // Dynamically generated
		accountModal: document.querySelector('#account-modal')
	};
	
	/**
	 * "postMessage" message channel
	 *
	 * @type {Object}
	 */
	const messageChannels = {
		authorization: 'Authorization',
		orderStatuses: 'OrderStatuses',
		styleConfig: 'StyleConfig',
		reload: 'Reload',
		configuration: 'Configuration',
		action: 'Action'
	};
	
	/**
	 * URIs
	 *
	 * @type {Object}
	 */
	const uris = {
		shopData: null,
		createSessionKey: null,
		translations: null,
		orderStatuses: null,
		styleConfig: null
	};
	
	/**
	 * Fetched Contents
	 *
	 * @type {Object}
	 */
	const contents = {
		translations: null,
		shopData: null,
		sessionKey: null,
		languageCode: null,
		orderStatuses: null,
		styleConfig: null
	};
	
	/**
	 * Is this an old shop version?
	 *
	 * @type {Boolean}
	 */
	let isLegacyShop;
	
	/**
	 * Handle AJAX Error
	 *
	 * This method will throw an exception if the response has the 500 status code.
	 *
	 * {@link https://developer.mozilla.org/en-US/docs/Web/API/Response/ok}
	 *
	 * @param {Response} response
	 *
	 * @return {Response}
	 */
	function handleAjaxErrors(response) {
		if (!response.ok) {
			throw new Error(response);
		}
		
		return response;
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
			const $modal = $('<div/>', {class: 'gambio-hub-modal', html: content});
			
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
			.then(handleAjaxErrors)
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
			.then(handleAjaxErrors)
			.then(response => response.json())
			.then(parsed => contents.shopData = parsed)
			.catch(() => showMessage(contents.translations.ERROR, contents.translations.GET_SHOP_DATA_ERROR));
	}
	
	/**
	 * Fetches the existing order statuses.
	 *
	 * @return {Promise}
	 */
	function fetchOrderStatuses() {
		const options = {credentials: 'include'};
		const request = new Request(uris.orderStatuses, options);
		
		return window.fetch(request)
			.then(handleAjaxErrors)
			.then(response => response.json())
			.then(parsed => contents.orderStatuses = parsed)
			.catch(() => showMessage(contents.translations.ERROR, contents.translations.GET_ORDER_STATUS_ERROR));
	}
	
	/**
	 * Fetches the style edit configurations.
	 *
	 * @return {Promise}
	 */
	function fetchStyleConfig() {
		const options = {credentials: 'include'};
		const request = new Request(uris.styleConfig, options);
		
		return window.fetch(request)
			.then(handleAjaxErrors)
			.then(response => response.json())
			.then(parsed => contents.styleConfig = parsed)
			.catch(() => showMessage(contents.translations.ERROR, contents.translations.GET_SHOP_DATA_ERROR));
	}
	
	/**
	 * Sends the hub client key to the iframe.
	 */
	function sendShopDataToIframe() {
		if (!contents.shopData.clientkey || !contents.sessionKey) {
			elements.iframe.removeEventListener('load', onIframeLoad);
			elements.iframe.src += `&demo`;
			return; // No need to continue ...
		}
		
		const messages = [
			{
				channel: messageChannels.authorization,
				message: {
					clientKey: contents.shopData.clientkey,
					sessionKey: contents.sessionKey
				}
			},
			{
				channel: messageChannels.orderStatuses,
				message: contents.orderStatuses
			},
			{
				channel: messageChannels.styleConfig,
				message: contents.styleConfig
			},
		
		];
		
		const queryParameters = location.search.slice(1).split('&');
		
		const action = queryParameters.filter(function(queryParameter) {
			return queryParameter.indexOf('action=') > -1;
		});
		
		if (action.length) {
			const split = action[0].split('=').pop().split(':'); // e.g. action=name:value
			
			messages.push({
				channel: messageChannels.action,
				message: {
					name: split[0],
					value: split[1]
				}
			});
			
			history.pushState({}, '', jse.core.config.get('appUrl')
				+ '/admin/admin.php?do=HubConfiguration/PaymentMethods');
		}
		
		
		elements.iframe.contentWindow.postMessage(messages, '*');
	}
	
	/**
	 * Returns whether the event data contains a message sent from the PostMessageBridge.
	 *
	 * @param {Event} event Triggered event.
	 *
	 * @return {Boolean} Is the message a PostMessageBridge message?
	 */
	function isPostMessageBridgeMessage(event) {
		return (
			Array.isArray(event.data)
			&& event.data.length
			&& Object.keys(event.data[0]).includes('channel')
			&& Object.keys(event.data[0]).includes('message')
		);
	}
	
	/**
	 * Handles incoming messages.
	 *
	 * @param {MessageEvent} event Triggered event.
	 */
	function onMessage(event) {
		if (!isPostMessageBridgeMessage(event)) {
			return;
		}
		
		const data = event.data[0];
		
		if (data.channel.startsWith('Modal') && data.message.startsWith('registration')) {
			if (isLegacyShop) {
				$(elements.accountModal).dialog({
					minWidth: 800,
					minHeight: 700,
					class: 'gx-container'
				});
			} else {
				$(elements.accountModal).modal();
			}
		}
		
		if (data.channel.startsWith(messageChannels.reload)) {
			window.location.reload();
		}
	}
	
	/**
	 * Performs a request that creates a session key.
	 *
	 * The creation of a new session requires the existence of a registered client key.
	 */
	function createSessionKey() {
		if (!contents.shopData || !contents.shopData.clientkey) {
			return; // Do not start a new session as there's no client key. 
		}
		
		const options = {
			method: 'POST',
			credentials: 'include'
		};
		
		const request = new Request(uris.createSessionKey, options);
		
		return window.fetch(request)
			.then(handleAjaxErrors)
			.then(response => response.json())
			.then(data => contents.sessionKey = data.gambio_hub_session_key)
			.catch(() => showMessage(contents.translations.ERROR, contents.translations.CREATE_SESSION_ERROR));
	}
	
	/**
	 * Handles the iframe's load event.
	 */
	function onIframeLoad() {
		window.iFrameResize({
			heightCalculationMethod: 'grow'
		}, '#payment-methods-iframe');
		
		fetchTranslations()
			.then(fetchShopData)
			.then(fetchOrderStatuses)
			.then(fetchStyleConfig)
			.then(createSessionKey)
			.then(sendShopDataToIframe);
	}
	
	/**
	 * Handles the module initialization.
	 */
	function onInit() {
		isLegacyShop = elements.container.getAttribute('data-legacy-mode') !== null;
		
		contents.languageCode =
			isLegacyShop ? js_options.global.language_code : window.jse.core.config.get('languageCode');
		
		uris.translations = isLegacyShop ?
		                    'request_port.php?module=HubConfiguration&action=get_translations' :
		                    'admin.php?do=HubConfigurationAjax/GetTranslations';
		
		uris.shopData = isLegacyShop ?
		                'request_port.php?module=HubConfiguration&action=get_shop_data' :
		                'admin.php?do=HubConfigurationAjax/GetShopData';
		
		uris.createSessionKey = isLegacyShop ?
		                        'request_port.php?module=HubConfiguration&action=create_session_key' :
		                        'admin.php?do=HubConfigurationAjax/CreateSessionKey';
		
		uris.orderStatuses = isLegacyShop ?
		                     'request_port.php?module=HubConfiguration&action=get_order_statuses' :
		                     'admin.php?do=HubConfigurationAjax/GetOrderStatuses';
		
		uris.styleConfig = isLegacyShop ?
		                   'request_port.php?module=HubConfiguration&action=get_style_edit_configuration' :
		                   'admin.php?do=HubConfigurationAjax/GetStyleEditConfiguration';
		
		elements.iframe = document.createElement('iframe');
		elements.iframe.setAttribute('src', elements.container.getAttribute('data-target'));
		elements.iframe.setAttribute('id', 'payment-methods-iframe');
		elements.iframe.classList.add('hub-iframe');
		elements.iframe.addEventListener('load', onIframeLoad);
		elements.container.appendChild(elements.iframe);
		elements.container.removeAttribute('data-target');
		elements.container.removeAttribute('data-autostart');
		elements.container.removeAttribute('data-legacy-mode');
		
		const event = document.createEvent('Event');
		event.initEvent('hub:account:start', true, false);
		window.dispatchEvent(event);
	}
	
	const initEvent = elements.container.getAttribute('data-autostart') !== null
		? 'DOMContentLoaded' : 'hub:payment_methods:start';
	window.addEventListener(initEvent, onInit);
	window.addEventListener('message', onMessage);
})();
