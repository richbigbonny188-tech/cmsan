<?php
/*--------------------------------------------------------------------
 OnPresentSellingUnitIdEvent.php 2020-3-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation\Events;

use Gambio\Shop\SellingUnit\Presentation\Collections\PresentationIdCollectionInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;

/**
 * Class OnPresentSellingUnitIdEvent
 * @package Gambio\Shop\SellingUnit\Presentation\Events
 */
class OnPresentSellingUnitIdEvent
{
    /**
     * @var SellingUnitId
     */
    protected $unitId;
    
    /**
     * @var PresentationIdCollectionInterface
     */
    protected $presentationIdCollection;
    
    
    /**
     * OnPresentSellingUnitIdEvent constructor.
     *
     * @param SellingUnitId                     $unitId
     * @param PresentationIdCollectionInterface $presentationIdCollection
     */
    public function __construct(SellingUnitId $unitId, PresentationIdCollectionInterface $presentationIdCollection)
    {
        $this->unitId                   = $unitId;
        $this->presentationIdCollection = $presentationIdCollection;
    }
    
    
    /**
     * @return PresentationIdCollectionInterface
     */
    public function presentationIdCollection(): PresentationIdCollectionInterface
    {
        return $this->presentationIdCollection;
    }
    
    
    /**
     * @return SellingUnitId
     */
    public function sellingUnitId(): SellingUnitId
    {
        return $this->unitId;
    }
}