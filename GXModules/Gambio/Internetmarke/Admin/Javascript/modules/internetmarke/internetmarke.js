/* --------------------------------------------------------------
	internetmarke.js 2018-11-08
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

$(function() {
	'use strict';

	/* -------------------------------------------------------------------------------------------------- */
	
	var reloadOnClose = false;

	const _initSingleForm = function(orders_id)
	{
		let grid_x = 1, grid_y = 1;

		$('#inetmarke_single_form .receiver_data input').on('change', function(e) {
			const fieldname = $(e.target).attr('name').replace(/receiver\[(.*)\]/, 'receiver_$1');
			let fieldvalue = $(e.target).val();
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

	const _updatePagePosTable = function()
	{
		var row, col, $tablerow, $cell;
		row = $('input[name="position_labely"]').val();
		col = $('input[name="position_labelx"]').val();
		$tablerow = $('#pagepos_table tr:nth-child('+row+')');
		$cell = $('td:nth-child('+col+')', $tablerow);
		$('#pagepos_table td').removeClass('selected');
		$cell.addClass('selected');
	};

	const _initPagePosTableHandler = function()
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

	const _initCredentialsForm = function()
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

	const _credentialsSubmitHandler = function(e) {
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

	const _singleFormSubmitHandler = function (e) {
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
					$('#inetmarke_modal_content').load('admin.php?do=InternetMarke/ListVouchers&template_version=2&orders_id=' + data.orders_id,
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
					} else {
						alert(jse.core.lang.translate('submit_error', 'internetmarke'));
					}
					$('#internetmarke_modal').dialog('close');
				}
			})
			.fail(function (data) {
				if(data.error_message) {
					alert(data.error_message);
				} else {
					alert(jse.core.lang.translate('submit_error', 'internetmarke'));
				}
				$('#internetmarke_modal').dialog('close');
			});

		return false;
	};

	const _shopPreviewHandler = function(e)
	{
		e.preventDefault();
		_updatePreview();
		return false;
	};

	const _updatePreview = function()
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

	const _initVoucherList = function() {
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

	const _openSingleFormModal = function(event)
	{
		const orders_id = $(event.target).parents('tr').attr('id') || $('body').find('#gm_order_id').val();
		$('#inetmarke_modal_content').empty().addClass('inetmarke_loading');

		let internetmarke_modal_buttons = [];
		internetmarke_modal_buttons.push({
			'text': jse.core.lang.translate('close', 'buttons'),
			'class': 'btn',
			'click': function () {
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
			'click': function(e) {
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
		$('#inetmarke_modal_content').load('admin.php?do=InternetMarke/CreateLabelForm&template_version=2&orders_id=' + orders_id,
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

	/* -------------------------------------------------------------------------------------------------- */

	const $table = $('.orders .table-main');
	
	$('body').prepend($('<div id="internetmarke_modal" title="' +
		jse.core.lang.translate('create_label_window_title', 'internetmarke') +
		'" style="display: none;"><div id="inetmarke_modal_content"></div></div>'));

	$table.on('init.dt', function() {
		const _initSingleAction = function($table) {
			$table.find('tbody .btn-group.dropdown').each(function(index, dropdown) {
				const orderId = $(this).parents('tr').data('id');
				const defaultRowAction = $table.data('defaultRowAction') || 'edit';

				jse.libs.button_dropdown.addAction($(dropdown), {
					text: jse.core.lang.translate('create_label', 'internetmarke'),
					href: jse.core.config.get('appUrl') + '/admin/dummy.php?orders_id=' + orderId,
					class: 'internetmarke-single',
					data: {configurationValue: 'internetmarke-single'},
					isDefault: defaultRowAction === 'internetmarke-single',
					callback: function(e) { e.preventDefault(); _openSingleFormModal(e); },
				});
			});
		};

        $table.on('draw.dt', () => _initSingleAction($table));
		_initSingleAction($table);
	}) ;

});
