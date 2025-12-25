<?php
/*--------------------------------------------------------------
   ShoppingCartItemId.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects;

class ShoppingCartItemId
{
    /**
     * @var int
     */
    private int $id;
    
    
    /**
     * Constructor
     */
    private function __construct(int $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * Creates a new instance of ShoppingCartItemId
     */
    public static function create(int $id): self
    {
        return new self($id);
    }
    
    
    /**
     * Returns the ID
     */
    public function value(): int
    {
        return $this->id;
    }
}