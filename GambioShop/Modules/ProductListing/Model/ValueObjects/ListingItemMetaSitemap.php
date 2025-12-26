<?php
/* --------------------------------------------------------------
   ListingItemMetaSitemap.php 2022-01-17
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
 * Class ListingItemMetaSitemap
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemMetaSitemap
{
    private ListingItemMetaSitemapFrequency $frequency;
    private ListingItemMetaSitemapPriority  $priority;
    
    
    /**
     * ListingItemMetaSitemap constructor.
     *
     * @param ListingItemMetaSitemapFrequency $frequency
     * @param ListingItemMetaSitemapPriority  $priority
     */
    public function __construct(ListingItemMetaSitemapFrequency $frequency, ListingItemMetaSitemapPriority $priority)
    {
        $this->frequency = $frequency;
        $this->priority  = $priority;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'frequency' => $this->frequency->frequency(),
            'priority'  => $this->priority->priority(),
        ];
    }
}