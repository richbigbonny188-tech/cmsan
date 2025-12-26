<?php
/*--------------------------------------------------------------------
 Repository.php 2020-2-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Properties\SellingUnitImages\Database\Repository;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\Properties\SellingUnitImages\Database\Exceptions\ImageListIsEmptyException;
use Gambio\Shop\Properties\SellingUnitImages\Database\Exceptions\PropertyDoesNotHaveAnImageListException;
use Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Factories\ImageFactoryInterface;
use Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Helpers\CombisIdIdentifierInterface;
use Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Readers\ReaderInterface;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageInterface;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageCollectionInterface;
use Gambio\Shop\SellingUnit\Images\Entities\SellingUnitImageCollection;

/**
 * Class Repository
 * @package Gambio\Shop\Properties\SellingUnitImages\Database\Repository
 */
class Repository implements RepositoryInterface
{
    /**
     * @var ReaderInterface
     */
    protected $reader;
    
    /**
     * @var CombisIdIdentifierInterface
     */
    protected $combisIdIdentifier;
    
    /**
     * @var ImageFactoryInterface
     */
    protected $factory;
    
    
    /**
     * Repository constructor.
     *
     * @param ReaderInterface             $reader
     * @param CombisIdIdentifierInterface $combisIdIdentifier
     * @param ImageFactoryInterface       $factory
     */
    public function __construct(
        ReaderInterface $reader,
        CombisIdIdentifierInterface $combisIdIdentifier,
        ImageFactoryInterface $factory
    ) {
        $this->reader             = $reader;
        $this->combisIdIdentifier = $combisIdIdentifier;
        $this->factory            = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getMainImageListImage(
        ProductId $identification,
        ModifierIdentifierCollectionInterface $identifierCollection,
        LanguageId $languageId
    ): SellingUnitImageInterface {
        
        $combisDto = $this->combisIdIdentifier->getCombisId($identification, $identifierCollection);
        $imageDto  = $this->reader->getMainImageListImage($combisDto, $languageId);
        
        return $this->factory->createImage($imageDto);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListImages(
        ProductId $identification,
        ModifierIdentifierCollectionInterface $identifierCollection,
        LanguageId $languageId
    ): SellingUnitImageCollectionInterface {
    
        $combisDto = $this->combisIdIdentifier->getCombisId($identification, $identifierCollection);
        $imageDtos = $this->reader->getImageListImages($combisDto, $languageId);
        $result    = new SellingUnitImageCollection;
        
        foreach ($imageDtos as $imageDto) {
            
            $image = $this->factory->createImage($imageDto);
            $result[] = $image;
        }
        
        return $result;
    }
}