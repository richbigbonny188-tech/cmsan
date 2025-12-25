<?php
/*--------------------------------------------------------------------
 gm_get_language_link.inc.php 2020-2-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

function gm_get_language_link($file, $action, $submenu, $currentLang)
{
    $gm_lang = gm_get_language();
    
    $lang = '<div id="gm_language">';
    foreach ($gm_lang as $value) {
        if ((bool)$value['status_admin'] !== true) {
            continue;
        }
        $lang .= '<span onclick="gm_get_content(\'' . xtc_href_link($file,
                                                                    'action=' . $action . '&subpage='
                                                                    . htmlentities_wrapper($_GET['subpage'] ?? '')
                                                                    . '&lang_id=' . $value['languages_id']) . '\', \''
                 . $action . '\', \'' . xtc_href_link($file, 'action=' . $submenu) . '\', false)"' .
                 ' class="' . ($currentLang === $value['languages_id'] ? 'active' : '') . '">'
                 . //xtc_image(DIR_WS_LANGUAGES . $value['directory'] . '/admin/images/' . $value['image']) .
                 '<span class="flag-icon flag-icon-' . $value['code'] . ' gm-get-language-link"></span>' . '</span> ';
    }
    $lang .= '</div>';
    
    return $lang;
}

function gm_get_lang_link($file, $action)
{
    $gm_lang = gm_get_language();
    
    $lang = '<div id="gm_language">';
    foreach ($gm_lang as $value) {
        if ((bool)$value['status_admin'] !== true) {
            continue;
        }
        $lang .= '<a href="' . xtc_href_link($file, 'action=' . $action . '&lang_id=' . $value['languages_id']) . '">'
                 . '<span class="flag-icon flag-icon-' . $value['code'] . ' gm-get-lang-link"></span>' . '</a> ';
    }
    $lang .= '</div>';
    
    return $lang;
}
