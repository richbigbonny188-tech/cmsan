<?php
/* --------------------------------------------------------------
   ProductListingPriceVariantStockCheckReader.php 2023-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Price\Components;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\ProductNotFoundException;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemStockSettings;

/**
 * Class ProductListingPriceVariantStockCheckReader
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingPriceVariantStockCheckReader
{
    private const FLAG_GLOBAL_STOCK_SETTINGS = 0;
    private const FLAG_PRODUCT_STOCK         = 1;
    private const FLAG_VARIANT_STOCK         = 2;
    private const FLAG_NO_STOCK_CHECK        = 3;
    
    private Connection $connection;
    
    
    /**
     * ProductListingPriceVariantStockCheckReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Checks if variant stock check is enabled.
     *
     * To do so, it uses the flag from the products table column `use_properties_combis_quantity` and the given
     * stock settings to determine if the variant stock check is enabled.
     *
     * @param ListingItemId            $id
     * @param ListingItemStockSettings $stockSettings
     *
     * @return bool
     * @throws ProductNotFoundException
     */
    public function isVariantStockCheckEnabled(ListingItemId $id, ListingItemStockSettings $stockSettings): bool
    {
        $col   = 'use_properties_combis_quantity';
        $qb    = $this->connection->createQueryBuilder();
        $query = $qb->select($col)->from('products')->where("products_id = {$qb->createNamedParameter($id->asInt())}");
        try {
            $statement = $query->executeQuery();
        } catch (Exception $e) {
            $message = "Product with id '{$id->asInt()}' not found due to an issue with the database driver.";
            throw new ProductNotFoundException($message, 500, $e);
        }
        
        $result = $statement->fetchAssociative();
        if ($result === false) {
            $message = "Product with id '{$id->asInt()}' not found. Therefore, no variant can be found.";
            throw new ProductNotFoundException($message);
        }
        $useVariantStockFlag = (int)($result[$col] ?? '0');
        
        switch ($useVariantStockFlag) {
            case self::FLAG_GLOBAL_STOCK_SETTINGS:
                return !$stockSettings->isCheckoutAllowed() && $stockSettings->isStockCheckEnabled()
                       && $stockSettings->isAttributeStockCheckEnabled();
            case self::FLAG_VARIANT_STOCK:
                return !$stockSettings->isCheckoutAllowed() && $stockSettings->isStockCheckEnabled();
            case self::FLAG_PRODUCT_STOCK:
            case self::FLAG_NO_STOCK_CHECK:
            default:
                return false;
        }
    }
}