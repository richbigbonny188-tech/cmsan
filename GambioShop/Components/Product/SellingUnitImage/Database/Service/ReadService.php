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

namespace Gambio\Shop\Product\SellingUnitImage\Database\Service;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierCollectionInterface;
use Gambio\Shop\Product\SellingUnitImage\Database\Repository\RepositoryInterface;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageCollectionInterface;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageInterface;
use Gambio\Shop\SellingUnit\Images\Entities\SellingUnitImageCollection;

/**
 * Class ReadService
 * @package Gambio\Shop\SellingUnit\ProductInformation\Services\ProductImage
 */
class ReadService implements ReadServiceInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ReadService constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function mainProductImage(ProductId $id, LanguageId $languageId): ?SellingUnitImageInterface
    {
        return $this->repository->mainProductImage($id, $languageId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductImages(ProductId $id, LanguageId $languageId): ?SellingUnitImageCollectionInterface
    {
        return $this->repository->getProductImages($id, $languageId);
    }
}