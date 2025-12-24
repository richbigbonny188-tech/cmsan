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
gxmodules.controllers.module(
	'orders_internetmarke_new',

	[
		gx.source + '/libs/action_mapper',
		gx.source + '/libs/button_dropdown'
	],

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

		var _initSingleForm = function(orders_id)
		{
			var grid_x = 1, grid_y = 1;

			$('#inetmarke_single_form .receiver_data input').on('change', function(e) {
				var fieldname = $(e.target).attr('name').replace(/receiver\[(.*)\]/, 'receiver_$1'),
				    fieldvalue = $(e.target).val();
				$('span#'+fieldname).toggle(fieldvalue.length > 0);
				if(fieldname === 'receiver_additional' || fieldname === 'receiver_company')
				{
					fieldvalue += '<br>';
				}
				$('span#'+fieldname).html(fieldvalue);
			});
			$('#inetmarke_single_form .receiver_data input').trigger('change');

			$('#inetmarke_single_form select[name="receiver[country]"]').on('change', function (e) {
				var isNational  = $('option[value="DEU"]:selected', e.target).length > 0,
				    countryName = $('option:selected', $(this)).text(),
				    $preferredProduct = $('select[name="productCode"] option.preferred');
				$('span#receiver_country').html(countryName);
				$('#inetmarke_single_form option.dest_national').toggle(isNational === true);
				$('#inetmarke_single_form option.dest_international').toggle(isNational === false);
				var productCode;
				if($preferredProduct.filter(':enabled').length > 0)
				{
					productCode = $preferredProduct.attr('value');
				}
				else
				{
					if(isNational === true)
					{
						productCode = $('select[name="productCode"] option.dest_national:first').attr('value');
					}
					else
					{
						productCode = $('select[name="productCode"] option.dest_international:first').attr('value');
					}
				}
				$('select[name="productCode"]').val(productCode);
				_updatePreview();
			});
			$('#inetmarke_single_form select[name="receiver[country]"]').trigger('change');

			$('#inetmarke_single_form select[name="voucherLayout"]').on('change', function(e) {
				var isFrankingZone = $('#inetmarke_single_form select[name="voucherLayout"] option[value="FrankingZone"]:selected').length > 0;
				$('#inetmarke_single_form .sender_line').toggle(isFrankingZone === false);
				$('#inetmarke_single_form .receiver_block').toggle(isFrankingZone === false);
			});
			$('#inetmarke_single_form select[name="voucherLayout"]').trigger('change');

			$('#inetmarke_single_form select[name="pageFormatID"]').on('change', function(e) {
				var grid_dimensions_match,
					$pagepos_widget, $pagepos_table, $pagepos_row, $pagepos_col,
					row, col;
				$pagepos_widget = $('#pagepos_widget');
				grid_dimensions_match = $('#inetmarke_single_form select[name="pageFormatID"] option:selected').text().match(/.*\((\d+) x (\d+) .*/);
				grid_x = grid_dimensions_match[1];
				grid_y = grid_dimensions_match[2];
				$pagepos_widget.empty();
				//$pagepos_widget.append($('<div>grid '+grid_x+' / '+grid_y+'</div>'));
				$pagepos_table = $('<table id="pagepos_table"></table>');
				for(row = 1; row <= grid_y; row++)
				{
					$pagepos_row = $('<tr class="pagepos_'+row+'"></tr>');
					for(col = 1; col <= grid_x; col++)
					{
						$pagepos_col = $('<td class="col_'+col+'"><span>'+row+'_'+col+'</span></td>');
						$pagepos_row.append($pagepos_col);
					}
					$pagepos_table.append($pagepos_row);
				}
				$pagepos_widget.append($pagepos_table);
				$('input[name="position_labelx"]').val('1');
				$('input[name="position_labely"]').val('1');
				_initPagePosTableHandler();
				$('#pagepos_table td').first().trigger('click');
				if(grid_x == '1' && grid_y == '1')
				{
					$('.pagepos').css('opacity', '0.5');
				}
				else
				{
					$('.pagepos').css('opacity', '1.0');
				}
			});
			$('#inetmarke_single_form select[name="pageFormatID"]').trigger('change');

			$('#pagepos_table td').first().trigger('click');
			$('input[name="position_labelx"]').change(function(e) { if(parseInt($(this).val()) > grid_x) { $(this).val(grid_x); } });
			$('input[name="position_labely"]').change(function(e) { if(parseInt($(this).val()) > grid_y) { $(this).val(grid_y); } });
			$('input[name="position_labelx"], input[name="position_labely"]').on('change', function(e) {
				_updatePagePosTable();
			});

			$('#inetmarke_single_form .previewOption').on('change', function(e) {
				_updatePreview();
			});

			_updatePreview();
		};

		var _updatePagePosTable = function()
		{
			var row, col, $tablerow, $cell;
			row = $('input[name="position_labely"]').val();
			col = $('input[name="position_labelx"]').val();
			$tablerow = $('#pagepos_table tr:nth-child('+row+')');
			$cell = $('td:nth-child('+col+')', $tablerow);
			$('#pagepos_table td').removeClass('selected');
			$cell.addClass('selected');
		};

		var _initPagePosTableHandler = function()
		{
			$('#pagepos_table td').on('click', function(e) {
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

		var _initCredentialsForm = function()
		{
			$('#inetmarke_enter_credentials').on('submit', _credentialsSubmitHandler);
			$('#tos_accepted').on('change', function(e) {
				var accepted = $('#tos_accepted').get(0).checked;
				if(accepted === true)
				{
					$('#submit_session_credentials').removeAttr('disabled');
				}
				else
				{
					$('#submit_session_credentials').attr('disabled', 'disabled');
				}
			});
			$('#tos_accepted').trigger('change');
		};

		var _credentialsSubmitHandler = function(e) {
			var formdata = $(this).serialize();
			$.ajax({
				type: 'POST',
				url: jse.core.config.get('appUrl') + '/admin/admin.php?do=InternetMarke/SetSessionCredentials',
				data: formdata,
				dataType: 'json'
			})
			.done(function (data) {
				if(data.result === 'OK')
				{
					$('#inetmarke_modal_content').empty().addClass('inetmarke_loading');
					$('#inetmarke_modal_content').load('admin.php?do=InternetMarke/CreateLabelForm&orders_id=' + data.orders_id,
						function (responseText, textStatus, jqXHR) {
							$('#inetmarke_modal_content').removeClass('inetmarke_loading');
							if($('#inetmarke_single_form').length > 0)
							{
								_initSingleForm();
							}
						}
					);
				}
				else
				{
					alert(data.error_message);
				}
			})
			.fail(function (data) {
				alert(jse.core.lang.translate('submit_error', 'internetmarke'));
			});

			return false;
		};

		var _singleFormSubmitHandler = function (e) {
			var formdata = $('#inetmarke_single_form').serialize();
			$('#inetmarke_modal_content').empty().addClass('inetmarke_loading');
			$.ajax({
				       type: 'POST',
				       url: jse.core.config.get('appUrl') + '/admin/admin.php?do=InternetMarke/CreateLabelFormSubmit',
				       data: formdata,
				       dataType: 'json'
			       })
				.done(function (data) {
					$('#inetmarke_modal_content').removeClass('inetmarke_loading');
					if (data.result === 'OK') {
						$('#inetmarke_modal_content').load('admin.php?do=InternetMarke/ListVouchers&orders_id=' + data.orders_id,
							function() {
								_initVoucherList();
								$('.inetmarke_vouchers a.iframedl:last').trigger('click');
								reloadOnClose = true;
							}
						);
					}
					else {
						if(data.error_message) {
							alert(data.error_message);
						}
						else
						{
							alert(jse.core.lang.translate('submit_error', 'internetmarke'));
						}
					}
				})
				.fail(function (data) {
					alert(jse.core.lang.translate('submit_error', 'internetmarke'));
				});

			return false;
		};

		var _shopPreviewHandler = function(e)
		{
			e.preventDefault();
			_updatePreview();
			return false;
		};

		var _updatePreview = function()
		{
			if($('select[name="productCode"]').val() === null)
			{
				let firstNonDisabledProductCode = $('select[name="productCode"] option').not(':disabled').first().val();
				$('select[name="productCode"]').val(firstNonDisabledProductCode);
			}

			var formdata = $('#inetmarke_single_form').serialize();
			$.ajax({
					type: 'POST',
					url: jse.core.config.get('appUrl') + '/admin/admin.php?do=InternetMarke/PreviewVoucher',
					data: formdata,
					dataType: 'json'
			       })
				.done(function (data) {
					if(data.result === 'OK') {
						if($('#inetmarke_preview img').length > 0)
						{
							$('#inetmarke_preview img').attr('src', data.previewlink);
						}
						else
						{
							var previewImg = $('<img src="'+ data.previewlink +'">');
							$('#inetmarke_preview').empty().append(previewImg).show();
						}
					}
					else {
						if(data.error_message) {
							alert(data.error_message);
						}
					}
				})
				.fail(function (data) {
					alert(jse.core.lang.translate('submit_error', 'internetmarke'));
				});
		};

		var _initVoucherList = function() {
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

		var _openSingleFormModal = function(event)
		{
			var orders_id = $(event.target).parents('tr').data('row-id');
			if(!orders_id && $(event.target).attr('href'))
			{
				orders_id = $(event.target).attr('href').replace(/.*oID=(\d+).*/, '$1');
			}
			else
			{
				orders_id = $('body').find('#gm_order_id').val();
			}
			$('#inetmarke_modal_content').empty().addClass('inetmarke_loading');

			var internetmarke_modal_buttons = [];
			internetmarke_modal_buttons.push({
				'text': jse.core.lang.translate('close', 'buttons'),
				'class': 'btn',
				'click': function () {
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
				'click': function(e) {
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
				close: function(event, ui) {
					if(reloadOnClose) {
						console.info('reloading');
						location.reload();
					} else {
						console.info('not reloading');
					}
				}
			});
			$('#internetmarke_modal').dialog('open');
			$('#inetmarke_modal_content').load('admin.php?do=InternetMarke/CreateLabelForm&orders_id=' + orders_id,
				function (responseText, textStatus, jqXHR) {
					$('#inetmarke_modal_content').removeClass('inetmarke_loading');
					if($('#inetmarke_single_form').length > 0)
					{
						_initSingleForm(orders_id);
					}
					if($('#inetmarke_enter_credentials').length > 0)
					{
						_initCredentialsForm();
					}
					if($('#inetmarke_balance_too_low').length > 0)
					{
						$('#internetmarke_modal').dialog('option', 'buttons',
							[
								{
									'text': jse.core.lang.translate('close', 'buttons'),
									'class': 'btn',
									'click': function() {
										$('#internetmarke_modal').dialog('close');
									}
								}
							]
						);
					}
				}
			);
		};
		
		module.init = function (done) {
			$('div.internetmarke_orderdetails').closest('div.frame-wrapper').hide();
			$('head').append($('<link rel="stylesheet" href="'+jse.core.config.get('appUrl')+'/GXModules/Gambio/Internetmarke/Admin/Styles/internetmarke.css">'));
			$('body').prepend($('<div id="internetmarke_modal" title="' + jse.core.lang.translate(
				                    'create_label_window_title', 'internetmarke') +
			                    '" style="display: none;"><div id="inetmarke_modal_content"></div></div>'));
			
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
