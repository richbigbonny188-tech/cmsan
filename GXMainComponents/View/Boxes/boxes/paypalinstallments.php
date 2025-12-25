<?php
/* --------------------------------------------------------------
  paypalinstallments.php 2021-04-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * @deprecated will be removed in a future version
 */

$t_box_html = '';
$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('paypalinstallments');
$this->set_content_data($gm_box_pos, $t_box_html);
