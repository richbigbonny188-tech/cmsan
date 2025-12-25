/* --------------------------------------------------------------
 transactions.js 2017-02-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Gambio Hub Transactions Configuration Controller
 *
 * This module transfers the shop data to the hub transactions controller
 * and handles the incoming messages sent via window.postMessage() from the hub.
 *
 * You have the ability to prevent the automatic initialization by defining 'window.doNotStartHubModule = true;'
 */
(function() {
	
	'use strict';
	
	/**
	 * Elements
	 *
	 * @type {Object}
	 */
	const elements = {
		iframe: null,
		container: null
	};
	
	/**
	 * Events
	 *
	 * @type {Object}
	 */
	const events = {
		domLoaded: 'DOMContentLoaded',
		loaded: 'load',
		message: 'message'
	};
	
	/**
	 * "postMessage" message channel
	 *
	 * @type {Object}
	 */
	const messageChannels = {
		authorization: 'Authorization',
		reload: 'Reload'
	};
	
	/**
	 * URIs
	 *
	 * @type {Object}
	 */
	const uris = {
		iframe: 'https://dev-gui.gambiohub.com?section=transactions', // @todo Change URL once the remote page is ready and make the URL configurable (e.g.: ini file)
		shopData: null,
		createSessionKey: null,
		translations: null
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
		languageCode: null
	};
	
	/**
	 * Is the browser an old Internet Explorer.
	 *
	 * @type {Boolean}
	 */
	const isOldInternetExplorer = (navigator.userAgent.indexOf('MSIE') !== -1);
	
	/**
	 * "iframeResizer" library options.
	 *
	 * @type {Object}
	 */
	const iframeResizeOptions = {
		heightCalculationMethod: isOldInternetExplorer ? 'max' : 'lowestElement',
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
			const $modal = $('<div/>', {class: classes.modal, html: content});
			
			$modal.dialog({
				title,
				buttons: [{text: 'OK', click: () => $modal.dialog('close')}],
				close: () => $modal.remove()
			});
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
	 * Sends the shop data or the hub client key to the iframe.
	 */
	function sendShopDataToIframe() {
		if (!contents.shopData.clientkey || !contents.sessionKey) {
			elements.iframe.removeEventListener(events.loaded, onIframeLoaded);
			elements.iframe.src = uris.iframe + '&demo';
			return; // No need to continue ... 
		}
		
		const messages = [
			{
				channel: messageChannels.authorization,
				message: {
					clientKey: contents.shopData.clientkey,
					sessionKey: contents.sessionKey
				}
			}
		];
		
		elements.iframe.contentWindow.postMessage(messages, '*');
	}
	
	/**
	 * Handles the iframe load event.
	 */
	function onIframeLoad() {
		window.iFrameResize(iframeResizeOptions);
		
		fetchTranslations()
			.then(fetchShopData)
			.then(createSessionKey)
			.then(sendShopDataToIframe);
	}
	
	/**
	 * Handles the document's DOMContentLoaded event - or the custom triggered hub start event.
	 */
	function onDomLoaded() {
		isLegacyShop = !window.jse;
		
		// @todo: Change this for older shops if no jse is available
		contents.languageCode = isLegacyShop ? 'de' : window.jse.core.config.get('languageCode');
		uris.iframe += `?language=${contents.languageCode}`;
		
		// Set container element.
		elements.container = document.querySelector('#iframe-container');
		
		uris.translations = isLegacyShop ?
		                    'request_port.php?module=HubConfiguration&action=get_translations' :
		                    'admin.php?do=HubConfigurationAjax/GetTranslations';
		
		uris.shopData = isLegacyShop ?
		                'request_port.php?module=HubConfiguration&action=get_shop_data' :
		                'admin.php?do=HubConfigurationAjax/GetShopData';
		
		uris.createSessionKey = isLegacyShop ?
		                        'request_port.php?module=HubConfiguration&action=create_session_key' :
		                        'admin.php?do=HubConfigurationAjax/CreateSessionKey';
		
		elements.iframe.src = uris.iframe;
		elements.iframe.addEventListener(events.loaded, onIframeLoad);
		
		elements.container.appendChild(elements.iframe);
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
		
		if (data.channel.startsWith(messageChannels.reload)) {
			window.location.reload();
		}
	}
	
	elements.iframe = document.createElement('iframe');
	
	window.addEventListener(events.domLoaded, onDomLoaded);
	window.addEventListener(events.message, onMessage);
})();

