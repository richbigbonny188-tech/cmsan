<?php
/* --------------------------------------------------------------
   ListingItemPriceExtraInfo.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Interface ListingItemPriceExtraInfo
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
interface ListingItemPriceExtraInfo
{
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array;
    
    
    /**
     * Defines the kind of information.
     *
     * @return string
     */
    public function kind(): string;
}