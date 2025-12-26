<?php
/*--------------------------------------------------------------
   CustomerId.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects;

class CustomerId
{
    /**
     * @var int
     */
    private int $id;
    
    
    /**
     * Constructor.
     */
    private function __construct(int $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * Returns a new instance of Customer ID
     */
    public static function create(int $id): self
    {
        return new self($id);
    }
    
    
    /**
     * Returns the id
     */
    public function value(): int
    {
        return $this->id;
    }
}