/* --------------------------------------------------------------
   extend_order_status_modal_entry_selection.js 2022-04-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

/**
 * Extends the order status modal with a "entry selection" widget.
 */
(function() {
	'use strict';
	
	/**
	 * Updates the dropdown button text with the number of the selected items.
	 *
	 * @param {jQuery} $buttonDropdown
	 */
	const updateButtonDropdownText = ($buttonDropdown) => {
		const text = $buttonDropdown.find('input:checkbox:checked').length + ' '
			+ KlarnaHub.Config.lang.SELECTED_ENTRIES;
		$buttonDropdown.find('button:first').text(text);
	};
	
	/**
	 * Returns the order line display name, displayed as a button dropdown menu item.
	 *
	 * @param {Object} orderLine Contains order line data.
	 *
	 * @return {String}
	 */
	const getOrderLineDisplayName = (orderLine) => {
		let displayName = KlarnaHub.Config.lang.UNMATCHED_PRODUCT;
		
		if (!KlarnaHub.Config.order) {
			return displayName;
		}
		
		if (orderLine.reference === 'ot_shipping' || orderLine.type === 'surcharge') {
			return orderLine.name.replace(/:$/, '');
		}
		
		// Product properties (format: "#x#" or "#{#}#").
        const reference = orderLine.merchant_data !== null ? orderLine.merchant_data : orderLine.reference;
		if (/\d+\D\d+/.test(reference) || /\d+{\d+}\d+/.test(reference)) {
			const item = KlarnaHub.Config.order.items.find(item => {
				return item.addonValues.identifier === reference;
			});
			
			if (item) {
				displayName = item.attributes.map(attributes => attributes.name + ': ' + attributes.value).join(', ');
			}
		}
		
		// Product without properties or attributes.
		if (/^\d+$/.test(reference)) {
			displayName = '';
		}
		
		return orderLine.name + (displayName !== '' ? ' (' + displayName + ')' : '');
	};
	
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
		
		// Filter captured order lines out.
		const orderLines = klarnaOrder.order_lines.filter((orderLine) => {
			const capture = findCaptureWithOrderLine(orderLine);
			return orderLine.reference !== 'ot_coupon' && orderLine.reference !== 'ot_discount' && orderLine.reference !== 'ot_gv' && !capture;
		});
		
		const $buttonDropdown = $('<div/>', {
			'data-gx-widget': 'button_dropdown',
			'html': [
				$('<div/>', {
					'id': 'klarna-selected-entries',
					'data-use-button_dropdown': 'true',
					'css': {
						'margin-left': '0',
						'margin-bottom': '0'
					},
					'html': [
						$('<button/>', {
							'text': '0 ' + KlarnaHub.Config.lang.SELECTED_ENTRIES,
							'on': {
								'click': (event) => {
									$(event.target).next('button').trigger('click');
								}
							}
						}),
						$('<ul/>', {
							'html': orderLines.map((orderLine) => {
								return $('<li/>', {
									'html': [
										$('<span/>', {
											'html': [
												$('<input/>', {
													'type': 'checkbox',
													'data': orderLine,
													'css': {
														'height': 'auto',
														'margin-top': '5px'
													},
													'prop': {
														'checked': true
													},
													'on': {
														'click': (event) => {
															event.stopPropagation();
															updateButtonDropdownText($buttonDropdown);
														}
													}
												}),
												$('<span/>', {
													'text': getOrderLineDisplayName(orderLine),
													'css': {
														'display': 'inline-block',
														'padding-left': '5px'
													},
													'on': {
														'click': (event) => {
															event.stopPropagation();
															$(event.target).prev('input:checkbox').trigger('click');
														}
													}
												})
											]
										})
									]
								})
							})
						})
					]
				})
			]
		});
		
		updateButtonDropdownText($buttonDropdown);
		
		const $allEntriesCaptured = $('<span/>', {
			'text': KlarnaHub.Config.lang.ALL_ENTRIES_CAPTURED
		});
		
		const $form = $('#update_orders_status_form')
		const $select = $form.find('[name="gm_status"]');
		
		// Add the notify-klarna-hub checkbox (hidden by default).
		const $sourceControlGroup = $form.find('[name="gm_notify_klarna"]').closest('.control-group');
		const $controlGroup = $sourceControlGroup.clone(true);
		$controlGroup.addClass('hidden');
		
		const $label = $controlGroup.find('label');
		$label.text(KlarnaHub.Config.lang.KLARNA_CAPTURE);
		$controlGroup.find('.single-checkbox').remove();
		
		if (orderLines.length) {
			$controlGroup.append($buttonDropdown);
			gx.widgets.init($controlGroup);
		} else {
			$controlGroup.append($allEntriesCaptured);
		}
		
		$controlGroup.insertBefore($sourceControlGroup.next());
		
		// Bind status type change event, toggle visibility.
		$select.on('change', (event) => {
			if (event.target.value && event.target.value == KlarnaHub.Config.orderStatusShipped) {
				$controlGroup.removeClass('hidden');
			} else {
				$controlGroup.addClass('hidden');
			}
		});
	};
	
	KlarnaHub.on('module', (event) => {
		if (event.module !== 'extend_order_status_modal') {
			return;
		}
		init();
	});
})();
