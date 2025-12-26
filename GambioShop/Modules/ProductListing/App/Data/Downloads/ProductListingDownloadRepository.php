<?php
/*--------------------------------------------------------------
   ProductListingDownloadRepository.php 2023-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Downloads;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemDownloadInformation;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;

/**
 * Class ProductListingDownloadRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Downloads
 */
class ProductListingDownloadRepository
{
    /**
     * @param Connection $connection
     */
    public function __construct(private Connection $connection) { }
    
    
    /**
     * @param ListingItemId $listingItemId
     *
     * @return ListingItemDownloadInformation
     */
    public function getDownloadInformation(ListingItemId $listingItemId): ListingItemDownloadInformation
    {
        $downloadCount = (int)$this->createQuery()
                                  ->where('`pa`.`products_id` = :product_id')
                                  ->setParameter('product_id', $listingItemId->asInt())
                                  ->executeQuery()
                                  ->fetchAssociative()['download_attributes_count'];
        
        return $downloadCount
               >= 1 ? ListingItemDownloadInformation::createForDownloadProduct() : ListingItemDownloadInformation::createForNoneDownloadProduct();
    }
    
    
    /**
     * @return QueryBuilder
     */
    private function createQuery(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT(`pad`.`products_attributes_id`) as download_attributes_count')
            ->from('products_attributes_download', 'pad')
            ->innerJoin('pad',
                        'products_attributes',
                        'pa',
                        '`pad`.`products_attributes_id` = `pa`.`products_attributes_id`');
    }
}