<?php
/*--------------------------------------------------------------
   ReverseCrossSellingFilter.php 2023-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Filters;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class ReverseCrossSellingFilter
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Filters
 */
class ReverseCrossSellingFilter extends CrossSellingFilter
{
    use FilterUtilityTrait;
    
    protected function createQuery(int $xSellGroupId): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder()
            ->select('p.products_id')
            ->from('products', 'p')
            ->from('products_xsell', 'xp')
            ->where('xp.xsell_id = :base_product_id')
            ->setParameter('base_product_id', $this->baseProductId)
            ->andWhere('xp.products_id = p.products_id')
            ->andWhere('p.products_status = "1"')
            ->orderBy('xp.sort_order', 'ASC');
        
        if ($this->isGroupCheckEnabled($this->configurationFinder)) {
            $customerGroupId = $this->customerIdToStatusId($this->settings->customerId(),
                                                           $this->connection,
                                                           $this->configurationFinder);
            $query           = $this->andWhereCustomerGroupCheck($query, $customerGroupId);
        }
        
        if ($this->isFskHidden($this->settings, $this->configurationFinder, $this->connection)) {
            $query = $this->andWhereHiddenFsk18($query);
        }
        
        return $query;
    }
    
    protected function getBaseProductsCrossSellingGroupId(): ?int
    {
        $groupIdResult = $this->connection->createQueryBuilder()
            ->select('products_xsell_grp_name_id')
            ->from('products_xsell')
            ->where('xsell_id = :base_product_id')
            ->setParameter('base_product_id', $this->baseProductId)
            ->groupBy('products_xsell_grp_name_id')
            ->execute();
        
        if ($groupIdResult->rowCount() === 0) {
            
            return null;
        }
        
        return (int)$groupIdResult->fetch()['products_xsell_grp_name_id'];
    }
}