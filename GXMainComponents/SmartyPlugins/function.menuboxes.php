<?php
/* --------------------------------------------------------------
   function.menuboxes.php 2015-10-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function smarty_function_menuboxes($params, &$smarty)
{
    $output        = '';
    $assigned_vars = $smarty->getTemplateVars();
    
    $first_i = !empty($params['first']) ? $params['first'] : 1;
    $last_i  = !empty($params['last']) ? $params['last'] : 200;
    $html    = !empty($params['html']) ? $params['html'] : '<div id="gm_box_pos_[COUNTER]" class="[CSS]">[CONTENT]</div>';
    
    $arrBoxesNo = [];
    if (isset($params['exclude'])) {
        $arrBoxesNo = explode(',', $params['exclude']);
    }
    $arrBoxesYes = [];
    if (isset($params['only'])) {
        $arrBoxesYes = explode(',', $params['only']);
    }
    
    for ($i = $first_i; $i <= $last_i; $i++) {
        $content = $assigned_vars['gm_box_pos_' . $i] ?? '';

        if (StyleEditServiceFactory::service()->isEditing() && empty($content)) {
            $content = ' ';
        } elseif (empty($content)) {
            continue;
        }
        
        if (count($arrBoxesNo) > 0) {
            $logInList = false;
            foreach ($arrBoxesNo as $kBox => $vBox) {
                if (strpos($content, 'box-' . $vBox)) {
                    $logInList = true;
                }
            }
            if (!$logInList) {
                $html_out = str_replace('[COUNTER]', $i, $html);
                $html_out = str_replace('[CONTENT]', $content, $html_out);
                $output   .= $html_out . "\n";
            }
        } else {
            if (count($arrBoxesYes) > 0) {
                foreach ($arrBoxesYes as $kBox => $vBox) {
                    if (strpos($content, 'box-' . $vBox)) {
                        $html_out = str_replace('[COUNTER]', $i, $html);
                        $html_out = str_replace('[CONTENT]', $content, $html_out);
                        $output   .= $html_out . "\n";
                    }
                }
            } else {
                $html_out = str_replace('[COUNTER]', $i, $html);
                $html_out = str_replace('[CONTENT]', $content, $html_out);
                $output   .= $html_out . "\n";
            }
        }

        if (StyleEditServiceFactory::service()->isEditing()) {
            $themeId = StyleEditServiceFactory::service()->getCurrentTheme();
            $isActive = StyleEditServiceFactory::service()->getStyleEditReader($themeId)->get_status_by_position(($i-1));
            $cssClass = ($isActive || empty(trim($content))) ? 'gm_box_container' : 'gm_box_container disabled-menu-box';
            $output = str_replace('[CSS]', $cssClass, $output);
        } else {
            $output = str_replace('[CSS]', 'gm_box_container', $output);
        }
    }
    
    return $output;
}
