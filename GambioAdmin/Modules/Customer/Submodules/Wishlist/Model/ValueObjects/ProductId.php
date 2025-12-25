<?php
/*--------------------------------------------------------------
   ProductId.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects;

class ProductId
{
    /**
     * @var string
     */
    private string $value;
    
    
    /**
     * Constructor
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * Returns a new instance of Product ID
     */
    public static function create(string $value): self
    {
        return new self($value);
    }
    
    
    /**
     * Returns the ID
     */
    public function value(): int
    {
        return (int)$this->value;
    }
    
    
    /**
     * @return string
     */
    public function extended(): string
    {
        return $this->value;
    }
}