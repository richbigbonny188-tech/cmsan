<?php
/*--------------------------------------------------------------------------------------------------
    Repository.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\SellingUnitImage\Database\Repository;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\SellingUnitImage\Database\Exceptions\CombinationDoesNotHaveAnImageListException;
use Gambio\Shop\Product\SellingUnitImage\Database\Exceptions\ImageListIsEmptyException;
use Gambio\Shop\Product\SellingUnitImage\Database\Exceptions\ProductDoesNotHaveAnImageException;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\Factories\ImageFactoryInterface;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\Readers\ReaderInterface;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageCollectionInterface;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageInterface;
use Gambio\Shop\SellingUnit\Images\Entities\SellingUnitImageCollection;

/**
 * Class Repository
 * @package Gambio\Shop\SellingUnit\ProductInformation\Services\ProductImage\Repositories
 */
class Repository implements RepositoryInterface
{
    /**
     * @var ImageFactoryInterface
     */
    protected $factory;
    /**
     * @var ReaderInterface
     */
    protected $reader;
    
    
    /**
     * ProductImageRepository constructor.
     *
     * @param ReaderInterface       $reader
     * @param ImageFactoryInterface $factory
     */
    public function __construct(ReaderInterface $reader, ImageFactoryInterface $factory)
    {
        $this->reader  = $reader;
        $this->factory = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function mainProductImage(ProductId $id, LanguageId $languageId): ?SellingUnitImageInterface
    {
        try {
            $imageDto = $this->reader->getMainProductImage($id, $languageId);
        } catch (ProductDoesNotHaveAnImageException $doesNotHaveAnImageException) {
            unset($doesNotHaveAnImageException);

            return null;
        }
        
        return $this->factory->createImage($imageDto);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductImages(ProductId $id, LanguageId $languageId): SellingUnitImageCollectionInterface
    {
        $result = new SellingUnitImageCollection();
        try {
            
            foreach ($this->reader->getProductImages($id, $languageId) as $dto) {
                $result[] = $this->factory->createImage($dto);
            }
        } catch (ProductDoesNotHaveAnImageException $doesNotHaveAnImageException) {
            unset($doesNotHaveAnImageException);
        }
        
        return $result;
    }
}