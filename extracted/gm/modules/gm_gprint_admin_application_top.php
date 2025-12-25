<?php
/* --------------------------------------------------------------
   gm_gprint_admin_application_top.php 2020-05-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

if($_SESSION['customers_status']['customers_status_id'] === '0')
{
	define('FILENAME_GM_GPRINT', 'gm_gprint.php');

	require_once('../gm/modules/gm_gprint_tables.php');
	require_once('../gm/classes/GMGPrintProductManager.php');
	
	require_once('../inc/xtc_get_categories.inc.php');

	// save product
    if (isset($_GET['action']) && substr_count($_SERVER["SCRIPT_NAME"], 'categories.php') > 0
        && $_GET['action'] === 'update_product'
        && isset($_POST['gm_gprint_surfaces_groups_id'])) {
		$t_gm_gprint_products_id = (int)$_POST['products_id'];
		$t_gm_gprint_surfaces_groups_id = (int)$_POST['gm_gprint_surfaces_groups_id'];

		$coo_gm_gprint_product_manager = new GMGPrintProductManager();
		
		if($t_gm_gprint_surfaces_groups_id > 0)
		{
			$coo_gm_gprint_product_manager->add($t_gm_gprint_surfaces_groups_id, $t_gm_gprint_products_id);
		}
		else
		{
			$coo_gm_gprint_product_manager->remove($t_gm_gprint_products_id);
		}
	} elseif (isset($_GET['action']) && substr_count($_SERVER["SCRIPT_NAME"], 'categories.php') > 0
              && $_GET['action'] === 'insert_product'
              && isset($_POST['gm_gprint_surfaces_groups_id'])) {
        if (empty($_POST['products_id'])) {
            xtc_db_query("INSERT INTO `products` SET `products_model` = 'GX-Customizer next product ID check'");
            $t_next_product_id = xtc_db_insert_id();
            xtc_db_query("DELETE FROM `products` WHERE `products_id` = $t_next_product_id");
            xtc_db_query("ALTER TABLE `products` AUTO_INCREMENT=$t_next_product_id");
        }

        $t_gm_gprint_products_id        = !empty($_POST['products_id']) ? (int)$_POST['products_id'] : $t_next_product_id;
        $t_gm_gprint_surfaces_groups_id = (int)$_POST['gm_gprint_surfaces_groups_id'];

		$coo_gm_gprint_product_manager = new GMGPrintProductManager();
		
		if($t_gm_gprint_surfaces_groups_id > 0)
		{
			$coo_gm_gprint_product_manager->add($t_gm_gprint_surfaces_groups_id, $t_gm_gprint_products_id);
		}
		else
		{
			$coo_gm_gprint_product_manager->remove($t_gm_gprint_products_id);
		}
	}
	// save category
    elseif (isset($_GET['action']) && substr_count($_SERVER["SCRIPT_NAME"], 'categories.php') > 0
            && $_GET['action'] === 'update_category'
            && isset($_POST['gm_gprint_surfaces_groups_id'])) {
		$coo_gm_gprint_product_manager = new GMGPrintProductManager();
		
		$coo_gm_gprint_product_manager->save_category($_POST['categories_id'], $_POST['gm_gprint_surfaces_groups_id'] ?? 0, $_POST['gm_gprint_subcategories'] ?? false, isset($_POST['gm_gprint_delete_assignment']) ? (int)$_POST['gm_gprint_delete_assignment'] : 0);
	}
}
