<?php
/*--------------------------------------------------------------------------------------------------
    ProductModifierReadService.php 2020-02-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Services;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\ProductModifiers\Groups\Collections\GroupCollectionInterface;
use Gambio\Shop\ProductModifiers\Groups\Repositories\GroupRepositoryInterface;
use Gambio\Shop\Product\ValueObjects\ProductId;
use IdType;

class ProductModifierReadService implements ProductModifierReadServiceInterface
{
    /**
     * @var GroupRepositoryInterface
     */
    private $repository;
    
    
    /**
     * ProductModifierReadService constructor.
     *
     * @param GroupRepositoryInterface $repository
     */
    public function __construct(GroupRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getGroupsByProduct(ProductId $id, LanguageId $languageId): GroupCollectionInterface
    {
        return $this->repository->getGroupsByProduct($id, $languageId);
    }
}