<?php
/*--------------------------------------------------------------------------------------------------
    ReadService.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Attributes\SellingUnitImages\Database;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\Attributes\ProductModifiers\Database\ValueObjects\AttributeModifierIdentifier;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageCollectionInterface;
use Gambio\Shop\Attributes\SellingUnitImages\Database\Interfaces\ReadServiceInterface;
use Gambio\Shop\Attributes\SellingUnitImages\Database\Interfaces\ReadRepositoryInterface;

class ReadService implements ReadServiceInterface
{
    /**
     * @var ReadRepositoryInterface
     */
    protected $repository;
    
    
    public function __construct(
        ReadRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAttributeOptionImagesByProductId(
        AttributeModifierIdentifier $attributeId,
        ProductId $productId,
        LanguageId $languageId
    ) : SellingUnitImageCollectionInterface {
        return $this->repository->getAttributeOptionImagesByProductId($attributeId, $productId, $languageId);
    }
}