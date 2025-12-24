<?php
/* --------------------------------------------------------------
   ProductsQuantityUpdateService.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes;

use CI_DB_query_builder;
use Doctrine\DBAL\Connection;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ProductsMapper;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ProductsMappingRepository;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\AfterbuyProduct;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\BaseProduct;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\MappingMode;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\ProductMapping;
use LegacyDependencyContainer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

require_once(DIR_FS_CATALOG . 'gm/inc/set_shipping_status.php');

/**
 * Class ProductsQuantityUpdateService
 *
 * Updates product quantities.
 *
 * This class does not use ProductReadService and ProductWriteService due to performance issues.
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes
 */
class ProductsQuantityUpdateService implements LoggerAwareInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected CI_DB_query_builder $db;
    
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    
    
    /**
     * @var ProductsMappingRepository
     */
    private ProductsMappingRepository $productsMappingRepository;
    
    
    /**
     * @var bool
     */
    private bool $setOutOfStockProducts;
    
    
    /**
     * @param CI_DB_query_builder $db
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db                        = $db;
        $dbConnection                    = LegacyDependencyContainer::getInstance()->get(Connection::class);
        $this->productsMappingRepository = new ProductsMappingRepository($dbConnection);
        $this->setOutOfStockProducts     = defined('GM_SET_OUT_OF_STOCK_PRODUCTS')
                                           && GM_SET_OUT_OF_STOCK_PRODUCTS === 'true';
    }
    
    
    /**
     * Uses a set of AfterbuyProducts to update local product quantities.
     *
     * Uses the EAN (External Article Number) from Afterbuy to identify local products by their products_id.
     *
     * @param string          $mode
     * @param AfterbuyProduct ...$products
     *
     * @return void
     */
    public function updateProductQuantities(string $mode = 'pid_ean', AfterbuyProduct ...$products): void
    {
        $this->log("Match mode: {$mode}");
        $mapper = new ProductsMapper(new MappingMode($mode));
        $mapper->setLogger($this->logger);
        foreach ($products as $product) {
            $this->log("Afterbuy Produkt {$product->getProductID()} / {$product->getName()} / Anr {$product->getAnr()} / EXAN {$product->getEAN()}");
            
            if ($product->getBaseProductFlag() === 1 || $product->getBaseProductFlag() === 2) {
                $baseProducts    = $product->getBaseProducts();
                $variantProducts = [];
                /** @var BaseProduct $baseProduct */
                foreach ($baseProducts as $baseProduct) {
                    $variantProduct = $product->getVariantProduct($baseProduct->getBaseProductID());
                    if ($variantProduct !== null) {
                        $variantProducts[] = $variantProduct;
                    }
                }
                if (!empty($variantProducts)) {
                    $this->log("Afterbuy Produkt {$product->getProductID()} has BaseProducts, processing");
                    static::updateProductQuantities($mode, ...$variantProducts);
                }
                $this->log("Finished processing of BaseProducts of Afterbuy Produkt {$product->getProductID()}");
            }
            
            $productMapping = $this->productsMappingRepository->findMappingByAfterbuyProduct($product);
            if ($productMapping->isType(ProductMapping::MAPPING_NONE)) {
                $productMapping = $mapper->findProductByAfterbuyProduct($product);
            }
            if ($productMapping->isType(ProductMapping::MAPPING_NONE)) {
                $this->log("Product {$product->getProductID()} cannot be mapped to a product/variant; skipping");
                continue;
            }
            $productsId = $productMapping->getProductsId();
            $combiId    = $productMapping->getCombiId();
            if (!$this->isValidProductsId($productsId)) {
                $this->log("Product with products_id {$productsId} does not exist, skipping.");
                continue;
            }
            if (!empty($combiId) && $this->productHasProperties($productsId)) {
                $oldTotalVariantsQuantity  = $this->getProductVariantsQuantitySum($productsId);
                $newProductVariantQuantity = (float)$product->getQuantity();
                $this->log("Setting quantity of product variant {$productsId}x{$combiId} to {$product->getQuantity()}.");
                $this->setProductVariantsQuantity($productsId, $combiId, $newProductVariantQuantity);
                if ($this->setOutOfStockProducts) {
                    $newTotalVariantsQuantity = $this->getProductVariantsQuantitySum($productsId);
                    if ($oldTotalVariantsQuantity <= 0 && $newTotalVariantsQuantity > 0) {
                        $this->log("Setting status of product {$productsId} ({$product->getName()}) to ACTIVE (variant re-stocked).  {$oldTotalVariantsQuantity} -> {$newTotalVariantsQuantity}");
                        $this->setProductActive($productsId);
                    }
                    if ($oldTotalVariantsQuantity > 0 && $newTotalVariantsQuantity <= 0) {
                        $this->log("Setting status of product {$productsId} ({$product->getName()}) to INACTIVE (variants out of stock). {$oldTotalVariantsQuantity} -> {$newTotalVariantsQuantity}");
                        $this->setProductInactive($productsId);
                    }
                }
            } else {
                $oldProductsQuantity = $this->getProductQuantity($productsId);
                $newProductsQuantity = (float)$product->getQuantity();
                $this->log("Setting quantity of product {$productsId} ({$product->getName()}) from {$oldProductsQuantity} to {$newProductsQuantity}.");
                $this->setProductQuantity($productsId, $newProductsQuantity);
                if ($this->setOutOfStockProducts) {
                    if ($oldProductsQuantity <= 0 && $newProductsQuantity > 0) {
                        $this->log("Setting status of product {$productsId} ({$product->getName()}) to ACTIVE (re-stocked).");
                        $this->setProductActive($productsId);
                    }
                    if ($oldProductsQuantity > 0 && $newProductsQuantity <= 0) {
                        $this->log("Setting status of product {$productsId} ({$product->getName()}) to INACTIVE (out of stock).");
                        $this->setProductInactive($productsId);
                    }
                }
            }
        }
    }
    
    
    /**
     * @param int   $productId
     * @param int   $combiId
     * @param float $quantity
     *
     * @return void
     */
    protected function setProductVariantsQuantity(int $productId, int $combiId, float $quantity): void
    {
        $this->db->set('combi_quantity', $quantity)->where([
                                                               'products_id'                   => $productId,
                                                               'products_properties_combis_id' => $combiId,
                                                           ])->update('products_properties_combis');
    }
    
    
    /**
     * @param int $productsId
     *
     * @return float
     */
    protected function getProductVariantsQuantitySum(int $productsId): float
    {
        $result = $this->db->select('combi_quantity')
            ->from('products_properties_combis')
            ->where('products_id', $productsId)
            ->get()
            ->result();
        $sum    = 0.0;
        foreach ($result as $row) {
            $sum += max(0, (float)$row->combi_quantity);
        }
        
        return $sum;
    }
    
    
    /**
     * @param int   $productsId
     * @param float $quantity
     *
     * @return void
     */
    protected function setProductQuantity(int $productsId, float $quantity): void
    {
        $this->db->update('products', ['products_quantity' => $quantity], ['products_id' => $productsId]);
        set_shipping_status($productsId, null, $quantity);
    }
    
    
    /**
     * @param int $productsId
     *
     * @return void
     */
    protected function setProductActive(int $productsId): void
    {
        $this->db->update('products', ['products_status' => 1], ['products_id' => $productsId]);
    }
    
    
    /**
     * @param int $productsId
     *
     * @return void
     */
    protected function setProductInactive(int $productsId): void
    {
        $this->db->update('products', ['products_status' => 0], ['products_id' => $productsId]);
    }
    
    
    /**
     * @param int $productsId
     *
     * @return float
     */
    protected function getProductQuantity(int $productsId): float
    {
        $productsRow = $this->db->select('products_quantity')
            ->from('products')
            ->where('products_id', $productsId)
            ->get()
            ->row_array();
        
        return (float)$productsRow['products_quantity'];
    }
    
    
    /**
     * @param int $productsId
     *
     * @return bool
     */
    protected function isValidProductsId(int $productsId): bool
    {
        $rows = $this->db->get_where('products', ['products_id' => $productsId])->num_rows();
        $this->db->reset_query();
        
        return $rows === 1;
    }
    
    
    /**
     * @param int $productsId
     *
     * @return bool
     */
    protected function productHasAttributes(int $productsId): bool
    {
        $numAttributesRow = $this->db->select('COUNT(*) AS num_attribs')
            ->from('products_attributes')
            ->where('products_id', $productsId)
            ->get()
            ->row_array();
        $numAttributes    = $numAttributesRow['num_attribs'];
        
        return $numAttributes > 0;
    }
    
    
    /**
     * @param int $productsId
     *
     * @return bool
     */
    protected function productHasProperties(int $productsId): bool
    {
        $numPropertiesRow = $this->db->select('COUNT(*) AS num_combis')
            ->from('products_properties_combis')
            ->where('products_id', $productsId)
            ->get()
            ->row_array();
        $numProperties    = $numPropertiesRow['num_combis'];
        
        return $numProperties > 0;
    }
    
    
    /**
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    
    /**
     * @param string $message
     *
     * @return void
     */
    protected function log(string $message): void
    {
        if ($this->logger !== null) {
            $this->logger->info($message);
        }
    }
}