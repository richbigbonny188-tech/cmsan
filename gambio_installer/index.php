<?php
/* --------------------------------------------------------------
   index.php 2023-01-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2003	 nextcommerce (index.php,v 1.18 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: index.php 1220 2005-09-16 15:53:13Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require_once __DIR__ . '/../GXMainComponents/Shared/SystemAnalyzer.inc.php';
require_once __DIR__ . '/../GXMainComponents/Shared/SystemRequirements.inc.php';

if (!SystemAnalyzer::arePhpRequirementsMet(new SystemRequirements())) {
    include_once __DIR__ . '/language/german.php';
    
    die(sprintf(REQUIREMENT_WARNING, SystemRequirements::getPhpMinVersion(), SystemAnalyzer::getPhpVersion()));
}

require('includes/application.php');

if (isset($_GET['delete_installer']) && isset($_GET['auth_token']))
{
	gm_delete_installer($_GET['return_url'], $_GET['auth_token']);
}

// include needed functions
require_once(DIR_FS_INC.'xtc_redirect.inc.php');
require_once(DIR_FS_INC.'xtc_href_link.inc.php');

if(isset($_SESSION['language']) && $_SESSION['language'] == 'english')
{
	include('language/english.php');
}
else
{
	include('language/german.php');
}

if(isset($_GET['precheck']) && $_GET['precheck'] == '1')
{
	// check register_globals
	$t_register_globals = false;
	if(ini_get('register_globals') == '1' || ini_get('register_globals') == 'on' || ini_get('register_globals') == 'On')
	{
		$t_register_globals = true;
	}

	// check uploaded files
	$fp = fopen("txt/filelist.txt", "r");
	$t_missing_files_array = array();
	while($t_line = fgets($fp, 1024))
	{
		$t_dir = DIR_FS_CATALOG . $t_line;
		if(file_exists(trim($t_dir)) == false)
		{
			if(is_dir(DIR_FS_CATALOG . 'templates/EyeCandy/') == false && strstr($t_line, 'EyeCandy') !== false) continue;
			$t_missing_files_array[] = $t_line;
		}
	}
	fclose($fp);
 
	if($t_register_globals === false && empty($t_missing_files_array) && extension_loaded('intl') && extension_loaded('soap'))
	{
		header('Location: index.php?language=' . rawurlencode($_GET['language']) . '&confirmation_form=1');
		exit;
	}
}

if (!$script_filename = str_replace("\\", '/', getenv('PATH_TRANSLATED'))) {
	$script_filename = getenv('SCRIPT_FILENAME');
}
$script_filename = str_replace('//', '/', $script_filename);

if (!$request_uri = getenv('REQUEST_URI')) {
	if (!$request_uri = getenv('PATH_INFO')) {
		$request_uri = getenv('SCRIPT_NAME');
	}

	if (getenv('QUERY_STRING')) $request_uri .=  '?' . getenv('QUERY_STRING');
}

$dir_fs_www_root_array = explode('/', dirname($script_filename));
$dir_fs_www_root = array();
for ($i=0; $i<sizeof($dir_fs_www_root_array)-2; $i++) {
	$dir_fs_www_root[] = $dir_fs_www_root_array[$i];
}
$dir_fs_www_root = implode('/', $dir_fs_www_root);

$dir_ws_www_root_array = explode('/', dirname($request_uri));
$dir_ws_www_root = array();
for ($i=0; $i<sizeof($dir_ws_www_root_array)-1; $i++) {
	$dir_ws_www_root[] = $dir_ws_www_root_array[$i];
}
$dir_ws_www_root = implode('/', $dir_ws_www_root);

$coo_ftp_manager2 = new FTPManager(false, '', '', '', '');
$t_wrong_chmod_array = $coo_ftp_manager2->check_chmod();

for($i = 0; $i < count($t_wrong_chmod_array); $i++)
{
	$t_wrong_chmod_array[$i] = str_replace(DIR_FS_CATALOG, '', $t_wrong_chmod_array[$i]);
}
sort($t_wrong_chmod_array);

if(isset($_POST['FTP_HOST']) && !empty($t_wrong_chmod_array) && !isset($_GET['chmod']))
{
	$t_host = $_POST['FTP_HOST'];
	$t_user = $_POST['FTP_USER'];
	$t_password = $_POST['FTP_PASSWORD'];
	$t_port = (int)$_POST['FTP_PORT'];
	$t_pasv = false;
	if(!empty($_POST['FTP_PASV'])) $t_pasv = true;

	if($_POST['protocol'] === 'ftp')
	{
		$coo_ftp_manager = new FTPManager(true, $t_host, $t_user, $t_password, $t_pasv);
	}
	else
	{
		$coo_ftp_manager = new SFTPManager(true, $t_host, $t_user, $t_password, $t_port);
	}
	

	if($coo_ftp_manager->getError() == '')
	{
		if(isset($_POST['dir']) && $_POST['dir'] !== '/')
		{
			$t_dir = $_POST['dir'];
		}
		else
		{
			$t_dir = $coo_ftp_manager->find_shop_dir('/');
		}

		$t_list_array = $coo_ftp_manager->get_directories($t_dir);

		$_SESSION['protocol'] = $_POST['protocol'];
		$_SESSION['FTP_HOST'] = $_POST['FTP_HOST'];
		$_SESSION['FTP_USER'] = $_POST['FTP_USER'];
		$_SESSION['FTP_PASSWORD'] = $_POST['FTP_PASSWORD'];
		$_SESSION['FTP_PORT'] = $_POST['FTP_PORT'];
		if(!empty($_POST['FTP_PASV']))
		{
			$_SESSION['FTP_PASV'] = $_POST['FTP_PASV'];
		}
	}
}

if(empty($t_wrong_chmod_array) 
	&& !isset($_GET['chmod'])
	&& isset($_GET['language'])
	&& ((($t_memory_limit_ok ?? null) === true
		&& ($t_register_globals ?? null) === false
		&& empty($t_missing_files_array)
        && extension_loaded('intl')
        && extension_loaded('soap'))
		|| !isset($_GET['precheck'])))
{
	header('Location: index.php?chmod=ok&language=' . rawurlencode($_GET['language']));
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Installation Gambio GX4</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="icon" href="../admin/html/assets/images/gx-admin/favicon-gambio-de.ico" type="image/x-icon">
		
		<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
		<link type="text/css" rel="stylesheet" href="font-awesome/css/font-awesome.min.css<?php echo '?rand=' . time(); ?>" />
		<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css<?php echo '?rand=' . time(); ?>" />
		<link type="text/css" rel="stylesheet" href="css/stylesheet.css<?php echo '?rand=' . time(); ?>" />
		
		<script type="text/javascript" src="../JSEngine/build/vendor/jquery/jquery.min.js"></script>
		<script type="text/javascript" src="javascript/placeholder.js"></script>
		<script type="text/javascript" src="javascript/javascripts.js.php?language=<?php echo rawurlencode($_SESSION['language']) . '&rand=' . time(); ?>"></script>
	</head>

	<body>
		
		<div class="page-header clearfix">
			<img id="logo" class="img-repsonsive" src="images/gambio-admin-g-square-white-logo.svg" alt="" />
			<h1>Gambio Installer</h1>
		</div>
		
		<div class="container">
			<div class="row">
				<div id="main" class="col-xs-12">
					
					<div id="install_service">
						<p>
							<strong><?php echo HEADING_INSTALLATION_SERVICE; ?></strong>
						</p>
						<p>
							<?php echo TEXT_INSTALLATION_SERVICE; ?>
							<br />
							<br />
							<a href="https://www.gambio.de/901fB" class="btn btn-primary btn-lg" target="_blank"><?php echo BUTTON_GAMBIO_PORTAL; ?></a>
						</p>
					</div>
		
					<form name="install" id="install_form" action="index.php?language=<?php echo rawurlencode($_GET['language'] ?? null); ?>" method="post">
					<?php
					if(($t_session_started ?? null) === false)
					{
					?>
						<span class="alert alert-danger"><?php echo sprintf(ERROR_SESSION_SAVE_PATH, $dir_ws_www_root . '/cache'); ?></span>
					<?php
					}
					elseif(!isset($_GET['language']))
					{
					?>
						<p><h2><?php echo HEADING_INSTALLATION; ?></h2></p>
						
						<div class="row">
							<div class="col-xs-12 installation-text">
                                <?php echo TEXT_INSTALLATION; ?><br />
							</div>
							<div class="button-container">
								<div class="col-xs-3">
									<a href="index.php?language=german&precheck=1" class="btn btn-default btn-block btn-lg"><?php echo BUTTON_GERMAN; ?></a>
								</div>
								<div class="col-xs-3">
									<a href="index.php?language=english&precheck=1" class="btn btn-default btn-block btn-lg"><?php echo BUTTON_ENGLISH; ?></a>
								</div>
							</div>
						</div>
						
					<?php
					}
					elseif(isset($_GET['precheck']) && $_GET['precheck'] == '1')
					{
					?>
						<div class="precheck">
						<?php
						if(!extension_loaded('intl') || !extension_loaded('soap'))
						{
						?>
							<h2><?php echo HEADING_SYSTEM_REQUIREMENTS; ?></h2>
							<?php echo TEXT_SYSTEM_REQUIREMENTS; ?>
							<br />
							<br />
						<?php
						}
						if(($t_register_globals ?? null))
						{
						?>
							<h2><?php echo HEADING_REGISTER_GLOBALS; ?></h2>
							<?php echo TEXT_REGISTER_GLOBALS; ?>
							<br />
						<?php
						}
						if(($t_memory_limit_ok ?? null) === false)
						{
						?>
							<h2><?php echo ERROR_MEMORY_LIMIT; ?></h2>
							<?php echo sprintf(ERROR_TEXT_MEMORY_LIMIT, ($t_memory_limit ?? 0)); ?>
							<br />
						<?php
						}
						if(!empty($t_missing_files_array))
						{
						?>
							<h2><?php echo ERROR_MISSING_FILES; ?></h2>
							<?php echo TEXT_MISSING_FILES; ?>
							
							<div class="error_field">
							<?php
								echo implode('<br />', $t_missing_files_array);
							?>
							</div>
							
							<a href="index.php?precheck=1&language=<?php echo rawurlencode($_GET['language']); ?>&confirmation_form=1" class="btn btn-default btn-lg"><?php echo BUTTON_CHECK_MISSING_FILES; ?></a>
							<br />
							<br />
						<?php
						}
						?>
							<a href="index.php?language=<?php echo rawurlencode($_GET['language']); ?>&confirmation_form=1" class="btn btn-primary btn-lg"><?php echo BUTTON_CONTINUE; ?></a>
						</div>
					<?php
					}
					elseif(!isset($_GET) || (isset($_GET['ftp']) && $_GET['ftp'] === 'done') || !isset($_GET['chmod']))
					{
					?>
						<div class="ftp_data">
						<?php
						if(isset($_GET['ftp']) && $_GET['ftp'] === 'done')
						{
						?>
		
							<span class="alert alert-danger"><?php echo ERROR_SET_PERMISSIONS_FAILED; ?></span><br>
							
							<a href="index.php?language=<?php echo rawurlencode($_GET['language']); ?>" class="btn btn-default btn-lg"><?php echo BUTTON_BACK; ?></a>&nbsp;
							<a href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>" class="btn btn-primary btn-lg"><?php echo BUTTON_CONTINUE; ?></a>
							
							<h2><?php echo HEADING_WRONG_PERMISSIONS; ?></h2>
							<div class="error_field">
							<?php
								echo implode('<br />', $t_wrong_chmod_array);
							?>
							</div>
							
							<a href="index.php?ftp=donw&language=<?php echo rawurlencode($_GET['language']); ?>" class="btn btn-default btn-lg"><?php echo BUTTON_CHECK_PERMISSIONS; ?></a>
						<?php
						}
						else
						{
							$t_ftp_checked = ' checked';
							$t_sftp_checked = '';
							if(isset($_POST['protocol']) && $_POST['protocol'] === 'sftp')
							{
								$t_ftp_checked = '';
								$t_sftp_checked = ' checked';
							}
						?>
		
							<h2><?php echo HEADING_WRONG_PERMISSIONS; ?></h2>
							
							<div class="error_field">
							<?php
								echo implode('<br />', $t_wrong_chmod_array);
							?>
							</div>
							
							<a href="index.php?language=<?php echo rawurlencode($_GET['language']); ?>" class="btn btn-default btn-lg"><?php echo BUTTON_CHECK_PERMISSIONS; ?></a>
							<br>
							<br>
							<p><?php echo TEXT_SET_PERMISSIONS; ?></p>
						
							<fieldset class="block_head">
								<legend><i class="fa fa-upload"></i> <?php echo HEADING_FTP_DATA; ?></legend>
								
								<div class="row">
									<div class="col-xs-6">
										<div class="form-group">
											<input id="ftp" type="radio" name="protocol" value="ftp" style="margin-left: 0;"<?php echo $t_ftp_checked; ?>>
											<label for="ftp" style="margin-right: 30px;"><?php echo LABEL_FTP; ?></label>
											
											<input id="sftp" type="radio" name="protocol" value="sftp" style="margin-left: 0;"<?php echo $t_sftp_checked; ?>>
											<label for="sftp"><?php echo LABEL_SFTP; ?></label>
										</div>
										
										<div class="form-group">
											<input type="text" 
											       class="input_field form-control input-lg"
											       placeholder="<?php echo LABEL_FTP_SERVER; ?>"
											       name="FTP_HOST" 
											       size="35" 
											       value="<?php echo str_replace('"', '&quot;', ($_POST['FTP_HOST'] ?? '')); ?>"
											       autocomplete="off" />
										</div>
										
										<div class="form-group">
											<input type="text" 
											       class="input_field form-control input-lg"
											       placeholder="<?php echo LABEL_FTP_USER; ?>"
											       name="FTP_USER" 
											       size="35" 
											       value="<?php echo str_replace('"', '&quot;', ($_POST['FTP_USER'] ?? '')); ?>"
											       autocomplete="off" />
										</div>
										
										<div class="form-group">
											<input type="password" 
											       class="input_field form-control input-lg"
											       placeholder="<?php echo LABEL_FTP_PASSWORD; ?>"
											       name="FTP_PASSWORD" 
											       size="35" 
											       value="<?php echo str_replace('"', '&quot;', ($_POST['FTP_PASSWORD']?? '')); ?>"
											       autocomplete="off" />
										</div>
										
										<div class="form-group ftp-port">
											<input type="text" 
											       class="input_field form-control input-lg"
											       placeholder="<?php echo LABEL_FTP_PORT; ?>"
											       name="FTP_PORT" 
											       size="35" 
											       value="<?php echo (isset($_POST['FTP_PORT']) ? (int)($_POST['FTP_PORT']?? null) : (int)22); ?>">
										</div>
										
										<div class="form-group ftp-pasv">
											<label for="pasv"><?php echo LABEL_FTP_PASV; ?></label>
											<input type="checkbox" 
											       id="pasv" 
											       name="FTP_PASV" 
											       value="true" 
											       <?php echo (isset($_POST['FTP_PASV']) || !isset($_POST['FTP_USER'])) ? ' checked="checked"' : ''; ?> />
										</div>
									</div>
								</div>
							</fieldset>
		
							<?php
							if(!isset($_POST['FTP_HOST']))
							{
							?>
							<input type="submit" name="go" value="<?php echo BUTTON_CONNECT; ?>" class="btn btn-primary btn-lg" />
							<?php
							}
							else
							{
							?>
							<fieldset>
								<legend><?php echo HEADING_REMOTE_CONSOLE; ?></legend>
								
								<div class="ftp-dir-listing">
									<?php
				
									if(isset($coo_ftp_manager) && $coo_ftp_manager->getError() != '')
									{
										echo '<div class="error">' . $coo_ftp_manager->getError() . '</div>';
									}
									else
									{
										if(isset($coo_ftp_manager) && is_object($coo_ftp_manager) && $coo_ftp_manager->is_shop($t_dir ?? null))
										{
											if(!isset($_POST['chmod_777']) || empty($_POST['chmod_777']))
											{
												echo '<input type="hidden" name="dir" value="' . ($t_dir ?? null) . '" />';
												echo '';
											}
                                            if(isset($_POST['chmod_777']) || !empty($_POST['chmod_777']))
											{
												$coo_ftp_manager->chmod_777($t_dir ?? null);
												echo '<script type="text/javascript">
														<!--
														self.location.href="index.php?ftp=done&language=' . rawurlencode($_GET['language']) . '";
														//-->
														</script>';
											}
										}
				
										if(isset($_POST['FTP_HOST']) && (!isset($_POST['chmod_777']) || empty($_POST['chmod_777'] ?? null)))
										{
											if(strrpos($t_dir ?? '', '/') !== false && ($t_dir ?? '') != '/')
											{
												if(strrpos($t_dir ?? '', '/') === 0)
												{
													echo '<i class="fa fa-folder-open"></i> <input type="submit" class="dir" name="dir" value="/" />' . LABEL_DIR_UP . '<br /><br />';
												}
												else
												{
													echo '<i class="fa fa-folder-open"></i> <input type="submit" class="dir" name="dir" value="' . substr($t_dir ?? '', 0, strrpos($t_dir ?? '', '/')) . '" /> ' . LABEL_DIR_UP . '<br /><br />';
												}
											}
				
											for($i = 0; $i < count($t_list_array ?? null); $i++)
											{
												echo '<i class="fa fa-folder-open"></i> <input type="submit" class="dir" name="dir" value="' . ($t_list_array[$i] ?? null) . '" /><br />';
											}
										}
									}
									?>
								</div>
							</fieldset>
							<?php
							}
                            
                            if (isset($coo_ftp_manager) && is_object($coo_ftp_manager ?? null)) {
                                
                                if ($coo_ftp_manager->is_shop($t_dir ?? null)) {
                                    if (!isset($_POST['chmod_777']) || empty($_POST['chmod_777'])) { ?>
										<input type="hidden" name="dir" value="<?php echo ($t_dir ?? null) ?>" />
										<input type="submit"
										       name="chmod_777"
										       value="<?php echo BUTTON_SET_PERMISSIONS ?>"
										       class="btn btn-primary btn-lg" /> &nbsp;
                                    <?php }
                                } ?>
								<input type="submit"
								       name="reconnect"
								       value="<?php echo BUTTON_CONNECT_NEW; ?>"
								       class="btn btn-default btn-lg" />
                                <?php
                            }
                            ?>
							<br />
							<div class="alert alert-warning">
								<?php echo TEXT_SKIP; ?>
							</div>
							
							<a href="index.php?language=<?php echo rawurlencode($_GET['language']); ?>&chmod=ok" 
							   class="btn btn-default btn-lg skip"><?php echo BUTTON_SKIP; ?></a>
						<?php
							}
							?>
						</div>
					<?php
					}
					else
					{
					?>
						<fieldset class="block_head server_data">
							<legend><i class="fa fa-database" ></i> <?php echo HEADING_DATABASE; ?></legend>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text"
										       class="input_field_short form-control input-lg"
										       placeholder="<?php echo LABEL_DB_SERVER; ?>"
										       name="DB_SERVER"
										       size="15"
										       autocomplete="off" />
									</div>
									
									<span class="input_error col-xs-6"><?php echo ERROR_INPUT_DB_CONNECTION; ?></span>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text"
										       class="input_field_short form-control input-lg"
										       placeholder="<?php echo LABEL_DB_USER; ?>"
										       name="DB_SERVER_USERNAME"
										       size="15"
										       autocomplete="off" />
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="password"
										       class="input_field_short form-control input-lg"
										       placeholder="<?php echo LABEL_DB_PASSWORD; ?>"
										       name="DB_SERVER_PASSWORD"
										       size="15"
										       autocomplete="off" />
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text"
										       class="input_field_short form-control input-lg"
										       placeholder="<?php echo LABEL_DB_DATABASE; ?>"
										       name="DB_DATABASE"
										       size="15"
										       autocomplete="off" />
									</div>
									
									<span class="input_error nonexistent col-xs-6"><?php echo ERROR_INPUT_DB_DATABASE; ?></span>
									<span class="input_error naming col-xs-6"><?php echo ERROR_INPUT_DB_DATABASE_NAMING; ?></span>
								</div>
							</div>
						</fieldset>
						
						<br class="server_data">
						
						<fieldset class="block_head server_data">
							<legend><i class="fa fa-server"></i> <?php echo HEADING_SHOP_INFORMATION; ?></legend>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-2">
										<select id="ssl" class="input_span form-control input-lg" name="ENABLE_SSL">
											<option value="true">https://</option>
										</select>
									</div>
									
									<div class="col-xs-4">
										<input type="text"
										       class="input_field form-control input-lg"
										       name="HTTP_SERVER"
										       size="35"
										       value="<?php echo getenv('HTTP_HOST'); ?>" />
									</div>
									
									<span id="http_server_error" 
									      class="col-xs-6"><?php echo ERROR_INPUT_SERVER_URL; ?></span>
								</div>
							</div>
							
							<span id="ssl_notice">(<?php echo LABEL_NOTICE . ERROR_INPUT_SERVER_HTTPS; ?>)</span>
						</fieldset>
						
						<fieldset class="block_head shop_data">
							<legend><i class="fa fa-user"></i> <?php echo HEADING_ADMIN_DATA; ?></legend>
							
							<div class="form-group">
								<input type="radio" value="m" name="GENDER" /> <?php echo LABEL_MALE; ?>
								<input type="radio" value="f" name="GENDER" /> <?php echo LABEL_FEMALE; ?>
								<input type="radio" value="o" name="GENDER" /> <?php echo LABEL_OTHER; ?>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text" 
										       class="input_field form-control input-lg"
										       placeholder="<?php echo LABEL_FIRSTNAME; ?>"
										       name="FIRST_NAME" 
										       size="35" 
										       value="" />
									</div>
									
									<span class="input_error col-xs-6"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text" 
										       class="input_field form-control input-lg"
										       placeholder="<?php echo LABEL_LASTNAME; ?>"
										       name="LAST_NAME" 
										       size="35" 
										       value="" />
									</div>
									
									<span class="input_error col-xs-6"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text" 
										       class="input_field form-control input-lg"
										       placeholder="<?php echo LABEL_STREET; ?>"
										       name="STREET_ADRESS" 
										       size="35" 
										       value="" />
									</div>
									
									<span class="input_error col-xs-6"><?php echo ERROR_INPUT_MIN_LENGTH_1; ?></span>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text"
										       class="input_field form-control input-lg"
										       placeholder="<?php echo LABEL_STREET_NUMBER; ?>"
										       name="STREET_NUMBER"
										       size="35"
										       value="" />
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text" 
										       class="input_field form-control input-lg"
										       placeholder="<?php echo LABEL_POSTCODE; ?>"
										       name="POST_CODE" 
										       size="35" 
										       value="" />
									</div>
									
									<span class="input_error col-xs-6"><?php echo ERROR_INPUT_MIN_LENGTH_4; ?></span>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text" 
										       class="input_field form-control input-lg"
										       placeholder="<?php echo LABEL_CITY; ?>"
										       name="CITY" 
										       size="35" 
										       value="" />
									</div>
									
									<span class="input_error col-xs-6"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div id="states_container" class="col-xs-6">
										<select name="STATE" class="form-control input-lg">
											<option value="81">Bremen</option>
										</select>
									</div>
									
									<span class="input_error col-xs-6"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div id="countries_container" class="col-xs-6">
										<select name="COUNTRY" class="form-control input-lg">
											<option value="81">Germany</option>
										</select>
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text" 
										       class="input_field form-control input-lg"
										       placeholder="<?php echo LABEL_TELEPHONE; ?>"
										       name="TELEPHONE" 
										       size="35" 
										       value="" />
									</div>
								</div>
							</div>
							
							<br>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text" 
										       class="input_field form-control input-lg"
										       placeholder="<?php echo LABEL_EMAIL; ?>"
										       name="EMAIL_ADRESS" 
										       size="35" 
										       value="" />
									</div>
									
									<span class="input_error col-xs-6"><?php echo ERROR_INPUT_EMAIL; ?></span>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="password" 
										       class="input_field form-control input-lg"
										       placeholder="<?php echo LABEL_PASSWORD; ?>"
										       name="PASSWORD" 
										       size="35" 
										       value="" />
									</div>
									
									<span class="input_error col-xs-6"><?php echo ERROR_INPUT_MIN_LENGTH_5; ?></span>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="password" 
										       class="input_field form-control input-lg"
										       placeholder="<?php echo LABEL_CONFIRMATION; ?>"
										       name="PASSWORD_CONFIRMATION" 
										       size="35" 
										       value="" />
									</div>
									
									<span class="input_error col-xs-6"><?php echo ERROR_INPUT_PASSWORD_CONFIRMATION; ?></span>
								</div>
							</div>
							
							<br>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text" 
										       class="input_field form-control input-lg"
										       placeholder="<?php echo LABEL_SHOP_NAME; ?>"
										       name="STORE_NAME" 
										       size="35" 
										       value="" />
									</div>
									
									<span class="input_error col-xs-6"><?php echo ERROR_INPUT_MIN_LENGTH_3; ?></span>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text" 
										       class="input_field form-control input-lg"
										       placeholder="<?php echo LABEL_COMPANY; ?>"
										       name="COMPANY" 
										       size="35" 
										       value="" />
									</div>
									
									<span class="input_error col-xs-6"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div class="col-xs-6">
										<input type="text" 
										       class="input_field form-control input-lg"
										       placeholder="<?php echo LABEL_EMAIL_FROM; ?>"
										       name="EMAIL_ADRESS_FROM" 
										       size="35" 
										       value="" />
									</div>
									
									<span class="input_error col-xs-6"><?php echo ERROR_INPUT_EMAIL; ?></span>
								</div>
							</div>

							<div class="form-group">
								<div class="row">
									<div id="error_reports" class="confirmation-box">
										<p>
											<label for="error-reports">
												<input type="checkbox" id="error-reports" name="CHECKBOX_ERROR_REPORTS" checked>
												<span style="margin-left: 5px;"><?= LABEL_ERROR_REPORTS ?></span>
											</label>
										</p>
										<div>
											<?= TEXT_ERROR_REPORTS ?>
										</div>
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<div class="row">
									<div id="shop_information_data_processing" class="confirmation-box">
										<p>
											<label for="ACCEPT_SHOP_INFORMATION_DATA_PROCESSING">
												<input type="checkbox" id="ACCEPT_SHOP_INFORMATION_DATA_PROCESSING" name="ACCEPT_SHOP_INFORMATION_DATA_PROCESSING" value="true" checked>
												<span style="margin-left: 5px;"><?= CHECKBOX_ADMIN_FEED_SHOP_INFORMATION; ?></span>
											</label>
										</p>
										<div>
											<?= TEXT_ADMIN_FEED_SHOP_INFORMATION ?>
										</div>
									</div>
								</div>
							</div>
						</fieldset>
						
						<br class="shop_data">
		
						<div class="progress">
							<h2><?php echo HEADING_PROGRESS; ?></h2>
							<p><?php echo TEXT_PROGRESS; ?></p>
						</div>
		
						<div id="ajax"></div>
		
						<p class="robots_data">
							<strong><?php echo HEADLINE_ROBOTS; ?></strong>
							<br />
							<?php echo TEXT_ROBOTS; ?>
							<br />
							<br />
							<a class="btn btn-success btn-lg" id="download" href="get_robots.php?download=robot"><?php echo BUTTON_DOWNLOAD; ?></a>
							<br />
							<br />
						</p>
						<div class="finish">
							<h2 class="finish"><?php echo HEADING_SUCCESS; ?></h2>
							<p><?php echo TEXT_SUCCESS; ?></p>
							<a class="btn btn-primary btn-lg" href="<?php echo $dir_ws_www_root . '/'; ?>"><?php echo BUTTON_OPEN_SHOP; ?></a>
						</div>
						
						<br class="server_data">
						<a class="btn btn-primary btn-lg server_data" id="import_sql"><?php echo BUTTON_START; ?></a>
						<a class="btn btn-primary btn-lg shop_data" id="run_config"><?php echo BUTTON_FINISH; ?></a>
						
		
						<?php
							echo xtc_draw_hidden_field_installer('install[]', 'database');
							echo xtc_draw_hidden_field_installer('install[]', 'configure');
		
							echo xtc_draw_hidden_field_installer('DIR_FS_DOCUMENT_ROOT', $dir_fs_www_root);
							echo xtc_draw_hidden_field_installer('DIR_FS_CATALOG', ($local_install_path ?? ''));
							echo xtc_draw_hidden_field_installer('DIR_FS_ADMIN', ($local_install_path ?? '') . 'admin/');
							echo xtc_draw_hidden_field_installer('DIR_WS_CATALOG', $dir_ws_www_root . '/');
							echo xtc_draw_hidden_field_installer('DIR_WS_ADMIN', $dir_ws_www_root . '/admin/');
		
							echo xtc_draw_hidden_field_installer('ZONE_SETUP', 'yes');
		
							echo xtc_draw_hidden_field_installer('STATUS_DISCOUNT', '0.00');
							echo xtc_draw_hidden_field_installer('STATUS_OT_DISCOUNT_FLAG', '0');
							echo xtc_draw_hidden_field_installer('STATUS_OT_DISCOUNT', '0.00');
							echo xtc_draw_hidden_field_installer('STATUS_GRADUATED_PRICE', '1');
							echo xtc_draw_hidden_field_installer('STATUS_SHOW_PRICE', '1');
							echo xtc_draw_hidden_field_installer('STATUS_SHOW_TAX', '1');
							echo xtc_draw_hidden_field_installer('STATUS_DISCOUNT2', '0.00');
							echo xtc_draw_hidden_field_installer('STATUS_OT_DISCOUNT_FLAG2', '0');
							echo xtc_draw_hidden_field_installer('STATUS_OT_DISCOUNT2', '0.00');
							echo xtc_draw_hidden_field_installer('STATUS_GRADUATED_PRICE2', '1');
							echo xtc_draw_hidden_field_installer('STATUS_SHOW_PRICE2', '1');
							echo xtc_draw_hidden_field_installer('STATUS_SHOW_TAX2', '1');
						?>
					<?php
					}
					?>
		
					</form>
				</div>
				
				<footer>
					<div id="copyright">
						<strong> <a href="https://www.gambio.de" target="_blank">Gambio.de</a> - Gambio-Installer &copy; 2023 Gambio GmbH</strong><br />
						Gambio GmbH provides no warranty.<br/>
						The Shopsoftware is redistributable under the <br/>
						<a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU General Public License (Version 2)</a><br />
						based on: E-Commerce Engine Copyright &copy; 2006 <a href="http://www.xt-commerce.com" target="_blank">xt:Commerce</a>, <br />
						<a href="http://www.xt-commerce.com" target="_blank">xt:Commerce</a> provides no warranty.
					</div>
				</footer>
			</div>
		</div>
	</body>
</html>
<?php
if(isset($coo_ftp_manager) && is_object($coo_ftp_manager))
{
	$coo_ftp_manager->quit();
}
