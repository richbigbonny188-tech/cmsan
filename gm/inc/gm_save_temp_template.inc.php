<?php
/*--------------------------------------------------------------------
 gm_save_temp_template.inc.php 2020-2-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/
	
function gm_save_temp_template($content) {
	$fp = fopen(DIR_FS_CATALOG . 'cache/gm_temp_email.html', "w+");
	fwrite($fp, stripslashes($content));
	fclose($fp);			
}
?>