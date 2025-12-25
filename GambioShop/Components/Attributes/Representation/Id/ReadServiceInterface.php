<?php
/*--------------------------------------------------------------------
 ReadServiceInterface.php 2020-3-11
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
use Gambio\Shop\SellingUnit\Presentation\Collections\PresentationIdCollectionInterface;

/**
 * Interface ReadServiceInterface
 * @package Gambio\Shop\Attributes\Representation\Id
 */
interface ReadServiceInterface
{
    /**
     * @param PresentationIdCollectionInterface     $presentationIdCollection
     * @param ModifierIdentifierCollectionInterface $modifierIdentifierCollection
     *
     * @param ProductId                             $productId
     *
     * @return PresentationIdCollectionInterface
     */
    public function extendPresentationIdCollection(
        PresentationIdCollectionInterface $presentationIdCollection,
        ModifierIdentifierCollectionInterface $modifierIdentifierCollection,
        ProductId $productId
    ): PresentationIdCollectionInterface;
}