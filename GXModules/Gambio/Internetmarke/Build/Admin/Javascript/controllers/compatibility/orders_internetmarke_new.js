'use strict';

/* --------------------------------------------------------------
 orders_internetmarke_new.js 2019-12-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Orders InternetMarke Module
 *
 * This module implements the user interface for creating shipping labels via DP InternetMarke
 *
 * @module Compatibility/orders/orders_internetmarke
 */
gxmodules.controllers.module('orders_internetmarke_new', [gx.source + '/libs/action_mapper', gx.source + '/libs/button_dropdown'],

/**  @lends module:Compatibility/orders/orders_internetmarke */

function (data) {

	'use strict';

	var reloadOnClose = false;

	var
	/**
  * Module Selector
  *
  * @var {object}
  */
	$this = $(this),


	/**
  * The mapper library
  *
  * @var {object}
  */
	mapper = jse.libs.action_mapper,


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	var _initSingleForm = function _initSingleForm(orders_id) {
		var grid_x = 1,
		    grid_y = 1;

		$('#inetmarke_single_form .receiver_data input').on('change', function (e) {
			var fieldname = $(e.target).attr('name').replace(/receiver\[(.*)\]/, 'receiver_$1'),
			    fieldvalue = $(e.target).val();
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
				$('#inetmarke_modal_content').load('admin.php?do=InternetMarke/ListVouchers&orders_id=' + data.orders_id, function () {
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
			}
		}).fail(function (data) {
			alert(jse.core.lang.translate('submit_error', 'internetmarke'));
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
		var orders_id = $(event.target).parents('tr').data('row-id');
		if (!orders_id && $(event.target).attr('href')) {
			orders_id = $(event.target).attr('href').replace(/.*oID=(\d+).*/, '$1');
		} else {
			orders_id = $('body').find('#gm_order_id').val();
		}
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
			'text': jse.core.lang.translate('internetmarke_create_voucher', 'admin_labels'),
			'class': 'btn btn-primary',
			'click': _singleFormSubmitHandler,
			'id': 'inetmarke_create_label'
		});
		internetmarke_modal_buttons.push({
			'text': jse.core.lang.translate('internetmarke_show_vouchers', 'admin_labels'),
			'class': 'btn',
			'click': function click(e) {
				e.preventDefault();
				$('#inetmarke_modal_content').empty().addClass('inetmarke_loading');
				$('#inetmarke_modal_content').load('admin.php?do=InternetMarke/ListVouchers&orders_id=' + orders_id, _initVoucherList);
			},
			'id': 'inetmarke_showVoucherList'
		});

		$('#internetmarke_modal').dialog({
			autoOpen: false,
			modal: true,
			'title': jse.core.lang.translate('internetmarke_create_label', 'admin_labels'),
			'dialogClass': 'gx-container',
			buttons: internetmarke_modal_buttons,
			width: 1000,
			position: { my: 'center top', at: 'center bottom', of: '.main-top-header' },
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
		$('#inetmarke_modal_content').load('admin.php?do=InternetMarke/CreateLabelForm&orders_id=' + orders_id, function (responseText, textStatus, jqXHR) {
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

	module.init = function (done) {
		$('div.internetmarke_orderdetails').closest('div.frame-wrapper').hide();
		$('head').append($('<link rel="stylesheet" href="' + jse.core.config.get('appUrl') + '/GXModules/Gambio/Internetmarke/Admin/Styles/internetmarke.css">'));
		$('body').prepend($('<div id="internetmarke_modal" title="' + jse.core.lang.translate('create_label_window_title', 'internetmarke') + '" style="display: none;"><div id="inetmarke_modal_content"></div></div>'));

		var interval_counter = 10,
		    interval = setInterval(function () {
			if (jse.libs.button_dropdown && $('.js-button-dropdown').length) {
				clearInterval(interval);
				jse.libs.button_dropdown.mapAction($('.bottom-save-bar'), 'internetmarke_create_label', 'admin_labels', _openSingleFormModal);
			}
			if (interval_counter-- === 0) {
				clearInterval(interval);
			}
		}, 400);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvY29udHJvbGxlcnMvY29tcGF0aWJpbGl0eS9vcmRlcnNfaW50ZXJuZXRtYXJrZV9uZXcuanMiXSwibmFtZXMiOlsiZ3htb2R1bGVzIiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJneCIsInNvdXJjZSIsImRhdGEiLCJyZWxvYWRPbkNsb3NlIiwiJHRoaXMiLCIkIiwibWFwcGVyIiwianNlIiwibGlicyIsImFjdGlvbl9tYXBwZXIiLCJfaW5pdFNpbmdsZUZvcm0iLCJvcmRlcnNfaWQiLCJncmlkX3giLCJncmlkX3kiLCJvbiIsImUiLCJmaWVsZG5hbWUiLCJ0YXJnZXQiLCJhdHRyIiwicmVwbGFjZSIsImZpZWxkdmFsdWUiLCJ2YWwiLCJ0b2dnbGUiLCJsZW5ndGgiLCJodG1sIiwidHJpZ2dlciIsImlzTmF0aW9uYWwiLCJjb3VudHJ5TmFtZSIsInRleHQiLCIkcHJlZmVycmVkUHJvZHVjdCIsInByb2R1Y3RDb2RlIiwiZmlsdGVyIiwiX3VwZGF0ZVByZXZpZXciLCJpc0ZyYW5raW5nWm9uZSIsImdyaWRfZGltZW5zaW9uc19tYXRjaCIsIiRwYWdlcG9zX3dpZGdldCIsIiRwYWdlcG9zX3RhYmxlIiwiJHBhZ2Vwb3Nfcm93IiwiJHBhZ2Vwb3NfY29sIiwicm93IiwiY29sIiwibWF0Y2giLCJlbXB0eSIsImFwcGVuZCIsIl9pbml0UGFnZVBvc1RhYmxlSGFuZGxlciIsImZpcnN0IiwiY3NzIiwiY2hhbmdlIiwicGFyc2VJbnQiLCJfdXBkYXRlUGFnZVBvc1RhYmxlIiwiJHRhYmxlcm93IiwiJGNlbGwiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiY2VsbHRleHQiLCJjZWxsdGV4dF9zcGxpdCIsInNlbGVjdGVkQ2VsbCIsIngiLCJ5IiwiX2luaXRDcmVkZW50aWFsc0Zvcm0iLCJfY3JlZGVudGlhbHNTdWJtaXRIYW5kbGVyIiwiYWNjZXB0ZWQiLCJnZXQiLCJjaGVja2VkIiwicmVtb3ZlQXR0ciIsImZvcm1kYXRhIiwic2VyaWFsaXplIiwiYWpheCIsInR5cGUiLCJ1cmwiLCJjb3JlIiwiY29uZmlnIiwiZGF0YVR5cGUiLCJkb25lIiwicmVzdWx0IiwibG9hZCIsInJlc3BvbnNlVGV4dCIsInRleHRTdGF0dXMiLCJqcVhIUiIsImFsZXJ0IiwiZXJyb3JfbWVzc2FnZSIsImZhaWwiLCJsYW5nIiwidHJhbnNsYXRlIiwiX3NpbmdsZUZvcm1TdWJtaXRIYW5kbGVyIiwiX2luaXRWb3VjaGVyTGlzdCIsIl9zaG9wUHJldmlld0hhbmRsZXIiLCJwcmV2ZW50RGVmYXVsdCIsImZpcnN0Tm9uRGlzYWJsZWRQcm9kdWN0Q29kZSIsIm5vdCIsInByZXZpZXdsaW5rIiwicHJldmlld0ltZyIsInNob3ciLCJoaWRlIiwiX29wZW5TaW5nbGVGb3JtTW9kYWwiLCJldmVudCIsInBhcmVudHMiLCJmaW5kIiwiaW50ZXJuZXRtYXJrZV9tb2RhbF9idXR0b25zIiwicHVzaCIsImRpYWxvZyIsImF1dG9PcGVuIiwibW9kYWwiLCJidXR0b25zIiwid2lkdGgiLCJwb3NpdGlvbiIsIm15IiwiYXQiLCJvZiIsImNsb3NlIiwidWkiLCJjb25zb2xlIiwiaW5mbyIsImxvY2F0aW9uIiwicmVsb2FkIiwiaW5pdCIsImNsb3Nlc3QiLCJwcmVwZW5kIiwiaW50ZXJ2YWxfY291bnRlciIsImludGVydmFsIiwic2V0SW50ZXJ2YWwiLCJidXR0b25fZHJvcGRvd24iLCJjbGVhckludGVydmFsIiwibWFwQWN0aW9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsVUFBVUMsV0FBVixDQUFzQkMsTUFBdEIsQ0FDQywwQkFERCxFQUdDLENBQ0NDLEdBQUdDLE1BQUgsR0FBWSxxQkFEYixFQUVDRCxHQUFHQyxNQUFILEdBQVksdUJBRmIsQ0FIRDs7QUFRQzs7QUFFQSxVQUFVQyxJQUFWLEVBQWdCOztBQUVmOztBQUdBLEtBQUlDLGdCQUFnQixLQUFwQjs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7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R0FSUSxDQURaOztBQVdBOUM7QUFDQSxFQW5CRDs7QUFxQkEsUUFBTzFFLE1BQVA7QUFDQSxDQXphRiIsImZpbGUiOiJBZG1pbi9KYXZhc2NyaXB0L2NvbnRyb2xsZXJzL2NvbXBhdGliaWxpdHkvb3JkZXJzX2ludGVybmV0bWFya2VfbmV3LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBvcmRlcnNfaW50ZXJuZXRtYXJrZV9uZXcuanMgMjAxOS0xMi0wNlxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgT3JkZXJzIEludGVybmV0TWFya2UgTW9kdWxlXG4gKlxuICogVGhpcyBtb2R1bGUgaW1wbGVtZW50cyB0aGUgdXNlciBpbnRlcmZhY2UgZm9yIGNyZWF0aW5nIHNoaXBwaW5nIGxhYmVscyB2aWEgRFAgSW50ZXJuZXRNYXJrZVxuICpcbiAqIEBtb2R1bGUgQ29tcGF0aWJpbGl0eS9vcmRlcnMvb3JkZXJzX2ludGVybmV0bWFya2VcbiAqL1xuZ3htb2R1bGVzLmNvbnRyb2xsZXJzLm1vZHVsZShcblx0J29yZGVyc19pbnRlcm5ldG1hcmtlX25ldycsXG5cblx0W1xuXHRcdGd4LnNvdXJjZSArICcvbGlicy9hY3Rpb25fbWFwcGVyJyxcblx0XHRneC5zb3VyY2UgKyAnL2xpYnMvYnV0dG9uX2Ryb3Bkb3duJ1xuXHRdLFxuXG5cdC8qKiAgQGxlbmRzIG1vZHVsZTpDb21wYXRpYmlsaXR5L29yZGVycy9vcmRlcnNfaW50ZXJuZXRtYXJrZSAqL1xuXG5cdGZ1bmN0aW9uIChkYXRhKSB7XG5cblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0XG5cdFx0dmFyIHJlbG9hZE9uQ2xvc2UgPSBmYWxzZTtcblxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBUaGUgbWFwcGVyIGxpYnJhcnlcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1hcHBlciA9IGpzZS5saWJzLmFjdGlvbl9tYXBwZXIsXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXG5cdFx0dmFyIF9pbml0U2luZ2xlRm9ybSA9IGZ1bmN0aW9uKG9yZGVyc19pZClcblx0XHR7XG5cdFx0XHR2YXIgZ3JpZF94ID0gMSwgZ3JpZF95ID0gMTtcblxuXHRcdFx0JCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybSAucmVjZWl2ZXJfZGF0YSBpbnB1dCcpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdHZhciBmaWVsZG5hbWUgPSAkKGUudGFyZ2V0KS5hdHRyKCduYW1lJykucmVwbGFjZSgvcmVjZWl2ZXJcXFsoLiopXFxdLywgJ3JlY2VpdmVyXyQxJyksXG5cdFx0XHRcdCAgICBmaWVsZHZhbHVlID0gJChlLnRhcmdldCkudmFsKCk7XG5cdFx0XHRcdCQoJ3NwYW4jJytmaWVsZG5hbWUpLnRvZ2dsZShmaWVsZHZhbHVlLmxlbmd0aCA+IDApO1xuXHRcdFx0XHRpZihmaWVsZG5hbWUgPT09ICdyZWNlaXZlcl9hZGRpdGlvbmFsJyB8fCBmaWVsZG5hbWUgPT09ICdyZWNlaXZlcl9jb21wYW55Jylcblx0XHRcdFx0e1xuXHRcdFx0XHRcdGZpZWxkdmFsdWUgKz0gJzxicj4nO1xuXHRcdFx0XHR9XG5cdFx0XHRcdCQoJ3NwYW4jJytmaWVsZG5hbWUpLmh0bWwoZmllbGR2YWx1ZSk7XG5cdFx0XHR9KTtcblx0XHRcdCQoJyNpbmV0bWFya2Vfc2luZ2xlX2Zvcm0gLnJlY2VpdmVyX2RhdGEgaW5wdXQnKS50cmlnZ2VyKCdjaGFuZ2UnKTtcblxuXHRcdFx0JCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybSBzZWxlY3RbbmFtZT1cInJlY2VpdmVyW2NvdW50cnldXCJdJykub24oJ2NoYW5nZScsIGZ1bmN0aW9uIChlKSB7XG5cdFx0XHRcdHZhciBpc05hdGlvbmFsICA9ICQoJ29wdGlvblt2YWx1ZT1cIkRFVVwiXTpzZWxlY3RlZCcsIGUudGFyZ2V0KS5sZW5ndGggPiAwLFxuXHRcdFx0XHQgICAgY291bnRyeU5hbWUgPSAkKCdvcHRpb246c2VsZWN0ZWQnLCAkKHRoaXMpKS50ZXh0KCksXG5cdFx0XHRcdCAgICAkcHJlZmVycmVkUHJvZHVjdCA9ICQoJ3NlbGVjdFtuYW1lPVwicHJvZHVjdENvZGVcIl0gb3B0aW9uLnByZWZlcnJlZCcpO1xuXHRcdFx0XHQkKCdzcGFuI3JlY2VpdmVyX2NvdW50cnknKS5odG1sKGNvdW50cnlOYW1lKTtcblx0XHRcdFx0JCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybSBvcHRpb24uZGVzdF9uYXRpb25hbCcpLnRvZ2dsZShpc05hdGlvbmFsID09PSB0cnVlKTtcblx0XHRcdFx0JCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybSBvcHRpb24uZGVzdF9pbnRlcm5hdGlvbmFsJykudG9nZ2xlKGlzTmF0aW9uYWwgPT09IGZhbHNlKTtcblx0XHRcdFx0dmFyIHByb2R1Y3RDb2RlO1xuXHRcdFx0XHRpZigkcHJlZmVycmVkUHJvZHVjdC5maWx0ZXIoJzplbmFibGVkJykubGVuZ3RoID4gMClcblx0XHRcdFx0e1xuXHRcdFx0XHRcdHByb2R1Y3RDb2RlID0gJHByZWZlcnJlZFByb2R1Y3QuYXR0cigndmFsdWUnKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRlbHNlXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRpZihpc05hdGlvbmFsID09PSB0cnVlKVxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdHByb2R1Y3RDb2RlID0gJCgnc2VsZWN0W25hbWU9XCJwcm9kdWN0Q29kZVwiXSBvcHRpb24uZGVzdF9uYXRpb25hbDpmaXJzdCcpLmF0dHIoJ3ZhbHVlJyk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdGVsc2Vcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRwcm9kdWN0Q29kZSA9ICQoJ3NlbGVjdFtuYW1lPVwicHJvZHVjdENvZGVcIl0gb3B0aW9uLmRlc3RfaW50ZXJuYXRpb25hbDpmaXJzdCcpLmF0dHIoJ3ZhbHVlJyk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cdFx0XHRcdCQoJ3NlbGVjdFtuYW1lPVwicHJvZHVjdENvZGVcIl0nKS52YWwocHJvZHVjdENvZGUpO1xuXHRcdFx0XHRfdXBkYXRlUHJldmlldygpO1xuXHRcdFx0fSk7XG5cdFx0XHQkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtIHNlbGVjdFtuYW1lPVwicmVjZWl2ZXJbY291bnRyeV1cIl0nKS50cmlnZ2VyKCdjaGFuZ2UnKTtcblxuXHRcdFx0JCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybSBzZWxlY3RbbmFtZT1cInZvdWNoZXJMYXlvdXRcIl0nKS5vbignY2hhbmdlJywgZnVuY3Rpb24oZSkge1xuXHRcdFx0XHR2YXIgaXNGcmFua2luZ1pvbmUgPSAkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtIHNlbGVjdFtuYW1lPVwidm91Y2hlckxheW91dFwiXSBvcHRpb25bdmFsdWU9XCJGcmFua2luZ1pvbmVcIl06c2VsZWN0ZWQnKS5sZW5ndGggPiAwO1xuXHRcdFx0XHQkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtIC5zZW5kZXJfbGluZScpLnRvZ2dsZShpc0ZyYW5raW5nWm9uZSA9PT0gZmFsc2UpO1xuXHRcdFx0XHQkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtIC5yZWNlaXZlcl9ibG9jaycpLnRvZ2dsZShpc0ZyYW5raW5nWm9uZSA9PT0gZmFsc2UpO1xuXHRcdFx0fSk7XG5cdFx0XHQkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtIHNlbGVjdFtuYW1lPVwidm91Y2hlckxheW91dFwiXScpLnRyaWdnZXIoJ2NoYW5nZScpO1xuXG5cdFx0XHQkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtIHNlbGVjdFtuYW1lPVwicGFnZUZvcm1hdElEXCJdJykub24oJ2NoYW5nZScsIGZ1bmN0aW9uKGUpIHtcblx0XHRcdFx0dmFyIGdyaWRfZGltZW5zaW9uc19tYXRjaCxcblx0XHRcdFx0XHQkcGFnZXBvc193aWRnZXQsICRwYWdlcG9zX3RhYmxlLCAkcGFnZXBvc19yb3csICRwYWdlcG9zX2NvbCxcblx0XHRcdFx0XHRyb3csIGNvbDtcblx0XHRcdFx0JHBhZ2Vwb3Nfd2lkZ2V0ID0gJCgnI3BhZ2Vwb3Nfd2lkZ2V0Jyk7XG5cdFx0XHRcdGdyaWRfZGltZW5zaW9uc19tYXRjaCA9ICQoJyNpbmV0bWFya2Vfc2luZ2xlX2Zvcm0gc2VsZWN0W25hbWU9XCJwYWdlRm9ybWF0SURcIl0gb3B0aW9uOnNlbGVjdGVkJykudGV4dCgpLm1hdGNoKC8uKlxcKChcXGQrKSB4IChcXGQrKSAuKi8pO1xuXHRcdFx0XHRncmlkX3ggPSBncmlkX2RpbWVuc2lvbnNfbWF0Y2hbMV07XG5cdFx0XHRcdGdyaWRfeSA9IGdyaWRfZGltZW5zaW9uc19tYXRjaFsyXTtcblx0XHRcdFx0JHBhZ2Vwb3Nfd2lkZ2V0LmVtcHR5KCk7XG5cdFx0XHRcdC8vJHBhZ2Vwb3Nfd2lkZ2V0LmFwcGVuZCgkKCc8ZGl2PmdyaWQgJytncmlkX3grJyAvICcrZ3JpZF95Kyc8L2Rpdj4nKSk7XG5cdFx0XHRcdCRwYWdlcG9zX3RhYmxlID0gJCgnPHRhYmxlIGlkPVwicGFnZXBvc190YWJsZVwiPjwvdGFibGU+Jyk7XG5cdFx0XHRcdGZvcihyb3cgPSAxOyByb3cgPD0gZ3JpZF95OyByb3crKylcblx0XHRcdFx0e1xuXHRcdFx0XHRcdCRwYWdlcG9zX3JvdyA9ICQoJzx0ciBjbGFzcz1cInBhZ2Vwb3NfJytyb3crJ1wiPjwvdHI+Jyk7XG5cdFx0XHRcdFx0Zm9yKGNvbCA9IDE7IGNvbCA8PSBncmlkX3g7IGNvbCsrKVxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdCRwYWdlcG9zX2NvbCA9ICQoJzx0ZCBjbGFzcz1cImNvbF8nK2NvbCsnXCI+PHNwYW4+Jytyb3crJ18nK2NvbCsnPC9zcGFuPjwvdGQ+Jyk7XG5cdFx0XHRcdFx0XHQkcGFnZXBvc19yb3cuYXBwZW5kKCRwYWdlcG9zX2NvbCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdCRwYWdlcG9zX3RhYmxlLmFwcGVuZCgkcGFnZXBvc19yb3cpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdCRwYWdlcG9zX3dpZGdldC5hcHBlbmQoJHBhZ2Vwb3NfdGFibGUpO1xuXHRcdFx0XHQkKCdpbnB1dFtuYW1lPVwicG9zaXRpb25fbGFiZWx4XCJdJykudmFsKCcxJyk7XG5cdFx0XHRcdCQoJ2lucHV0W25hbWU9XCJwb3NpdGlvbl9sYWJlbHlcIl0nKS52YWwoJzEnKTtcblx0XHRcdFx0X2luaXRQYWdlUG9zVGFibGVIYW5kbGVyKCk7XG5cdFx0XHRcdCQoJyNwYWdlcG9zX3RhYmxlIHRkJykuZmlyc3QoKS50cmlnZ2VyKCdjbGljaycpO1xuXHRcdFx0XHRpZihncmlkX3ggPT0gJzEnICYmIGdyaWRfeSA9PSAnMScpXG5cdFx0XHRcdHtcblx0XHRcdFx0XHQkKCcucGFnZXBvcycpLmNzcygnb3BhY2l0eScsICcwLjUnKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRlbHNlXG5cdFx0XHRcdHtcblx0XHRcdFx0XHQkKCcucGFnZXBvcycpLmNzcygnb3BhY2l0eScsICcxLjAnKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHQkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtIHNlbGVjdFtuYW1lPVwicGFnZUZvcm1hdElEXCJdJykudHJpZ2dlcignY2hhbmdlJyk7XG5cblx0XHRcdCQoJyNwYWdlcG9zX3RhYmxlIHRkJykuZmlyc3QoKS50cmlnZ2VyKCdjbGljaycpO1xuXHRcdFx0JCgnaW5wdXRbbmFtZT1cInBvc2l0aW9uX2xhYmVseFwiXScpLmNoYW5nZShmdW5jdGlvbihlKSB7IGlmKHBhcnNlSW50KCQodGhpcykudmFsKCkpID4gZ3JpZF94KSB7ICQodGhpcykudmFsKGdyaWRfeCk7IH0gfSk7XG5cdFx0XHQkKCdpbnB1dFtuYW1lPVwicG9zaXRpb25fbGFiZWx5XCJdJykuY2hhbmdlKGZ1bmN0aW9uKGUpIHsgaWYocGFyc2VJbnQoJCh0aGlzKS52YWwoKSkgPiBncmlkX3kpIHsgJCh0aGlzKS52YWwoZ3JpZF95KTsgfSB9KTtcblx0XHRcdCQoJ2lucHV0W25hbWU9XCJwb3NpdGlvbl9sYWJlbHhcIl0sIGlucHV0W25hbWU9XCJwb3NpdGlvbl9sYWJlbHlcIl0nKS5vbignY2hhbmdlJywgZnVuY3Rpb24oZSkge1xuXHRcdFx0XHRfdXBkYXRlUGFnZVBvc1RhYmxlKCk7XG5cdFx0XHR9KTtcblxuXHRcdFx0JCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybSAucHJldmlld09wdGlvbicpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdF91cGRhdGVQcmV2aWV3KCk7XG5cdFx0XHR9KTtcblxuXHRcdFx0X3VwZGF0ZVByZXZpZXcoKTtcblx0XHR9O1xuXG5cdFx0dmFyIF91cGRhdGVQYWdlUG9zVGFibGUgPSBmdW5jdGlvbigpXG5cdFx0e1xuXHRcdFx0dmFyIHJvdywgY29sLCAkdGFibGVyb3csICRjZWxsO1xuXHRcdFx0cm93ID0gJCgnaW5wdXRbbmFtZT1cInBvc2l0aW9uX2xhYmVseVwiXScpLnZhbCgpO1xuXHRcdFx0Y29sID0gJCgnaW5wdXRbbmFtZT1cInBvc2l0aW9uX2xhYmVseFwiXScpLnZhbCgpO1xuXHRcdFx0JHRhYmxlcm93ID0gJCgnI3BhZ2Vwb3NfdGFibGUgdHI6bnRoLWNoaWxkKCcrcm93KycpJyk7XG5cdFx0XHQkY2VsbCA9ICQoJ3RkOm50aC1jaGlsZCgnK2NvbCsnKScsICR0YWJsZXJvdyk7XG5cdFx0XHQkKCcjcGFnZXBvc190YWJsZSB0ZCcpLnJlbW92ZUNsYXNzKCdzZWxlY3RlZCcpO1xuXHRcdFx0JGNlbGwuYWRkQ2xhc3MoJ3NlbGVjdGVkJyk7XG5cdFx0fTtcblxuXHRcdHZhciBfaW5pdFBhZ2VQb3NUYWJsZUhhbmRsZXIgPSBmdW5jdGlvbigpXG5cdFx0e1xuXHRcdFx0JCgnI3BhZ2Vwb3NfdGFibGUgdGQnKS5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdHZhciBjZWxsdGV4dCwgY2VsbHRleHRfc3BsaXQsIHNlbGVjdGVkQ2VsbDtcblx0XHRcdFx0Y2VsbHRleHQgPSAkKCdzcGFuJywgJCh0aGlzKSkudGV4dCgpO1xuXHRcdFx0XHRjZWxsdGV4dF9zcGxpdCA9IGNlbGx0ZXh0Lm1hdGNoKC8oXFxkKylfKFxcZCspLyk7XG5cdFx0XHRcdHNlbGVjdGVkQ2VsbCA9IHtcblx0XHRcdFx0XHR4OiBjZWxsdGV4dF9zcGxpdFsyXSxcblx0XHRcdFx0XHR5OiBjZWxsdGV4dF9zcGxpdFsxXVxuXHRcdFx0XHR9O1xuXHRcdFx0XHQkKCdpbnB1dFtuYW1lPVwicG9zaXRpb25fbGFiZWx4XCJdJykudmFsKHNlbGVjdGVkQ2VsbC54KTtcblx0XHRcdFx0JCgnaW5wdXRbbmFtZT1cInBvc2l0aW9uX2xhYmVseVwiXScpLnZhbChzZWxlY3RlZENlbGwueSk7XG5cdFx0XHRcdCQoJyNwYWdlcG9zX3RhYmxlIHRkJykucmVtb3ZlQ2xhc3MoJ3NlbGVjdGVkJyk7XG5cdFx0XHRcdCQodGhpcykuYWRkQ2xhc3MoJ3NlbGVjdGVkJyk7XG5cdFx0XHR9KTtcblx0XHR9O1xuXG5cdFx0dmFyIF9pbml0Q3JlZGVudGlhbHNGb3JtID0gZnVuY3Rpb24oKVxuXHRcdHtcblx0XHRcdCQoJyNpbmV0bWFya2VfZW50ZXJfY3JlZGVudGlhbHMnKS5vbignc3VibWl0JywgX2NyZWRlbnRpYWxzU3VibWl0SGFuZGxlcik7XG5cdFx0XHQkKCcjdG9zX2FjY2VwdGVkJykub24oJ2NoYW5nZScsIGZ1bmN0aW9uKGUpIHtcblx0XHRcdFx0dmFyIGFjY2VwdGVkID0gJCgnI3Rvc19hY2NlcHRlZCcpLmdldCgwKS5jaGVja2VkO1xuXHRcdFx0XHRpZihhY2NlcHRlZCA9PT0gdHJ1ZSlcblx0XHRcdFx0e1xuXHRcdFx0XHRcdCQoJyNzdWJtaXRfc2Vzc2lvbl9jcmVkZW50aWFscycpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG5cdFx0XHRcdH1cblx0XHRcdFx0ZWxzZVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0JCgnI3N1Ym1pdF9zZXNzaW9uX2NyZWRlbnRpYWxzJykuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHQkKCcjdG9zX2FjY2VwdGVkJykudHJpZ2dlcignY2hhbmdlJyk7XG5cdFx0fTtcblxuXHRcdHZhciBfY3JlZGVudGlhbHNTdWJtaXRIYW5kbGVyID0gZnVuY3Rpb24oZSkge1xuXHRcdFx0dmFyIGZvcm1kYXRhID0gJCh0aGlzKS5zZXJpYWxpemUoKTtcblx0XHRcdCQuYWpheCh7XG5cdFx0XHRcdHR5cGU6ICdQT1NUJyxcblx0XHRcdFx0dXJsOiBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwP2RvPUludGVybmV0TWFya2UvU2V0U2Vzc2lvbkNyZWRlbnRpYWxzJyxcblx0XHRcdFx0ZGF0YTogZm9ybWRhdGEsXG5cdFx0XHRcdGRhdGFUeXBlOiAnanNvbidcblx0XHRcdH0pXG5cdFx0XHQuZG9uZShmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0XHRpZihkYXRhLnJlc3VsdCA9PT0gJ09LJylcblx0XHRcdFx0e1xuXHRcdFx0XHRcdCQoJyNpbmV0bWFya2VfbW9kYWxfY29udGVudCcpLmVtcHR5KCkuYWRkQ2xhc3MoJ2luZXRtYXJrZV9sb2FkaW5nJyk7XG5cdFx0XHRcdFx0JCgnI2luZXRtYXJrZV9tb2RhbF9jb250ZW50JykubG9hZCgnYWRtaW4ucGhwP2RvPUludGVybmV0TWFya2UvQ3JlYXRlTGFiZWxGb3JtJm9yZGVyc19pZD0nICsgZGF0YS5vcmRlcnNfaWQsXG5cdFx0XHRcdFx0XHRmdW5jdGlvbiAocmVzcG9uc2VUZXh0LCB0ZXh0U3RhdHVzLCBqcVhIUikge1xuXHRcdFx0XHRcdFx0XHQkKCcjaW5ldG1hcmtlX21vZGFsX2NvbnRlbnQnKS5yZW1vdmVDbGFzcygnaW5ldG1hcmtlX2xvYWRpbmcnKTtcblx0XHRcdFx0XHRcdFx0aWYoJCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybScpLmxlbmd0aCA+IDApXG5cdFx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0XHRfaW5pdFNpbmdsZUZvcm0oKTtcblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdCk7XG5cdFx0XHRcdH1cblx0XHRcdFx0ZWxzZVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0YWxlcnQoZGF0YS5lcnJvcl9tZXNzYWdlKTtcblx0XHRcdFx0fVxuXHRcdFx0fSlcblx0XHRcdC5mYWlsKGZ1bmN0aW9uIChkYXRhKSB7XG5cdFx0XHRcdGFsZXJ0KGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdzdWJtaXRfZXJyb3InLCAnaW50ZXJuZXRtYXJrZScpKTtcblx0XHRcdH0pO1xuXG5cdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0fTtcblxuXHRcdHZhciBfc2luZ2xlRm9ybVN1Ym1pdEhhbmRsZXIgPSBmdW5jdGlvbiAoZSkge1xuXHRcdFx0dmFyIGZvcm1kYXRhID0gJCgnI2luZXRtYXJrZV9zaW5nbGVfZm9ybScpLnNlcmlhbGl6ZSgpO1xuXHRcdFx0JCgnI2luZXRtYXJrZV9tb2RhbF9jb250ZW50JykuZW1wdHkoKS5hZGRDbGFzcygnaW5ldG1hcmtlX2xvYWRpbmcnKTtcblx0XHRcdCQuYWpheCh7XG5cdFx0XHRcdCAgICAgICB0eXBlOiAnUE9TVCcsXG5cdFx0XHRcdCAgICAgICB1cmw6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89SW50ZXJuZXRNYXJrZS9DcmVhdGVMYWJlbEZvcm1TdWJtaXQnLFxuXHRcdFx0XHQgICAgICAgZGF0YTogZm9ybWRhdGEsXG5cdFx0XHRcdCAgICAgICBkYXRhVHlwZTogJ2pzb24nXG5cdFx0XHQgICAgICAgfSlcblx0XHRcdFx0LmRvbmUoZnVuY3Rpb24gKGRhdGEpIHtcblx0XHRcdFx0XHQkKCcjaW5ldG1hcmtlX21vZGFsX2NvbnRlbnQnKS5yZW1vdmVDbGFzcygnaW5ldG1hcmtlX2xvYWRpbmcnKTtcblx0XHRcdFx0XHRpZiAoZGF0YS5yZXN1bHQgPT09ICdPSycpIHtcblx0XHRcdFx0XHRcdCQoJyNpbmV0bWFya2VfbW9kYWxfY29udGVudCcpLmxvYWQoJ2FkbWluLnBocD9kbz1JbnRlcm5ldE1hcmtlL0xpc3RWb3VjaGVycyZvcmRlcnNfaWQ9JyArIGRhdGEub3JkZXJzX2lkLFxuXHRcdFx0XHRcdFx0XHRmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0XHRfaW5pdFZvdWNoZXJMaXN0KCk7XG5cdFx0XHRcdFx0XHRcdFx0JCgnLmluZXRtYXJrZV92b3VjaGVycyBhLmlmcmFtZWRsOmxhc3QnKS50cmlnZ2VyKCdjbGljaycpO1xuXHRcdFx0XHRcdFx0XHRcdHJlbG9hZE9uQ2xvc2UgPSB0cnVlO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHQpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHRlbHNlIHtcblx0XHRcdFx0XHRcdGlmKGRhdGEuZXJyb3JfbWVzc2FnZSkge1xuXHRcdFx0XHRcdFx0XHRhbGVydChkYXRhLmVycm9yX21lc3NhZ2UpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0ZWxzZVxuXHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHRhbGVydChqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnc3VibWl0X2Vycm9yJywgJ2ludGVybmV0bWFya2UnKSk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KVxuXHRcdFx0XHQuZmFpbChmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0XHRcdGFsZXJ0KGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdzdWJtaXRfZXJyb3InLCAnaW50ZXJuZXRtYXJrZScpKTtcblx0XHRcdFx0fSk7XG5cblx0XHRcdHJldHVybiBmYWxzZTtcblx0XHR9O1xuXG5cdFx0dmFyIF9zaG9wUHJldmlld0hhbmRsZXIgPSBmdW5jdGlvbihlKVxuXHRcdHtcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdF91cGRhdGVQcmV2aWV3KCk7XG5cdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0fTtcblxuXHRcdHZhciBfdXBkYXRlUHJldmlldyA9IGZ1bmN0aW9uKClcblx0XHR7XG5cdFx0XHRpZigkKCdzZWxlY3RbbmFtZT1cInByb2R1Y3RDb2RlXCJdJykudmFsKCkgPT09IG51bGwpXG5cdFx0XHR7XG5cdFx0XHRcdGxldCBmaXJzdE5vbkRpc2FibGVkUHJvZHVjdENvZGUgPSAkKCdzZWxlY3RbbmFtZT1cInByb2R1Y3RDb2RlXCJdIG9wdGlvbicpLm5vdCgnOmRpc2FibGVkJykuZmlyc3QoKS52YWwoKTtcblx0XHRcdFx0JCgnc2VsZWN0W25hbWU9XCJwcm9kdWN0Q29kZVwiXScpLnZhbChmaXJzdE5vbkRpc2FibGVkUHJvZHVjdENvZGUpO1xuXHRcdFx0fVxuXG5cdFx0XHR2YXIgZm9ybWRhdGEgPSAkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtJykuc2VyaWFsaXplKCk7XG5cdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHRcdHR5cGU6ICdQT1NUJyxcblx0XHRcdFx0XHR1cmw6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89SW50ZXJuZXRNYXJrZS9QcmV2aWV3Vm91Y2hlcicsXG5cdFx0XHRcdFx0ZGF0YTogZm9ybWRhdGEsXG5cdFx0XHRcdFx0ZGF0YVR5cGU6ICdqc29uJ1xuXHRcdFx0ICAgICAgIH0pXG5cdFx0XHRcdC5kb25lKGZ1bmN0aW9uIChkYXRhKSB7XG5cdFx0XHRcdFx0aWYoZGF0YS5yZXN1bHQgPT09ICdPSycpIHtcblx0XHRcdFx0XHRcdGlmKCQoJyNpbmV0bWFya2VfcHJldmlldyBpbWcnKS5sZW5ndGggPiAwKVxuXHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHQkKCcjaW5ldG1hcmtlX3ByZXZpZXcgaW1nJykuYXR0cignc3JjJywgZGF0YS5wcmV2aWV3bGluayk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRlbHNlXG5cdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdHZhciBwcmV2aWV3SW1nID0gJCgnPGltZyBzcmM9XCInKyBkYXRhLnByZXZpZXdsaW5rICsnXCI+Jyk7XG5cdFx0XHRcdFx0XHRcdCQoJyNpbmV0bWFya2VfcHJldmlldycpLmVtcHR5KCkuYXBwZW5kKHByZXZpZXdJbWcpLnNob3coKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0ZWxzZSB7XG5cdFx0XHRcdFx0XHRpZihkYXRhLmVycm9yX21lc3NhZ2UpIHtcblx0XHRcdFx0XHRcdFx0YWxlcnQoZGF0YS5lcnJvcl9tZXNzYWdlKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pXG5cdFx0XHRcdC5mYWlsKGZ1bmN0aW9uIChkYXRhKSB7XG5cdFx0XHRcdFx0YWxlcnQoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3N1Ym1pdF9lcnJvcicsICdpbnRlcm5ldG1hcmtlJykpO1xuXHRcdFx0XHR9KTtcblx0XHR9O1xuXG5cdFx0dmFyIF9pbml0Vm91Y2hlckxpc3QgPSBmdW5jdGlvbigpIHtcblx0XHRcdCQoJyNpbmV0bWFya2VfbW9kYWxfY29udGVudCcpLnJlbW92ZUNsYXNzKCdpbmV0bWFya2VfbG9hZGluZycpO1xuXHRcdFx0JCgnI2luZXRtYXJrZV9jcmVhdGVfbGFiZWwnKS5oaWRlKCk7XG5cdFx0XHQkKCcjaW5ldG1hcmtlX3Nob3dWb3VjaGVyTGlzdCcpLmhpZGUoKTtcblx0XHRcdC8qXG5cdFx0XHQkKCcuaW5ldG1hcmtlX3ZvdWNoZXJzIGEuaWZyYW1lZGwnKS5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdHZhciAkcGFyZW50ID0gJCh0aGlzKS5wYXJlbnQoKSxcblx0XHRcdFx0ICAgIGhyZWYgPSAkKHRoaXMpLmF0dHIoJ2hyZWYnKTtcblx0XHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0XHQkKCdpZnJhbWUnLCAkcGFyZW50KS5yZW1vdmUoKTtcblx0XHRcdFx0dmFyICRkbGlmcmFtZSA9ICQoJzxpZnJhbWUgc3JjPVwiJytocmVmKydcIiBzdHlsZT1cIndpZHRoOjA7IGhlaWdodDowOyBib3JkZXI6bm9uZTtcIj48L2lmcmFtZT4nKTtcblx0XHRcdFx0JHBhcmVudC5hcHBlbmQoJGRsaWZyYW1lKTtcblx0XHRcdH0pO1xuXHRcdFx0Ki9cblx0XHR9O1xuXG5cdFx0dmFyIF9vcGVuU2luZ2xlRm9ybU1vZGFsID0gZnVuY3Rpb24oZXZlbnQpXG5cdFx0e1xuXHRcdFx0dmFyIG9yZGVyc19pZCA9ICQoZXZlbnQudGFyZ2V0KS5wYXJlbnRzKCd0cicpLmRhdGEoJ3Jvdy1pZCcpO1xuXHRcdFx0aWYoIW9yZGVyc19pZCAmJiAkKGV2ZW50LnRhcmdldCkuYXR0cignaHJlZicpKVxuXHRcdFx0e1xuXHRcdFx0XHRvcmRlcnNfaWQgPSAkKGV2ZW50LnRhcmdldCkuYXR0cignaHJlZicpLnJlcGxhY2UoLy4qb0lEPShcXGQrKS4qLywgJyQxJyk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlXG5cdFx0XHR7XG5cdFx0XHRcdG9yZGVyc19pZCA9ICQoJ2JvZHknKS5maW5kKCcjZ21fb3JkZXJfaWQnKS52YWwoKTtcblx0XHRcdH1cblx0XHRcdCQoJyNpbmV0bWFya2VfbW9kYWxfY29udGVudCcpLmVtcHR5KCkuYWRkQ2xhc3MoJ2luZXRtYXJrZV9sb2FkaW5nJyk7XG5cblx0XHRcdHZhciBpbnRlcm5ldG1hcmtlX21vZGFsX2J1dHRvbnMgPSBbXTtcblx0XHRcdGludGVybmV0bWFya2VfbW9kYWxfYnV0dG9ucy5wdXNoKHtcblx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnY2xvc2UnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHQnY2xhc3MnOiAnYnRuJyxcblx0XHRcdFx0J2NsaWNrJzogZnVuY3Rpb24gKCkge1xuXHRcdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdGludGVybmV0bWFya2VfbW9kYWxfYnV0dG9ucy5wdXNoKHtcblx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnaW50ZXJuZXRtYXJrZV9jcmVhdGVfdm91Y2hlcicsICdhZG1pbl9sYWJlbHMnKSxcblx0XHRcdFx0J2NsYXNzJzogJ2J0biBidG4tcHJpbWFyeScsXG5cdFx0XHRcdCdjbGljayc6IF9zaW5nbGVGb3JtU3VibWl0SGFuZGxlcixcblx0XHRcdFx0J2lkJzogJ2luZXRtYXJrZV9jcmVhdGVfbGFiZWwnXG5cdFx0XHR9KTtcblx0XHRcdGludGVybmV0bWFya2VfbW9kYWxfYnV0dG9ucy5wdXNoKHtcblx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnaW50ZXJuZXRtYXJrZV9zaG93X3ZvdWNoZXJzJywgJ2FkbWluX2xhYmVscycpLFxuXHRcdFx0XHQnY2xhc3MnOiAnYnRuJyxcblx0XHRcdFx0J2NsaWNrJzogZnVuY3Rpb24oZSkge1xuXHRcdFx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFx0XHQkKCcjaW5ldG1hcmtlX21vZGFsX2NvbnRlbnQnKS5lbXB0eSgpLmFkZENsYXNzKCdpbmV0bWFya2VfbG9hZGluZycpO1xuXHRcdFx0XHRcdCQoJyNpbmV0bWFya2VfbW9kYWxfY29udGVudCcpLmxvYWQoJ2FkbWluLnBocD9kbz1JbnRlcm5ldE1hcmtlL0xpc3RWb3VjaGVycyZvcmRlcnNfaWQ9JyArIG9yZGVyc19pZCwgX2luaXRWb3VjaGVyTGlzdCk7XG5cdFx0XHRcdH0sXG5cdFx0XHRcdCdpZCc6ICdpbmV0bWFya2Vfc2hvd1ZvdWNoZXJMaXN0J1xuXHRcdFx0fSk7XG5cblx0XHRcdCQoJyNpbnRlcm5ldG1hcmtlX21vZGFsJykuZGlhbG9nKHtcblx0XHRcdFx0YXV0b09wZW46IGZhbHNlLFxuXHRcdFx0XHRtb2RhbDogdHJ1ZSxcblx0XHRcdFx0J3RpdGxlJzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2ludGVybmV0bWFya2VfY3JlYXRlX2xhYmVsJywgJ2FkbWluX2xhYmVscycpLFxuXHRcdFx0XHQnZGlhbG9nQ2xhc3MnOiAnZ3gtY29udGFpbmVyJyxcblx0XHRcdFx0YnV0dG9uczogaW50ZXJuZXRtYXJrZV9tb2RhbF9idXR0b25zLFxuXHRcdFx0XHR3aWR0aDogMTAwMCxcblx0XHRcdFx0cG9zaXRpb246IHsgbXk6ICdjZW50ZXIgdG9wJywgYXQ6ICdjZW50ZXIgYm90dG9tJywgb2Y6ICcubWFpbi10b3AtaGVhZGVyJyB9LFxuXHRcdFx0XHRjbG9zZTogZnVuY3Rpb24oZXZlbnQsIHVpKSB7XG5cdFx0XHRcdFx0aWYocmVsb2FkT25DbG9zZSkge1xuXHRcdFx0XHRcdFx0Y29uc29sZS5pbmZvKCdyZWxvYWRpbmcnKTtcblx0XHRcdFx0XHRcdGxvY2F0aW9uLnJlbG9hZCgpO1xuXHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHRjb25zb2xlLmluZm8oJ25vdCByZWxvYWRpbmcnKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0JCgnI2ludGVybmV0bWFya2VfbW9kYWwnKS5kaWFsb2coJ29wZW4nKTtcblx0XHRcdCQoJyNpbmV0bWFya2VfbW9kYWxfY29udGVudCcpLmxvYWQoJ2FkbWluLnBocD9kbz1JbnRlcm5ldE1hcmtlL0NyZWF0ZUxhYmVsRm9ybSZvcmRlcnNfaWQ9JyArIG9yZGVyc19pZCxcblx0XHRcdFx0ZnVuY3Rpb24gKHJlc3BvbnNlVGV4dCwgdGV4dFN0YXR1cywganFYSFIpIHtcblx0XHRcdFx0XHQkKCcjaW5ldG1hcmtlX21vZGFsX2NvbnRlbnQnKS5yZW1vdmVDbGFzcygnaW5ldG1hcmtlX2xvYWRpbmcnKTtcblx0XHRcdFx0XHRpZigkKCcjaW5ldG1hcmtlX3NpbmdsZV9mb3JtJykubGVuZ3RoID4gMClcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRfaW5pdFNpbmdsZUZvcm0ob3JkZXJzX2lkKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0aWYoJCgnI2luZXRtYXJrZV9lbnRlcl9jcmVkZW50aWFscycpLmxlbmd0aCA+IDApXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0X2luaXRDcmVkZW50aWFsc0Zvcm0oKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0aWYoJCgnI2luZXRtYXJrZV9iYWxhbmNlX3Rvb19sb3cnKS5sZW5ndGggPiAwKVxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdCQoJyNpbnRlcm5ldG1hcmtlX21vZGFsJykuZGlhbG9nKCdvcHRpb24nLCAnYnV0dG9ucycsXG5cdFx0XHRcdFx0XHRcdFtcblx0XHRcdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdFx0XHQndGV4dCc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjbG9zZScsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuJyxcblx0XHRcdFx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdFx0XHQkKCcjaW50ZXJuZXRtYXJrZV9tb2RhbCcpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdF1cblx0XHRcdFx0XHRcdCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cdFx0XHQpO1xuXHRcdH07XG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuXHRcdFx0JCgnZGl2LmludGVybmV0bWFya2Vfb3JkZXJkZXRhaWxzJykuY2xvc2VzdCgnZGl2LmZyYW1lLXdyYXBwZXInKS5oaWRlKCk7XG5cdFx0XHQkKCdoZWFkJykuYXBwZW5kKCQoJzxsaW5rIHJlbD1cInN0eWxlc2hlZXRcIiBocmVmPVwiJytqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSsnL0dYTW9kdWxlcy9HYW1iaW8vSW50ZXJuZXRtYXJrZS9BZG1pbi9TdHlsZXMvaW50ZXJuZXRtYXJrZS5jc3NcIj4nKSk7XG5cdFx0XHQkKCdib2R5JykucHJlcGVuZCgkKCc8ZGl2IGlkPVwiaW50ZXJuZXRtYXJrZV9tb2RhbFwiIHRpdGxlPVwiJyArIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKFxuXHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICdjcmVhdGVfbGFiZWxfd2luZG93X3RpdGxlJywgJ2ludGVybmV0bWFya2UnKSArXG5cdFx0XHQgICAgICAgICAgICAgICAgICAgICdcIiBzdHlsZT1cImRpc3BsYXk6IG5vbmU7XCI+PGRpdiBpZD1cImluZXRtYXJrZV9tb2RhbF9jb250ZW50XCI+PC9kaXY+PC9kaXY+JykpO1xuXHRcdFx0XG5cdFx0XHR2YXIgaW50ZXJ2YWxfY291bnRlciA9IDEwLFxuXHRcdFx0XHRpbnRlcnZhbCA9IHNldEludGVydmFsKGZ1bmN0aW9uICgpIHtcblx0XHRcdFx0XHRpZiAoanNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duICYmICQoJy5qcy1idXR0b24tZHJvcGRvd24nKS5sZW5ndGgpIHtcblx0XHRcdFx0XHRcdGNsZWFySW50ZXJ2YWwoaW50ZXJ2YWwpO1xuXHRcdFx0XHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLm1hcEFjdGlvbigkKCcuYm90dG9tLXNhdmUtYmFyJyksICdpbnRlcm5ldG1hcmtlX2NyZWF0ZV9sYWJlbCcsICdhZG1pbl9sYWJlbHMnLCBfb3BlblNpbmdsZUZvcm1Nb2RhbCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdGlmIChpbnRlcnZhbF9jb3VudGVyLS0gPT09IDApIHtcblx0XHRcdFx0XHRcdGNsZWFySW50ZXJ2YWwoaW50ZXJ2YWwpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSwgNDAwKTtcblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
