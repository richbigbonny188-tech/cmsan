/* --------------------------------------------------------------
 extend_order_status_modal.js 2019-04-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2019 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Extends the order status modal with a "notify-klarna-hub" checkbox.
 *
 * This module works closely with the extend_order_status_modal_entry_selection.
 */
(function() {
	'use strict';
    
    /**
     * Flag used for avoiding multiple capture requests.
     *
     * @type {Boolean}
     */
	let processingCapture = false;
	
	/**
	 * Finds the capture the order line belongs to or returns null if the order line is not captured.
	 *
	 * @param {Object} orderLine Contains order line data.
	 *
	 * @return {Object|null}
	 */
	const findCaptureWithOrderLine = (orderLine) => {
		return KlarnaHub.Config.klarnaOrder.captures.find((capture) => {
			return capture.order_lines.find((capturedOrderLine) => {
				return capturedOrderLine.merchant_data !== null
                       ? capturedOrderLine.merchant_data === orderLine.merchant_data
                       : capturedOrderLine.reference === orderLine.reference;
			});
		});
	};
	
	/**
	 * Initializes the module.
	 *
	 * @private
	 */
	const init = () => {
		const klarnaOrder = KlarnaHub.Config.klarnaOrder;
		
		if (!klarnaOrder) {
			return;
		}
		
		const $form = $('#update_orders_status_form')
		const $select = $form.find('[name="gm_status"]');
		
		// Add the notify-klarna-hub checkbox (hidden by default).
		const $sourceControlGroup = $form.find('.single-checkbox:first').parent();
		const $controlGroup = $sourceControlGroup.clone(true);
		$controlGroup.addClass('hidden');
		
		const $label = $controlGroup.find('label');
		$label.text(KlarnaHub.Config.lang.NOTIFY_KLARNA);
		
		const $singleCheckbox = $controlGroup.find('.single-checkbox');
		const $checkbox = $controlGroup.find('input');
		$checkbox.attr('name', 'gm_notify_klarna');
		$controlGroup.insertBefore($sourceControlGroup.next());
		
		// Filter captured order lines out.
		const orderLines = klarnaOrder.order_lines.filter((orderLine) => {
			const capture = findCaptureWithOrderLine(orderLine);
			return orderLine.reference !== 'ot_coupon' && orderLine.reference !== 'ot_discount' && orderLine.reference !== 'ot_gv' && !capture;
		});
		
		if (!orderLines.length) {
			$singleCheckbox.addClass('disabled');
		}
		
		// Bind form submit event.
		$form.on('submit', (event) => {
			event.preventDefault();
			
			if (processingCapture) {
			    return;
            }
			
			if (($select.val() && $select.val() != KlarnaHub.Config.orderStatusShipped) || !$checkbox.prop('checked')) {
				$form[0].submit();
				return;
			}
			
			// Fetch selected items for capture
			const orderLines = [];
			$('#klarna-selected-entries input:checkbox:checked').each((index, checkbox) => {
				orderLines.push($(checkbox).data());
			});
			
			if (!orderLines.length) {
				$form[0].submit();
				return; // Do not submit an empty order lines set.
			}
			
			processingCapture = true;
			
			KlarnaHub.Api.executeCapture(orderLines)
                .then(() => $form[0].submit())
                .finally(() => processingCapture = false)
		});
		
		// Bind status type change event.
		$select.on('change', (event) => {
			if (event.target.value && event.target.value == KlarnaHub.Config.orderStatusShipped) {
				$controlGroup.removeClass('hidden');
				$checkbox.prop('checked', true);
				if (!$singleCheckbox.hasClass('disabled')) {
					$singleCheckbox.addClass('checked');
				}
			} else {
				$controlGroup.addClass('hidden');
				$checkbox.prop('checked', false);
				$singleCheckbox.removeClass('checked');
			}
		});
		
		KlarnaHub.trigger('module', {module: 'extend_order_status_modal'});
	};
	
	KlarnaHub.on('ready', () => init());
})();
