<?php
/* --------------------------------------------------------------
  ProductListingShippingRepository.php 2023-11-16
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Shipping;

use Gambio\Shop\Modules\ProductListing\App\Exceptions\NoShippingStatusFoundException;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemShipping;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemShippingRangeBound;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class ProductListingShippingRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Shipping
 */
class ProductListingShippingRepository
{
    /**
     * @var ProductListingShippingReader
     */
    private ProductListingShippingReader $reader;
    
    
    /**
     * @var ProductListingShippingModelFactory
     */
    private ProductListingShippingModelFactory $factory;
    
    
    /**
     * ProductListingShippingRepository constructor.
     *
     * @param ProductListingShippingReader       $reader
     * @param ProductListingShippingModelFactory $factory
     */
    public function __construct(
        ProductListingShippingReader       $reader,
        ProductListingShippingModelFactory $factory
    ) {
        $this->reader  = $reader;
        $this->factory = $factory;
    }
    
    
    /**
     * Returns shipping information of a listing item based on the products ID.
     *
     * Throws a NoShippingStatusFoundException if product ID (for unknown reasons) had no shipping status
     * linked to it.
     *
     * @param ListingItemId   $listingItemId
     * @param ListingSettings $listingSettings
     * @param bool            $withRange
     *
     * @return ListingItemShipping
     */
    public function getListingItemShipping(
        ListingItemId   $listingItemId,
        ListingSettings $listingSettings,
        bool            $withRange = true
    ): ListingItemShipping {
        $productId           = $listingItemId->asInt();
        $languageId          = $listingSettings->languageId();
        $productShippingInfo = $this->getStatus($productId, $languageId);
        $range               = null;
        
        if (true === $withRange) {
            $shippingRangeBounds = $this->getStatusRange($productId, $languageId);
            if (empty($shippingRangeBounds)) {
                $shippingRangeBounds = [
                    $productShippingInfo,
                    $productShippingInfo,
                ];
            }
            $range = $this->factory->createListingItemShippingRange(...
                array_values(array_map([$this, 'createShippingRangeBound'], $shippingRangeBounds)));
        }
        
        $days         = (int)$productShippingInfo['days'];
        $name         = $productShippingInfo['name'] ?? '';
        $image        = $productShippingInfo['image'] ?? '';
        $linkIsActive = filter_var($productShippingInfo['linkIsActive'], FILTER_VALIDATE_BOOLEAN);
        
        return $this->factory->createListingItemShipping($days, $name, $image, $linkIsActive, $range);
    }
    
    
    /**
     * @param int $productId
     * @param int $languageId
     *
     * @return array
     */
    private function getStatus(int $productId, int $languageId): array
    {
        return $this->reader->fetchStatus($productId, $languageId);
    }
    
    
    /**
     * @param int $productId
     * @param int $languageId
     *
     * @return array[]|null
     */
    private function getStatusRange(int $productId, int $languageId): ?array
    {
        $bounds = [
            'low'  => [
                'days'  => null,
                'name'  => '',
                'image' => '',
            ],
            'high' => [
                'days'  => null,
                'name'  => '',
                'image' => '',
            ],
        ];
        
        // returns null if no statuses were found at all
        if (empty($statuses = $this->reader->fetchStatusRange($productId, $languageId))) {
            return null;
        }
        
        // loops statuses and append value for low/high if value is lower/bigger than existing one
        foreach ($statuses as $status) {
            if ($status['days'] > $bounds['high']['days']
                || $bounds['high']['days'] === null) {
                $bounds['high'] = [
                    'days'  => $status['days'],
                    'name'  => $status['name'],
                    'image' => $status['image'],
                ];
            }
            if ($status['days'] < $bounds['low']['days']
                || $bounds['low']['days'] === null) {
                $bounds['low'] = [
                    'days'  => $status['days'],
                    'name'  => $status['name'],
                    'image' => $status['image'],
                ];
            }
        }
        
        return $bounds;
    }
    
    
    /**
     * @param array $bound
     *
     * @return ListingItemShippingRangeBound
     */
    private function createShippingRangeBound(array $bound): ListingItemShippingRangeBound
    {
        [$days, $name, $image] = array_values($bound);
        
        return $this->factory->createListingItemShippingRangeBound((int)$days, (string)$name, (string)$image);
    }
}