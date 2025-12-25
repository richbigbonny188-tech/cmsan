<?php
/* --------------------------------------------------------------
   javascripts.js.php 2022-08-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2003	 nextcommerce (index.php,v 1.18 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: index.php 1220 2005-09-16 15:53:13Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

if(isset($_GET['language']) && $_GET['language'] == 'english')
{
	include('../language/english.php');
}
else
{
	include('../language/german.php');
}

header('Content-type: text/javascript; charset=utf-8');
?>
var t_http_valid = false;
var t_https_valid = false;
var t_http_interval = 0;
var t_https_interval = 0;
var t_http_counter = 0;
var t_https_counter = 0;

async function getImageHeightAsync(imageUrl) {
	return await new Promise((resolve) => {
		const img = new Image();
		img.onload = function() {
			resolve(this.height);
		};
		img.onerror = function() {
			resolve(undefined);
		}
		img.src = imageUrl;
	});
}

async function validateServerAsync(p_ssl) {
	if(
		$('.server_data').css('display') != 'none' &&
		typeof($('input[name="HTTP_SERVER"]').val()) !== 'undefined'
	) {
		if(p_ssl === true)
		{
			const t_ssl_test_image = await getImageHeightAsync(
				'https://' +
				$('input[name="HTTP_SERVER"]').val() +
				$('input[name="DIR_WS_CATALOG"]').val() +
				'images/pixel_trans.gif'
			);

			if(typeof(t_ssl_test_image) !== 'undefined' && t_ssl_test_image > 0) {
				clearInterval(t_https_interval);
				t_https_valid = true;
				t_https_counter = 0;
				console.log(['t_https_valid', true]);
				$('#http_server_error').hide();
				$('#ssl').val('true');
				$('#server_prefix').text('https://');
				$('input[name="HTTP_SERVER"]').addClass('valid');
				$('input[name="HTTP_SERVER"]').removeClass('invalid');
				
				return true;
			}
			else
			{
				if(t_https_counter > 10)
				{
					clearInterval(t_https_interval);
				}
				t_https_counter++;
				t_https_valid = false;
				console.log(['t_https_valid', false]);
				
				$('#ssl').val('false');
				$('#server_prefix').text('http://');
				
				return false;
			}
		}
		else
		{
			const t_test_image = await getImageHeightAsync(
				'http://' +
				$('input[name="HTTP_SERVER"]').val() +
				$('input[name="DIR_WS_CATALOG"]').val() +
				'images/pixel_trans.gif'
			);

			if(typeof(t_test_image) !== 'undefined' && t_test_image > 0)
			{
				clearInterval(t_http_interval);
				t_http_valid = true;
				t_http_counter = 0;
				console.log(['t_http_valid', true]);
				
				if(!t_https_valid)
				{
					$('#ssl_notice').hide();
					$('#http_server_error').hide();
					$('input[name="HTTP_SERVER"]').addClass('valid');
					$('input[name="HTTP_SERVER"]').removeClass('invalid');
				}
				else
				{
					$('#ssl').val('true');
					$('#server_prefix').text('https://');
					$('input[name="HTTP_SERVER"]').addClass('valid');
					$('input[name="HTTP_SERVER"]').removeClass('invalid');
				}
				
				return true;
			}
			else
			{
				if(t_http_counter > 10)
				{
					clearInterval(t_http_interval);
				}
				t_http_counter++;
				t_http_valid = false;
				console.log(['t_http_valid', false]);
				
				if(!t_https_valid)
				{
					$('#ssl_notice').hide();
					$('#http_server_error').show();
					$('input[name="HTTP_SERVER"]').removeClass('valid');
					$('input[name="HTTP_SERVER"]').addClass('invalid');
				}
				else
				{
					$('#ssl').val('true');
					$('#server_prefix').text('https://');
					$('input[name="HTTP_SERVER"]').addClass('valid');
					$('input[name="HTTP_SERVER"]').removeClass('invalid');
				}
				
				return false;
			}
		}
	}
}

var validate_input = function(p_string, p_min_len, p_only_numbers, p_mail)
{
	var t_string = jQuery.trim(p_string);

	if(typeof(p_mail) == 'boolean' && p_mail == true)
	{
		t_pattern = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		
		return t_pattern.test(t_string);
	}

	if(typeof(p_only_numbers) == 'boolean' && p_only_numbers == true)
	{
		t_pattern = /(^[0-9]+$)/g;

		if(!t_pattern.test(t_string))
		{
			return false;
		}
	}

	if(typeof(p_min_len) == 'undefined')
	{
		p_min_len = 0;
	}
	else
	{
		if(t_string.length >= Number(p_min_len))
		{
			return true;
		}
		
		return false;
	}

	return true;
}

var validate_form = function()
{
	var t_valid = true;

	if(!validate_input($('input[name="FIRST_NAME"]').val(), 2))
	{
		$('input[name="FIRST_NAME"]').removeClass('valid').addClass('invalid');
		$('input[name="FIRST_NAME"]').closest('div.row').find('.input_error').show();
		t_valid = false;
	}

	if(!validate_input($('input[name="LAST_NAME"]').val(), 2))
	{
		$('input[name="LAST_NAME"]').removeClass('valid').addClass('invalid');
		$('input[name="LAST_NAME"]').closest('div.row').find('.input_error').show();
		t_valid = false;
	}

	if(!validate_input($('input[name="CITY"]').val(), 2))
	{
		$('input[name="CITY"]').removeClass('valid').addClass('invalid');
		$('input[name="CITY"]').closest('div.row').find('.input_error').show();
		t_valid = false;
	}

	if(!validate_input($('input[name="COMPANY"]').val(), 2))
	{
		$('input[name="COMPANY"]').removeClass('valid').addClass('invalid');
		$('input[name="COMPANY"]').closest('div.row').find('.input_error').show();
		t_valid = false;
	}

	if($('input[name="STATE"]').length > 0 && !validate_input($('input[name="STATE"]').val(), 2))
	{
		$('input[name="STATE"]').removeClass('valid').addClass('invalid');
		$('input[name="STATE"]').closest('div.row').find('.input_error').show();
		t_valid = false;
	}

	if(!validate_input($('input[name="STREET_ADRESS"]').val(), 1))
	{
		$('input[name="STREET_ADRESS"]').removeClass('valid').addClass('invalid');
		$('input[name="STREET_ADRESS"]').closest('div.row').find('.input_error').show();
		t_valid = false;
	}

	if($('input[name="PASSWORD"]').val() != $('input[name="PASSWORD_CONFIRMATION"]').val())
	{
		$('input[name="PASSWORD_CONFIRMATION"]').val('');
		t_valid = false;
	}

	if(!validate_input($('input[name="PASSWORD"]').val(), 5))
	{
		$('input[name="PASSWORD"]').removeClass('valid').addClass('invalid');
		$('input[name="PASSWORD"]').closest('div.row').find('.input_error').show();
		t_valid = false;
	}

	if(!validate_input($('input[name="PASSWORD_CONFIRMATION"]').val(), 5))
	{
		$('input[name="PASSWORD_CONFIRMATION"]').removeClass('valid').addClass('invalid');
		$('input[name="PASSWORD_CONFIRMATION"]').closest('div.row').find('.input_error').show();
		t_valid = false;
	}

	if(!validate_input($('input[name="POST_CODE"]').val(), 4))
	{
		$('input[name="POST_CODE"]').removeClass('valid').addClass('invalid');
		$('input[name="POST_CODE"]').closest('div.row').find('.input_error').show();
		t_valid = false;
	}

	if(!validate_input($('input[name="STORE_NAME"]').val(), 3))
	{
		$('input[name="STORE_NAME"]').removeClass('valid').addClass('invalid');
		$('input[name="STORE_NAME"]').closest('div.row').find('.input_error').show();
		t_valid = false;
	}

	if(!validate_input($('input[name="EMAIL_ADRESS"]').val(), 6, false, true))
	{
		$('input[name="EMAIL_ADRESS"]').removeClass('valid').addClass('invalid');
		$('input[name="EMAIL_ADRESS"]').closest('div.row').find('.input_error').show();
		t_valid = false;
	}

	if(!validate_input($('input[name="EMAIL_ADRESS_FROM"]').val(), 6, false, true))
	{
		$('input[name="EMAIL_ADRESS_FROM"]').removeClass('valid').addClass('invalid');
		$('input[name="EMAIL_ADRESS_FROM"]').closest('div.row').find('.input_error').show();
		t_valid = false;
	}

	return t_valid;
}


var write_config = function()
{
	$.ajax(
	{
		data:		'action=write_config&' + $('#install_form').serialize(),
		url: 		'request_port.php',
		type: 		"POST",
		async:		true,
        dataType:	'json',
		success:	function(t_sql_result)
		{
			$('.progress').hide();

			t_result = t_sql_result;

			if(t_result == 'success')
			{
				$('#ajax').html('');

				jQuery.ajax(
				{
					data:		'action=get_states&' + $('#install_form').serialize(),
					url: 		'request_port.php',
					type: 		"POST",
					async:		true,
                    dataType:	'json',
					success:	function(t_states)
					{
						$('#states_container').html(t_states);
					}
				}).html;

				jQuery.ajax(
				{
					data:		'action=get_countries&' + $('#install_form').serialize(),
					url: 		'request_port.php',
					type: 		"POST",
					async:		true,
                    dataType:	'json',
					success:	function(t_countries)
					{
						$('#countries_container').html(t_countries);

						$('.shop_data').show();
					}
				}).html;
			}
			else
			{
				$('.progress-bar').hide();
				$('#ajax').html('<div class="error"><?php echo ERROR_CONFIG_FILES; ?></div><br /><br /><a class="btn btn-default btn-lg" href="index.php?language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
			}
		}
	}).html;
}

var import_sql = function(p_sql_part)
{
	var t_progress_bar_width = $('.progress-bar').css('width').replace('px', '');

	$.ajax(
	{
		data:		'action=import_sql&' + $('#install_form').serialize(),
		url: 		'request_port.php?sql_part=' + p_sql_part,
		type: 		"POST",
		async:		true,
		dataType:	'json',
		success:	function(p_result_json)
		{
			if(typeof(p_result_json.progress) != 'undefined')
			{
				var t_progress = p_result_json.progress;
				
				if(t_progress > 99)
				{
					t_progress = 99;
				}
				
				t_progress_bar_width *= (100 - t_progress) / 100
				t_progress_bar_width = Math.ceil(t_progress_bar_width);
				
				document.title = t_progress + "% | " + window.document_title;
				
				$('.progress-bar').html(t_progress + '%');
				$('.progress-bar').css('box-shadow', '0 1px 1px rgba(0, 0, 0, 0.10), inset -' + t_progress_bar_width + 'px 0 #EEEEEE');
			}
		
			if(p_result_json.success == true && p_result_json.progress != 100)
			{
				import_sql(p_result_json.next_sql);
				return;
			}
			else if(p_result_json.success == true && p_result_json.progress == 100)
			{
				document.title = window.document_title;
				write_config();
			}
			
			if(typeof(p_result_json.success) == 'undefined' || p_result_json.success != true)
			{
				$('.progress').hide();
				$('.progress-bar').hide();
				$('#ajax').html(t_result + '<br /><br /><a class="btn btn-default btn-lg" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
			}
		},
		error:	function(){
			$('.progress').hide();
			$('.progress-bar').hide();
			$('#ajax').html(t_result + '<br /><br /><a class="btn btn-default btn-lg" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
		}
		
	});
}

$(document).ready(async function()
{
	// try to reset shop admin data form
	$('.shop_data input[type="text"], .shop_data input[type="password"]').val('');
	
	if (location.protocol !== 'https:') {
		const httpsActive = await validateServerAsync(true);
		
		if (!httpsActive) {
			const httpOption = document.createElement('option');
			httpOption.value = false;
			httpOption.innerHTML = 'http://'
			
			var ssl_select_input = document.getElementById('ssl');
			// append http option to the list only if it exists
			if(ssl_select_input !== null && ssl_select_input !== 'undefined') {
				ssl_select_input.appendChild(httpOption);
			}
			
			t_http_interval = setInterval(async () => await validateServerAsync(false), 100);
		}
	}
	
	t_https_interval = setInterval(async () => await validateServerAsync(true), 100);

	$('body').on('blur', 'input[name="HTTP_SERVER"]', () => {
		t_http_counter = 0;
		t_https_counter = 0;
		t_http_interval = setInterval(async () => await validateServerAsync(false), 100);
		t_https_interval = setInterval(async () => await validateServerAsync(true), 100);
	})
	
	$('#ssl_notice').hide();
	$('#ssl').change(function()
	{
		if($(this).val() == 'true')
		{
			$('#server_prefix').text('https://');
			if(!t_https_valid)
			{
				$('#ssl_notice').show();
				$('input[name="HTTP_SERVER"]').removeClass('valid');
				$('input[name="HTTP_SERVER"]').addClass('invalid');
			}
			else
			{
				$('#ssl_notice').hide();
				$('input[name="HTTP_SERVER"]').removeClass('invalid');
				$('input[name="HTTP_SERVER"]').addClass('valid');
			}
		}
		else
		{
			$('#ssl_notice').hide();
			$('#server_prefix').text('http://');
			if(!t_http_valid)
			{
				$('#http_server_error').show();
				$('input[name="HTTP_SERVER"]').removeClass('valid');
				$('input[name="HTTP_SERVER"]').addClass('invalid');
			}
			else
			{
				$('#http_server_error').hide();
				$('input[name="HTTP_SERVER"]').removeClass('invalid');
				$('input[name="HTTP_SERVER"]').addClass('valid');
			}
		}
	});


	var t_test_db_connection = Object();

    var handle_test_db_connection_result = function (result) {
        switch (result.type) {
            case 'invalid database name':
                $('input[name="DB_DATABASE"]').removeClass('valid').addClass('invalid');
                $('input[name="DB_DATABASE"]').closest('div.row').find('.input_error.nonexistent').hide();
                $('input[name="DB_DATABASE"]').closest('div.row').find('.input_error.naming').show();
                break;
            case 'no connection':
                $('input[name="DB_SERVER"]').removeClass('valid').addClass('invalid');
                $('input[name="DB_SERVER_USERNAME"]').removeClass('valid').addClass('invalid');
                $('input[name="DB_SERVER_PASSWORD"]').removeClass('valid').addClass('invalid');
                $('input[name="DB_SERVER"]').closest('div.row').find('.input_error').html(result.message);
                $('input[name="DB_SERVER"]').closest('div.row').find('.input_error').show();
                break;
            case 'no database':
                $('input[name="DB_SERVER"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_SERVER_USERNAME"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_SERVER_PASSWORD"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_SERVER"]').closest('div.row').find('.input_error').hide();
                if ($('input[name="DB_DATABASE"]').val() != '') {
                    $('input[name="DB_DATABASE"]').removeClass('valid').addClass('invalid');
                    $('input[name="DB_DATABASE"]').closest('div.row').find('.input_error.naming').hide();
                    $('input[name="DB_DATABASE"]').closest('div.row').find('.input_error.nonexistent').show();
                }
                break;
            case 'requirements not met':
                $('input[name="DB_SERVER"]').removeClass('valid').addClass('invalid');
                $('input[name="DB_SERVER_USERNAME"]').removeClass('valid').addClass('invalid');
                $('input[name="DB_SERVER_PASSWORD"]').removeClass('valid').addClass('invalid');
                $('input[name="DB_SERVER"]').closest('div.row').find('.input_error').html(result.message);
                $('input[name="DB_SERVER"]').closest('div.row').find('.input_error').show();
                break;
            case 'missing permissions':
                $('input[name="DB_SERVER"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_SERVER_USERNAME"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_SERVER_PASSWORD"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_DATABASE"]').removeClass('invalid').addClass('valid');

                $('#ajax').html('<div class="error_field">' + result.message + '<?php echo ERROR_DB_QUERY; ?></div><br />');
                break;
            case 'existing tables':
                $('input[name="DB_SERVER"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_SERVER_USERNAME"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_SERVER_PASSWORD"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_DATABASE"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_SERVER"]').closest('div.row').find('.input_error').hide();
                $('input[name="DB_DATABASE"]').closest('div.row').find('.input_error').hide();
                
                $('#ajax').html('<div class="error_field">' + result.message + '</div>');
                $('#ajax').prepend('<div class="db-duplicates"><?php echo ERROR_TABLES_EXIST; ?> <?php echo TEXT_TABLES_EXIST; ?></div>');
                $('#ajax').append('<input type="checkbox" name="force_db" value="1" id="force_db" /><label for="force_db"> <?php echo LABEL_FORCE_DB; ?></label><br /><br />');
                break;
            case 'success':
                $('input[name="DB_SERVER"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_SERVER_USERNAME"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_SERVER_PASSWORD"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_DATABASE"]').removeClass('invalid').addClass('valid');
                $('input[name="DB_SERVER"]').closest('div.row').find('.input_error').hide();
                $('input[name="DB_DATABASE"]').closest('div.row').find('.input_error').hide();

                $('#ajax').html('');
                break;
        }
    }

	var test_db_connection = function()
	{
		if($('input[name="DB_SERVER"]').val() != '' && $('input[name="DB_SERVER_USERNAME"]').val() != '' && $('input[name="DB_SERVER_PASSWORD"]').val() != '')
		{
			if(typeof(t_test_db_connection.abort) == 'function')
			{
				t_test_db_connection.abort();
			}

			t_test_db_connection = jQuery.ajax(
			{
				data:		'action=test_db_connection&' + $('#install_form').serialize(),
				url: 		'request_port.php',
				type: 		"POST",
				async:		true,
                dataType:	'json',
				success:	function(t_db_result)
				{
                    handle_test_db_connection_result(t_db_result);
				}
			});
		}
	}

	$('body').on('blur', '.server_data input', test_db_connection);


	$('#import_sql').click(function()
	{
		var t_result;
		var t_force_import = $('#install_form').serialize().search('force_db=1');

		// HTTP-Server hast to be valid, invalid HTTPS-Server is tolerated even if SSL is set active
		if($('input[name="HTTP_SERVER"]').hasClass('valid'))
		{
			jQuery.ajax(
			{
				data:		'action=test_db_connection&' + $('#install_form').serialize(),
				url: 		'request_port.php',
				type: 		"POST",
				async:		true,
                dataType:	'json',
				success:	function(t_db_result)
				{
                    handle_test_db_connection_result(t_db_result);
                    
                    if (t_db_result.type === 'success' || (t_db_result.type === 'existing tables' && t_force_import > 0)) {
                        $('.server_data').hide();
                        $('.progress').show();
                        
                        $('#ajax').html('<div class="progress-bar-container"><div class="progress-bar">0%</div></div>');
                        
                        window.document_title = document.title;
                        document.title = "0% | " + document.title;
                        
                        import_sql('gambio');
                    }
				}
			}).html;
		}
	});


	$('body').on('blur', 'input[name="FIRST_NAME"], input[name="LAST_NAME"], input[name="CITY"], input[name="COMPANY"], input[name="STATE"]', function()
	{
		if(validate_input($(this).val(), 2))
		{
			$(this).removeClass('invalid').addClass('valid');
			$(this).closest('div.row').find('.input_error').hide();
		}
		else
		{
			$(this).removeClass('valid').addClass('invalid');
			$(this).closest('div.row').find('.input_error').show();
		}
	});

	$('body').on('blur', 'input[name="STREET_ADRESS"]', function()
	{
		if(validate_input($(this).val(), 1))
		{
			$(this).removeClass('invalid').addClass('valid');
			$(this).closest('div.row').find('.input_error').hide();
		}
		else
		{
			$(this).removeClass('valid').addClass('invalid');
			$(this).closest('div.row').find('.input_error').show();
		}
	});

	$('body').on('blur', 'input[name="STREET_NUMBER"]', function()
	{
		$(this).removeClass('invalid').addClass('valid');
		$(this).closest('div.row').find('.input_error').hide();
	});
	
	$('body').on('blur', 'input[name="PASSWORD"], input[name="PASSWORD"]', function()
	{
		if($(this).val() != $('input[name="PASSWORD_CONFIRMATION"]').val())
		{
			$('input[name="PASSWORD_CONFIRMATION"]').val('');
		}

		if(validate_input($(this).val(), 5))
		{
			$(this).removeClass('invalid').addClass('valid');
			$(this).closest('div.row').find('.input_error').hide();
		}
		else
		{
			$(this).removeClass('valid').addClass('invalid');
			$(this).closest('div.row').find('.input_error').show();
		}
	});

	$('body').on('blur', 'input[name="PASSWORD_CONFIRMATION"]', function()
	{
		if(validate_input($(this).val(), 5) && $(this).val() == $('input[name="PASSWORD"]').val())
		{
			$(this).removeClass('invalid').addClass('valid');
			$(this).closest('div.row').find('.input_error').hide();
		}
		else
		{
			$(this).removeClass('valid').addClass('invalid');
			$(this).closest('div.row').find('.input_error').show();
		}
	});

	$('body').on('blur', 'input[name="POST_CODE"]', function()
	{
		if(validate_input($(this).val(), 4))
		{
			$(this).removeClass('invalid').addClass('valid');
			$(this).closest('div.row').find('.input_error').hide();
		}
		else
		{
			$(this).removeClass('valid').addClass('invalid');
			$(this).closest('div.row').find('.input_error').show();
		}
	});

	$('body').on('blur', 'input[name="TELEPHONE"]', function()
	{
		if(validate_input($(this).val(), 0))
		{
			$(this).removeClass('invalid').addClass('valid');
			$(this).closest('div.row').find('.input_error').hide();
		}
		else
		{
			$(this).removeClass('valid').addClass('invalid');
			$(this).closest('div.row').find('.input_error').show();
		}
	});

	$('body').on('blur', 'input[name="STORE_NAME"]', function()
	{
		if(validate_input($(this).val(), 3))
		{
			$(this).removeClass('invalid').addClass('valid');
			$(this).closest('div.row').find('.input_error').hide();
		}
		else
		{
			$(this).removeClass('valid').addClass('invalid');
			$(this).closest('div.row').find('.input_error').show();
		}
	});

	$('body').on('blur', 'input[name="EMAIL_ADRESS"], input[name="EMAIL_ADRESS_FROM"]', function()
	{
		if(validate_input($(this).val(), 6, false, true))
		{
			$(this).removeClass('invalid').addClass('valid');
			$(this).closest('div.row').find('.input_error').hide();
		}
		else
		{
			$(this).removeClass('valid').addClass('invalid');
			$(this).closest('div.row').find('.input_error').show();
		}
	});

	$('body').on('click', '#run_config', function()
	{
		var t_result;

		if(validate_form() == true)
		{
			$('.shop_data').hide();

			jQuery.ajax(
			{
				data:		'action=create_account&' + $('#install_form').serialize(),
				url: 		'request_port.php',
				type: 		"POST",
				async:		true,
                dataType:	'json',
				success:	function(t_sql_result)
				{
					t_result = t_sql_result;
					$('#ajax').html('<span class="loading-spinner"><i class="fa fa-spinner step-spin fa-2x fa-fw"></i></span><span class="final-settings-text"><?php echo TEXT_FINAL_SETTINGS; ?></span>');

					if(t_result == 'success')
					{
						jQuery.ajax(
						{
							data:		'action=setup_shop&' + $('#install_form').serialize(),
							url: 		'request_port.php',
							type: 		"POST",
							async:		true,
                            dataType:	'json',
							success:	function(t_sql_result)
							{
								t_result = t_sql_result;

								if(t_result == 'success')
								{
									jQuery.ajax(
									{
										data:		'action=clear_cache',
										url: 		'request_port.php',
										type: 		"POST",
										async:		false,
                                        dataType:	'json'
									}).html;									

									$('#ajax').html('');

									jQuery.ajax(
									{
										data:		'action=chmod_444&' + $('#install_form').serialize(),
										url: 		'request_port.php',
										type: 		"POST",
										async:		true,
                                        dataType:	'json'
									}).html;

									if($('input[name="DIR_WS_CATALOG"]').val() != '/')
									{
										$('#ajax').html('<?php echo TEXT_WRITE_ROBOTS_FILE; ?>');

										jQuery.ajax(
										{
											data:		'action=write_robots_file&' + $('#install_form').serialize(),
											url: 		'request_port.php',
											type: 		"POST",
											async:		true,
                                            dataType:	'json',
											success:	function(t_sql_result)
											{
												t_result = t_sql_result;

												if(t_result == 'failed')
												{
													$('.robots_data').show();
												}

												$('#ajax').html('');
												$('#install_service').hide();
												$('.finish').show();
												$('.finish.button').css('display', 'inline-block');
											},
											error:	function()
											{
												$('#ajax').html('');
												$('#install_service').hide();
												$('.finish').show();
												$('.robots_data').show();
												$('.finish.button').css('display', 'inline-block');
											}
										}).html;
									}
									else
									{
										$('#install_service').hide();
										$('.finish').show();
										$('.finish.button').css('display', 'inline-block');
									}
								}
								else
								{
									$('#ajax').html('<div class="error"><?php echo ERROR_UNEXPECTED; ?></div><br /><br /><a class="btn btn-default btn-lg" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
								}
							}
						}).html;
					}
					else
					{
						$('#ajax').html('<div class="error"><?php echo ERROR_UNEXPECTED; ?></div><br /><br /><a class="btn btn-default btn-lg" href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>"><?php echo BUTTON_BACK; ?></a>');
					}
				}
			}).html;
		}
	});
	
	$('body').on('change', 'select[name="COUNTRY"]', function()
	{
		jQuery.ajax(
		{
			data:		'action=get_states&' + $('#install_form').serialize(),
			url: 		'request_port.php',
			type: 		"POST",
			async:		true,
            dataType:	'json',
			success:	function(t_states)
			{
				$('input[name="STATE"]').closest('div.row').find('.input_error').hide();
				$('#states_container').html(t_states);
			}
		}).html;
	});

	$('input[name="FTP_HOST"]').change(function()
	{
		var t_ftp_data = $(this).val().trim();

		if(t_ftp_data.substr(0, 4) == 'ftp:')
		{
			var t_ftp = t_ftp_data.substring(6, t_ftp_data.length);

			var t_user = t_ftp.substring(0, t_ftp.search(':'));
			var t_password = t_ftp.substring(t_ftp.search(':')+1, t_ftp.lastIndexOf('@'));
			var t_host = t_ftp.substring(t_ftp.lastIndexOf('@')+1, t_ftp.length);

			if(t_host.search('/') != -1)
			{
				t_host = t_host.substring(0, t_host.search('/'));
			}

			$('input[name="FTP_HOST"]').val(t_host);
			$('input[name="FTP_USER"]').val(t_user);
			$('input[name="FTP_PASSWORD"]').val(t_password);
		}
	});
	
	if($('#sftp').is(':checked')) {
		$('.ftp-pasv').css('display', 'none');
	}
	
	if($('#ftp').is(':checked')) {
		$('.ftp-port').css('display', 'none');
	}
	
	// Disable and hide FTP passiv checkbox when SFTP is selected or disable and hide FTP Port when FTP is selected
	$('input[name="protocol"]').on('change', function() {
		$('#pasv').prop('disabled', $('#sftp').is(':checked'));
		$('input[name="FTP_PORT"]').prop('disabled', $('#ftp').is(':checked'));
		
		if($('#sftp').is(':checked')) {
			$('.ftp-pasv').css('display', 'none');
			$('.ftp-port').css('display', 'block');
		}
		
		if($('#ftp').is(':checked')) {
			$('.ftp-pasv').css('display', 'block');
			$('.ftp-port').css('display', 'none');
		}
	});
	
	$('input[name="reconnect"]').on('click', function() {
		$('input[name="dir"]').val('/');
	});
});
