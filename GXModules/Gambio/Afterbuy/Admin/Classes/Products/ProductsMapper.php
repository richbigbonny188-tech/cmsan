<?php
/* --------------------------------------------------------------
   ProductsMapper.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products;

use CI_DB_query_builder;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\AfterbuyProduct;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\MappingMode;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\ProductMapping;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ProductsMapper
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products
 */
class ProductsMapper implements LoggerAwareInterface
{
    /**
     * @var MappingMode
     */
    private MappingMode $mode;
    
    /**
     * @var LoggerInterface|null
     */
    private ?LoggerInterface $logger;
    
    /**
     * @var CI_DB_query_builder
     */
    private CI_DB_query_builder $db;
    
    
    /**
     * @param MappingMode              $mode
     * @param CI_DB_query_builder|null $db
     */
    public function __construct(MappingMode $mode, CI_DB_query_builder $db = null)
    {
        $this->mode = $mode;
        $this->db   = $db ?? \StaticGXCoreLoader::getDatabaseQueryBuilder();
    }
    
    
    /**
     * @param AfterbuyProduct $afterbuyProduct
     *
     * @return ProductMapping
     */
    public function findProductByAfterbuyProduct(AfterbuyProduct $afterbuyProduct): ProductMapping
    {
        $productsId = null;
        $combiId    = null;
        if ($this->mode->is(MappingMode::PID_EAN)) {
            $exan = $afterbuyProduct->getEAN();
            if (preg_match('/^\d+$/', $exan, $exanMatches) === 1) {
                $productsId = (int)$exanMatches[0];
            } elseif (preg_match('/^(\d+)x(\d+)$/', $exan, $exanMatches)) {
                $productsId = (int)$exanMatches[1];
                $combiId    = (int)$exanMatches[2];
            } else {
                $this->log("Product with unsupported EXAN syntax \"{$exan}\" cannot be mapped, skipping");
                
                return new ProductMapping(null);
            }
        } elseif ($this->mode->is(MappingMode::PID_ANR)) {
            $productsId = (int)$afterbuyProduct->getAnr();
            if (empty($productsId)) {
                $this->log("Product with Afterbuy ProductsID {$afterbuyProduct->getProductID()} has no usable Anr ({$afterbuyProduct->getAnr()}), skipping");
                
                return new ProductMapping(null);
            }
        } else {
            if ($this->mode->is(MappingMode::MODEL_EAN)) {
                $model = $afterbuyProduct->getEAN();
            } elseif ($this->mode->is(MappingMode::MODEL_ANR)) {
                $model = (string)$afterbuyProduct->getAnr();
            } elseif ($this->mode->is(MappingMode::MODEL_PRODID)) {
                $model = (string)$afterbuyProduct->getProductID();
            }
            if (empty($model)) { // N. B.: This intentionally covers $model === '0'!
                $this->log("Product with Afterbuy ProductsID {$afterbuyProduct->getProductID()} has no usable identification, skipping");
                
                return new ProductMapping(null);
            }
            $productsIds = $this->findProductsIdsByModel($model);
            if (count($productsIds) === 1) {
                $productsId = (int)$productsIds[0];
            } else {
                $this->log("Product model \"{$model}\" not found or ambigous, trying as variant");
                $productsCombisIds = $this->findProductsCombisIdsByModel($model);
                if (empty($productsCombisIds)) {
                    $this->log("No product or variant for model \"{$model}\" found; skipping");
                    
                    return new ProductMapping(null);
                }
                $productsId = (int)$productsCombisIds['products_id'];
                $combiId    = (int)$productsCombisIds['combis_id'];
            }
        }
        
        return new ProductMapping($productsId, $combiId, $afterbuyProduct->getProductID());
    }
    
    
    /**
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
    
    
    /**
     * @param string $message
     *
     * @return void
     */
    private function log(string $message): void
    {
        if (!is_null($this->logger)) {
            $this->logger->notice($message);
        }
    }
    
    
    /**
     * @param string $model
     *
     * @return array
     */
    private function findProductsIdsByModel(string $model): array
    {
        $rows = $this->db->select('products_id')->get_where('products', ['products_model' => $model])->result_array();
        
        $productIds = [];
        foreach ($rows as $row) {
            $productIds[] = $row['products_id'];
        }
        
        return $productIds;
    }
    
    
    /**
     * @param string $model
     *
     * @return array
     */
    private function findProductsCombisIdsByModel(string $model): array
    {
        $modelParts = explode('-', $model, 2);
        if (count($modelParts) === 1) {
            // no separator
            $productModel = '';
            $combiModel   = $modelParts[0];
            
            $rows = $this->db->from('products_properties_combis')
                ->select('products_properties_combis_id, products_id')
                ->where('combi_model', $combiModel)
                ->get()
                ->result_array();
            if (count($rows) === 1) {
                return [
                    'combis_id'   => $rows[0]['products_properties_combis_id'],
                    'products_id' => $rows[0]['products_id'],
                ];
            }
        } else {
            $productModel = $modelParts[0];
            $combiModel   = $modelParts[1];
            
            $rows = $this->db->from('products_properties_combis ppc')
                ->select('ppc.products_properties_combis_id, ppc.products_id')
                ->join('products p', 'p.products_id = ppc.products_id')
                ->where(['p.products_model' => $productModel, 'ppc.combi_model' => $combiModel])
                ->get()
                ->result_array();
            
            if (count($rows) === 1) {
                return [
                    'combis_id'   => $rows[0]['products_properties_combis_id'],
                    'products_id' => $rows[0]['products_id'],
                ];
            }
        }
        
        return [];
    }
}