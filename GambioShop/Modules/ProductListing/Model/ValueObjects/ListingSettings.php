<?php
/* --------------------------------------------------------------
   ListingSettings.php 2022-01-07
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
 * Class ListingSettings
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingSettings
{
    private int    $languageId;
    private ?int   $customerId;
    private string $currencyCode;
    
    
    /**
     * ListingSettings constructor.
     *
     * @param int      $languageId
     * @param int|null $customerId
     * @param string   $currencyCode
     */
    public function __construct(int $languageId, ?int $customerId, string $currencyCode)
    {
        $this->languageId   = $languageId;
        $this->customerId   = $customerId;
        $this->currencyCode = $currencyCode;
    }
    
    
    /**
     * @return int
     */
    public function languageId(): int
    {
        return $this->languageId;
    }
    
    
    /**
     * @return int|null
     */
    public function customerId(): ?int
    {
        return $this->customerId;
    }
    
    
    /**
     * @return string
     */
    public function currencyCode(): string
    {
        return $this->currencyCode;
    }
}
