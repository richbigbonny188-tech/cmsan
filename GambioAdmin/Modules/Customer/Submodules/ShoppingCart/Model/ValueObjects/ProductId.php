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

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects;

use Webmozart\Assert\Assert;

class ProductId
{
    /**
     * @var string
     */
    private string $value;
    
    
    /**
     * Constructor
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * Creates a new instance of ProductId
     */
    public static function create(string $value): self
    {
        Assert::stringNotEmpty($value);
        
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