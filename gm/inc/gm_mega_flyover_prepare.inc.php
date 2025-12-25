<?php
/*--------------------------------------------------------------------
 gm_mega_flyover_prepare.inc.php 2020-2-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

function gm_mega_flyover_prepare($area_prefix, $products_id, $link_content) 
{
	$out = '<span id="'.$area_prefix.'_'.$products_id.'" class="flyover_item">'.$link_content.'</span>';
	return $out;
}

?>