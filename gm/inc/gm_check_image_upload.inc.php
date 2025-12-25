<?php
/*--------------------------------------------------------------------
 gm_check_image_upload.inc.php 2020-2-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

function gm_check_image_upload($p_file)
{
	$image = $_FILES[$p_file];
	
	$extensions_whitelist = [
		'jpeg',
		'jpg',
		'png',
		'gif'
	];
	$mime_types_whitelist = [
		'image/jpeg',
		'image/jpg',
		'image/png',
		'image/gif'
	];
	
	// check file extension
	$file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
	if(empty($file_extension) || !in_array(strtolower($file_extension), $extensions_whitelist))
	{
		return false;
	}
	
	$image_info = getimagesize($image['tmp_name']);
	if(!$image_info || $image['type'] !== $image_info['mime']
	   || !in_array($image_info['mime'], $mime_types_whitelist)
	)
	{
		return false;
	}
	
	return true;
}