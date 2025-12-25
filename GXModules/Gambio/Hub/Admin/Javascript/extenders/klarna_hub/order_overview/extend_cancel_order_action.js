/* --------------------------------------------------------------
 extend_cancel_order_action.js 2017-11-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Extends cancel-order row action (will call the respective KlarnaHub callback).
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
		const $modal = $('.cancel.modal');
		
		const $sourceFormGroup = $modal.find('.single-checkbox:first').closest('.form-group');
		
		const $formGroup = $sourceFormGroup.clone(true);
		$formGroup.addClass('hidden');
		
		const $label = $formGroup.find('label');
		$label.text(KlarnaHub.Config.lang.NOTIFY_KLARNA);
		
		const $singleCheckbox = $formGroup.find('.single-checkbox');
		const $checkbox = $formGroup.find('input:checkbox');
		$checkbox.addClass('notify-klarna');
		$checkbox.on('change', (event) => $checkbox.parent().toggleClass('checked', $checkbox.prop('checked')));
		
		$formGroup.insertBefore($sourceFormGroup.next());
		
		const $table = $('.orders.overview table');
		
		$table.on('click', 'a.cancel', (event) => {
			const $row = $(event.target).closest('tr');
			const orderNumber = $row.data('id');
			const moduleCode = $row.data('gambioHubModule');
			const moduleCodes = [
				'KlarnaHub',
				'KlarnaPaynowHub',
				'KlarnaPaylaterHub',
				'KlarnaSliceitHub',
				'KlarnaBanktransferHub'
			];
			
			if (orderNumber && moduleCode && moduleCodes.includes(moduleCode)) {
				KlarnaHub.Config.orderNumber = orderNumber;
				KlarnaHub.Config.moduleCode = moduleCode;
				$formGroup.removeClass('hidden');
				$checkbox.prop('checked', true);
				$singleCheckbox.addClass('checked');
			} else {
				KlarnaHub.Config.orderNumber = null;
				KlarnaHub.Config.moduleCode = null;
				$formGroup.addClass('hidden');
				$checkbox.prop('checked', false);
				$singleCheckbox.removeClass('checked');
			}
		});
		
		$modal.on('click', '.btn.send', () => {
			if (!$checkbox.prop('checked') || $formGroup.hasClass('hidden')) {
				return;
			}
			
			const orderNumbers = selectedKlarnaHubOrderNumbers.length ? selectedKlarnaHubOrderNumbers : [KlarnaHub.Config.orderNumber]; 
			
			orderNumbers.forEach(orderNumber => {
				KlarnaHub.Config.orderNumber = orderNumber;
				KlarnaHub.Config.moduleCode = $table.find(`tr#${orderNumber}`).data('gambioHubModule');
				KlarnaHub.Api.executeCancelOrder();
			});
		});
		
		$modal.on('hide.bs.modal', () => {
			KlarnaHub.Config.orderNumber = null;
			KlarnaHub.Config.moduleCode = null;
			selectedKlarnaHubOrderNumbers = [];
		});
		
		const $bulkAction = $('.bulk-action'); 
		
		$bulkAction.on('click', 'a.cancel', () => {
			selectedKlarnaHubOrderNumbers = KlarnaHub.Lib.getSelectedKlarnaHubOrderNumbers();
			
			if (selectedKlarnaHubOrderNumbers.length) {
				$formGroup.removeClass('hidden');
				$checkbox.prop('checked', true);
				$singleCheckbox.addClass('checked');
			} else {
				$formGroup.addClass('hidden');
				$checkbox.prop('checked', false);
				$singleCheckbox.removeClass('checked');
			}
		}); 
	};
	
	KlarnaHub.on('ready', () => init());
})(); 
