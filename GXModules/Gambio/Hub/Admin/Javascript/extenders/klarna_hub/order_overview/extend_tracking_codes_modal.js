/* --------------------------------------------------------------
 extend_tracking_codes_modal.js 2017-11-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Extends tracking codes modal with notify-klarna checkbox.
 */
(function() {
	'use strict';
	
	/**
	 * Initializes the module.
	 *
	 * @private
	 */
	const init = () => {
		const $trackingCodeText = $('#tracking-number');
		
		if (!$trackingCodeText.length) {
			console.log('KlarnaHub', 'Tracking code element could not be found.', '#tracking-number');
		}
		
		const $shippingCompanySelect = $('#delivery-service');
		
		if (!$shippingCompanySelect.length) {
			console.log('KlarnaHub', 'Shipping company element could not be found.', '#delivery-service');
		}
		
		const $container = $('<div/>', {
			'data-gx-widget': 'single_checkbox',
			'html': [
				$('<input/>', {
					'type': 'checkbox',
					'checked': true
				}),
				
				$('<span/>', {
					'html': '&nbsp;&nbsp;' + KlarnaHub.Config.lang.NOTIFY_KLARNA
				})
			]
		});
		
		const $modal = $('.add-tracking-number.modal');
		$modal.find('form fieldset').append($container);
		
		const $storeTrackingNumber = $('#store-tracking-number');
		
		$storeTrackingNumber.on('click', (event) => {
			event.preventDefault();
			
			if ($trackingCodeText.val() === '') {
				return;
			}
			
			if (!$container.find('input:checkbox').prop('checked')) {
				return;
			}
			
			const shippingCompany = $shippingCompanySelect.find('option:selected').text(); 
			const trackingNumber = $trackingCodeText.val(); 
			
			KlarnaHub.Api.executeAddTrackingCode(shippingCompany, trackingNumber);
		});
		
		const $table = $('.orders.overview table');
		
		$table.on('click', '.shipping-method, .add-tracking-number', (event) => {
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
				$container.removeClass('hidden');
				$container.find('input:checkbox').prop('checked', true);
				$container.find('.single-checkbox').addClass('checked');
			} else {
				KlarnaHub.Config.orderNumber = null;
				KlarnaHub.Config.moduleCode = null;
				$container.addClass('hidden');
				$container.find('input:checkbox').prop('checked', false);
				$container.find('.single-checkbox').removeClass('checked');
			}
		});
		
		gx.widgets.init($container);
	};
	
	KlarnaHub.on('ready', () => init());
})();