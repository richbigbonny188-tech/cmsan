<?php
/*
 * --------------------------------------------------------------
 *   ListingItemDates.php 2023-09-18
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2023 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

use DateTime;

/**
 * Class ListingItemDates
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingItemDates
{
    private ?DateTime  $createdAt;
    private ?DateTime  $modifiedAt;
    private ?DateTime $availableAt;
    private ?DateTime $expiresAt;
    
    
    /**
     * @param DateTime|null $createdAt
     * @param DateTime|null $modifiedAt
     * @param DateTime|null $availableAt
     * @param DateTime|null $expiresAt
     */
    public function __construct(?DateTime $createdAt, ?DateTime $modifiedAt, ?DateTime $availableAt, ?DateTime $expiresAt)
    {
        $this->createdAt   = $createdAt;
        $this->modifiedAt  = $modifiedAt;
        $this->availableAt = $availableAt;
        $this->expiresAt   = $expiresAt;
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        $format = 'Y-m-d\TH:i:sP';
        
        if (null !== $this->createdAt) {
            $dates['createdAt'] = $this->createdAt->format($format);
        }
        if (null !== $this->modifiedAt) {
            $dates['modifiedAt'] = $this->modifiedAt->format($format);
        }
        if (null !== $this->availableAt) {
            $dates['availableAt'] = $this->availableAt->format($format);
        }
        if (null !== $this->expiresAt) {
            $dates['expiresAt'] = $this->expiresAt->format($format);
        }
        
        return $dates;
    }
}