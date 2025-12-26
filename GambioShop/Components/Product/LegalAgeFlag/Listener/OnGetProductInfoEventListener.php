<?php
/* --------------------------------------------------------------
  OnGetProductInfoEventListener.php 2020-02-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\LegalAgeFlag\Listener;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductInfoEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductLegalAgeFlagEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetProductLegalAgeFlagEvent;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\LegalAgeFlag;

/**
 * Class OnGetProductInfoEventListener
 * @package Gambio\Shop\Product\LegalAgeFlag\Listener
 */
class OnGetProductInfoEventListener
{
    /**
     * @param OnGetProductInfoEventInterface $event
     */
    public function __invoke(OnGetProductInfoEventInterface $event)
    {
        $product      = $event->product();
        $legalAgeFlag = $this->legalAgeFlag($product->getLegalAgeFlag());
        
        $event->builder()->withLegalAgeFlag($legalAgeFlag);
        $event->dispatcher()->dispatch($this->createOnGetProductLegalAgeFlagEvent($event));
    }
    
    
    protected function createOnGetProductLegalAgeFlagEvent(OnGetProductInfoEventInterface $event
    ) : OnGetProductLegalAgeFlagEventInterface {
        return new OnGetProductLegalAgeFlagEvent($event->builder(), $event->product());
    }
    
    
    /**
     * @param bool $legalAgeFlag
     *
     * @return LegalAgeFlag
     */
    protected function legalAgeFlag(bool $legalAgeFlag) : LegalAgeFlag
    {
        return new LegalAgeFlag($legalAgeFlag);
    }
    
}