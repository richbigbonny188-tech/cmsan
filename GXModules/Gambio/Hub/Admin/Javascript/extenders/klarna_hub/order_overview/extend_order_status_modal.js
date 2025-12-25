/* --------------------------------------------------------------
 extend_order_status_modal.js 2017-11-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Extends the order status modal with a "notify-klarna-hub" checkbox.
 */
(function() {
	'use strict';
	
	/**
	 * Used for bulk actions.
	 *
	 * @type {Number[]}
	 */
	let selectedKlarnaHubOrderNumbers = [];
	
	/**
	 * Initializes the module.
	 *
	 * @private
	 */
	const init = () => {
		const $modal = $('.modal.status');
		const $select = $('#status-dropdown');
		const $orderStatusSave = $modal.find('.btn.save');
		const moduleCodes = [
			'KlarnaHub',
			'KlarnaPaynowHub',
			'KlarnaPaylaterHub',
			'KlarnaSliceitHub',
			'KlarnaBanktransferHub'
		];
		
		// Add the notify-klarna-hub checkbox (hidden by default).
		const $sourceListItem = $modal.find('.single-checkbox:first').closest('li');
		
		const $listItem = $sourceListItem.clone(true);
		$listItem.addClass('hidden');
		
		const $label = $listItem.find('label');
		$label.text(KlarnaHub.Config.lang.NOTIFY_KLARNA);
		
		const $singleCheckbox = $listItem.find('.single-checkbox');
		const $checkbox = $listItem.find('input:checkbox');
		$checkbox.addClass('notify-klarna');
		$checkbox.on('change', (event) => $checkbox.parent().toggleClass('checked', $checkbox.prop('checked')));
		
		$listItem.insertBefore($sourceListItem.next());
		
		// Bind status type change event.
		$select.on('change', () => {
			const hasCorrectStatus = $select.val() 
				&& ($select.val() == KlarnaHub.Config.orderStatusShipped);
			const hasSelectedOrders = KlarnaHub.Config.orderNumber !== null || selectedKlarnaHubOrderNumbers.length;
			
			if (hasCorrectStatus && hasSelectedOrders) {
				$listItem.removeClass('hidden');
				$checkbox.prop('checked', true);
				$singleCheckbox.addClass('checked');
			} else {
				$listItem.addClass('hidden');
				$checkbox.prop('checked', false);
				$singleCheckbox.removeClass('checked');
			}
		});
		
		// Bind order status save button click event.
		$orderStatusSave.on('click', () => {
			const hasCorrectStatus = $select.val() 
				&& ($select.val() == KlarnaHub.Config.orderStatusShipped);
			const hasSelectedOrders = KlarnaHub.Config.orderNumber !== null || selectedKlarnaHubOrderNumbers.length;
			
			if (!hasCorrectStatus || !hasSelectedOrders || !$checkbox.prop('checked')) {
				return;
			}
			
			const orderNumbers = selectedKlarnaHubOrderNumbers.length
				? selectedKlarnaHubOrderNumbers : [KlarnaHub.Config.orderNumber];
			
			orderNumbers.forEach(orderNumber => {
				KlarnaHub.Config.orderNumber = orderNumber;
				KlarnaHub.Config.moduleCode = $table.find(`tr#${orderNumber}`).data('gambioHubModule');
				
				if ($select.val() == KlarnaHub.Config.orderStatusShipped) {
					KlarnaHub.Api.executeFullCapture();
				}
			});
		});
		
		const $table = $('.orders.overview table');
		
		$table.on('click', '.change-status, .tooltip-order-status-history', (event) => {
			const $row = $(event.target).closest('tr');
			const orderNumber = $row.data('id');
			const moduleCode = $row.data('gambioHubModule');
			
			if (orderNumber && moduleCode && moduleCodes.includes(moduleCode)) {
				KlarnaHub.Config.orderNumber = orderNumber;
				KlarnaHub.Config.moduleCode = moduleCode;
			} else {
				KlarnaHub.Config.orderNumber = null;
				KlarnaHub.Config.moduleCode = null;
			}
		});
		
		$modal.on('shown.bs.modal', () => $select.trigger('change'));
		
		$modal.on('hide.bs.modal', () => {
			KlarnaHub.Config.orderNumber = null;
			KlarnaHub.Config.moduleCode = null;
			selectedKlarnaHubOrderNumbers = [];
		});
		
		const $bulkAction = $('.bulk-action');
		
		$bulkAction.on('click', '.change-status', () => {
			selectedKlarnaHubOrderNumbers = KlarnaHub.Lib.getSelectedKlarnaHubOrderNumbers();
		});
		
	};
	
	KlarnaHub.on('ready', () => init());
})(); 