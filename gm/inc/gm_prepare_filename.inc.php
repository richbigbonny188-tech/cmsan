<?php
/*--------------------------------------------------------------------
 gm_prepare_filename.inc.php 2020-2-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/
	
function gm_prepare_filename($filename) {	

	$array_filename = explode('.', $filename);
	$suffix			= array_pop($array_filename);

	$search	 = "����������������������������&������������������������������������������������������ ";
	$replace = "AaEeIiOoUuCcAaAaAaEeIiOoOoUueSZszYAAAAACEEEEIIIINOOOOOUUUYaaaaaceeeeiiiinooooouuuyy_";
	$arr = array('�' => 'ae', '�' => 'oe', '�' => 'ue', '�' => 'ss');
	$filename = strtolower(strtr($array_filename[0], $search, $replace));
	$filename = strtr($filename, $arr);
	$filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);

	return $filename . '.' . $suffix;
}
?>