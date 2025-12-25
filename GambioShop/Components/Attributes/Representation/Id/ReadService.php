<?php
/*--------------------------------------------------------------------
 ReadService.php 2020-3-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\Id;

use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\Attributes\Representation\Id\Repository\RepositoryInterface;
use Gambio\Shop\SellingUnit\Presentation\Collections\PresentationIdCollectionInterface;
/**
 * Class ReadService
 * @package Gambio\Shop\Attributes\Representation\Id
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
    public function extendPresentationIdCollection(
        PresentationIdCollectionInterface $presentationIdCollection,
        ModifierIdentifierCollectionInterface $modifierIdentifierCollection,
        ProductId $productId
    ): PresentationIdCollectionInterface {
        
        return $this->repository->getPresentationIdCollection($presentationIdCollection, $modifierIdentifierCollection, $productId);
    }
}