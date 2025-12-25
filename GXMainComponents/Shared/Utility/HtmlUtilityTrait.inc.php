<?php
/* --------------------------------------------------------------
   HtmlUtilityTrait.inc.php 2018-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

trait HtmlUtilityTrait
{
    function htmlEntityDecodeWrapper($string, $flags = false, $encoding = '')
    {
        $t_flags = $flags;
        if ($flags === false) {
            if (defined('ENT_HTML401')) {
                $t_flags = ENT_COMPAT | ENT_HTML401;
            } else {
                $t_flags = ENT_COMPAT;
            }
        }
        
        $t_encoding = $encoding;
        if ($encoding === '') {
            if (isset($_SESSION['language_charset'])) {
                $t_allowed_charsets_array   = [];
                $t_allowed_charsets_array[] = 'ISO-8859-1';
                $t_allowed_charsets_array[] = 'ISO8859-1';
                $t_allowed_charsets_array[] = 'ISO-8859-15';
                $t_allowed_charsets_array[] = 'ISO8859-15';
                $t_allowed_charsets_array[] = 'UTF-8';
                $t_allowed_charsets_array[] = 'cp866';
                $t_allowed_charsets_array[] = 'ibm866';
                $t_allowed_charsets_array[] = '866';
                $t_allowed_charsets_array[] = 'cp1251';
                $t_allowed_charsets_array[] = 'Windows-1251';
                $t_allowed_charsets_array[] = 'win-1251';
                $t_allowed_charsets_array[] = '1251';
                $t_allowed_charsets_array[] = 'cp1252';
                $t_allowed_charsets_array[] = 'Windows-1252';
                $t_allowed_charsets_array[] = '1252';
                $t_allowed_charsets_array[] = 'KOI8-R';
                $t_allowed_charsets_array[] = 'koi8-ru';
                $t_allowed_charsets_array[] = 'koi8r';
                $t_allowed_charsets_array[] = 'BIG5';
                $t_allowed_charsets_array[] = '950';
                $t_allowed_charsets_array[] = 'GB2312';
                $t_allowed_charsets_array[] = '936';
                $t_allowed_charsets_array[] = 'BIG5-HKSCS';
                $t_allowed_charsets_array[] = 'Shift_JIS';
                $t_allowed_charsets_array[] = 'SJIS';
                $t_allowed_charsets_array[] = '932';
                $t_allowed_charsets_array[] = 'EUC-JP';
                $t_allowed_charsets_array[] = 'EUCJP';
                
                $t_key = array_search(strtolower(trim((string)$_SESSION['language_charset'])),
                                      array_map('strtolower', $t_allowed_charsets_array));
                if ($t_key !== false) {
                    $t_encoding = $t_allowed_charsets_array[$t_key];
                }
            } else {
                $t_encoding = 'ISO-8859-1';
            }
        }
        
        return html_entity_decode($string, $t_flags, $t_encoding);
    }
    
    
    function htmlentitiesWrapper($string, $flags = false, $encoding = '', $doubleEncode = true)
    {
        $t_flags = $flags;
        if ($flags === false) {
            if (defined('ENT_HTML401')) {
                $t_flags = ENT_COMPAT | ENT_HTML401;
            } else {
                $t_flags = ENT_COMPAT;
            }
        }
        
        $t_encoding = $encoding;
        if ($encoding === '') {
            // search for UTF-8 characters
            if (preg_match('//u', $string)) {
                $t_encoding = 'UTF-8';
            } elseif (isset($_SESSION['language_charset'])) {
                $t_allowed_charsets_array   = [];
                $t_allowed_charsets_array[] = 'ISO-8859-1';
                $t_allowed_charsets_array[] = 'ISO8859-1';
                $t_allowed_charsets_array[] = 'ISO-8859-15';
                $t_allowed_charsets_array[] = 'ISO8859-15';
                $t_allowed_charsets_array[] = 'UTF-8';
                $t_allowed_charsets_array[] = 'cp866';
                $t_allowed_charsets_array[] = 'ibm866';
                $t_allowed_charsets_array[] = '866';
                $t_allowed_charsets_array[] = 'cp1251';
                $t_allowed_charsets_array[] = 'Windows-1251';
                $t_allowed_charsets_array[] = 'win-1251';
                $t_allowed_charsets_array[] = '1251';
                $t_allowed_charsets_array[] = 'cp1252';
                $t_allowed_charsets_array[] = 'Windows-1252';
                $t_allowed_charsets_array[] = '1252';
                $t_allowed_charsets_array[] = 'KOI8-R';
                $t_allowed_charsets_array[] = 'koi8-ru';
                $t_allowed_charsets_array[] = 'koi8r';
                $t_allowed_charsets_array[] = 'BIG5';
                $t_allowed_charsets_array[] = '950';
                $t_allowed_charsets_array[] = 'GB2312';
                $t_allowed_charsets_array[] = '936';
                $t_allowed_charsets_array[] = 'BIG5-HKSCS';
                $t_allowed_charsets_array[] = 'Shift_JIS';
                $t_allowed_charsets_array[] = 'SJIS';
                $t_allowed_charsets_array[] = '932';
                $t_allowed_charsets_array[] = 'EUC-JP';
                $t_allowed_charsets_array[] = 'EUCJP';
                
                $t_key = array_search(strtolower(trim((string)$_SESSION['language_charset'])),
                                      array_map('strtolower', $t_allowed_charsets_array));
                if ($t_key !== false) {
                    $t_encoding = $t_allowed_charsets_array[$t_key];
                }
            } else {
                $t_encoding = 'ISO-8859-1';
            }
        }
        
        return htmlentities($string, $t_flags, $t_encoding, $doubleEncode);
    }
}