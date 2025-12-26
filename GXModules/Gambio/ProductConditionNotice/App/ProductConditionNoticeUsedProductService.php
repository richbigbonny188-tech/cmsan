<?php
/* --------------------------------------------------------------
   ProductConditionNoticeUsedProductService.php 2021-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\ProductConditionNotice\App;

use CI_DB_query_builder;
use CI_DB_result;
use GXModules\Gambio\ProductConditionNotice\Services\ProductConditionNoticeUsedProductService as ProductConditionNoticeUsedProductServiceInterface;

/**
 * Class ProductConditionNoticeUsedProductService
 *
 * @package GXModules\Gambio\ProductConditionNotice\App
 */
class ProductConditionNoticeUsedProductService implements ProductConditionNoticeUsedProductServiceInterface
{
    /**
     * @var CI_DB_query_builder
     */
    private $queryBuilder;
    
    
    /**
     * @param CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * @inheritDoc
     */
    public function containsAUsedOrRenewedProduct(int ...$productIds): bool
    {
        if (empty($productIds)) {
            return false;
        }
        
        if ($this->conditionDBColumnIsNew()) {
            $result = $this->queryBuilder->select()
                ->from('products_item_codes')
                ->where_in('products_id', $productIds)
                ->not_group_start()
                ->where('google_export_condition_id', '1')
                ->group_end()
                ->get();
        } else {
            $result = $this->queryBuilder->select()
                ->from('products_item_codes')
                ->where_in('products_id', $productIds)
                ->not_group_start()
                ->where('google_export_condition', 'neu')
                ->group_end()
                ->get();
        }
        
        return $result->num_rows() > 0;
    }
    
    
    /**
     * @return bool
     */
    protected function conditionDBColumnIsNew(): bool
    {
        /** @var CI_DB_result $result */
        $result = $this->queryBuilder->query('SHOW COLUMNS FROM `products_item_codes` LIKE \'google_export_condition_id\';');
        
        return $result->num_rows() !== 0;
    }
}
