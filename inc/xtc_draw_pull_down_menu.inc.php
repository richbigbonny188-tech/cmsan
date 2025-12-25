<?php
/* --------------------------------------------------------------
   xtc_draw_pull_down_menu.inc.php 2023-04-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
   
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.52 2003/03/19); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_draw_pull_down_menu.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_draw_pull_down_menu.inc.php 899 2023-04-24 02:40:57Z hhgag $)

   Released under the GNU General Public License
   --------------------------------------------------------------------------------------- */

// Output a form pull down menu
  function xtc_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
    $field = '<select name="' . xtc_parse_input_field_data($name, array('"' => '&quot;')) . '"';

    if (xtc_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    if (empty($default) && isset($GLOBALS[$name])) $default = $GLOBALS[$name];

    for ($i=0, $n=sizeof($values); $i<$n; $i++) {
      $field .= '<option value="' . xtc_parse_input_field_data($values[$i]['id'] ?? '', array('"' => '&quot;')) . '"';
      if ($default == ($values[$i]['id'] ?? null)) {
        $field .= ' selected="selected"';
      }

      $field .= '>' . xtc_parse_input_field_data($values[$i]['text'] ?? '', array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
    }
    $field .= '</select>';

    if ($required == true) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }
  
    function xtc_draw_pull_down_menuNote($data, $values, $default = '', $parameters = '', $required = false) {
    $field = '<select name="' . xtc_parse_input_field_data($data['name'], array('"' => '&quot;')) . '"';

    if (xtc_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    if (empty($default) && isset($GLOBALS[$data['name']])) $default = $GLOBALS[$data['name']];

    for ($i=0, $n=sizeof($values); $i<$n; $i++) {
      $field .= '<option value="' . xtc_parse_input_field_data($values[$i]['id'], array('"' => '&quot;')) . '"';
      if ($default == $values[$i]['id']) {
        $field .= ' selected="selected"';
      }

      $field .= '>' . xtc_parse_input_field_data($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
    }
    $field .= '</select>'.$data['text'];

    if ($required == true) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }
