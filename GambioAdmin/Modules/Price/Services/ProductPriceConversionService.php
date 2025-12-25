<?php
/*
 * --------------------------------------------------------------
 *   ProductPriceConversionService.php 2022-09-13
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Price\Services;

/**
 * Interface ProductPriceConversionService
 *
 * @package Gambio\Admin\Modules\Price\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains will be refactored into
 *             submodules too. All important changes will be documented in the developer journal as soon as they are
 *             implemented.
 */
interface ProductPriceConversionService
{
    /**
     * Converts a given net price of a product to a gross price if the gross-admin configuration is activated for the
     * Gambio Admin, otherwise it will return the net price unchanged.
     *
     * @param float $price     net price needed to be converted to gross
     * @param int   $productId product ID to get related tax rates
     * @param int   $precision for which decimal should the results be rounded up
     *
     * @return float
     */
    public function getGrossPrice(float $price, int $productId, int $precision = 4): float;
    
    
    /**
     * Converts a given gross price of a product to a net price if the gross-admin configuration is deactivated for the
     * Gambio Admin, otherwise it will return the gross price unchanged.
     *
     * @param float $price     gross price needed to be converted to net
     * @param int   $productId product ID to get related tax rates
     * @param int   $precision for which decimal should the results be rounded up
     *
     * @return float
     */
    public function getNetPrice(float $price, int $productId, int $precision = 4): float;
}