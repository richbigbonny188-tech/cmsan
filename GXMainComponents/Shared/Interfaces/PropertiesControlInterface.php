<?php
/*--------------------------------------------------------------------
 PropertiesControlInterface.php 2020-07-10
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);


/**
 * Interface PropertiesControlInterface
 */
interface PropertiesControlInterface
{
    /**
     * @param $p_properties_combis_id
     * @param $p_language_id
     *
     * @return array
     */
    public function get_combis_full_struct($p_properties_combis_id, $p_language_id);
    
    
    /**
     * @param $p_products_id
     * @param $p_language_id
     *
     * @return array
     */
    public function get_cheapest_combi($p_products_id, $p_language_id);
    
    
    /**
     * @param      $p_products_id
     * @param      $p_languages_id
     * @param      $p_quantity
     * @param      $p_properties_values_string
     * @param      $p_currency
     * @param      $p_customers_status_id
     * @param bool $p_initial
     * @param bool $p_include_html
     *
     * @return mixed
     */
    public function get_selection_data(
        $p_products_id,
        $p_languages_id,
        $p_quantity,
        $p_properties_values_string,
        $p_currency,
        $p_customers_status_id,
        $p_initial = false,
        $p_include_html = true
    );
}