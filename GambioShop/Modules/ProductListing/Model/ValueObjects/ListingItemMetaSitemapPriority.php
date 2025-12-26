<?php
/* --------------------------------------------------------------
   ListingItemMetaSitemapPriority.php 2023-11-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

use InvalidArgumentException;

/**
 * Class ListingItemMetaSitemapPriority
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemMetaSitemapPriority
{
    private float $priority;
    
    
    /**
     * ListingItemMetaSitemapPriority constructor.
     *
     * @param float $priority
     */
    public function __construct(float $priority)
    {
        $this->validate($priority);
        $this->priority = $priority;
    }
    
    
    /**
     * @return float
     */
    public function priority(): float
    {
        return $this->priority;
    }
    
    
    /**
     * Validates priority to be between 0 and 1 and only in .1 steps.
     *
     * @param float $priority
     *
     * @return void
     */
    private function validate(float $priority): void
    {
        $priorities = [];
        for ($i = 0; $i <= 10; $i++) {
            $priorities[] = $i / 10.0;
        }
        
        if (!in_array($priority, $priorities, true)) {
            $prioritiesString = implode("', '", $priorities);
            $message          = "Invalid priority ($priority) provided. Valid priorities are: '$prioritiesString'";
            
            throw new InvalidArgumentException($message);
        }
    }
}