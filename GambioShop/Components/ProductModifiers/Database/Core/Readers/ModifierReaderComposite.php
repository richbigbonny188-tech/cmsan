<?php
/*--------------------------------------------------------------------------------------------------
    ModifierReaderComposite.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\Readers;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers\ModifierDTOCollection;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers\ModifierDTOCollectionInterface;
use Gambio\Shop\ProductModifiers\Database\Core\Readers\Interfaces\ModifierReaderCompositeInterface;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use IdType;

/**
 * Class ModifierReaderComposite
 * @package Gambio\Shop\ProductModifiers\Database\Core\Readers
 */
class ModifierReaderComposite implements ModifierReaderCompositeInterface
{
    /**
     * @var ModifierReaderCompositeInterface[]
     */
    protected $composites;
    
    
    /**
     * GroupReaderComposite constructor.
     *
     * @param ModifierReaderCompositeInterface|null ...$composites
     */
    public function __construct(?ModifierReaderCompositeInterface ...$composites)
    {
        $this->composites = $composites;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getModifierByProduct(ProductId $id, LanguageId $languageId): ModifierDTOCollectionInterface
    {
        $result = new ModifierDTOCollection();
        
        foreach ($this->composites as $composite) {
            $result->addModifiers($composite->getModifierByProduct($id, $languageId));
        }
        
        return $result;
    }

    public function getModifierBySellingUnit(SellingUnitId $id, LanguageId $languageId): ModifierDTOCollectionInterface
    {
        $result = new ModifierDTOCollection();

        foreach ($this->composites as $composite) {
            $result->addModifiers($composite->getModifierBySellingUnit($id, $languageId));
        }

        return $result;
    }
}