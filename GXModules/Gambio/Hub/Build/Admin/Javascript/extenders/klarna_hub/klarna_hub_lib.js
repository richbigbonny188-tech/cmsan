'use strict';

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
(function () {
	'use strict';

	/**
  * Legacy pages flag.
  *
  * @type {Boolean}
  */

	var legacy = !$.fn.modal;

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
	var getUrlParameter = function getUrlParameter(name, url) {
		if (!url) {
			url = window.location.href;
		}

		name = name.replace(/[\[\]]/g, '\\$&');

		var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');

		var results = regex.exec(url);

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
	var showMessageLegacy = function showMessageLegacy(title, message, buttons) {
		var $dialog = $('<div/>', {
			'html': [$('<div/>', {
				'html': message
			})]
		}).appendTo('body');

		if (!buttons) {
			buttons = [{
				text: KlarnaHub.Config ? KlarnaHub.Config.lang.CLOSE : 'Close',
				click: function click() {
					$dialog.dialog('close').remove();
				}
			}];
		}

		$dialog.dialog({
			autoOpen: true,
			width: 500,
			height: 'auto',
			resizable: false,
			modal: true,
			title: title,
			dialogClass: 'gx-container',
			buttons: buttons
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
	var showMessageModern = function showMessageModern(title, message, buttons) {
		var html = '<div class="modal fade" tabindex="-1" role="dialog">\n\t\t\t\t\t\t<div class="modal-dialog">\n\t\t\t\t\t\t\t<div class="modal-content">\n\t\t\t\t\t\t\t\t<div class="modal-header">\n\t\t\t\t\t\t\t\t\t<button type="button" class="close" data-dismiss="modal" aria-label="Close">\n\t\t\t\t\t\t\t\t\t\t<span aria-hidden="true">&times;</span>\n\t\t\t\t\t\t\t\t\t</button>\n\t\t\t\t\t\t\t\t\t<h4 class="modal-title">' + title + '</h4>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class="modal-body">\n\t\t\t\t\t                ' + message + '\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class="modal-footer"></div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>';

		var $modal = $(html).appendTo('body');

		if (!buttons) {
			buttons = [{
				title: KlarnaHub.Config ? KlarnaHub.Config.lang.CLOSE : 'Close',
				class: 'btn btn-default',
				callback: function callback() {
					return $modal.modal('hide');
				}
			}];
		}

		buttons.forEach(function (button) {
			var $button = $('<button/>', {
				'text': button.title,
				'class': button.class || 'btn btn-default'
			}).appendTo($modal.find('.modal-footer'));

			if (button.callback) {
				$button.on('click', button.callback);
			}
		});

		$modal.on('hidden.bs.modal', function () {
			return $modal.remove();
		});

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
	var showMessage = legacy ? showMessageLegacy : showMessageModern;

	/**
  * Handles KlarnaHub related errors.
  *
  * @param {Error} error Error object.
  * 
  * @public 
  */
	var handleError = function handleError(error) {
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
	var getSelectedKlarnaHubOrderNumbers = function getSelectedKlarnaHubOrderNumbers() {
		var $table = $('.orders.overview .table-main');

		if (!$table.length) {
			throw new Error('This method can only be used in the orders overview page.');
		}

		var moduleCodes = ['KlarnaHub', 'KlarnaPaynowHub', 'KlarnaPaylaterHub', 'KlarnaSliceitHub', 'KlarnaBanktransferHub'];

		var selectedKlarnaHubOrders = [];

		$table.find('tbody input:checkbox:checked').each(function (index, checkbox) {
			var _$$parents$data = $(checkbox).parents('tr').data(),
			    id = _$$parents$data.id,
			    gambioHubModule = _$$parents$data.gambioHubModule;

			if (moduleCodes.includes(gambioHubModule)) {
				selectedKlarnaHubOrders.push(id);
			}
		});

		return selectedKlarnaHubOrders;
	};

	// Export
	window.KlarnaHub = window.KlarnaHub || {};
	window.KlarnaHub.Lib = Object.assign({}, {
		getUrlParameter: getUrlParameter,
		showMessage: showMessage,
		handleError: handleError,
		getSelectedKlarnaHubOrderNumbers: getSelectedKlarnaHubOrderNumbers
	}, window.KlarnaHub.Lib);
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2tsYXJuYV9odWIva2xhcm5hX2h1Yl9saWIuanMiXSwibmFtZXMiOlsibGVnYWN5IiwiJCIsImZuIiwibW9kYWwiLCJnZXRVcmxQYXJhbWV0ZXIiLCJuYW1lIiwidXJsIiwid2luZG93IiwibG9jYXRpb24iLCJocmVmIiwicmVwbGFjZSIsInJlZ2V4IiwiUmVnRXhwIiwicmVzdWx0cyIsImV4ZWMiLCJkZWNvZGVVUklDb21wb25lbnQiLCJzaG93TWVzc2FnZUxlZ2FjeSIsInRpdGxlIiwibWVzc2FnZSIsImJ1dHRvbnMiLCIkZGlhbG9nIiwiYXBwZW5kVG8iLCJ0ZXh0IiwiS2xhcm5hSHViIiwiQ29uZmlnIiwibGFuZyIsIkNMT1NFIiwiY2xpY2siLCJkaWFsb2ciLCJyZW1vdmUiLCJhdXRvT3BlbiIsIndpZHRoIiwiaGVpZ2h0IiwicmVzaXphYmxlIiwiZGlhbG9nQ2xhc3MiLCJzaG93TWVzc2FnZU1vZGVybiIsImh0bWwiLCIkbW9kYWwiLCJjbGFzcyIsImNhbGxiYWNrIiwiZm9yRWFjaCIsIiRidXR0b24iLCJidXR0b24iLCJmaW5kIiwib24iLCJzaG93TWVzc2FnZSIsImhhbmRsZUVycm9yIiwiZXJyb3IiLCJkZWJ1ZyIsImNvbnNvbGUiLCJncm91cCIsImdyb3VwRW5kIiwiVU5FWFBFQ1RFRF9SRVFVRVNUX0VSUk9SIiwiZ2V0U2VsZWN0ZWRLbGFybmFIdWJPcmRlck51bWJlcnMiLCIkdGFibGUiLCJsZW5ndGgiLCJFcnJvciIsIm1vZHVsZUNvZGVzIiwic2VsZWN0ZWRLbGFybmFIdWJPcmRlcnMiLCJlYWNoIiwiaW5kZXgiLCJjaGVja2JveCIsInBhcmVudHMiLCJkYXRhIiwiaWQiLCJnYW1iaW9IdWJNb2R1bGUiLCJpbmNsdWRlcyIsInB1c2giLCJMaWIiLCJPYmplY3QiLCJhc3NpZ24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7OztBQU9BLENBQUMsWUFBVztBQUNYOztB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QUNDLEtBQUQsRUFBUUMsUUFBUixFQUFxQjtBQUFBLHlCQUN2QzVELEVBQUU0RCxRQUFGLEVBQVlDLE9BQVosQ0FBb0IsSUFBcEIsRUFBMEJDLElBQTFCLEVBRHVDO0FBQUEsT0FDOURDLEVBRDhELG1CQUM5REEsRUFEOEQ7QUFBQSxPQUMxREMsZUFEMEQsbUJBQzFEQSxlQUQwRDs7QUFHckUsT0FBSVIsWUFBWVMsUUFBWixDQUFxQkQsZUFBckIsQ0FBSixFQUEyQztBQUMxQ1AsNEJBQXdCUyxJQUF4QixDQUE2QkgsRUFBN0I7QUFDQTtBQUNELEdBTkQ7O0FBUUEsU0FBT04sdUJBQVA7QUFDQSxFQTFCRDs7QUE0QkE7QUFDQW5ELFFBQU9nQixTQUFQLEdBQW1CaEIsT0FBT2dCLFNBQVAsSUFBb0IsRUFBdkM7QUFDQWhCLFFBQU9nQixTQUFQLENBQWlCNkMsR0FBakIsR0FBdUJDLE9BQU9DLE1BQVAsQ0FBYyxFQUFkLEVBQWtCO0FBQ3hDbEUsa0NBRHdDO0FBRXhDeUMsMEJBRndDO0FBR3hDQywwQkFId0M7QUFJeENPO0FBSndDLEVBQWxCLEVBS3BCOUMsT0FBT2dCLFNBQVAsQ0FBaUI2QyxHQUxHLENBQXZCO0FBTUEsQ0FyT0QiLCJmaWxlIjoiQWRtaW4vSmF2YXNjcmlwdC9leHRlbmRlcnMva2xhcm5hX2h1Yi9rbGFybmFfaHViX2xpYi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4ga2xhcm5hX2h1Yl9saWIuanMgMjAxNy0xMS0wMlxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTcgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogbGFybmFIdWIgTGliIE1vZHVsZVxuICogXG4gKiBIZWxwZXIgbWV0aG9kcyBmb3IgS2xhcm5hSHViIG1vZHVsZXMuIFxuICogXG4gKiBAbW9kdWxlIEtsYXJuYUh1Yi5MaWJcbiAqL1xuKGZ1bmN0aW9uKCkge1xuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvKipcblx0ICogTGVnYWN5IHBhZ2VzIGZsYWcuXG5cdCAqXG5cdCAqIEB0eXBlIHtCb29sZWFufVxuXHQgKi9cblx0Y29uc3QgbGVnYWN5ID0gISQuZm4ubW9kYWw7XG5cdFxuXHQvKipcblx0ICogUmV0dXJucyBVUkwgR0VUIHBhcmFtZXRlciB2YWx1ZS5cblx0ICpcblx0ICogQHBhcmFtIHtTdHJpbmd9IG5hbWUgVmFyaWFibGUgbmFtZSB0byBiZSByZXR1cm5lZC5cblx0ICogQHBhcmFtIHtTdHJpbmd9IHVybCBVUkwgdG8gYmUgcGFyc2VkLlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtTdHJpbmd9XG5cdCAqXG5cdCAqIEBwdWJsaWNcblx0ICovXG5cdGNvbnN0IGdldFVybFBhcmFtZXRlciA9IChuYW1lLCB1cmwpID0+IHtcblx0XHRpZiAoIXVybCkge1xuXHRcdFx0dXJsID0gd2luZG93LmxvY2F0aW9uLmhyZWY7XG5cdFx0fVxuXHRcdFxuXHRcdG5hbWUgPSBuYW1lLnJlcGxhY2UoL1tcXFtcXF1dL2csICdcXFxcJCYnKTtcblx0XHRcblx0XHRjb25zdCByZWdleCA9IG5ldyBSZWdFeHAoJ1s/Jl0nICsgbmFtZSArICcoPShbXiYjXSopfCZ8I3wkKScpO1xuXHRcdFxuXHRcdGNvbnN0IHJlc3VsdHMgPSByZWdleC5leGVjKHVybCk7XG5cdFx0XG5cdFx0aWYgKCFyZXN1bHRzKSB7XG5cdFx0XHRyZXR1cm4gbnVsbDtcblx0XHR9XG5cdFx0XG5cdFx0aWYgKCFyZXN1bHRzWzJdKSB7XG5cdFx0XHRyZXR1cm4gJyc7XG5cdFx0fVxuXHRcdFxuXHRcdHJldHVybiBkZWNvZGVVUklDb21wb25lbnQocmVzdWx0c1syXS5yZXBsYWNlKC9cXCsvZywgJyAnKSk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogU2hvd3MgbWVzc2FnZSBkaWFsb2cgdG8gdGhlIHVzZXIgKGxlZ2FjeSkuXG5cdCAqXG5cdCAqIFRoaXMgbWV0aG9kIG1ha2VzIHVzZSBvZiB0aGUgalF1ZXJ5IFVJIG1vZGFsIGNvbXBvbmVudC5cblx0ICpcblx0ICogQHBhcmFtIHtTdHJpbmd9IHRpdGxlIERpYWxvZyB0aXRsZS5cblx0ICogQHBhcmFtIHtTdHJpbmd9IG1lc3NhZ2UgRGlhbG9nIG1lc3NhZ2UuXG5cdCAqIEBwYXJhbSB7T2JqZWN0W119IFtidXR0b25zXSBEaWFsb2cgYnV0dG9ucyAodXNlIGpRdWVyeSBVSSBkaWFsb2cgZm9ybWF0KS5cblx0ICpcblx0ICogQHJldHVybiB7alF1ZXJ5fSBSZXR1cm5zIGRpYWxvZyBqUXVlcnkgc2VsZWN0b3IuXG5cdCAqXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHRjb25zdCBzaG93TWVzc2FnZUxlZ2FjeSA9ICh0aXRsZSwgbWVzc2FnZSwgYnV0dG9ucykgPT4ge1xuXHRcdGNvbnN0ICRkaWFsb2cgPSAkKCc8ZGl2Lz4nLCB7XG5cdFx0XHQnaHRtbCc6IFtcblx0XHRcdFx0JCgnPGRpdi8+Jywge1xuXHRcdFx0XHRcdCdodG1sJzogbWVzc2FnZVxuXHRcdFx0XHR9KVxuXHRcdFx0XVxuXHRcdH0pXG5cdFx0XHQuYXBwZW5kVG8oJ2JvZHknKTtcblx0XHRcblx0XHRpZiAoIWJ1dHRvbnMpIHtcblx0XHRcdGJ1dHRvbnMgPSBbXG5cdFx0XHRcdHtcblx0XHRcdFx0XHR0ZXh0OiBLbGFybmFIdWIuQ29uZmlnID8gS2xhcm5hSHViLkNvbmZpZy5sYW5nLkNMT1NFIDogJ0Nsb3NlJyxcblx0XHRcdFx0XHRjbGljazogKCkgPT4ge1xuXHRcdFx0XHRcdFx0JGRpYWxvZ1xuXHRcdFx0XHRcdFx0XHQuZGlhbG9nKCdjbG9zZScpXG5cdFx0XHRcdFx0XHRcdC5yZW1vdmUoKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblx0XHRcdF07XG5cdFx0fVxuXHRcdFxuXHRcdCRkaWFsb2cuZGlhbG9nKHtcblx0XHRcdGF1dG9PcGVuOiB0cnVlLFxuXHRcdFx0d2lkdGg6IDUwMCxcblx0XHRcdGhlaWdodDogJ2F1dG8nLFxuXHRcdFx0cmVzaXphYmxlOiBmYWxzZSxcblx0XHRcdG1vZGFsOiB0cnVlLFxuXHRcdFx0dGl0bGUsXG5cdFx0XHRkaWFsb2dDbGFzczogJ2d4LWNvbnRhaW5lcicsXG5cdFx0XHRidXR0b25zXG5cdFx0fSk7XG5cdFx0XG5cdFx0cmV0dXJuICRkaWFsb2c7XG5cdH07XG5cdFxuXHQvKipcblx0ICogU2hvd3MgbWVzc2FnZSBkaWFsb2cgdG8gdGhlIHVzZXIgKG1vZGVybikuXG5cdCAqXG5cdCAqIFRoaXMgbWV0aG9kIG1ha2VzIHVzZSBvZiB0aGUgQm9vdHN0cmFwIG1vZGFsIGNvbXBvbmVudC5cblx0ICpcblx0ICogQHBhcmFtIHtTdHJpbmd9IHRpdGxlIERpYWxvZyB0aXRsZS5cblx0ICogQHBhcmFtIHtTdHJpbmd9IG1lc3NhZ2UgRGlhbG9nIG1lc3NhZ2UuXG5cdCAqIEBwYXJhbSB7T2JqZWN0W119IFtidXR0b25zXSBEaWFsb2cgYnV0dG9ucyAodXNlIGpRdWVyeSBVSSBkaWFsb2cgZm9ybWF0KS5cblx0ICpcblx0ICogQHJldHVybiB7alF1ZXJ5fSBSZXR1cm5zIGRpYWxvZyBqUXVlcnkgc2VsZWN0b3IuXG5cdCAqXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHRjb25zdCBzaG93TWVzc2FnZU1vZGVybiA9ICh0aXRsZSwgbWVzc2FnZSwgYnV0dG9ucykgPT4ge1xuXHRcdGNvbnN0IGh0bWwgPSBgPGRpdiBjbGFzcz1cIm1vZGFsIGZhZGVcIiB0YWJpbmRleD1cIi0xXCIgcm9sZT1cImRpYWxvZ1wiPlxuXHRcdFx0XHRcdFx0PGRpdiBjbGFzcz1cIm1vZGFsLWRpYWxvZ1wiPlxuXHRcdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwibW9kYWwtY29udGVudFwiPlxuXHRcdFx0XHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJtb2RhbC1oZWFkZXJcIj5cblx0XHRcdFx0XHRcdFx0XHRcdDxidXR0b24gdHlwZT1cImJ1dHRvblwiIGNsYXNzPVwiY2xvc2VcIiBkYXRhLWRpc21pc3M9XCJtb2RhbFwiIGFyaWEtbGFiZWw9XCJDbG9zZVwiPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHQ8c3BhbiBhcmlhLWhpZGRlbj1cInRydWVcIj4mdGltZXM7PC9zcGFuPlxuXHRcdFx0XHRcdFx0XHRcdFx0PC9idXR0b24+XG5cdFx0XHRcdFx0XHRcdFx0XHQ8aDQgY2xhc3M9XCJtb2RhbC10aXRsZVwiPiR7dGl0bGV9PC9oND5cblx0XHRcdFx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwibW9kYWwtYm9keVwiPlxuXHRcdFx0XHRcdCAgICAgICAgICAgICAgICAke21lc3NhZ2V9XG5cdFx0XHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0XHRcdFx0PGRpdiBjbGFzcz1cIm1vZGFsLWZvb3RlclwiPjwvZGl2PlxuXHRcdFx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0XHRcdDwvZGl2PlxuXHRcdFx0XHRcdDwvZGl2PmA7XG5cdFx0XG5cdFx0Y29uc3QgJG1vZGFsID0gJChodG1sKS5hcHBlbmRUbygnYm9keScpO1xuXHRcdFxuXHRcdGlmICghYnV0dG9ucykge1xuXHRcdFx0YnV0dG9ucyA9IFtcblx0XHRcdFx0e1xuXHRcdFx0XHRcdHRpdGxlOiBLbGFybmFIdWIuQ29uZmlnID8gS2xhcm5hSHViLkNvbmZpZy5sYW5nLkNMT1NFIDogJ0Nsb3NlJyxcblx0XHRcdFx0XHRjbGFzczogJ2J0biBidG4tZGVmYXVsdCcsXG5cdFx0XHRcdFx0Y2FsbGJhY2s6ICgpID0+ICRtb2RhbC5tb2RhbCgnaGlkZScpXG5cdFx0XHRcdH1cblx0XHRcdF07XG5cdFx0fVxuXHRcdFxuXHRcdGJ1dHRvbnMuZm9yRWFjaChidXR0b24gPT4ge1xuXHRcdFx0Y29uc3QgJGJ1dHRvbiA9ICQoJzxidXR0b24vPicsIHtcblx0XHRcdFx0J3RleHQnOiBidXR0b24udGl0bGUsXG5cdFx0XHRcdCdjbGFzcyc6IGJ1dHRvbi5jbGFzcyB8fCAnYnRuIGJ0bi1kZWZhdWx0J1xuXHRcdFx0fSlcblx0XHRcdFx0LmFwcGVuZFRvKCRtb2RhbC5maW5kKCcubW9kYWwtZm9vdGVyJykpO1xuXHRcdFx0XG5cdFx0XHRpZiAoYnV0dG9uLmNhbGxiYWNrKSB7XG5cdFx0XHRcdCRidXR0b24ub24oJ2NsaWNrJywgYnV0dG9uLmNhbGxiYWNrKTtcblx0XHRcdH1cblx0XHR9KTtcblx0XHRcblx0XHQkbW9kYWwub24oJ2hpZGRlbi5icy5tb2RhbCcsICgpID0+ICRtb2RhbC5yZW1vdmUoKSk7XG5cdFx0XG5cdFx0JG1vZGFsLm1vZGFsKCdzaG93Jyk7XG5cdFx0XG5cdFx0cmV0dXJuICRtb2RhbDtcblx0fTtcblx0XG5cdFxuXHQvKipcblx0ICogU2hvd3MgbWVzc2FnZSBkaWFsb2cgdG8gdGhlIHVzZXIuXG5cdCAqXG5cdCAqIEBwYXJhbSB7U3RyaW5nfSB0aXRsZSBEaWFsb2cgdGl0bGUuXG5cdCAqIEBwYXJhbSB7U3RyaW5nfSBtZXNzYWdlIERpYWxvZyBtZXNzYWdlLlxuXHQgKiBAcGFyYW0ge09iamVjdFtdfSBbYnV0dG9uc10gRGlhbG9nIGJ1dHRvbnMgKHVzZSBqUXVlcnkgVUkgZGlhbG9nIGZvcm1hdCkuXG5cdCAqXG5cdCAqIEBwdWJsaWNcblx0ICovXG5cdGNvbnN0IHNob3dNZXNzYWdlID0gbGVnYWN5ID8gc2hvd01lc3NhZ2VMZWdhY3kgOiBzaG93TWVzc2FnZU1vZGVybjtcblx0XG5cdC8qKlxuXHQgKiBIYW5kbGVzIEtsYXJuYUh1YiByZWxhdGVkIGVycm9ycy5cblx0ICpcblx0ICogQHBhcmFtIHtFcnJvcn0gZXJyb3IgRXJyb3Igb2JqZWN0LlxuXHQgKiBcblx0ICogQHB1YmxpYyBcblx0ICovXG5cdGNvbnN0IGhhbmRsZUVycm9yID0gKGVycm9yKSA9PiB7XG5cdFx0aWYgKEtsYXJuYUh1Yi5Db25maWcgJiYgIUtsYXJuYUh1Yi5Db25maWcuZGVidWcpIHtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cdFx0XG5cdFx0Y29uc29sZS5ncm91cCgnS2xhcm5hSHViIEVycm9yJyk7XG5cdFx0Y29uc29sZS5lcnJvcighS2xhcm5hSHViLkNvbmZpZyA/ICdVbmV4cGVjdGVkIGVycm9yIGR1cmluZyBLbGFybmFIdWIgaW5pdGlhbGl6YXRpb24uJyA6ICdBbiB1bmV4cGVjdGVkIGVycm9yIG9jY3VycmVkLicpO1xuXHRcdGNvbnNvbGUuZXJyb3IoZXJyb3IpO1xuXHRcdGNvbnNvbGUuZ3JvdXBFbmQoKTtcblx0XHRcblx0XHRzaG93TWVzc2FnZSgnS2xhcm5hJywgS2xhcm5hSHViLkNvbmZpZy5sYW5nLlVORVhQRUNURURfUkVRVUVTVF9FUlJPUik7XG5cdH07XG5cdFxuXHQvKipcblx0ICogUmV0dXJucyBzZWxlY3RlZCBLbGFybmFIdWIgb3JkZXIgbnVtYmVycyAod29ya3Mgb25seSBpbiBvcmRlcnMgb3ZlcnZpZXcpLiBcblx0ICogXG5cdCAqIEByZXR1cm4ge051bWJlcltdfSBcblx0ICogXG5cdCAqIEBwdWJsaWNcblx0ICovXG5cdGNvbnN0IGdldFNlbGVjdGVkS2xhcm5hSHViT3JkZXJOdW1iZXJzID0gKCkgPT4ge1xuXHRcdGNvbnN0ICR0YWJsZSA9ICQoJy5vcmRlcnMub3ZlcnZpZXcgLnRhYmxlLW1haW4nKTsgXG5cdFx0XG5cdFx0aWYgKCEkdGFibGUubGVuZ3RoKSB7XG5cdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ1RoaXMgbWV0aG9kIGNhbiBvbmx5IGJlIHVzZWQgaW4gdGhlIG9yZGVycyBvdmVydmlldyBwYWdlLicpOyBcblx0XHR9XG5cdFx0XG5cdFx0Y29uc3QgbW9kdWxlQ29kZXMgPSBbXG5cdFx0XHQnS2xhcm5hSHViJyxcblx0XHRcdCdLbGFybmFQYXlub3dIdWInLFxuXHRcdFx0J0tsYXJuYVBheWxhdGVySHViJyxcblx0XHRcdCdLbGFybmFTbGljZWl0SHViJyxcblx0XHRcdCdLbGFybmFCYW5rdHJhbnNmZXJIdWInXG5cdFx0XTsgXG5cdFx0XG5cdFx0Y29uc3Qgc2VsZWN0ZWRLbGFybmFIdWJPcmRlcnMgPSBbXTtcblx0XHRcblx0XHQkdGFibGUuZmluZCgndGJvZHkgaW5wdXQ6Y2hlY2tib3g6Y2hlY2tlZCcpLmVhY2goKGluZGV4LCBjaGVja2JveCkgPT4ge1xuXHRcdFx0Y29uc3Qge2lkLCBnYW1iaW9IdWJNb2R1bGV9ID0gJChjaGVja2JveCkucGFyZW50cygndHInKS5kYXRhKCk7IFxuXHRcdFx0XG5cdFx0XHRpZiAobW9kdWxlQ29kZXMuaW5jbHVkZXMoZ2FtYmlvSHViTW9kdWxlKSkge1xuXHRcdFx0XHRzZWxlY3RlZEtsYXJuYUh1Yk9yZGVycy5wdXNoKGlkKTtcdFxuXHRcdFx0fVxuXHRcdH0pO1xuXHRcdFxuXHRcdHJldHVybiBzZWxlY3RlZEtsYXJuYUh1Yk9yZGVycztcblx0fVxuXHRcblx0Ly8gRXhwb3J0XG5cdHdpbmRvdy5LbGFybmFIdWIgPSB3aW5kb3cuS2xhcm5hSHViIHx8IHt9O1xuXHR3aW5kb3cuS2xhcm5hSHViLkxpYiA9IE9iamVjdC5hc3NpZ24oe30sIHtcblx0XHRnZXRVcmxQYXJhbWV0ZXIsXG5cdFx0c2hvd01lc3NhZ2UsXG5cdFx0aGFuZGxlRXJyb3IsXG5cdFx0Z2V0U2VsZWN0ZWRLbGFybmFIdWJPcmRlck51bWJlcnNcblx0fSwgd2luZG93LktsYXJuYUh1Yi5MaWIpO1xufSkoKTsiXX0=
