<?php
/*--------------------------------------------------------------------
 Repository.php 2020-3-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\Id\Repository;

use Gambio\Shop\Attributes\ProductModifiers\Database\ValueObjects\AttributeModifierIdentifier;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\Properties\SellingUnitImages\Database\Exceptions\ModifierCollectionDoesNotContainAnyPropertiesException;
use Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Helpers\CombisIdIdentifierInterface;
use Gambio\Shop\Attributes\Representation\Id\Exceptions\InvalidValueIdsSpecifiedException;
use Gambio\Shop\Attributes\Representation\Id\Repository\Factories\PresentationIdFactoryInterface;
use Gambio\Shop\Attributes\Representation\Id\Repository\Readers\ReaderInterface;
use Gambio\Shop\SellingUnit\Presentation\Collections\PresentationIdCollection;
use Gambio\Shop\SellingUnit\Presentation\Collections\PresentationIdCollectionInterface;

/**
 * Class Repository
 * @package Gambio\Shop\Attributes\Representation\Id\Repository
 */
class Repository implements RepositoryInterface
{
    /**
     * @var ReaderInterface
     */
    protected $reader;
    
    /**
     * @var PresentationIdFactoryInterface
     */
    private $factory;
    
    /**
     * @var CombisIdIdentifierInterface
     */
    protected $combisIdIdentifier;
    
    
    /**
     * Repository constructor.
     *
     * @param ReaderInterface                $reader
     * @param PresentationIdFactoryInterface $factory
     * @param CombisIdIdentifierInterface    $combisIdIdentifier
     */
    public function __construct(
        ReaderInterface $reader,
        PresentationIdFactoryInterface $factory,
        CombisIdIdentifierInterface $combisIdIdentifier
    ) {
        $this->reader             = $reader;
        $this->factory            = $factory;
        $this->combisIdIdentifier = $combisIdIdentifier;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getPresentationIdCollection(
        PresentationIdCollectionInterface $presentationIdCollection,
        ModifierIdentifierCollectionInterface $modifierIdentifierCollection,
        ProductId $productId
    ): PresentationIdCollectionInterface {
        
        try {
            
            $attributeValuesIds = $this->getAttributeOptionValuesIds($modifierIdentifierCollection);
            $attributeIdsDtos   = $this->reader->getAttributeIdFromValueId($attributeValuesIds);
            
            foreach ($attributeIdsDtos as $idsDto) {
                
                $presentationIdCollection[] = $this->factory->createAttributePresentationId($idsDto, $modifierIdentifierCollection);
            }
        } catch (InvalidValueIdsSpecifiedException $exception) {
            unset($exception);
        }
        
        return $presentationIdCollection;
    }
    
    
    /**
     * @param ModifierIdentifierCollectionInterface $collection
     *
     * @return int[]
     */
    protected function getAttributeOptionValuesIds(ModifierIdentifierCollectionInterface $collection): array
    {
        $result = [];
        
        foreach ($collection as $identifier) {
            
            if ($identifier instanceof AttributeModifierIdentifier) {
                
                $result[] = $identifier->value();
            }
        }
        
        return $result;
    }
}