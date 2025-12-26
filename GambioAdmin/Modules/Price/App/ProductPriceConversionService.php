<?php
/*
 * --------------------------------------------------------------
 *   ProductPriceConversionService.php 2021-10-27
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2021 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Price\App;

use Gambio\Admin\Modules\Price\App\Data\ProductPriceConversionReader;
use Gambio\Admin\Modules\Price\Services\ProductPriceConversionService as ServiceInterface;

/**
 * Class ProductPriceConversionService
 *
 * @package Gambio\Admin\Modules\Price\App
 */
class ProductPriceConversionService implements ServiceInterface
{
    /**
     * @var ProductPriceConversionReader
     */
    private $reader;

    /**
     * @var array
     */
    private $taxes = [];

    /**
     * @var bool
     */
    private $isAdminGrossEnabled;


    /**
     * @param ProductPriceConversionReader $reader
     */
    public function __construct(ProductPriceConversionReader $reader)
    {
        $this->reader = $reader;
    }


    /**
     * @inheritDoc
     */
    public function getGrossPrice(float $price, int $productId, int $precision = 4): float
    {
        return $this->getPrice($price, $productId, true, $precision);
    }


    /**
     * @inheritDoc
     */
    public function getNetPrice(float $price, int $productId, int $precision = 4): float
    {
        return $this->getPrice($price, $productId, false, $precision);
    }


    /**
     * @param float $price
     * @param int   $productId
     * @param bool  $calculateGross
     * @param int   $precision
     *
     * @return float
     */
    private function getPrice(float $price, int $productId, bool $calculateGross, int $precision): float
    {
        if (!$this->isGrossAdminEnabled()) {
            return $this->formatPrice($price, $precision);
        }

        $taxRates = $this->getProductTaxes($productId);

        foreach ($taxRates as $taxRate) {
            if ($calculateGross) {
                $price = $this->calculateGross($price, $taxRate);
            } else {
                $price = $this->calculateNet($price, $taxRate);
            }
        }

        return $this->formatPrice($price, $precision);
    }


    /**
     * @return bool
     */
    private function isGrossAdminEnabled(): bool
    {
        if (is_null($this->isAdminGrossEnabled)) {
            $this->isAdminGrossEnabled = $this->reader->isAdminGrossEnabled();
        }

        return $this->isAdminGrossEnabled;
    }


    /**
     * @param int $productId
     *
     * @return array
     */
    private function getProductTaxes(int $productId): array
    {
        if (!isset($this->taxes[$productId])) {
            $this->taxes[$productId] = $this->reader->getProductTaxes($productId);
        }

        return $this->taxes[$productId];
    }


    /**
     * @param float $net     The price, excluding any surcharge
     * @param float $percent surcharge percent
     *
     * @return float total price, including surcharge percent
     */
    private function calculateGross(float $net, float $percent): float
    {
        return $net + ($net / 100) * $percent;
    }


    /**
     * @param float $gross   The price, including all surcharges
     * @param float $percent surcharge percent
     *
     * @return float net price, excluding any surcharge
     */
    private function calculateNet(float $gross, float $percent): float
    {
        return $gross / (1 + ($percent / 100));
    }


    /**
     * @param float $price
     * @param int   $precision
     *
     * @return float
     */
    private function formatPrice(float $price, int $precision): float
    {
        return round($price, $precision, PHP_ROUND_HALF_UP);
    }
}