<?php
/*--------------------------------------------------------------------
 RepositoryInterface.php 2020-3-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\Id\Repository;

use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\Attributes\Representation\Id\Exceptions\InvalidValueIdsSpecifiedException;
use Gambio\Shop\SellingUnit\Presentation\Collections\PresentationIdCollectionInterface;

/**
 * Interface RepositoryInterface
 * @package Gambio\Shop\Attributes\Representation\Id\Repository
 */
interface RepositoryInterface
{
    /**
     * @param PresentationIdCollectionInterface     $presentationIdCollection
     * @param ModifierIdentifierCollectionInterface $modifierIdentifierCollection
     *
     * @param ProductId                             $productId
     *
     * @return PresentationIdCollectionInterface
     * @throws InvalidValueIdsSpecifiedException
     */
    public function getPresentationIdCollection(
        PresentationIdCollectionInterface $presentationIdCollection,
        ModifierIdentifierCollectionInterface $modifierIdentifierCollection,
        ProductId $productId
    ): PresentationIdCollectionInterface;
}