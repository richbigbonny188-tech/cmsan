<?php
/*--------------------------------------------------------------
   WishlistReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects\CustomerId;

/**
 * @package Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Data
 */
class WishlistReader
{
    private const DEFAULT_IMAGE_RELATIVE_PATH = '../../../images/product_images/gallery_images/artikelbild.jpg';
    
    /**
     * @var Connection
     */
    private Connection $database;
    
    
    /**
     * Constructor
     */
    public function __construct(Connection $database)
    {
        $this->database = $database;
    }
    
    
    /**
     * Get the data for a given productId with Property ID
     *
     * @throws Exception
     */
    public function getProduct(int $productId, int $languageId): array
    {
        return $this->database->createQueryBuilder()
            ->select(["p.products_id, p.products_model, d.products_name"])
            ->from('products', 'p')
            ->join('p', 'products_description', 'd', 'd.products_id = p.products_id AND d.language_id = :language_id')
            ->where("p.products_id = :product_id")
            ->setParameters(["product_id" => $productId, 'language_id' => $languageId])
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * Get the option Data for a given option ID with Option Value ID
     *
     * @throws Exception
     */
    private function getOption(int $optionId, int $optionValueId, int $languageId): array
    {
        return $this->database->createQueryBuilder()
            ->select('o.products_options_name AS option_name, v.products_options_values_name AS option_value')
            ->from('products_options', 'o')
            ->join('o', 'products_options_values', 'v', 'v.products_options_values_id = :option_value_id')
            ->where('o.products_options_id = :option_id')
            ->andWhere('o.language_id = :language_id')
            ->andWhere("v.language_id = :language_id")
            ->setParameters(["option_id"       => $optionId,
                             "option_value_id" => $optionValueId,
                             "language_id"     => $languageId,
                            ])
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * @param int $variantId
     * @param int $languageId
     *
     * @return array
     * @throws Exception
     */
    private function getVariantOptions(int $variantId, int $languageId): array
    {
        return $this->database->createQueryBuilder()
            ->select('i.properties_name as option_name', 'i.values_name as option_value')
            ->from('products_properties_index', 'i')
            ->where('products_properties_combis_id = :variantId')
            ->andWhere('language_id = :languageId')
            ->setParameters(['variantId' => $variantId, 'languageId' => $languageId])
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * @param int $productId
     *
     * @return array
     * @throws Exception
     */
    private function getProductImage(int $productId): array
    {
        return $this->database->createQueryBuilder()
            ->select('p.products_image')
            ->from('products', 'p')
            ->where('p.products_id = :productId')
            ->setParameter('productId', $productId)
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * @param int $variantId
     *
     * @return array
     * @throws Exception
     */
    private function getVariantImage(int $variantId): array
    {
        return $this->database->createQueryBuilder()
            ->select('*')
            ->from('product_image_list_combi', 'c')
            ->join('c', 'product_image_list_image', 'i', 'i.product_image_list_id = c.product_image_list_id')
            ->where('c.products_properties_combis_id = :variantId')
            ->orderBy('i.product_image_list_image_sort_order')
            ->setMaxResults(1)
            ->setParameter('variantId', $variantId)
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * @param array $product
     *
     * @return void
     */
    private function replaceBrokenImageLinks(array &$product): void
    {
        if ($product['image'] === '') {
            $product['image'] = static::DEFAULT_IMAGE_RELATIVE_PATH;
            
            return;
        }
        
        $absolutePath = dirname(__DIR__, 7) . '/images/product_images/thumbnail_images/' . $product['image'];
        if (is_file($absolutePath) === false) {
            $product['image'] = static::DEFAULT_IMAGE_RELATIVE_PATH;
        }
    }
    
    
    /**
     * Get the Shopping Data for a given Customer
     *
     * @throws Exception
     */
    public function getWishlist(CustomerId $customerId, int $languageId): array
    {
        $wishlistItems = $this->database->createQueryBuilder()
            ->select(["products_id, customers_basket_quantity, customers_basket_id, customers_basket_date_added"])
            ->from("customers_wishlist")
            ->where("customers_id = :customerId")
            ->setParameter("customerId", $customerId->value())
            ->executeQuery()
            ->fetchAllAssociative();
        
        $items        = [];
        $optionsRegex = '/(\{\d+\}\d+)/m';
        foreach ($wishlistItems as $wishlistItem) {
            preg_match_all($optionsRegex, $wishlistItem["products_id"], $chosenOptions, PREG_SET_ORDER);
            preg_match_all('/x(\d+)/m', $wishlistItem["products_id"], $variantId, PREG_SET_ORDER);
            $variantId = $variantId[0][1];
            
            $options = $this->getOptions($chosenOptions, $languageId);
            $options = array_merge($options,
                                   $this->getCustomizerOptions($customerId, $wishlistItem['products_id'], $languageId));
            
            $productId = $wishlistItem["products_id"];
            $product   = $this->getProduct((int)$productId, $languageId)[0];
            
            if ($variantId !== null) {
                $options = array_merge($options, $this->getVariantOptions((int)$variantId, $languageId));
                
                if (!$this->appendVariantModelNumber()) {
                    $product['products_model'] = '';
                }
            }
            
            $product['combi_model']         = $this->getOptionsModel($chosenOptions, (int)$variantId, $languageId);
            $product["amount"]              = $wishlistItem["customers_basket_quantity"];
            $product["customers_basket_id"] = $wishlistItem["customers_basket_id"];
            $product["added_at"]            = $wishlistItem["customers_basket_date_added"];
            if ($variantId !== null) {
                $product['image'] = $this->getVariantImage((int)$variantId)[0]['product_image_list_image_local_path'];
            }
            
            if ($variantId === null || $product['image'] === null) {
                $product['image'] = $this->getProductImage((int)$productId)[0]['products_image'];
            }
            
            $this->replaceBrokenImageLinks($product);
            
            $product["selected_options"] = $options;
            $product['products_id']      = $productId;
            
            $items[] = $product;
        }
        
        return $items;
    }
    
    
    /**
     * @param array $chosenOptions
     * @param int   $languageId
     *
     * @return array
     * @throws Exception
     */
    private function getOptions(array $chosenOptions, int $languageId): array
    {
        $options = [];
        
        foreach ($chosenOptions as $chosenOption) {
            preg_match_all('/(\d+)/m', $chosenOption[0], $optionValues, PREG_SET_ORDER);
            
            // Ignore Option if the second value is 0 (GXCustomizerId)
            if ((int)$optionValues[1][0] === 0) {
                continue;
            }
            
            $options[] = $this->getOption((int)$optionValues[0][0], (int)$optionValues[1][0], $languageId)[0];
        }
        
        return $options;
    }
    
    
    /**
     * @param array $options
     * @param int   $variantId
     * @param int   $languageId
     *
     * @return string
     * @throws Exception
     */
    public function getOptionsModel(array $options, int $variantId, int $languageId): string
    {
        $model = '';
        
        if (count($options) !== 0) {
            $valueIds = [];
            
            // each option contains a string like {2}9
            foreach ($options as [$option]) {
                [$optionId, $valueId] = explode('}', $option);
                $optionId = substr($optionId, 1);
                
                $optionId = (int)$optionId;
                $valueId  = (int)$valueId;
                
                if ($valueId === 0) {
                    //  this is a customizer set and not a product option
                    continue;
                }
                
                $valueIds[] = $valueId;
            }
            // options could only contain a customizer set
            if (count($valueIds) !== 0) {
                $productOptionValues = $this->database->createQueryBuilder()
                    ->select('value_model')
                    ->from('products_options_values')
                    ->where('products_options_values_id IN(' . implode(',', $valueIds) . ')')
                    ->andWhere('language_id=:language_id')
                    ->setParameter('language_id', $languageId)
                    ->orderBy('sort_order')
                    ->executeQuery()
                    ->fetchAllNumeric();
                $productOptionValues = array_map(fn(array $r): string => array_shift($r), $productOptionValues);
                $model               = implode('-', $productOptionValues);
            }
        }
        
        if ($variantId !== 0) {
            $variantModel = $this->database->createQueryBuilder()
                                ->select('combi_model')
                                ->from('products_properties_combis')
                                ->where('products_properties_combis_id = :variant_id')
                                ->setParameter('variant_id', $variantId)
                                ->executeQuery()
                                ->fetchNumeric()[0];
            
            // model is ordered like: product, variant and product options
            $model = $model === '' ? $variantModel : $variantModel . '-' . $model;
        }
        
        return $model;
    }
    
    
    /**
     * @param CustomerId $customerId
     * @param            $products_id
     * @param int        $languageId
     *
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    private function getCustomizerOptions(CustomerId $customerId, $products_id, int $languageId): array
    {
        return $this->database->createQueryBuilder()
            ->distinct()
            ->select(['v.`name` AS `option_name`, w.`elements_value` AS `option_value`'])
            ->from('gm_gprint_wishlist_elements', 'w')
            ->where("w.customers_id = :customerId")
            ->setParameter("customerId", $customerId->value())
            ->where("w.products_id = :productId")
            ->setParameter("productId", $products_id)
            ->leftJoin('w', 'gm_gprint_elements', 'e', 'w.gm_gprint_elements_id=e.gm_gprint_elements_id')
            ->leftJoin('e',
                       'gm_gprint_elements_values',
                       'v',
                       'e.gm_gprint_elements_groups_id=v.gm_gprint_elements_groups_id AND v.languages_id = :languageId')
            ->setParameter("languageId", $languageId)
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    private function appendVariantModelNumber(): bool
    {
        static $append;
        
        if ($append === null) {
            $result = $this->database->createQueryBuilder()
                ->select(['`value`'])
                ->from('gx_configurations')
                ->where('`key` = "configuration/APPEND_PROPERTIES_MODEL"')
                ->executeQuery()
                ->fetchAssociative();
            
            $append = ($result['value'] ?? null) === 'true';
        }
        
        return $append;
    }
}