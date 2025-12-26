<?php
/**
 * OnGetShortDescriptionEvent.php 2020-3-18
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation\Events;

use Gambio\Shop\SellingUnit\Presentation\Events\Interfaces\OnGetShortDescriptionEventInterface;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\ShortDescription;
use Gambio\Shop\SellingUnit\Unit\SellingUnit;
use Gambio\Shop\SellingUnit\Unit\SellingUnitInterface;

/**
 * Class OnGetShortDescriptionEvent
 * @package Gambio\Shop\SellingUnit\Presentation\Events
 */
class OnGetShortDescriptionEvent implements OnGetShortDescriptionEventInterface
{
    /**
     * @var SellingUnitInterface
     */
    protected $sellingUnit;
    
    /**
     * @var ShortDescription
     */
    protected $shortDescription;
    
    
    public function __construct(SellingUnitInterface $sellingUnit)
    {
        $this->sellingUnit = $sellingUnit;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setShortDescription(ShortDescription $description): void
    {
        $this->shortDescription = $description;
    }
    
    
    /**
     * @return ShortDescription
     */
    public function shortDescription(): ShortDescription
    {
        return $this->shortDescription;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSellingUnit(): SellingUnitInterface
    {
        return $this->sellingUnit;
    }
}