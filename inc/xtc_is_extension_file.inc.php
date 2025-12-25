<?php
/*--------------------------------------------------------------------------------------------------
    xtc_is_extension_file.php 2020-3-18
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

function xtc_is_extension_file($filename){
    //check if is not an extended file
    $result = false;
    $file_info = pathinfo($filename);
    $templatePath = dirname($filename);
    $filename_parts = explode('.',$file_info['filename']);
    if(is_numeric(end($filename_parts))){
        array_pop($filename_parts);
        $original_filename = implode('.', $filename_parts).'.'.$file_info['extension'];
        $result = file_exists($templatePath.DIRECTORY_SEPARATOR.$original_filename);
    }
    return $result;
}