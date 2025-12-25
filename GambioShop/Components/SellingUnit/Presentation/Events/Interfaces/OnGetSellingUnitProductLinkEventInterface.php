<?php
/*--------------------------------------------------------------------
  * Interface OnGetSellingUnitProductLinkEventInterface.php 2020-3-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation\Events\Interfaces;

use Gambio\Shop\SellingUnit\Presentation\Collections\PresentationIdCollectionInterface;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\ProductLink;
use Gambio\Shop\SellingUnit\Unit\SellingUnitInterface;

/**
 * Interface OnGetSellingUnitProductLinkEventInterface
 * @package Gambio\Shop\SellingUnit\Presentation\Events\Interfaces
 */
interface OnGetSellingUnitProductLinkEventInterface
{
    /**
     * @return SellingUnitInterface
     */
    public function sellingUnit(): SellingUnitInterface;
    
    
    /**
     * @return PresentationIdCollectionInterface
     */
    public function presentationId(): PresentationIdCollectionInterface;
    
    
    /**
     * @return ProductLink|null
     */
    public function productLink(): ?ProductLink;
    
    
    /**
     * @param ProductLink $productLink
     */
    public function setProductLink(ProductLink $productLink): void;
}