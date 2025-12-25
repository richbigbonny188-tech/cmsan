<?php
/*--------------------------------------------------------------
   CustomersFavoriteStateUpdated.php 2022-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\Events;

use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;

/**
 * Class CustomersFavoriteStateUpdated
 *
 * @package Gambio\Admin\Modules\Customer\Model\Events
 * @codeCoverageIgnore
 */
class CustomersIsFavoriteStateUpdated
{
    private CustomerId $customerId;
    private bool       $isFavorite;
    
    
    /**
     * @param CustomerId $customerId
     * @param bool       $isFavorite
     */
    private function __construct(CustomerId $customerId, bool $isFavorite)
    {
        $this->customerId = $customerId;
        $this->isFavorite = $isFavorite;
    }
    
    
    /**
     * @param CustomerId $customerId
     * @param bool       $isFavorite
     *
     * @return CustomersIsFavoriteStateUpdated
     */
    public static function create(CustomerId $customerId, bool $isFavorite): CustomersIsFavoriteStateUpdated
    {
        return new self($customerId, $isFavorite);
    }
    
    
    /**
     * @return CustomerId
     */
    public function customerId(): CustomerId
    {
        return $this->customerId;
    }
    
    
    /**
     * @return bool
     */
    public function isFavorite(): bool
    {
        return $this->isFavorite;
    }
}