<?php
/* --------------------------------------------------------------
  megadropdown.php 2021-07-15
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

$categoryId = $this->category_id;
if (!$this->category_id && is_string($this->c_path)) {
    $cPathArray = explode('_', $this->c_path);
    $categoryId = array_pop($cPathArray);
}

// Categories Top
$coo_content_view = MainFactory::create_object('CategoriesMenuBoxThemeContentView');
$coo_content_view->set_header_categories_template();
$coo_content_view->set_tree_depth(gm_get_conf('CATEGORY_TOP_SHOW_LEVEL'));
$coo_content_view->setCategoryId($categoryId);
$coo_content_view->setCPath($this->c_path);
$t_html = $coo_content_view->get_html();
$this->set_content_data('CATEGORIES_TOP', $t_html);

// Megadropdowns
$coo_content_view = MainFactory::create_object('CategoriesMenuBoxThemeContentView');
$coo_content_view->set_megadropdown_template();
$coo_content_view->set_tree_depth(1);
$coo_content_view->setCategoryId($categoryId);
$coo_content_view->setCPath($this->c_path);

/** @var CategoriesAgent $coo_categories_agent */
$coo_categories_agent    = MainFactory::create_object('CategoriesAgent', [], true);
$t_categories_info_array = $coo_categories_agent->get_categories_info_tree(0, $_SESSION['languages_id'], 0);

$t_html = '';

for ($i = 0; $i < sizeof($t_categories_info_array); $i++) {
    $t_categories_id = $t_categories_info_array[$i]['data']['id'];
    $coo_content_view->setCurrentCategoryId($t_categories_id);
    $t_html .= $coo_content_view->get_html();
}

$this->set_content_data('CATEGORIES_DROPDOWN', $t_html);