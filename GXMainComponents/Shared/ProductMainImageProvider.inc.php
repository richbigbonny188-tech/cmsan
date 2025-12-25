<?php
/* --------------------------------------------------------------
   ProductMainImageProvider.inc.php 2023-04-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Gambio\Core\Application\Application;
use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollection;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageCollectionInterface;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageInterface;
use Gambio\Shop\SellingUnit\Unit\Factories\Interfaces\SellingUnitIdFactoryInterface;
use Gambio\Shop\SellingUnit\Unit\Services\Interfaces\SellingUnitReadServiceInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;

class ProductMainImageProvider
{
    /**
     * @param string   $productIdentifier
     * @param int|null $languageId
     *
     * @return SellingUnitImageInterface|null
     */
    public static function getImage(string $productIdentifier, ?int $languageId = null): ?SellingUnitImageInterface
    {
        static $cache = [];
        
        if (isset($cache[$productIdentifier][$languageId])) {
            return $cache[$productIdentifier][$languageId];
        }
        
        $sellingUnitImage = null;
        
        try {
            $languageId = $languageId ?? (int)$_SESSION['languages_id'];
            
            $sellingUnitId     = self::sellingUnitIdFactory()
                ->createFromProductString($productIdentifier, new LanguageId($languageId));
            $sellingUnit       = self::sellingUnitReadService()
                ->getSellingUnitBy($sellingUnitId, new product((int)$productIdentifier));
            $sellingUnitImages = $sellingUnit->images();
            $slideIndex        = self::hasProductVariants($productIdentifier) ? self::getInitialSlideIndex($productIdentifier,
                                                                                                           $sellingUnitImages,
                                                                                                           $languageId) : 0;
            $sellingUnitImage  = $sellingUnitImages[$slideIndex];
        } catch (Throwable $throwable) {
            unset($throwable);
        }
        
        if (!isset($cache[$productIdentifier])) {
            $cache[$productIdentifier] = [];
        }
        
        $cache[$productIdentifier][$languageId] = $sellingUnitImage;
        
        return $sellingUnitImage;
    }
    
    
    /**
     * @param string                              $productIdentifier
     * @param SellingUnitImageCollectionInterface $sellingUnitImages
     * @param int|null                            $languageId
     *
     * @return int
     */
    public static function getInitialSlideIndex(
        string                              $productIdentifier,
        SellingUnitImageCollectionInterface $sellingUnitImages,
        ?int                                $languageId = null
    ): int {
        $languageId = $languageId ?? (int)$_SESSION['languages_id'];
        
        $productReadService  = StaticGXCoreLoader::getService('ProductRead');
        $product             = $productReadService->getProductById(new IdType((int)$productIdentifier));
        $imageUrls           = [];
        $comparisonImageUrls = [];
        
        foreach ($sellingUnitImages as $sellingUnitImage) {
            $imageUrls[] = $sellingUnitImage->thumbNail()->value();
        }
        
        $defaultImages = self::getDefaultImages($product->getProductId(), $languageId);
        
        foreach ($defaultImages as $sellingUnitImage) {
            $comparisonImageUrls[] = $sellingUnitImage->thumbNail()->value();
        }
        
        $imageIndex = self::getIndexOfFirstNewImage($imageUrls, $comparisonImageUrls);
        
        // if all images of the new gallery are present in the default gallery, find the first image belonging to an option
        if ($imageIndex === 0) {
            $comparisonImageUrls = [];
            
            if ($product->getPrimaryImage()->getFilename() !== '' && $product->getPrimaryImage()->isVisible()) {
                $comparisonImageUrls[] = DIR_WS_THUMBNAIL_IMAGES . $product->getPrimaryImage()->getFilename();
            }
            
            /* @var ProductImage $image */
            foreach ($product->getAdditionalImages() as $image) {
                if ($image->isVisible()) {
                    $comparisonImageUrls[] = DIR_WS_THUMBNAIL_IMAGES . $image->getFilename();
                }
            }
            
            $imageIndex = self::getIndexOfFirstNewImage($imageUrls, $comparisonImageUrls);
            
            // if the selected variant option contains only images already containing in the main gallery
            if ($imageIndex === 0 && $imageUrls !== $comparisonImageUrls) {
                $variantImages = array_intersect_assoc($imageUrls, $comparisonImageUrls);
                
                if (count($variantImages)) {
                    $imageIndex = array_search($variantImages[0], $imageUrls) + 1; // first image that differs
                }
            }
        }
        
        return $imageIndex;
    }
    
    
    /**
     * @param string $productIdentifier
     *
     * @return bool
     */
    protected static function hasProductVariants(string $productIdentifier): bool
    {
        return strpos($productIdentifier, 'x') !== false;
    }
    
    
    /**
     * @return SellingUnitReadServiceInterface
     */
    protected static function sellingUnitReadService(): SellingUnitReadServiceInterface
    {
        return self::application()->get(SellingUnitReadServiceInterface::class);
    }
    
    
    /**
     * @return Application
     */
    protected static function application(): Application
    {
        return LegacyDependencyContainer::getInstance();
    }
    
    
    protected static function getDefaultImages(int $productId, int $languageId): SellingUnitImageCollectionInterface
    {
        $productId  = new ProductId($productId);
        $languageId = new LanguageId($languageId);
        
        $modifiers     = new ModifierIdentifierCollection();
        $sellingUnitId = new SellingUnitId($modifiers, $productId, $languageId);
        
        $service            = LegacyDependencyContainer::getInstance()->get(SellingUnitReadServiceInterface::class);
        $defaultSellingUnit = $service->getSellingUnitBy($sellingUnitId,
                                                         MainFactory::create('product',
                                                                             $productId->value()));
        
        return $defaultSellingUnit->images();
    }
    
    
    /**
     * @param array $imageUrls
     * @param array $defaultImageUrls
     *
     * @return false|int|string
     */
    protected static function getIndexOfFirstNewImage(array $imageUrls, array $defaultImageUrls)
    {
        $newImages = array_values(array_diff($imageUrls, $defaultImageUrls));
        
        return !empty($newImages) ? array_search($newImages[0], $imageUrls) : 0;
    }
    
    
    /**
     * @return SellingUnitIdFactoryInterface
     */
    protected static function sellingUnitIdFactory(): SellingUnitIdFactoryInterface
    {
        return self::application()->get(SellingUnitIdFactoryInterface::class);
    }
}
