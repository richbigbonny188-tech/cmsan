<?php
/* --------------------------------------------------------------
  manufacturers.php 2021-03-29 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

$coo_manufacturers = MainFactory::create_object('ManufacturersBoxThemeContentView');
if (isset($_GET['manufacturers_id']) && empty($_GET['manufacturers_id']) == false) {
    $coo_manufacturers->set_('manufacturer_id', (int)$_GET['manufacturers_id']);
}
$t_box_html = $coo_manufacturers->get_html();

$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('manufacturers');
$this->set_content_data($gm_box_pos, $t_box_html);