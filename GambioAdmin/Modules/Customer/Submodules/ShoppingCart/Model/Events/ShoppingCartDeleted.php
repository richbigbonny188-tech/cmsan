<?php
/*--------------------------------------------------------------
   ShoppingCartDeleted.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\Events;

use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects\CustomerId;

class ShoppingCartDeleted
{
    /**
     * @var CustomerId
     */
    private CustomerId $customerId;
    
    
    /**
     * Constructor
     */
    private function __construct(CustomerId $customerId)
    {
        $this->customerId = $customerId;
    }
    
    
    /**
     * Creates a new instance of ShoppingCartDeleted
     */
    public static function create(CustomerId $customerId): self
    {
        return new self($customerId);
    }
    
    
    /**
     * Returns the CustomerId
     */
    public function customerId(): CustomerId
    {
        return $this->customerId;
    }
}