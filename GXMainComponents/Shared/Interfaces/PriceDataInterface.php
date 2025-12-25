<?php
/**
 * PriceDataInterface.php 2020-3-23
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);


/**
 * Interface PriceDataInterface
 */
interface PriceDataInterface
{
    /**
     * @param      $p_products_id
     * @param      $p_format_price
     * @param      $p_quantity
     * @param      $p_tax_class_id
     * @param      $p_products_price
     * @param int  $p_return_array
     * @param int  $p_customers_id
     * @param bool $p_include_special
     * @param bool $p_consider_properties
     * @param int  $p_combis_id
     * @param bool $p_consider_attributes
     *
     * @return array
     */
    public function xtcGetPrice(
        $p_products_id,
        $p_format_price,
        $p_quantity,
        $p_tax_class_id,
        $p_products_price,
        $p_return_array = 0,
        $p_customers_id = 0,
        $p_include_special = true,
        $p_consider_properties = false,
        $p_combis_id = 0,
        $p_consider_attributes = false
    );
    
    
    /**
     * @param bool $showFromAttributes
     */
    public function setShowFromAttributes(bool $showFromAttributes): void;
    
    
    /**
     * @param $p_products_id
     *
     * @return mixed
     */
    public function gm_check_price_status($p_products_id);
    
    
    /**
     * @param $taxClassId
     *
     * @return mixed
     */
    public function getTaxRateByTaxClassId($taxClassId);
    
    
    /**
     * @param      $t_price
     * @param      $p_format
     * @param int  $p_tax_class
     * @param bool $p_calculate_currency
     * @param int  $p_return_array
     * @param int  $p_products_id
     *
     * @return mixed
     */
    public function xtcFormat($t_price, $p_format, $p_tax_class = 0, $p_calculate_currency  = false, $p_return_array = 0, $p_products_id = 0);
}