'use strict';

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
(function () {
	'use strict';

	/**
  * Updates the dropdown button text with the number of the selected items.
  *
  * @param {jQuery} $buttonDropdown
  */

	var updateButtonDropdownText = function updateButtonDropdownText($buttonDropdown) {
		var text = $buttonDropdown.find('input:checkbox:checked').length + ' ' + KlarnaHub.Config.lang.SELECTED_ENTRIES;
		$buttonDropdown.find('button:first').text(text);
	};

	/**
  * Returns the order line display name, displayed as a button dropdown menu item.
  *
  * @param {Object} orderLine Contains order line data.
  *
  * @return {String}
  */
	var getOrderLineDisplayName = function getOrderLineDisplayName(orderLine) {
		var displayName = KlarnaHub.Config.lang.UNMATCHED_PRODUCT;

		if (!KlarnaHub.Config.order) {
			return displayName;
		}

		if (orderLine.reference === 'ot_shipping' || orderLine.type === 'surcharge') {
			return orderLine.name.replace(/:$/, '');
		}

		// Product properties (format: "#x#" or "#{#}#").
		var reference = orderLine.merchant_data !== null ? orderLine.merchant_data : orderLine.reference;
		if (/\d+\D\d+/.test(reference) || /\d+{\d+}\d+/.test(reference)) {
			var item = KlarnaHub.Config.order.items.find(function (item) {
				return item.addonValues.identifier === reference;
			});

			if (item) {
				displayName = item.attributes.map(function (attributes) {
					return attributes.name + ': ' + attributes.value;
				}).join(', ');
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
	var findCaptureWithOrderLine = function findCaptureWithOrderLine(orderLine) {
		return KlarnaHub.Config.klarnaOrder.captures.find(function (capture) {
			return capture.order_lines.find(function (capturedOrderLine) {
				return capturedOrderLine.merchant_data !== null ? capturedOrderLine.merchant_data === orderLine.merchant_data : capturedOrderLine.reference === orderLine.reference;
			});
		});
	};

	/**
  * Initializes the module.
  *
  * @private
  */
	var init = function init() {
		var klarnaOrder = KlarnaHub.Config.klarnaOrder;

		if (!klarnaOrder) {
			return;
		}

		// Filter captured order lines out.
		var orderLines = klarnaOrder.order_lines.filter(function (orderLine) {
			var capture = findCaptureWithOrderLine(orderLine);
			return orderLine.reference !== 'ot_coupon' && orderLine.reference !== 'ot_discount' && orderLine.reference !== 'ot_gv' && !capture;
		});

		var $buttonDropdown = $('<div/>', {
			'data-gx-widget': 'button_dropdown',
			'html': [$('<div/>', {
				'id': 'klarna-selected-entries',
				'data-use-button_dropdown': 'true',
				'css': {
					'margin-left': '0',
					'margin-bottom': '0'
				},
				'html': [$('<button/>', {
					'text': '0 ' + KlarnaHub.Config.lang.SELECTED_ENTRIES,
					'on': {
						'click': function click(event) {
							$(event.target).next('button').trigger('click');
						}
					}
				}), $('<ul/>', {
					'html': orderLines.map(function (orderLine) {
						return $('<li/>', {
							'html': [$('<span/>', {
								'html': [$('<input/>', {
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
										'click': function click(event) {
											event.stopPropagation();
											updateButtonDropdownText($buttonDropdown);
										}
									}
								}), $('<span/>', {
									'text': getOrderLineDisplayName(orderLine),
									'css': {
										'display': 'inline-block',
										'padding-left': '5px'
									},
									'on': {
										'click': function click(event) {
											event.stopPropagation();
											$(event.target).prev('input:checkbox').trigger('click');
										}
									}
								})]
							})]
						});
					})
				})]
			})]
		});

		updateButtonDropdownText($buttonDropdown);

		var $allEntriesCaptured = $('<span/>', {
			'text': KlarnaHub.Config.lang.ALL_ENTRIES_CAPTURED
		});

		var $form = $('#update_orders_status_form');
		var $select = $form.find('[name="gm_status"]');

		// Add the notify-klarna-hub checkbox (hidden by default).
		var $sourceControlGroup = $form.find('[name="gm_notify_klarna"]').closest('.control-group');
		var $controlGroup = $sourceControlGroup.clone(true);
		$controlGroup.addClass('hidden');

		var $label = $controlGroup.find('label');
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
		$select.on('change', function (event) {
			if (event.target.value && event.target.value == KlarnaHub.Config.orderStatusShipped) {
				$controlGroup.removeClass('hidden');
			} else {
				$controlGroup.addClass('hidden');
			}
		});
	};

	KlarnaHub.on('module', function (event) {
		if (event.module !== 'extend_order_status_modal') {
			return;
		}
		init();
	});
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2tsYXJuYV9odWIvb3JkZXJfZGV0YWlscy9leHRlbmRfb3JkZXJfc3RhdHVzX21vZGFsX2VudHJ5X3NlbGVjdGlvbi5qcyJdLCJuYW1lcyI6WyJ1cGRhdGVCdXR0b25Ecm9wZG93blRleHQiLCIkYnV0dG9uRHJvcGRvd24iLCJ0ZXh0IiwiZmluZCIsImxlbmd0aCIsIktsYXJuYUh1YiIsIkNvbmZpZyIsImxhbmciLCJTRUxFQ1RFRF9FTlRSSUVTIiwiZ2V0T3JkZXJMaW5lRGlzcGxheU5hbWUiLCJvcmRlckxpbmUiLCJkaXNwbGF5TmFtZSIsIlVOTUFUQ0hFRF9QUk9EVUNUIiwib3JkZXIiLCJyZWZlcmVuY2UiLCJ0eXBlIiwibmFtZSIsInJlcGxhY2UiLCJtZXJjaGFudF9kYXRhIiwidGVzdCIsIml0ZW0iLCJpdGVtcyIsImFkZG9uVmFsdWVzIiwiaWRlbnRpZmllciIsImF0dHJpYnV0ZXMiLCJtYXAiLCJ2YWx1ZSIsImpvaW4iLCJmaW5kQ2FwdHVyZVdpdGhPcmRlckxpbmUiLCJrbGFybmFPcmRlciIsImNhcHR1cmVzIiwiY2FwdHVyZSIsIm9yZGVyX2xpbmVzIiwiY2FwdHVyZWRPcmRlckxpbmUiLCJpbml0Iiwib3JkZXJMaW5lcyIsImZpbHRlciIsIiQiLCJldmVudCIsInRhcmdldCIsIm5leHQiLCJ0cmlnZ2VyIiwic3RvcFByb3BhZ2F0aW9uIiwicHJldiIsIiRhbGxFbnRyaWVzQ2FwdHVyZWQiLCJBTExfRU5UUklFU19DQVBUVVJFRCIsIiRmb3JtIiwiJHNlbGVjdCIsIiRzb3VyY2VDb250cm9sR3JvdXAiLCJjbG9zZXN0IiwiJGNvbnRyb2xHcm91cCIsImNsb25lIiwiYWRkQ2xhc3MiLCIkbGFiZWwiLCJLTEFSTkFfQ0FQVFVSRSIsInJlbW92ZSIsImFwcGVuZCIsImd4Iiwid2lkZ2V0cyIsImluc2VydEJlZm9yZSIsIm9uIiwib3JkZXJTdGF0dXNTaGlwcGVkIiwicmVtb3ZlQ2xhc3MiLCJtb2R1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QUosZ0JBQWMvQyxJQUFkLENBQW1CLGtCQUFuQixFQUF1Q29ELE1BQXZDOztBQUVBLE1BQUlwQixXQUFXL0IsTUFBZixFQUF1QjtBQUN0QjhDLGlCQUFjTSxNQUFkLENBQXFCdkQsZUFBckI7QUFDQXdELE1BQUdDLE9BQUgsQ0FBV3hCLElBQVgsQ0FBZ0JnQixhQUFoQjtBQUNBLEdBSEQsTUFHTztBQUNOQSxpQkFBY00sTUFBZCxDQUFxQlosbUJBQXJCO0FBQ0E7O0FBRURNLGdCQUFjUyxZQUFkLENBQTJCWCxvQkFBb0JSLElBQXBCLEVBQTNCOztBQUVBO0FBQ0FPLFVBQVFhLEVBQVIsQ0FBVyxRQUFYLEVBQXFCLFVBQUN0QixLQUFELEVBQVc7QUFDL0IsT0FBSUEsTUFBTUMsTUFBTixDQUFhYixLQUFiLElBQXNCWSxNQUFNQyxNQUFOLENBQWFiLEtBQWIsSUFBc0JyQixVQUFVQyxNQUFWLENBQWlCdUQsa0JBQWpFLEVBQXFGO0FBQ3BGWCxrQkFBY1ksV0FBZCxDQUEwQixRQUExQjtBQUNBLElBRkQsTUFFTztBQUNOWixrQkFBY0UsUUFBZCxDQUF1QixRQUF2QjtBQUNBO0FBQ0QsR0FORDtBQU9BLEVBbEhEOztBQW9IQS9DLFdBQVV1RCxFQUFWLENBQWEsUUFBYixFQUF1QixVQUFDdEIsS0FBRCxFQUFXO0FBQ2pDLE1BQUlBLE1BQU15QixNQUFOLEtBQWlCLDJCQUFyQixFQUFrRDtBQUNqRDtBQUNBO0FBQ0Q3QjtBQUNBLEVBTEQ7QUFNQSxDQXBNRCIsImZpbGUiOiJBZG1pbi9KYXZhc2NyaXB0L2V4dGVuZGVycy9rbGFybmFfaHViL29yZGVyX2RldGFpbHMvZXh0ZW5kX29yZGVyX3N0YXR1c19tb2RhbF9lbnRyeV9zZWxlY3Rpb24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAgZXh0ZW5kX29yZGVyX3N0YXR1c19tb2RhbF9lbnRyeV9zZWxlY3Rpb24uanMgMjAyMi0wNC0xMlxuICAgR2FtYmlvIEdtYkhcbiAgIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gICBDb3B5cmlnaHQgKGMpIDIwMjIgR2FtYmlvIEdtYkhcbiAgIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuICAgW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogRXh0ZW5kcyB0aGUgb3JkZXIgc3RhdHVzIG1vZGFsIHdpdGggYSBcImVudHJ5IHNlbGVjdGlvblwiIHdpZGdldC5cbiAqL1xuKGZ1bmN0aW9uKCkge1xuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvKipcblx0ICogVXBkYXRlcyB0aGUgZHJvcGRvd24gYnV0dG9uIHRleHQgd2l0aCB0aGUgbnVtYmVyIG9mIHRoZSBzZWxlY3RlZCBpdGVtcy5cblx0ICpcblx0ICogQHBhcmFtIHtqUXVlcnl9ICRidXR0b25Ecm9wZG93blxuXHQgKi9cblx0Y29uc3QgdXBkYXRlQnV0dG9uRHJvcGRvd25UZXh0ID0gKCRidXR0b25Ecm9wZG93bikgPT4ge1xuXHRcdGNvbnN0IHRleHQgPSAkYnV0dG9uRHJvcGRvd24uZmluZCgnaW5wdXQ6Y2hlY2tib3g6Y2hlY2tlZCcpLmxlbmd0aCArICcgJ1xuXHRcdFx0KyBLbGFybmFIdWIuQ29uZmlnLmxhbmcuU0VMRUNURURfRU5UUklFUztcblx0XHQkYnV0dG9uRHJvcGRvd24uZmluZCgnYnV0dG9uOmZpcnN0JykudGV4dCh0ZXh0KTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBSZXR1cm5zIHRoZSBvcmRlciBsaW5lIGRpc3BsYXkgbmFtZSwgZGlzcGxheWVkIGFzIGEgYnV0dG9uIGRyb3Bkb3duIG1lbnUgaXRlbS5cblx0ICpcblx0ICogQHBhcmFtIHtPYmplY3R9IG9yZGVyTGluZSBDb250YWlucyBvcmRlciBsaW5lIGRhdGEuXG5cdCAqXG5cdCAqIEByZXR1cm4ge1N0cmluZ31cblx0ICovXG5cdGNvbnN0IGdldE9yZGVyTGluZURpc3BsYXlOYW1lID0gKG9yZGVyTGluZSkgPT4ge1xuXHRcdGxldCBkaXNwbGF5TmFtZSA9IEtsYXJuYUh1Yi5Db25maWcubGFuZy5VTk1BVENIRURfUFJPRFVDVDtcblx0XHRcblx0XHRpZiAoIUtsYXJuYUh1Yi5Db25maWcub3JkZXIpIHtcblx0XHRcdHJldHVybiBkaXNwbGF5TmFtZTtcblx0XHR9XG5cdFx0XG5cdFx0aWYgKG9yZGVyTGluZS5yZWZlcmVuY2UgPT09ICdvdF9zaGlwcGluZycgfHwgb3JkZXJMaW5lLnR5cGUgPT09ICdzdXJjaGFyZ2UnKSB7XG5cdFx0XHRyZXR1cm4gb3JkZXJMaW5lLm5hbWUucmVwbGFjZSgvOiQvLCAnJyk7XG5cdFx0fVxuXHRcdFxuXHRcdC8vIFByb2R1Y3QgcHJvcGVydGllcyAoZm9ybWF0OiBcIiN4I1wiIG9yIFwiI3sjfSNcIikuXG4gICAgICAgIGNvbnN0IHJlZmVyZW5jZSA9IG9yZGVyTGluZS5tZXJjaGFudF9kYXRhICE9PSBudWxsID8gb3JkZXJMaW5lLm1lcmNoYW50X2RhdGEgOiBvcmRlckxpbmUucmVmZXJlbmNlO1xuXHRcdGlmICgvXFxkK1xcRFxcZCsvLnRlc3QocmVmZXJlbmNlKSB8fCAvXFxkK3tcXGQrfVxcZCsvLnRlc3QocmVmZXJlbmNlKSkge1xuXHRcdFx0Y29uc3QgaXRlbSA9IEtsYXJuYUh1Yi5Db25maWcub3JkZXIuaXRlbXMuZmluZChpdGVtID0+IHtcblx0XHRcdFx0cmV0dXJuIGl0ZW0uYWRkb25WYWx1ZXMuaWRlbnRpZmllciA9PT0gcmVmZXJlbmNlO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGlmIChpdGVtKSB7XG5cdFx0XHRcdGRpc3BsYXlOYW1lID0gaXRlbS5hdHRyaWJ1dGVzLm1hcChhdHRyaWJ1dGVzID0+IGF0dHJpYnV0ZXMubmFtZSArICc6ICcgKyBhdHRyaWJ1dGVzLnZhbHVlKS5qb2luKCcsICcpO1xuXHRcdFx0fVxuXHRcdH1cblx0XHRcblx0XHQvLyBQcm9kdWN0IHdpdGhvdXQgcHJvcGVydGllcyBvciBhdHRyaWJ1dGVzLlxuXHRcdGlmICgvXlxcZCskLy50ZXN0KHJlZmVyZW5jZSkpIHtcblx0XHRcdGRpc3BsYXlOYW1lID0gJyc7XG5cdFx0fVxuXHRcdFxuXHRcdHJldHVybiBvcmRlckxpbmUubmFtZSArIChkaXNwbGF5TmFtZSAhPT0gJycgPyAnICgnICsgZGlzcGxheU5hbWUgKyAnKScgOiAnJyk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogRmluZHMgdGhlIGNhcHR1cmUgdGhlIG9yZGVyIGxpbmUgYmVsb25ncyB0byBvciByZXR1cm5zIG51bGwgaWYgdGhlIG9yZGVyIGxpbmUgaXMgbm90IGNhcHR1cmVkLlxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gb3JkZXJMaW5lIENvbnRhaW5zIG9yZGVyIGxpbmUgZGF0YS5cblx0ICpcblx0ICogQHJldHVybiB7T2JqZWN0fG51bGx9XG5cdCAqL1xuXHRjb25zdCBmaW5kQ2FwdHVyZVdpdGhPcmRlckxpbmUgPSAob3JkZXJMaW5lKSA9PiB7XG5cdFx0cmV0dXJuIEtsYXJuYUh1Yi5Db25maWcua2xhcm5hT3JkZXIuY2FwdHVyZXMuZmluZCgoY2FwdHVyZSkgPT4ge1xuXHRcdFx0cmV0dXJuIGNhcHR1cmUub3JkZXJfbGluZXMuZmluZCgoY2FwdHVyZWRPcmRlckxpbmUpID0+IHtcbiAgICAgICAgICAgICAgICByZXR1cm4gY2FwdHVyZWRPcmRlckxpbmUubWVyY2hhbnRfZGF0YSAhPT0gbnVsbFxuICAgICAgICAgICAgICAgICAgICAgICA/IGNhcHR1cmVkT3JkZXJMaW5lLm1lcmNoYW50X2RhdGEgPT09IG9yZGVyTGluZS5tZXJjaGFudF9kYXRhXG4gICAgICAgICAgICAgICAgICAgICAgIDogY2FwdHVyZWRPcmRlckxpbmUucmVmZXJlbmNlID09PSBvcmRlckxpbmUucmVmZXJlbmNlO1xuXHRcdFx0fSk7XG5cdFx0fSk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogSW5pdGlhbGl6ZXMgdGhlIG1vZHVsZS5cblx0ICpcblx0ICogQHByaXZhdGVcblx0ICovXG5cdGNvbnN0IGluaXQgPSAoKSA9PiB7XG5cdFx0Y29uc3Qga2xhcm5hT3JkZXIgPSBLbGFybmFIdWIuQ29uZmlnLmtsYXJuYU9yZGVyO1xuXHRcdFxuXHRcdGlmICgha2xhcm5hT3JkZXIpIHtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cdFx0XG5cdFx0Ly8gRmlsdGVyIGNhcHR1cmVkIG9yZGVyIGxpbmVzIG91dC5cblx0XHRjb25zdCBvcmRlckxpbmVzID0ga2xhcm5hT3JkZXIub3JkZXJfbGluZXMuZmlsdGVyKChvcmRlckxpbmUpID0+IHtcblx0XHRcdGNvbnN0IGNhcHR1cmUgPSBmaW5kQ2FwdHVyZVdpdGhPcmRlckxpbmUob3JkZXJMaW5lKTtcblx0XHRcdHJldHVybiBvcmRlckxpbmUucmVmZXJlbmNlICE9PSAnb3RfY291cG9uJyAmJiBvcmRlckxpbmUucmVmZXJlbmNlICE9PSAnb3RfZGlzY291bnQnICYmIG9yZGVyTGluZS5yZWZlcmVuY2UgIT09ICdvdF9ndicgJiYgIWNhcHR1cmU7XG5cdFx0fSk7XG5cdFx0XG5cdFx0Y29uc3QgJGJ1dHRvbkRyb3Bkb3duID0gJCgnPGRpdi8+Jywge1xuXHRcdFx0J2RhdGEtZ3gtd2lkZ2V0JzogJ2J1dHRvbl9kcm9wZG93bicsXG5cdFx0XHQnaHRtbCc6IFtcblx0XHRcdFx0JCgnPGRpdi8+Jywge1xuXHRcdFx0XHRcdCdpZCc6ICdrbGFybmEtc2VsZWN0ZWQtZW50cmllcycsXG5cdFx0XHRcdFx0J2RhdGEtdXNlLWJ1dHRvbl9kcm9wZG93bic6ICd0cnVlJyxcblx0XHRcdFx0XHQnY3NzJzoge1xuXHRcdFx0XHRcdFx0J21hcmdpbi1sZWZ0JzogJzAnLFxuXHRcdFx0XHRcdFx0J21hcmdpbi1ib3R0b20nOiAnMCdcblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdCdodG1sJzogW1xuXHRcdFx0XHRcdFx0JCgnPGJ1dHRvbi8+Jywge1xuXHRcdFx0XHRcdFx0XHQndGV4dCc6ICcwICcgKyBLbGFybmFIdWIuQ29uZmlnLmxhbmcuU0VMRUNURURfRU5UUklFUyxcblx0XHRcdFx0XHRcdFx0J29uJzoge1xuXHRcdFx0XHRcdFx0XHRcdCdjbGljayc6IChldmVudCkgPT4ge1xuXHRcdFx0XHRcdFx0XHRcdFx0JChldmVudC50YXJnZXQpLm5leHQoJ2J1dHRvbicpLnRyaWdnZXIoJ2NsaWNrJyk7XG5cdFx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHR9KSxcblx0XHRcdFx0XHRcdCQoJzx1bC8+Jywge1xuXHRcdFx0XHRcdFx0XHQnaHRtbCc6IG9yZGVyTGluZXMubWFwKChvcmRlckxpbmUpID0+IHtcblx0XHRcdFx0XHRcdFx0XHRyZXR1cm4gJCgnPGxpLz4nLCB7XG5cdFx0XHRcdFx0XHRcdFx0XHQnaHRtbCc6IFtcblx0XHRcdFx0XHRcdFx0XHRcdFx0JCgnPHNwYW4vPicsIHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnaHRtbCc6IFtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCQoJzxpbnB1dC8+Jywge1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQndHlwZSc6ICdjaGVja2JveCcsXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdkYXRhJzogb3JkZXJMaW5lLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnY3NzJzoge1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdoZWlnaHQnOiAnYXV0bycsXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J21hcmdpbi10b3AnOiAnNXB4J1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQncHJvcCc6IHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnY2hlY2tlZCc6IHRydWVcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0fSxcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J29uJzoge1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdjbGljayc6IChldmVudCkgPT4ge1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0ZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR1cGRhdGVCdXR0b25Ecm9wZG93blRleHQoJGJ1dHRvbkRyb3Bkb3duKTtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0pLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JCgnPHNwYW4vPicsIHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3RleHQnOiBnZXRPcmRlckxpbmVEaXNwbGF5TmFtZShvcmRlckxpbmUpLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnY3NzJzoge1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdkaXNwbGF5JzogJ2lubGluZS1ibG9jaycsXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3BhZGRpbmctbGVmdCc6ICc1cHgnXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCdvbic6IHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQnY2xpY2snOiAoZXZlbnQpID0+IHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JChldmVudC50YXJnZXQpLnByZXYoJ2lucHV0OmNoZWNrYm94JykudHJpZ2dlcignY2xpY2snKTtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XVxuXHRcdFx0XHRcdFx0XHRcdFx0XHR9KVxuXHRcdFx0XHRcdFx0XHRcdFx0XVxuXHRcdFx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHR9KVxuXHRcdFx0XHRcdF1cblx0XHRcdFx0fSlcblx0XHRcdF1cblx0XHR9KTtcblx0XHRcblx0XHR1cGRhdGVCdXR0b25Ecm9wZG93blRleHQoJGJ1dHRvbkRyb3Bkb3duKTtcblx0XHRcblx0XHRjb25zdCAkYWxsRW50cmllc0NhcHR1cmVkID0gJCgnPHNwYW4vPicsIHtcblx0XHRcdCd0ZXh0JzogS2xhcm5hSHViLkNvbmZpZy5sYW5nLkFMTF9FTlRSSUVTX0NBUFRVUkVEXG5cdFx0fSk7XG5cdFx0XG5cdFx0Y29uc3QgJGZvcm0gPSAkKCcjdXBkYXRlX29yZGVyc19zdGF0dXNfZm9ybScpXG5cdFx0Y29uc3QgJHNlbGVjdCA9ICRmb3JtLmZpbmQoJ1tuYW1lPVwiZ21fc3RhdHVzXCJdJyk7XG5cdFx0XG5cdFx0Ly8gQWRkIHRoZSBub3RpZnkta2xhcm5hLWh1YiBjaGVja2JveCAoaGlkZGVuIGJ5IGRlZmF1bHQpLlxuXHRcdGNvbnN0ICRzb3VyY2VDb250cm9sR3JvdXAgPSAkZm9ybS5maW5kKCdbbmFtZT1cImdtX25vdGlmeV9rbGFybmFcIl0nKS5jbG9zZXN0KCcuY29udHJvbC1ncm91cCcpO1xuXHRcdGNvbnN0ICRjb250cm9sR3JvdXAgPSAkc291cmNlQ29udHJvbEdyb3VwLmNsb25lKHRydWUpO1xuXHRcdCRjb250cm9sR3JvdXAuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuXHRcdFxuXHRcdGNvbnN0ICRsYWJlbCA9ICRjb250cm9sR3JvdXAuZmluZCgnbGFiZWwnKTtcblx0XHQkbGFiZWwudGV4dChLbGFybmFIdWIuQ29uZmlnLmxhbmcuS0xBUk5BX0NBUFRVUkUpO1xuXHRcdCRjb250cm9sR3JvdXAuZmluZCgnLnNpbmdsZS1jaGVja2JveCcpLnJlbW92ZSgpO1xuXHRcdFxuXHRcdGlmIChvcmRlckxpbmVzLmxlbmd0aCkge1xuXHRcdFx0JGNvbnRyb2xHcm91cC5hcHBlbmQoJGJ1dHRvbkRyb3Bkb3duKTtcblx0XHRcdGd4LndpZGdldHMuaW5pdCgkY29udHJvbEdyb3VwKTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0JGNvbnRyb2xHcm91cC5hcHBlbmQoJGFsbEVudHJpZXNDYXB0dXJlZCk7XG5cdFx0fVxuXHRcdFxuXHRcdCRjb250cm9sR3JvdXAuaW5zZXJ0QmVmb3JlKCRzb3VyY2VDb250cm9sR3JvdXAubmV4dCgpKTtcblx0XHRcblx0XHQvLyBCaW5kIHN0YXR1cyB0eXBlIGNoYW5nZSBldmVudCwgdG9nZ2xlIHZpc2liaWxpdHkuXG5cdFx0JHNlbGVjdC5vbignY2hhbmdlJywgKGV2ZW50KSA9PiB7XG5cdFx0XHRpZiAoZXZlbnQudGFyZ2V0LnZhbHVlICYmIGV2ZW50LnRhcmdldC52YWx1ZSA9PSBLbGFybmFIdWIuQ29uZmlnLm9yZGVyU3RhdHVzU2hpcHBlZCkge1xuXHRcdFx0XHQkY29udHJvbEdyb3VwLnJlbW92ZUNsYXNzKCdoaWRkZW4nKTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdCRjb250cm9sR3JvdXAuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuXHRcdFx0fVxuXHRcdH0pO1xuXHR9O1xuXHRcblx0S2xhcm5hSHViLm9uKCdtb2R1bGUnLCAoZXZlbnQpID0+IHtcblx0XHRpZiAoZXZlbnQubW9kdWxlICE9PSAnZXh0ZW5kX29yZGVyX3N0YXR1c19tb2RhbCcpIHtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cdFx0aW5pdCgpO1xuXHR9KTtcbn0pKCk7XG4iXX0=
