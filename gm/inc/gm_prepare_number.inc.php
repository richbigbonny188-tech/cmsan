<?php
/*--------------------------------------------------------------------
 gm_prepare_number.inc.php 2020-2-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/
	
function gm_prepare_number($number, $delimiter = '.'){
	$number = (double)$number;
	if(round($number) == $number) $number = (int)$number;
	return str_replace('.', $delimiter, $number);
}
?>