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

namespace Gambio\Shop\Product\ReleaseDate\Listener;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductReleaseDateEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductInfoEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetProductReleaseDateEvent;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\ReleaseDate;

/**
 * Class OnGetProductInfoEventListener
 * @package Gambio\Shop\Product\ReleaseDate\Listener
 */
class OnGetProductInfoEventListener
{
    /**
     * @param OnGetProductInfoEventInterface $event
     */
    public function __invoke(OnGetProductInfoEventInterface $event)
    {
        $product     = $event->product();
        $releaseDate = $this->releaseDate($product->getReleaseDate(), $product->isShowReleaseDate());
        
        $event->builder()->withReleaseDate($releaseDate);
        $event->dispatcher()->dispatch($this->createOnGetProductReleaseDateEvent($event));
    }
    
    
    /**
     * @param OnGetProductInfoEventInterface $event
     *
     * @return OnGetProductReleaseDateEventInterface
     */
    protected function createOnGetProductReleaseDateEvent(OnGetProductInfoEventInterface $event
    ) : OnGetProductReleaseDateEventInterface {
        return new OnGetProductReleaseDateEvent($event->builder(), $event->product());
    }


    /**
     * @param string $releaseDate
     *
     * @param bool $show
     * @return ReleaseDate
     */
    protected function releaseDate(string $releaseDate, bool $show) : ReleaseDate
    {
        return new ReleaseDate($releaseDate, $show);
    }
    
}