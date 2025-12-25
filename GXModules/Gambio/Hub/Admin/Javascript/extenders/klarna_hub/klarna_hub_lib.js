/* --------------------------------------------------------------
 klarna_hub_lib.js 2017-11-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * larnaHub Lib Module
 * 
 * Helper methods for KlarnaHub modules. 
 * 
 * @module KlarnaHub.Lib
 */
(function() {
	'use strict';
	
	/**
	 * Legacy pages flag.
	 *
	 * @type {Boolean}
	 */
	const legacy = !$.fn.modal;
	
	/**
	 * Returns URL GET parameter value.
	 *
	 * @param {String} name Variable name to be returned.
	 * @param {String} url URL to be parsed.
	 *
	 * @return {String}
	 *
	 * @public
	 */
	const getUrlParameter = (name, url) => {
		if (!url) {
			url = window.location.href;
		}
		
		name = name.replace(/[\[\]]/g, '\\$&');
		
		const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
		
		const results = regex.exec(url);
		
		if (!results) {
			return null;
		}
		
		if (!results[2]) {
			return '';
		}
		
		return decodeURIComponent(results[2].replace(/\+/g, ' '));
	};
	
	/**
	 * Shows message dialog to the user (legacy).
	 *
	 * This method makes use of the jQuery UI modal component.
	 *
	 * @param {String} title Dialog title.
	 * @param {String} message Dialog message.
	 * @param {Object[]} [buttons] Dialog buttons (use jQuery UI dialog format).
	 *
	 * @return {jQuery} Returns dialog jQuery selector.
	 *
	 * @private
	 */
	const showMessageLegacy = (title, message, buttons) => {
		const $dialog = $('<div/>', {
			'html': [
				$('<div/>', {
					'html': message
				})
			]
		})
			.appendTo('body');
		
		if (!buttons) {
			buttons = [
				{
					text: KlarnaHub.Config ? KlarnaHub.Config.lang.CLOSE : 'Close',
					click: () => {
						$dialog
							.dialog('close')
							.remove();
					}
				}
			];
		}
		
		$dialog.dialog({
			autoOpen: true,
			width: 500,
			height: 'auto',
			resizable: false,
			modal: true,
			title,
			dialogClass: 'gx-container',
			buttons
		});
		
		return $dialog;
	};
	
	/**
	 * Shows message dialog to the user (modern).
	 *
	 * This method makes use of the Bootstrap modal component.
	 *
	 * @param {String} title Dialog title.
	 * @param {String} message Dialog message.
	 * @param {Object[]} [buttons] Dialog buttons (use jQuery UI dialog format).
	 *
	 * @return {jQuery} Returns dialog jQuery selector.
	 *
	 * @private
	 */
	const showMessageModern = (title, message, buttons) => {
		const html = `<div class="modal fade" tabindex="-1" role="dialog">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
									<h4 class="modal-title">${title}</h4>
								</div>
								<div class="modal-body">
					                ${message}
								</div>
								<div class="modal-footer"></div>
							</div>
						</div>
					</div>`;
		
		const $modal = $(html).appendTo('body');
		
		if (!buttons) {
			buttons = [
				{
					title: KlarnaHub.Config ? KlarnaHub.Config.lang.CLOSE : 'Close',
					class: 'btn btn-default',
					callback: () => $modal.modal('hide')
				}
			];
		}
		
		buttons.forEach(button => {
			const $button = $('<button/>', {
				'text': button.title,
				'class': button.class || 'btn btn-default'
			})
				.appendTo($modal.find('.modal-footer'));
			
			if (button.callback) {
				$button.on('click', button.callback);
			}
		});
		
		$modal.on('hidden.bs.modal', () => $modal.remove());
		
		$modal.modal('show');
		
		return $modal;
	};
	
	
	/**
	 * Shows message dialog to the user.
	 *
	 * @param {String} title Dialog title.
	 * @param {String} message Dialog message.
	 * @param {Object[]} [buttons] Dialog buttons (use jQuery UI dialog format).
	 *
	 * @public
	 */
	const showMessage = legacy ? showMessageLegacy : showMessageModern;
	
	/**
	 * Handles KlarnaHub related errors.
	 *
	 * @param {Error} error Error object.
	 * 
	 * @public 
	 */
	const handleError = (error) => {
		if (KlarnaHub.Config && !KlarnaHub.Config.debug) {
			return;
		}
		
		console.group('KlarnaHub Error');
		console.error(!KlarnaHub.Config ? 'Unexpected error during KlarnaHub initialization.' : 'An unexpected error occurred.');
		console.error(error);
		console.groupEnd();
		
		showMessage('Klarna', KlarnaHub.Config.lang.UNEXPECTED_REQUEST_ERROR);
	};
	
	/**
	 * Returns selected KlarnaHub order numbers (works only in orders overview). 
	 * 
	 * @return {Number[]} 
	 * 
	 * @public
	 */
	const getSelectedKlarnaHubOrderNumbers = () => {
		const $table = $('.orders.overview .table-main'); 
		
		if (!$table.length) {
			throw new Error('This method can only be used in the orders overview page.'); 
		}
		
		const moduleCodes = [
			'KlarnaHub',
			'KlarnaPaynowHub',
			'KlarnaPaylaterHub',
			'KlarnaSliceitHub',
			'KlarnaBanktransferHub'
		]; 
		
		const selectedKlarnaHubOrders = [];
		
		$table.find('tbody input:checkbox:checked').each((index, checkbox) => {
			const {id, gambioHubModule} = $(checkbox).parents('tr').data(); 
			
			if (moduleCodes.includes(gambioHubModule)) {
				selectedKlarnaHubOrders.push(id);	
			}
		});
		
		return selectedKlarnaHubOrders;
	}
	
	// Export
	window.KlarnaHub = window.KlarnaHub || {};
	window.KlarnaHub.Lib = Object.assign({}, {
		getUrlParameter,
		showMessage,
		handleError,
		getSelectedKlarnaHubOrderNumbers
	}, window.KlarnaHub.Lib);
})();