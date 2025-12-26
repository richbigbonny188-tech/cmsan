<?php
/*--------------------------------------------------------------------------------------------------
    GroupReaderComposite.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\Readers;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups\GroupDTOCollection;
use Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups\GroupDTOCollectionInterface;
use Gambio\Shop\ProductModifiers\Database\Core\Readers\Interfaces\GroupReaderCompositeInterface;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use IdType;

/**
 * Class GroupReaderComposite
 * @package Gambio\Shop\ProductModifiers\Database\Core\Readers
 */
class GroupReaderComposite implements GroupReaderCompositeInterface
{
    /**
     * @var GroupReaderCompositeInterface[]
     */
    protected $composites;
    
    
    /**
     * GroupReaderComposite constructor.
     *
     * @param GroupReaderCompositeInterface|null ...$composites
     */
    public function __construct(?GroupReaderCompositeInterface ...$composites)
    {
        $this->composites = $composites;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getGroupsByProduct(
        ProductId $id,
        LanguageId $languageId
    ): GroupDTOCollectionInterface {
        $result = new GroupDTOCollection();
        
        foreach ($this->composites as $composite) {
            $result->addGroups($composite->getGroupsByProduct($id, $languageId));
        }
        
        return $result;
    }

    public function getGroupsBySellingUnit(SellingUnitId $id, LanguageId $languageId): GroupDTOCollectionInterface
    {
        $result = new GroupDTOCollection();

        foreach ($this->composites as $composite) {
            $result->addGroups($composite->getGroupsBySellingUnit($id, $languageId));
        }

        return $result;
    }
}