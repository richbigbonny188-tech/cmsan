'use strict';

/* --------------------------------------------------------------
	internetmarke.js 2018-11-08
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

$(function () {
	'use strict';

	/* -------------------------------------------------------------------------------------------------- */

	var reloadOnClose = false;

	var _initSingleForm = function _initSingleForm(orders_id) {
		var grid_x = 1,
		    grid_y = 1;

		$('#inetmarke_single_form .receiver_data input').on('change', function (e) {
			var fieldname = $(e.target).attr('name').replace(/receiver\[(.*)\]/, 'receiver_$1');
			var fieldvalue = $(e.target).val();
			$('span#' + fieldname).toggle(fieldvalue.length > 0);
			if (fieldname === 'receiver_additional' || fieldname === 'receiver_company') {
				fieldvalue += '<br>';
			}
			$('span#' + fieldname).html(fieldvalue);
		});
		$('#inetmarke_single_form .receiver_data input').trigger('change');

		$('#inetmarke_single_form select[name="receiver[country]"]').on('change', function (e) {
			var isNational = $('option[value="DEU"]:selected', e.target).length > 0,
			    countryName = $('option:selected', $(this)).text(),
			    $preferredProduct = $('select[name="productCode"] option.preferred');
			$('span#receiver_country').html(countryName);
			$('#inetmarke_single_form option.dest_national').toggle(isNational === true);
			$('#inetmarke_single_form option.dest_international').toggle(isNational === false);
			var productCode;
			if ($preferredProduct.filter(':enabled').length > 0) {
				productCode = $preferredProduct.attr('value');
			} else {
				if (isNational === true) {
					productCode = $('select[name="productCode"] option.dest_national:first').attr('value');
				} else {
					productCode = $('select[name="productCode"] option.dest_international:first').attr('value');
				}
			}
			$('select[name="productCode"]').val(productCode);
			_updatePreview();
		});
		$('#inetmarke_single_form select[name="receiver[country]"]').trigger('change');

		$('#inetmarke_single_form select[name="voucherLayout"]').on('change', function (e) {
			var isFrankingZone = $('#inetmarke_single_form select[name="voucherLayout"] option[value="FrankingZone"]:selected').length > 0;
			$('#inetmarke_single_form .sender_line').toggle(isFrankingZone === false);
			$('#inetmarke_single_form .receiver_block').toggle(isFrankingZone === false);
		});
		$('#inetmarke_single_form select[name="voucherLayout"]').trigger('change');

		$('#inetmarke_single_form select[name="pageFormatID"]').on('change', function (e) {
			var grid_dimensions_match, $pagepos_widget, $pagepos_table, $pagepos_row, $pagepos_col, row, col;
			$pagepos_widget = $('#pagepos_widget');
			grid_dimensions_match = $('#inetmarke_single_form select[name="pageFormatID"] option:selected').text().match(/.*\((\d+) x (\d+) .*/);
			grid_x = grid_dimensions_match[1];
			grid_y = grid_dimensions_match[2];
			$pagepos_widget.empty();
			//$pagepos_widget.append($('<div>grid '+grid_x+' / '+grid_y+'</div>'));
			$pagepos_table = $('<table id="pagepos_table"></table>');
			for (row = 1; row <= grid_y; row++) {
				$pagepos_row = $('<tr class="pagepos_' + row + '"></tr>');
				for (col = 1; col <= grid_x; col++) {
					$pagepos_col = $('<td class="col_' + col + '"><span>' + row + '_' + col + '</span></td>');
					$pagepos_row.append($pagepos_col);
				}
				$pagepos_table.append($pagepos_row);
			}
			$pagepos_widget.append($pagepos_table);
			$('input[name="position_labelx"]').val('1');
			$('input[name="position_labely"]').val('1');
			_initPagePosTableHandler();
			$('#pagepos_table td').first().trigger('click');
			if (grid_x == '1' && grid_y == '1') {
				$('.pagepos').css('opacity', '0.5');
			} else {
				$('.pagepos').css('opacity', '1.0');
			}
		});
		$('#inetmarke_single_form select[name="pageFormatID"]').trigger('change');

		$('#pagepos_table td').first().trigger('click');
		$('input[name="position_labelx"]').change(function (e) {
			if (parseInt($(this).val()) > grid_x) {
				$(this).val(grid_x);
			}
		});
		$('input[name="position_labely"]').change(function (e) {
			if (parseInt($(this).val()) > grid_y) {
				$(this).val(grid_y);
			}
		});
		$('input[name="position_labelx"], input[name="position_labely"]').on('change', function (e) {
			_updatePagePosTable();
		});

		$('#inetmarke_single_form .previewOption').on('change', function (e) {
			_updatePreview();
		});

		_updatePreview();
	};

	var _updatePagePosTable = function _updatePagePosTable() {
		var row, col, $tablerow, $cell;
		row = $('input[name="position_labely"]').val();
		col = $('input[name="position_labelx"]').val();
		$tablerow = $('#pagepos_table tr:nth-child(' + row + ')');
		$cell = $('td:nth-child(' + col + ')', $tablerow);
		$('#pagepos_table td').removeClass('selected');
		$cell.addClass('selected');
	};

	var _initPagePosTableHandler = function _initPagePosTableHandler() {
		$('#pagepos_table td').on('click', function (e) {
			var celltext, celltext_split, selectedCell;
			celltext = $('span', $(this)).text();
			celltext_split = celltext.match(/(\d+)_(\d+)/);
			selectedCell = {
				x: celltext_split[2],
				y: celltext_split[1]
			};
			$('input[name="position_labelx"]').val(selectedCell.x);
			$('input[name="position_labely"]').val(selectedCell.y);
			$('#pagepos_table td').removeClass('selected');
			$(this).addClass('selected');
		});
	};

	var _initCredentialsForm = function _initCredentialsForm() {
		$('#inetmarke_enter_credentials').on('submit', _credentialsSubmitHandler);
		$('#tos_accepted').on('change', function (e) {
			var accepted = $('#tos_accepted').get(0).checked;
			if (accepted === true) {
				$('#submit_session_credentials').removeAttr('disabled');
			} else {
				$('#submit_session_credentials').attr('disabled', 'disabled');
			}
		});
		$('#tos_accepted').trigger('change');
	};

	var _credentialsSubmitHandler = function _credentialsSubmitHandler(e) {
		var formdata = $(this).serialize();
		$.ajax({
			type: 'POST',
			url: jse.core.config.get('appUrl') + '/admin/admin.php?do=InternetMarke/SetSessionCredentials',
			data: formdata,
			dataType: 'json'
		}).done(function (data) {
			if (data.result === 'OK') {
				$('#inetmarke_modal_content').empty().addClass('inetmarke_loading');
				$('#inetmarke_modal_content').load('admin.php?do=InternetMarke/CreateLabelForm&orders_id=' + data.orders_id, function (responseText, textStatus, jqXHR) {
					$('#inetmarke_modal_content').removeClass('inetmarke_loading');
					if ($('#inetmarke_single_form').length > 0) {
						_initSingleForm();
					}
				});
			} else {
				alert(data.error_message);
			}
		}).fail(function (data) {
			alert(jse.core.lang.translate('submit_error', 'internetmarke'));
		});

		return false;
	};

	var _singleFormSubmitHandler = function _singleFormSubmitHandler(e) {
		var formdata = $('#inetmarke_single_form').serialize();
		$('#inetmarke_modal_content').empty().addClass('inetmarke_loading');
		$.ajax({
			type: 'POST',
			url: jse.core.config.get('appUrl') + '/admin/admin.php?do=InternetMarke/CreateLabelFormSubmit',
			data: formdata,
			dataType: 'json'
		}).done(function (data) {
			$('#inetmarke_modal_content').removeClass('inetmarke_loading');
			if (data.result === 'OK') {
				$('#inetmarke_modal_content').load('admin.php?do=InternetMarke/ListVouchers&template_version=2&orders_id=' + data.orders_id, function () {
					_initVoucherList();
					$('.inetmarke_vouchers a.iframedl:last').trigger('click');
					reloadOnClose = true;
				});
			} else {
				if (data.error_message) {
					alert(data.error_message);
				} else {
					alert(jse.core.lang.translate('submit_error', 'internetmarke'));
				}
				$('#internetmarke_modal').dialog('close');
			}
		}).fail(function (data) {
			if (data.error_message) {
				alert(data.error_message);
			} else {
				alert(jse.core.lang.translate('submit_error', 'internetmarke'));
			}
			$('#internetmarke_modal').dialog('close');
		});

		return false;
	};

	var _shopPreviewHandler = function _shopPreviewHandler(e) {
		e.preventDefault();
		_updatePreview();
		return false;
	};

	var _updatePreview = function _updatePreview() {
		if ($('select[name="productCode"]').val() === null) {
			var firstNonDisabledProductCode = $('select[name="productCode"] option').not(':disabled').first().val();
			$('select[name="productCode"]').val(firstNonDisabledProductCode);
		}
		var formdata = $('#inetmarke_single_form').serialize();
		$.ajax({
			type: 'POST',
			url: jse.core.config.get('appUrl') + '/admin/admin.php?do=InternetMarke/PreviewVoucher',
			data: formdata,
			dataType: 'json'
		}).done(function (data) {
			if (data.result === 'OK') {
				if ($('#inetmarke_preview img').length > 0) {
					$('#inetmarke_preview img').attr('src', data.previewlink);
				} else {
					var previewImg = $('<img src="' + data.previewlink + '">');
					$('#inetmarke_preview').empty().append(previewImg).show();
				}
			} else {
				if (data.error_message) {
					alert(data.error_message);
				}
			}
		}).fail(function (data) {
			alert(jse.core.lang.translate('submit_error', 'internetmarke'));
		});
	};

	var _initVoucherList = function _initVoucherList() {
		$('#inetmarke_modal_content').removeClass('inetmarke_loading');
		$('#inetmarke_create_label').hide();
		$('#inetmarke_showVoucherList').hide();
		/*
  $('.inetmarke_vouchers a.iframedl').on('click', function(e) {
  	var $parent = $(this).parent(),
  	    href = $(this).attr('href');
  	e.preventDefault();
  	$('iframe', $parent).remove();
  	var $dliframe = $('<iframe src="'+href+'" style="width:0; height:0; border:none;"></iframe>');
  	$parent.append($dliframe);
  });
  */
	};

	var _openSingleFormModal = function _openSingleFormModal(event) {
		var orders_id = $(event.target).parents('tr').attr('id') || $('body').find('#gm_order_id').val();
		$('#inetmarke_modal_content').empty().addClass('inetmarke_loading');

		var internetmarke_modal_buttons = [];
		internetmarke_modal_buttons.push({
			'text': jse.core.lang.translate('close', 'buttons'),
			'class': 'btn',
			'click': function click() {
				$(this).dialog('close');
			}
		});
		internetmarke_modal_buttons.push({
			'text': jse.core.lang.translate('create_voucher', 'internetmarke'),
			'class': 'btn btn-primary',
			'click': _singleFormSubmitHandler,
			'id': 'inetmarke_create_label'
		});
		internetmarke_modal_buttons.push({
			'text': jse.core.lang.translate('show_vouchers', 'internetmarke'),
			'class': 'btn',
			'click': function click(e) {
				e.preventDefault();
				$('#inetmarke_modal_content').empty().addClass('inetmarke_loading');
				$('#inetmarke_modal_content').load('admin.php?do=InternetMarke/ListVouchers&template_version=2&orders_id=' + orders_id, _initVoucherList);
			},
			'id': 'inetmarke_showVoucherList'
		});

		$('#internetmarke_modal').dialog({
			autoOpen: false,
			modal: true,
			'title': jse.core.lang.translate('create_label', 'internetmarke'),
			'dialogClass': 'internetmarke-modal',
			buttons: internetmarke_modal_buttons,
			width: 1200,
			position: { my: 'center top', at: 'center bottom', of: '#main-header' },
			close: function close(event, ui) {
				if (reloadOnClose) {
					console.info('reloading');
					location.reload();
				} else {
					console.info('not reloading');
				}
			}
		});
		$('#internetmarke_modal').dialog('open');
		$('#inetmarke_modal_content').load('admin.php?do=InternetMarke/CreateLabelForm&template_version=2&orders_id=' + orders_id, function (responseText, textStatus, jqXHR) {
			$('#inetmarke_modal_content').removeClass('inetmarke_loading');
			if ($('#inetmarke_single_form').length > 0) {
				_initSingleForm(orders_id);
			}
			if ($('#inetmarke_enter_credentials').length > 0) {
				_initCredentialsForm();
			}
			if ($('#inetmarke_balance_too_low').length > 0) {
				$('#internetmarke_modal').dialog('option', 'buttons', [{
					'text': jse.core.lang.translate('close', 'buttons'),
					'class': 'btn',
					'click': function click() {
						$('#internetmarke_modal').dialog('close');
					}
				}]);
			}
		});
	};

	/* -------------------------------------------------------------------------------------------------- */

	var $table = $('.orders .table-main');

	$('body').prepend($('<div id="internetmarke_modal" title="' + jse.core.lang.translate('create_label_window_title', 'internetmarke') + '" style="display: none;"><div id="inetmarke_modal_content"></div></div>'));

	$table.on('init.dt', function () {
		var _initSingleAction = function _initSingleAction($table) {
			$table.find('tbody .btn-group.dropdown').each(function (index, dropdown) {
				var orderId = $(this).parents('tr').data('id');
				var defaultRowAction = $table.data('defaultRowAction') || 'edit';

				jse.libs.button_dropdown.addAction($(dropdown), {
					text: jse.core.lang.translate('create_label', 'internetmarke'),
					href: jse.core.config.get('appUrl') + '/admin/dummy.php?orders_id=' + orderId,
					class: 'internetmarke-single',
					data: { configurationValue: 'internetmarke-single' },
					isDefault: defaultRowAction === 'internetmarke-single',
					callback: function callback(e) {
						e.preventDefault();_openSingleFormModal(e);
					}
				});
			});
		};

		$table.on('draw.dt', function () {
			return _initSingleAction($table);
		});
		_initSingleAction($table);
	});
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvbW9kdWxlcy9pbnRlcm5ldG1hcmtlL2ludGVybmV0bWFya2UuanMiXSwibmFtZXMiOlsiJCIsInJlbG9hZE9uQ2xvc2UiLCJfaW5pdFNpbmdsZUZvcm0iLCJvcmRlcnNfaWQiLCJncmlkX3giLCJncmlkX3kiLCJvbiIsImUiLCJmaWVsZG5hbWUiLCJ0YXJnZXQiLCJhdHRyIiwicmVwbGFjZSIsImZpZWxkdmFsdWUiLCJ2YWwiLCJ0b2dnbGUiLCJsZW5ndGgiLCJodG1sIiwidHJpZ2dlciIsImlzTmF0aW9uYWwiLCJjb3VudHJ5TmFtZSIsInRleHQiLCIkcHJlZmVycmVkUHJvZHVjdCIsInByb2R1Y3RDb2RlIiwiZmlsdGVyIiwiX3VwZGF0ZVByZXZpZXciLCJpc0ZyYW5raW5nWm9uZSIsImdyaWRfZGltZW5zaW9uc19tYXRjaCIsIiRwYWdlcG9zX3dpZGdldCIsIiRwYWdlcG9zX3RhYmxlIiwiJHBhZ2Vwb3Nfcm93IiwiJHBhZ2Vwb3NfY29sIiwicm93IiwiY29sIiwibWF0Y2giLCJlbXB0eSIsImFwcGVuZCIsIl9pbml0UGFnZVBvc1RhYmxlSGFuZGxlciIsImZpcnN0IiwiY3NzIiwiY2hhbmdlIiwicGFyc2VJbnQiLCJfdXBkYXRlUGFnZVBvc1RhYmxlIiwiJHRhYmxlcm93IiwiJGNlbGwiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiY2VsbHRleHQiLCJjZWxsdGV4dF9zcGxpdCIsInNlbGVjdGVkQ2VsbCIsIngiLCJ5IiwiX2luaXRDcmVkZW50aWFsc0Zvcm0iLCJfY3JlZGVudGlhbHNTdWJtaXRIYW5kbGVyIiwiYWNjZXB0ZWQiLCJnZXQiLCJjaGVja2VkIiwicmVtb3ZlQXR0ciIsImZvcm1kYXRhIiwic2VyaWFsaXplIiwiYWpheCIsInR5cGUiLCJ1cmwiLCJqc2UiLCJjb3JlIiwiY29uZmlnIiwiZGF0YSIsImRhdGFUeXBlIiwiZG9uZSIsInJlc3VsdCIsImxvYWQiLCJyZXNwb25zZVRleHQiLCJ0ZXh0U3RhdHVzIiwianFYSFIiLCJhbGVydCIsImVycm9yX21lc3NhZ2UiLCJmYWlsIiwibGFuZyIsInRyYW5zbGF0ZSIsIl9zaW5nbGVGb3JtU3VibWl0SGFuZGxlciIsIl9pbml0Vm91Y2hlckxpc3QiLCJkaWFsb2ciLCJfc2hvcFByZXZpZXdIYW5kbGVyIiwicHJldmVudERlZmF1bHQiLCJmaXJzdE5vbkRpc2FibGVkUHJvZHVjdENvZGUiLCJub3QiLCJwcmV2aWV3bGluayIsInByZXZpZXdJbWciLCJzaG93IiwiaGlkZSIsIl9vcGVuU2luZ2xlRm9ybU1vZGFsIiwiZXZlbnQiLCJwYXJlbnRzIiwiZmluZCIsImludGVybmV0bWFya2VfbW9kYWxfYnV0dG9ucyIsInB1c2giLCJhdXRvT3BlbiIsIm1vZGFsIiwiYnV0dG9ucyIsIndpZHRoIiwicG9zaXRpb24iLCJteSIsImF0Iiwib2YiLCJjbG9zZSIsInVpIiwiY29uc29sZSIsImluZm8iLCJsb2NhdGlvbiIsInJlbG9hZCIsIiR0YWJsZSIsInByZXBlbmQiLCJfaW5pdFNpbmdsZUFjdGlvbiIsImVhY2giLCJpbmRleCIsImRyb3Bkb3duIiwib3JkZXJJZCIsImRlZmF1bHRSb3dBY3Rpb24iLCJsaWJzIiwiYnV0dG9uX2Ryb3Bkb3duIiwiYWRkQWN0aW9uIiwiaHJlZiIsImNsYXNzIiwiY29uZmlndXJhdGlvblZhbHVlIiwiaXNEZWZhdWx0IiwiY2FsbGJhY2siXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsRUFBRSxZQUFXO0FBQ1o7O0FBRUE7O0FBRUEsS0FBSUMsZ0JBQWdCLEtBQXBCOztBQUVBLEtBQU1DLGtCQUFrQixTQUFsQkEsZUFBa0IsQ0FBU0MsU0FBVCxFQUN4QjtBQUNDLE1BQUlDLFNBQVMsQ0FBYjtBQUFBLE1BQWdCQyxTQUFTLENBQXpCOztBQUVBTCxJQUFFLDZDQUFGLEVBQWlETSxFQUFqRCxDQUFvRCxRQUFwRCxFQUE4RCxVQUFTQyxDQUFULEVBQVk7QUFDekUsT0FBTUMsWUFBWVIsRUFBRU8sRUFBRUUsTUFBSixFQUFZQyxJ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ZXRtYXJrZS9pbnRlcm5ldG1hcmtlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0aW50ZXJuZXRtYXJrZS5qcyAyMDE4LTExLTA4XG5cdEdhbWJpbyBHbWJIXG5cdGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG5cdENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuXHRSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcblx0W2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG5cdC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4qL1xuXG4kKGZ1bmN0aW9uKCkge1xuXHQndXNlIHN0cmljdCc7XG5cblx0LyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi9cblx0XG5cdHZhciByZWxvYWRPbkNsb3NlID0gZmFsc2U7XG5cblx0Y29uc3QgX2luaXRTaW5nbGVGb3JtID0gZnVuY3Rpb24ob3JkZXJzX2lkKVxuXHR7XG5cdFx0bGV0IGdyaWRfeCA9IDEsIGdyaWRfeSA9IDE7XG5cblx0XHQkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtIC5yZWNlaXZlcl9kYXRhIGlucHV0Jykub24oJ2NoYW5nZScsIGZ1bmN0aW9uKGUpIHtcblx0XHRcdGNvbnN0IGZpZWxkbmFtZSA9ICQoZS50YXJnZXQpLmF0dHIoJ25hbWUnKS5yZXBsYWNlKC9yZWNlaXZlclxcWyguKilcXF0vLCAncmVjZWl2ZXJfJDEnKTtcblx0XHRcdGxldCBmaWVsZHZhbHVlID0gJChlLnRhcmdldCkudmFsKCk7XG5cdFx0XHQkKCdzcGFuIycrZmllbGRuYW1lKS50b2dnbGUoZmllbGR2YWx1ZS5sZW5ndGggPiAwKTtcblx0XHRcdGlmKGZpZWxkbmFtZSA9PT0gJ3JlY2VpdmVyX2FkZGl0aW9uYWwnIHx8IGZpZWxkbmFtZSA9PT0gJ3JlY2VpdmVyX2NvbXBhbnknKVxuXHRcdFx0e1xuXHRcdFx0XHRmaWVsZHZhbHVlICs9ICc8YnI+Jztcblx0XHRcdH1cblx0XHRcdCQoJ3NwYW4jJytmaWVsZG5hbWUpLmh0bWwoZmllbGR2YWx1ZSk7XG5cdFx0fSk7XG5cdFx0JCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybSAucmVjZWl2ZXJfZGF0YSBpbnB1dCcpLnRyaWdnZXIoJ2NoYW5nZScpO1xuXG5cdFx0JCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybSBzZWxlY3RbbmFtZT1cInJlY2VpdmVyW2NvdW50cnldXCJdJykub24oJ2NoYW5nZScsIGZ1bmN0aW9uIChlKSB7XG5cdFx0XHR2YXIgaXNOYXRpb25hbCAgPSAkKCdvcHRpb25bdmFsdWU9XCJERVVcIl06c2VsZWN0ZWQnLCBlLnRhcmdldCkubGVuZ3RoID4gMCxcblx0XHRcdCAgICBjb3VudHJ5TmFtZSA9ICQoJ29wdGlvbjpzZWxlY3RlZCcsICQodGhpcykpLnRleHQoKSxcblx0XHRcdCAgICAkcHJlZmVycmVkUHJvZHVjdCA9ICQoJ3NlbGVjdFtuYW1lPVwicHJvZHVjdENvZGVcIl0gb3B0aW9uLnByZWZlcnJlZCcpO1xuXHRcdFx0JCgnc3BhbiNyZWNlaXZlcl9jb3VudHJ5JykuaHRtbChjb3VudHJ5TmFtZSk7XG5cdFx0XHQkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtIG9wdGlvbi5kZXN0X25hdGlvbmFsJykudG9nZ2xlKGlzTmF0aW9uYWwgPT09IHRydWUpO1xuXHRcdFx0JCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybSBvcHRpb24uZGVzdF9pbnRlcm5hdGlvbmFsJykudG9nZ2xlKGlzTmF0aW9uYWwgPT09IGZhbHNlKTtcblx0XHRcdHZhciBwcm9kdWN0Q29kZTtcblx0XHRcdGlmKCRwcmVmZXJyZWRQcm9kdWN0LmZpbHRlcignOmVuYWJsZWQnKS5sZW5ndGggPiAwKVxuXHRcdFx0e1xuXHRcdFx0XHRwcm9kdWN0Q29kZSA9ICRwcmVmZXJyZWRQcm9kdWN0LmF0dHIoJ3ZhbHVlJyk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlXG5cdFx0XHR7XG5cdFx0XHRcdGlmKGlzTmF0aW9uYWwgPT09IHRydWUpXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRwcm9kdWN0Q29kZSA9ICQoJ3NlbGVjdFtuYW1lPVwicHJvZHVjdENvZGVcIl0gb3B0aW9uLmRlc3RfbmF0aW9uYWw6Zmlyc3QnKS5hdHRyKCd2YWx1ZScpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2Vcblx0XHRcdFx0e1xuXHRcdFx0XHRcdHByb2R1Y3RDb2RlID0gJCgnc2VsZWN0W25hbWU9XCJwcm9kdWN0Q29kZVwiXSBvcHRpb24uZGVzdF9pbnRlcm5hdGlvbmFsOmZpcnN0JykuYXR0cigndmFsdWUnKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0JCgnc2VsZWN0W25hbWU9XCJwcm9kdWN0Q29kZVwiXScpLnZhbChwcm9kdWN0Q29kZSk7XG5cdFx0XHRfdXBkYXRlUHJldmlldygpO1xuXHRcdH0pO1xuXHRcdCQoJyNpbmV0bWFya2Vfc2luZ2xlX2Zvcm0gc2VsZWN0W25hbWU9XCJyZWNlaXZlcltjb3VudHJ5XVwiXScpLnRyaWdnZXIoJ2NoYW5nZScpO1xuXG5cdFx0JCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybSBzZWxlY3RbbmFtZT1cInZvdWNoZXJMYXlvdXRcIl0nKS5vbignY2hhbmdlJywgZnVuY3Rpb24oZSkge1xuXHRcdFx0dmFyIGlzRnJhbmtpbmdab25lID0gJCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybSBzZWxlY3RbbmFtZT1cInZvdWNoZXJMYXlvdXRcIl0gb3B0aW9uW3ZhbHVlPVwiRnJhbmtpbmdab25lXCJdOnNlbGVjdGVkJykubGVuZ3RoID4gMDtcblx0XHRcdCQoJyNpbmV0bWFya2Vfc2luZ2xlX2Zvcm0gLnNlbmRlcl9saW5lJykudG9nZ2xlKGlzRnJhbmtpbmdab25lID09PSBmYWxzZSk7XG5cdFx0XHQkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtIC5yZWNlaXZlcl9ibG9jaycpLnRvZ2dsZShpc0ZyYW5raW5nWm9uZSA9PT0gZmFsc2UpO1xuXHRcdH0pO1xuXHRcdCQoJyNpbmV0bWFya2Vfc2luZ2xlX2Zvcm0gc2VsZWN0W25hbWU9XCJ2b3VjaGVyTGF5b3V0XCJdJykudHJpZ2dlcignY2hhbmdlJyk7XG5cblx0XHQkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtIHNlbGVjdFtuYW1lPVwicGFnZUZvcm1hdElEXCJdJykub24oJ2NoYW5nZScsIGZ1bmN0aW9uKGUpIHtcblx0XHRcdHZhciBncmlkX2RpbWVuc2lvbnNfbWF0Y2gsXG5cdFx0XHRcdCRwYWdlcG9zX3dpZGdldCwgJHBhZ2Vwb3NfdGFibGUsICRwYWdlcG9zX3JvdywgJHBhZ2Vwb3NfY29sLFxuXHRcdFx0XHRyb3csIGNvbDtcblx0XHRcdCRwYWdlcG9zX3dpZGdldCA9ICQoJyNwYWdlcG9zX3dpZGdldCcpO1xuXHRcdFx0Z3JpZF9kaW1lbnNpb25zX21hdGNoID0gJCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybSBzZWxlY3RbbmFtZT1cInBhZ2VGb3JtYXRJRFwiXSBvcHRpb246c2VsZWN0ZWQnKS50ZXh0KCkubWF0Y2goLy4qXFwoKFxcZCspIHggKFxcZCspIC4qLyk7XG5cdFx0XHRncmlkX3ggPSBncmlkX2RpbWVuc2lvbnNfbWF0Y2hbMV07XG5cdFx0XHRncmlkX3kgPSBncmlkX2RpbWVuc2lvbnNfbWF0Y2hbMl07XG5cdFx0XHQkcGFnZXBvc193aWRnZXQuZW1wdHkoKTtcblx0XHRcdC8vJHBhZ2Vwb3Nfd2lkZ2V0LmFwcGVuZCgkKCc8ZGl2PmdyaWQgJytncmlkX3grJyAvICcrZ3JpZF95Kyc8L2Rpdj4nKSk7XG5cdFx0XHQkcGFnZXBvc190YWJsZSA9ICQoJzx0YWJsZSBpZD1cInBhZ2Vwb3NfdGFibGVcIj48L3RhYmxlPicpO1xuXHRcdFx0Zm9yKHJvdyA9IDE7IHJvdyA8PSBncmlkX3k7IHJvdysrKVxuXHRcdFx0e1xuXHRcdFx0XHQkcGFnZXBvc19yb3cgPSAkKCc8dHIgY2xhc3M9XCJwYWdlcG9zXycrcm93KydcIj48L3RyPicpO1xuXHRcdFx0XHRmb3IoY29sID0gMTsgY29sIDw9IGdyaWRfeDsgY29sKyspXG5cdFx0XHRcdHtcblx0XHRcdFx0XHQkcGFnZXBvc19jb2wgPSAkKCc8dGQgY2xhc3M9XCJjb2xfJytjb2wrJ1wiPjxzcGFuPicrcm93KydfJytjb2wrJzwvc3Bhbj48L3RkPicpO1xuXHRcdFx0XHRcdCRwYWdlcG9zX3Jvdy5hcHBlbmQoJHBhZ2Vwb3NfY29sKTtcblx0XHRcdFx0fVxuXHRcdFx0XHQkcGFnZXBvc190YWJsZS5hcHBlbmQoJHBhZ2Vwb3Nfcm93KTtcblx0XHRcdH1cblx0XHRcdCRwYWdlcG9zX3dpZGdldC5hcHBlbmQoJHBhZ2Vwb3NfdGFibGUpO1xuXHRcdFx0JCgnaW5wdXRbbmFtZT1cInBvc2l0aW9uX2xhYmVseFwiXScpLnZhbCgnMScpO1xuXHRcdFx0JCgnaW5wdXRbbmFtZT1cInBvc2l0aW9uX2xhYmVseVwiXScpLnZhbCgnMScpO1xuXHRcdFx0X2luaXRQYWdlUG9zVGFibGVIYW5kbGVyKCk7XG5cdFx0XHQkKCcjcGFnZXBvc190YWJsZSB0ZCcpLmZpcnN0KCkudHJpZ2dlcignY2xpY2snKTtcblx0XHRcdGlmKGdyaWRfeCA9PSAnMScgJiYgZ3JpZF95ID09ICcxJylcblx0XHRcdHtcblx0XHRcdFx0JCgnLnBhZ2Vwb3MnKS5jc3MoJ29wYWNpdHknLCAnMC41Jyk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlXG5cdFx0XHR7XG5cdFx0XHRcdCQoJy5wYWdlcG9zJykuY3NzKCdvcGFjaXR5JywgJzEuMCcpO1xuXHRcdFx0fVxuXHRcdH0pO1xuXHRcdCQoJyNpbmV0bWFya2Vfc2luZ2xlX2Zvcm0gc2VsZWN0W25hbWU9XCJwYWdlRm9ybWF0SURcIl0nKS50cmlnZ2VyKCdjaGFuZ2UnKTtcblxuXHRcdCQoJyNwYWdlcG9zX3RhYmxlIHRkJykuZmlyc3QoKS50cmlnZ2VyKCdjbGljaycpO1xuXHRcdCQoJ2lucHV0W25hbWU9XCJwb3NpdGlvbl9sYWJlbHhcIl0nKS5jaGFuZ2UoZnVuY3Rpb24oZSkgeyBpZihwYXJzZUludCgkKHRoaXMpLnZhbCgpKSA+IGdyaWRfeCkgeyAkKHRoaXMpLnZhbChncmlkX3gpOyB9IH0pO1xuXHRcdCQoJ2lucHV0W25hbWU9XCJwb3NpdGlvbl9sYWJlbHlcIl0nKS5jaGFuZ2UoZnVuY3Rpb24oZSkgeyBpZihwYXJzZUludCgkKHRoaXMpLnZhbCgpKSA+IGdyaWRfeSkgeyAkKHRoaXMpLnZhbChncmlkX3kpOyB9IH0pO1xuXHRcdCQoJ2lucHV0W25hbWU9XCJwb3NpdGlvbl9sYWJlbHhcIl0sIGlucHV0W25hbWU9XCJwb3NpdGlvbl9sYWJlbHlcIl0nKS5vbignY2hhbmdlJywgZnVuY3Rpb24oZSkge1xuXHRcdFx0X3VwZGF0ZVBhZ2VQb3NUYWJsZSgpO1xuXHRcdH0pO1xuXG5cdFx0JCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybSAucHJldmlld09wdGlvbicpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbihlKSB7XG5cdFx0XHRfdXBkYXRlUHJldmlldygpO1xuXHRcdH0pO1xuXG5cdFx0X3VwZGF0ZVByZXZpZXcoKTtcblx0fTtcblxuXHRjb25zdCBfdXBkYXRlUGFnZVBvc1RhYmxlID0gZnVuY3Rpb24oKVxuXHR7XG5cdFx0dmFyIHJvdywgY29sLCAkdGFibGVyb3csICRjZWxsO1xuXHRcdHJvdyA9ICQoJ2lucHV0W25hbWU9XCJwb3NpdGlvbl9sYWJlbHlcIl0nKS52YWwoKTtcblx0XHRjb2wgPSAkKCdpbnB1dFtuYW1lPVwicG9zaXRpb25fbGFiZWx4XCJdJykudmFsKCk7XG5cdFx0JHRhYmxlcm93ID0gJCgnI3BhZ2Vwb3NfdGFibGUgdHI6bnRoLWNoaWxkKCcrcm93KycpJyk7XG5cdFx0JGNlbGwgPSAkKCd0ZDpudGgtY2hpbGQoJytjb2wrJyknLCAkdGFibGVyb3cpO1xuXHRcdCQoJyNwYWdlcG9zX3RhYmxlIHRkJykucmVtb3ZlQ2xhc3MoJ3NlbGVjdGVkJyk7XG5cdFx0JGNlbGwuYWRkQ2xhc3MoJ3NlbGVjdGVkJyk7XG5cdH07XG5cblx0Y29uc3QgX2luaXRQYWdlUG9zVGFibGVIYW5kbGVyID0gZnVuY3Rpb24oKVxuXHR7XG5cdFx0JCgnI3BhZ2Vwb3NfdGFibGUgdGQnKS5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG5cdFx0XHR2YXIgY2VsbHRleHQsIGNlbGx0ZXh0X3NwbGl0LCBzZWxlY3RlZENlbGw7XG5cdFx0XHRjZWxsdGV4dCA9ICQoJ3NwYW4nLCAkKHRoaXMpKS50ZXh0KCk7XG5cdFx0XHRjZWxsdGV4dF9zcGxpdCA9IGNlbGx0ZXh0Lm1hdGNoKC8oXFxkKylfKFxcZCspLyk7XG5cdFx0XHRzZWxlY3RlZENlbGwgPSB7XG5cdFx0XHRcdHg6IGNlbGx0ZXh0X3NwbGl0WzJdLFxuXHRcdFx0XHR5OiBjZWxsdGV4dF9zcGxpdFsxXVxuXHRcdFx0fTtcblx0XHRcdCQoJ2lucHV0W25hbWU9XCJwb3NpdGlvbl9sYWJlbHhcIl0nKS52YWwoc2VsZWN0ZWRDZWxsLngpO1xuXHRcdFx0JCgnaW5wdXRbbmFtZT1cInBvc2l0aW9uX2xhYmVseVwiXScpLnZhbChzZWxlY3RlZENlbGwueSk7XG5cdFx0XHQkKCcjcGFnZXBvc190YWJsZSB0ZCcpLnJlbW92ZUNsYXNzKCdzZWxlY3RlZCcpO1xuXHRcdFx0JCh0aGlzKS5hZGRDbGFzcygnc2VsZWN0ZWQnKTtcblx0XHR9KTtcblx0fTtcblxuXHRjb25zdCBfaW5pdENyZWRlbnRpYWxzRm9ybSA9IGZ1bmN0aW9uKClcblx0e1xuXHRcdCQoJyNpbmV0bWFya2VfZW50ZXJfY3JlZGVudGlhbHMnKS5vbignc3VibWl0JywgX2NyZWRlbnRpYWxzU3VibWl0SGFuZGxlcik7XG5cdFx0JCgnI3Rvc19hY2NlcHRlZCcpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbihlKSB7XG5cdFx0XHR2YXIgYWNjZXB0ZWQgPSAkKCcjdG9zX2FjY2VwdGVkJykuZ2V0KDApLmNoZWNrZWQ7XG5cdFx0XHRpZihhY2NlcHRlZCA9PT0gdHJ1ZSlcblx0XHRcdHtcblx0XHRcdFx0JCgnI3N1Ym1pdF9zZXNzaW9uX2NyZWRlbnRpYWxzJykucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcblx0XHRcdH1cblx0XHRcdGVsc2Vcblx0XHRcdHtcblx0XHRcdFx0JCgnI3N1Ym1pdF9zZXNzaW9uX2NyZWRlbnRpYWxzJykuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcblx0XHRcdH1cblx0XHR9KTtcblx0XHQkKCcjdG9zX2FjY2VwdGVkJykudHJpZ2dlcignY2hhbmdlJyk7XG5cdH07XG5cblx0Y29uc3QgX2NyZWRlbnRpYWxzU3VibWl0SGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHR2YXIgZm9ybWRhdGEgPSAkKHRoaXMpLnNlcmlhbGl6ZSgpO1xuXHRcdCQuYWpheCh7XG5cdFx0XHR0eXBlOiAnUE9TVCcsXG5cdFx0XHR1cmw6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89SW50ZXJuZXRNYXJrZS9TZXRTZXNzaW9uQ3JlZGVudGlhbHMnLFxuXHRcdFx0ZGF0YTogZm9ybWRhdGEsXG5cdFx0XHRkYXRhVHlwZTogJ2pzb24nXG5cdFx0fSlcblx0XHQuZG9uZShmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0aWYoZGF0YS5yZXN1bHQgPT09ICdPSycpXG5cdFx0XHR7XG5cdFx0XHRcdCQoJyNpbmV0bWFya2VfbW9kYWxfY29udGVudCcpLmVtcHR5KCkuYWRkQ2xhc3MoJ2luZXRtYXJrZV9sb2FkaW5nJyk7XG5cdFx0XHRcdCQoJyNpbmV0bWFya2VfbW9kYWxfY29udGVudCcpLmxvYWQoJ2FkbWluLnBocD9kbz1JbnRlcm5ldE1hcmtlL0NyZWF0ZUxhYmVsRm9ybSZvcmRlcnNfaWQ9JyArIGRhdGEub3JkZXJzX2lkLFxuXHRcdFx0XHRcdGZ1bmN0aW9uIChyZXNwb25zZVRleHQsIHRleHRTdGF0dXMsIGpxWEhSKSB7XG5cdFx0XHRcdFx0XHQkKCcjaW5ldG1hcmtlX21vZGFsX2NvbnRlbnQnKS5yZW1vdmVDbGFzcygnaW5ldG1hcmtlX2xvYWRpbmcnKTtcblx0XHRcdFx0XHRcdGlmKCQoJyNpbmV0bWFya2Vfc2luZ2xlX2Zvcm0nKS5sZW5ndGggPiAwKVxuXHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHRfaW5pdFNpbmdsZUZvcm0oKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdCk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlXG5cdFx0XHR7XG5cdFx0XHRcdGFsZXJ0KGRhdGEuZXJyb3JfbWVzc2FnZSk7XG5cdFx0XHR9XG5cdFx0fSlcblx0XHQuZmFpbChmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0YWxlcnQoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3N1Ym1pdF9lcnJvcicsICdpbnRlcm5ldG1hcmtlJykpO1xuXHRcdH0pO1xuXG5cdFx0cmV0dXJuIGZhbHNlO1xuXHR9O1xuXG5cdGNvbnN0IF9zaW5nbGVGb3JtU3VibWl0SGFuZGxlciA9IGZ1bmN0aW9uIChlKSB7XG5cdFx0dmFyIGZvcm1kYXRhID0gJCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybScpLnNlcmlhbGl6ZSgpO1xuXHRcdCQoJyNpbmV0bWFya2VfbW9kYWxfY29udGVudCcpLmVtcHR5KCkuYWRkQ2xhc3MoJ2luZXRtYXJrZV9sb2FkaW5nJyk7XG5cdFx0JC5hamF4KHtcblx0XHRcdCAgICAgICB0eXBlOiAnUE9TVCcsXG5cdFx0XHQgICAgICAgdXJsOiBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwP2RvPUludGVybmV0TWFya2UvQ3JlYXRlTGFiZWxGb3JtU3VibWl0Jyxcblx0XHRcdCAgICAgICBkYXRhOiBmb3JtZGF0YSxcblx0XHRcdCAgICAgICBkYXRhVHlwZTogJ2pzb24nXG5cdFx0ICAgICAgIH0pXG5cdFx0XHQuZG9uZShmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0XHQkKCcjaW5ldG1hcmtlX21vZGFsX2NvbnRlbnQnKS5yZW1vdmVDbGFzcygnaW5ldG1hcmtlX2xvYWRpbmcnKTtcblx0XHRcdFx0aWYgKGRhdGEucmVzdWx0ID09PSAnT0snKSB7XG5cdFx0XHRcdFx0JCgnI2luZXRtYXJrZV9tb2RhbF9jb250ZW50JykubG9hZCgnYWRtaW4ucGhwP2RvPUludGVybmV0TWFya2UvTGlzdFZvdWNoZXJzJnRlbXBsYXRlX3ZlcnNpb249MiZvcmRlcnNfaWQ9JyArIGRhdGEub3JkZXJzX2lkLFxuXHRcdFx0XHRcdFx0ZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdF9pbml0Vm91Y2hlckxpc3QoKTtcblx0XHRcdFx0XHRcdFx0JCgnLmluZXRtYXJrZV92b3VjaGVycyBhLmlmcmFtZWRsOmxhc3QnKS50cmlnZ2VyKCdjbGljaycpO1xuXHRcdFx0XHRcdFx0XHRyZWxvYWRPbkNsb3NlID0gdHJ1ZTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHQpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2Uge1xuXHRcdFx0XHRcdGlmKGRhdGEuZXJyb3JfbWVzc2FnZSkge1xuXHRcdFx0XHRcdFx0YWxlcnQoZGF0YS5lcnJvcl9tZXNzYWdlKTtcblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0YWxlcnQoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3N1Ym1pdF9lcnJvcicsICdpbnRlcm5ldG1hcmtlJykpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHQkKCcjaW50ZXJuZXRtYXJrZV9tb2RhbCcpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0fVxuXHRcdFx0fSlcblx0XHRcdC5mYWlsKGZ1bmN0aW9uIChkYXRhKSB7XG5cdFx0XHRcdGlmKGRhdGEuZXJyb3JfbWVzc2FnZSkge1xuXHRcdFx0XHRcdGFsZXJ0KGRhdGEuZXJyb3JfbWVzc2FnZSk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0YWxlcnQoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3N1Ym1pdF9lcnJvcicsICdpbnRlcm5ldG1hcmtlJykpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdCQoJyNpbnRlcm5ldG1hcmtlX21vZGFsJykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0fSk7XG5cblx0XHRyZXR1cm4gZmFsc2U7XG5cdH07XG5cblx0Y29uc3QgX3Nob3BQcmV2aWV3SGFuZGxlciA9IGZ1bmN0aW9uKGUpXG5cdHtcblx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0X3VwZGF0ZVByZXZpZXcoKTtcblx0XHRyZXR1cm4gZmFsc2U7XG5cdH07XG5cblx0Y29uc3QgX3VwZGF0ZVByZXZpZXcgPSBmdW5jdGlvbigpXG5cdHtcblx0XHRpZigkKCdzZWxlY3RbbmFtZT1cInByb2R1Y3RDb2RlXCJdJykudmFsKCkgPT09IG51bGwpXG5cdFx0e1xuXHRcdFx0bGV0IGZpcnN0Tm9uRGlzYWJsZWRQcm9kdWN0Q29kZSA9ICQoJ3NlbGVjdFtuYW1lPVwicHJvZHVjdENvZGVcIl0gb3B0aW9uJykubm90KCc6ZGlzYWJsZWQnKS5maXJzdCgpLnZhbCgpO1xuXHRcdFx0JCgnc2VsZWN0W25hbWU9XCJwcm9kdWN0Q29kZVwiXScpLnZhbChmaXJzdE5vbkRpc2FibGVkUHJvZHVjdENvZGUpO1xuXHRcdH1cblx0XHR2YXIgZm9ybWRhdGEgPSAkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtJykuc2VyaWFsaXplKCk7XG5cdFx0JC5hamF4KHtcblx0XHRcdFx0dHlwZTogJ1BPU1QnLFxuXHRcdFx0XHR1cmw6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89SW50ZXJuZXRNYXJrZS9QcmV2aWV3Vm91Y2hlcicsXG5cdFx0XHRcdGRhdGE6IGZvcm1kYXRhLFxuXHRcdFx0XHRkYXRhVHlwZTogJ2pzb24nXG5cdFx0ICAgICAgIH0pXG5cdFx0XHQuZG9uZShmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0XHRpZihkYXRhLnJlc3VsdCA9PT0gJ09LJykge1xuXHRcdFx0XHRcdGlmKCQoJyNpbmV0bWFya2VfcHJldmlldyBpbWcnKS5sZW5ndGggPiAwKVxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdCQoJyNpbmV0bWFya2VfcHJldmlldyBpbWcnKS5hdHRyKCdzcmMnLCBkYXRhLnByZXZpZXdsaW5rKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0ZWxzZVxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdHZhciBwcmV2aWV3SW1nID0gJCgnPGltZyBzcmM9XCInKyBkYXRhLnByZXZpZXdsaW5rICsnXCI+Jyk7XG5cdFx0XHRcdFx0XHQkKCcjaW5ldG1hcmtlX3ByZXZpZXcnKS5lbXB0eSgpLmFwcGVuZChwcmV2aWV3SW1nKS5zaG93KCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2Uge1xuXHRcdFx0XHRcdGlmKGRhdGEuZXJyb3JfbWVzc2FnZSkge1xuXHRcdFx0XHRcdFx0YWxlcnQoZGF0YS5lcnJvcl9tZXNzYWdlKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblx0XHRcdH0pXG5cdFx0XHQuZmFpbChmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0XHRhbGVydChqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnc3VibWl0X2Vycm9yJywgJ2ludGVybmV0bWFya2UnKSk7XG5cdFx0XHR9KTtcblx0fTtcblxuXHRjb25zdCBfaW5pdFZvdWNoZXJMaXN0ID0gZnVuY3Rpb24oKSB7XG5cdFx0JCgnI2luZXRtYXJrZV9tb2RhbF9jb250ZW50JykucmVtb3ZlQ2xhc3MoJ2luZXRtYXJrZV9sb2FkaW5nJyk7XG5cdFx0JCgnI2luZXRtYXJrZV9jcmVhdGVfbGFiZWwnKS5oaWRlKCk7XG5cdFx0JCgnI2luZXRtYXJrZV9zaG93Vm91Y2hlckxpc3QnKS5oaWRlKCk7XG5cdFx0Lypcblx0XHQkKCcuaW5ldG1hcmtlX3ZvdWNoZXJzIGEuaWZyYW1lZGwnKS5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG5cdFx0XHR2YXIgJHBhcmVudCA9ICQodGhpcykucGFyZW50KCksXG5cdFx0XHQgICAgaHJlZiA9ICQodGhpcykuYXR0cignaHJlZicpO1xuXHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0JCgnaWZyYW1lJywgJHBhcmVudCkucmVtb3ZlKCk7XG5cdFx0XHR2YXIgJGRsaWZyYW1lID0gJCgnPGlmcmFtZSBzcmM9XCInK2hyZWYrJ1wiIHN0eWxlPVwid2lkdGg6MDsgaGVpZ2h0OjA7IGJvcmRlcjpub25lO1wiPjwvaWZyYW1lPicpO1xuXHRcdFx0JHBhcmVudC5hcHBlbmQoJGRsaWZyYW1lKTtcblx0XHR9KTtcblx0XHQqL1xuXHR9O1xuXG5cdGNvbnN0IF9vcGVuU2luZ2xlRm9ybU1vZGFsID0gZnVuY3Rpb24oZXZlbnQpXG5cdHtcblx0XHRjb25zdCBvcmRlcnNfaWQgPSAkKGV2ZW50LnRhcmdldCkucGFyZW50cygndHInKS5hdHRyKCdpZCcpIHx8ICQoJ2JvZHknKS5maW5kKCcjZ21fb3JkZXJfaWQnKS52YWwoKTtcblx0XHQkKCcjaW5ldG1hcmtlX21vZGFsX2NvbnRlbnQnKS5lbXB0eSgpLmFkZENsYXNzKCdpbmV0bWFya2VfbG9hZGluZycpO1xuXG5cdFx0bGV0IGludGVybmV0bWFya2VfbW9kYWxfYnV0dG9ucyA9IFtdO1xuXHRcdGludGVybmV0bWFya2VfbW9kYWxfYnV0dG9ucy5wdXNoKHtcblx0XHRcdCd0ZXh0JzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Nsb3NlJywgJ2J1dHRvbnMnKSxcblx0XHRcdCdjbGFzcyc6ICdidG4nLFxuXHRcdFx0J2NsaWNrJzogZnVuY3Rpb24gKCkge1xuXHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdH1cblx0XHR9KTtcblx0XHRpbnRlcm5ldG1hcmtlX21vZGFsX2J1dHRvbnMucHVzaCh7XG5cdFx0XHQndGV4dCc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjcmVhdGVfdm91Y2hlcicsICdpbnRlcm5ldG1hcmtlJyksXG5cdFx0XHQnY2xhc3MnOiAnYnRuIGJ0bi1wcmltYXJ5Jyxcblx0XHRcdCdjbGljayc6IF9zaW5nbGVGb3JtU3VibWl0SGFuZGxlcixcblx0XHRcdCdpZCc6ICdpbmV0bWFya2VfY3JlYXRlX2xhYmVsJ1xuXHRcdH0pO1xuXHRcdGludGVybmV0bWFya2VfbW9kYWxfYnV0dG9ucy5wdXNoKHtcblx0XHRcdCd0ZXh0JzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3Nob3dfdm91Y2hlcnMnLCAnaW50ZXJuZXRtYXJrZScpLFxuXHRcdFx0J2NsYXNzJzogJ2J0bicsXG5cdFx0XHQnY2xpY2snOiBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFx0JCgnI2luZXRtYXJrZV9tb2RhbF9jb250ZW50JykuZW1wdHkoKS5hZGRDbGFzcygnaW5ldG1hcmtlX2xvYWRpbmcnKTtcblx0XHRcdFx0JCgnI2luZXRtYXJrZV9tb2RhbF9jb250ZW50JykubG9hZCgnYWRtaW4ucGhwP2RvPUludGVybmV0TWFya2UvTGlzdFZvdWNoZXJzJnRlbXBsYXRlX3ZlcnNpb249MiZvcmRlcnNfaWQ9JyArIG9yZGVyc19pZCwgX2luaXRWb3VjaGVyTGlzdCk7XG5cdFx0XHR9LFxuXHRcdFx0J2lkJzogJ2luZXRtYXJrZV9zaG93Vm91Y2hlckxpc3QnXG5cdFx0fSk7XG5cblx0XHQkKCcjaW50ZXJuZXRtYXJrZV9tb2RhbCcpLmRpYWxvZyh7XG5cdFx0XHRhdXRvT3BlbjogZmFsc2UsXG5cdFx0XHRtb2RhbDogdHJ1ZSxcblx0XHRcdCd0aXRsZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjcmVhdGVfbGFiZWwnLCAnaW50ZXJuZXRtYXJrZScpLFxuXHRcdFx0J2RpYWxvZ0NsYXNzJzogJ2ludGVybmV0bWFya2UtbW9kYWwnLFxuXHRcdFx0YnV0dG9uczogaW50ZXJuZXRtYXJrZV9tb2RhbF9idXR0b25zLFxuXHRcdFx0d2lkdGg6IDEyMDAsXG5cdFx0XHRwb3NpdGlvbjogeyBteTogJ2NlbnRlciB0b3AnLCBhdDogJ2NlbnRlciBib3R0b20nLCBvZjogJyNtYWluLWhlYWRlcicgfSxcblx0XHRcdGNsb3NlOiBmdW5jdGlvbihldmVudCwgdWkpIHtcblx0XHRcdFx0aWYocmVsb2FkT25DbG9zZSkge1xuXHRcdFx0XHRcdGNvbnNvbGUuaW5mbygncmVsb2FkaW5nJyk7XG5cdFx0XHRcdFx0bG9jYXRpb24ucmVsb2FkKCk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0Y29uc29sZS5pbmZvKCdub3QgcmVsb2FkaW5nJyk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9KTtcblx0XHQkKCcjaW50ZXJuZXRtYXJrZV9tb2RhbCcpLmRpYWxvZygnb3BlbicpO1xuXHRcdCQoJyNpbmV0bWFya2VfbW9kYWxfY29udGVudCcpLmxvYWQoJ2FkbWluLnBocD9kbz1JbnRlcm5ldE1hcmtlL0NyZWF0ZUxhYmVsRm9ybSZ0ZW1wbGF0ZV92ZXJzaW9uPTImb3JkZXJzX2lkPScgKyBvcmRlcnNfaWQsXG5cdFx0XHRmdW5jdGlvbiAocmVzcG9uc2VUZXh0LCB0ZXh0U3RhdHVzLCBqcVhIUikge1xuXHRcdFx0XHQkKCcjaW5ldG1hcmtlX21vZGFsX2NvbnRlbnQnKS5yZW1vdmVDbGFzcygnaW5ldG1hcmtlX2xvYWRpbmcnKTtcblx0XHRcdFx0aWYoJCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybScpLmxlbmd0aCA+IDApXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRfaW5pdFNpbmdsZUZvcm0ob3JkZXJzX2lkKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRpZigkKCcjaW5ldG1hcmtlX2VudGVyX2NyZWRlbnRpYWxzJykubGVuZ3RoID4gMClcblx0XHRcdFx0e1xuXHRcdFx0XHRcdF9pbml0Q3JlZGVudGlhbHNGb3JtKCk7XG5cdFx0XHRcdH1cblx0XHRcdFx0aWYoJCgnI2luZXRtYXJrZV9iYWxhbmNlX3Rvb19sb3cnKS5sZW5ndGggPiAwKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0JCgnI2ludGVybmV0bWFya2VfbW9kYWwnKS5kaWFsb2coJ29wdGlvbicsICdidXR0b25zJyxcblx0XHRcdFx0XHRcdFtcblx0XHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHRcdCd0ZXh0JzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Nsb3NlJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuJyxcblx0XHRcdFx0XHRcdFx0XHQnY2xpY2snOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0XHRcdCQoJyNpbnRlcm5ldG1hcmtlX21vZGFsJykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XVxuXHRcdFx0XHRcdCk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHQpO1xuXHR9O1xuXG5cdC8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXG5cblx0Y29uc3QgJHRhYmxlID0gJCgnLm9yZGVycyAudGFibGUtbWFpbicpO1xuXHRcblx0JCgnYm9keScpLnByZXBlbmQoJCgnPGRpdiBpZD1cImludGVybmV0bWFya2VfbW9kYWxcIiB0aXRsZT1cIicgK1xuXHRcdGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjcmVhdGVfbGFiZWxfd2luZG93X3RpdGxlJywgJ2ludGVybmV0bWFya2UnKSArXG5cdFx0J1wiIHN0eWxlPVwiZGlzcGxheTogbm9uZTtcIj48ZGl2IGlkPVwiaW5ldG1hcmtlX21vZGFsX2NvbnRlbnRcIj48L2Rpdj48L2Rpdj4nKSk7XG5cblx0JHRhYmxlLm9uKCdpbml0LmR0JywgZnVuY3Rpb24oKSB7XG5cdFx0Y29uc3QgX2luaXRTaW5nbGVBY3Rpb24gPSBmdW5jdGlvbigkdGFibGUpIHtcblx0XHRcdCR0YWJsZS5maW5kKCd0Ym9keSAuYnRuLWdyb3VwLmRyb3Bkb3duJykuZWFjaChmdW5jdGlvbihpbmRleCwgZHJvcGRvd24pIHtcblx0XHRcdFx0Y29uc3Qgb3JkZXJJZCA9ICQodGhpcykucGFyZW50cygndHInKS5kYXRhKCdpZCcpO1xuXHRcdFx0XHRjb25zdCBkZWZhdWx0Um93QWN0aW9uID0gJHRhYmxlLmRhdGEoJ2RlZmF1bHRSb3dBY3Rpb24nKSB8fCAnZWRpdCc7XG5cblx0XHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLmFkZEFjdGlvbigkKGRyb3Bkb3duKSwge1xuXHRcdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjcmVhdGVfbGFiZWwnLCAnaW50ZXJuZXRtYXJrZScpLFxuXHRcdFx0XHRcdGhyZWY6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9kdW1teS5waHA/b3JkZXJzX2lkPScgKyBvcmRlcklkLFxuXHRcdFx0XHRcdGNsYXNzOiAnaW50ZXJuZXRtYXJrZS1zaW5nbGUnLFxuXHRcdFx0XHRcdGRhdGE6IHtjb25maWd1cmF0aW9uVmFsdWU6ICdpbnRlcm5ldG1hcmtlLXNpbmdsZSd9LFxuXHRcdFx0XHRcdGlzRGVmYXVsdDogZGVmYXVsdFJvd0FjdGlvbiA9PT0gJ2ludGVybmV0bWFya2Utc2luZ2xlJyxcblx0XHRcdFx0XHRjYWxsYmFjazogZnVuY3Rpb24oZSkgeyBlLnByZXZlbnREZWZhdWx0KCk7IF9vcGVuU2luZ2xlRm9ybU1vZGFsKGUpOyB9LFxuXHRcdFx0XHR9KTtcblx0XHRcdH0pO1xuXHRcdH07XG5cbiAgICAgICAgJHRhYmxlLm9uKCdkcmF3LmR0JywgKCkgPT4gX2luaXRTaW5nbGVBY3Rpb24oJHRhYmxlKSk7XG5cdFx0X2luaXRTaW5nbGVBY3Rpb24oJHRhYmxlKTtcblx0fSkgO1xuXG59KTtcbiJdfQ==
