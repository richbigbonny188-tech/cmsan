<?php
/*--------------------------------------------------------------------
 OnGetSellingUnitProductLinkEvent.php 2020-3-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation\Events;

use Gambio\Shop\SellingUnit\Presentation\Collections\PresentationIdCollectionInterface;
use Gambio\Shop\SellingUnit\Presentation\Events\Interfaces\OnGetSellingUnitProductLinkEventInterface;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\ProductLink;
use Gambio\Shop\SellingUnit\Unit\SellingUnitInterface;

/**
 * Class OnGetSellingUnitProductLinkEvent
 * @package Gambio\Shop\SellingUnit\Presentation\Events
 */
class OnGetSellingUnitProductLinkEvent implements OnGetSellingUnitProductLinkEventInterface
{
    /**
     * @var SellingUnitInterface
     */
    protected $sellingUnit;
    
    /**
     * @var PresentationIdCollectionInterface
     */
    protected $presentationId;
    
    /**
     * @var ProductLink|null
     */
    protected $productLink;
    
    
    /**
     * OnGetSellingUnitProductLinkEvent constructor.
     *
     * @param SellingUnitInterface              $sellingUnit
     * @param PresentationIdCollectionInterface $presentationId
     */
    public function __construct(SellingUnitInterface $sellingUnit, PresentationIdCollectionInterface $presentationId)
    {
        $this->sellingUnit    = $sellingUnit;
        $this->presentationId = $presentationId;
    }
    
    
    /**
     * @return SellingUnitInterface
     */
    public function sellingUnit(): SellingUnitInterface
    {
        return $this->sellingUnit;
    }
    
    
    /**
     * @return PresentationIdCollectionInterface
     */
    public function presentationId(): PresentationIdCollectionInterface
    {
        return $this->presentationId;
    }
    
    
    /**
     * @return ProductLink|null
     */
    public function productLink(): ?ProductLink
    {
        return $this->productLink;
    }
    
    
    /**
     * @param ProductLink $productLink
     */
    public function setProductLink(ProductLink $productLink): void
    {
        $this->productLink = $productLink;
    }
}