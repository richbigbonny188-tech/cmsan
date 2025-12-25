<?php
/*--------------------------------------------------------------
   SelectedOption.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\Model\ValueObjects;

class SelectedOption
{
    /**
     * @var string
     */
    private string $optionKey;
    
    /**
     * @var string
     */
    private string $optionValue;
    
    
    /**
     * Constructor
     */
    private function __construct(string $optionKey, string $optionValue)
    {
        $this->optionKey   = $optionKey;
        $this->optionValue = $optionValue;
    }
    
    
    /**
     * Creates a new instance of Selected Option
     */
    public static function create(string $optionKey, string $optionValue): self
    {
        return new self($optionKey, $optionValue);
    }
    
    
    /**
     * Returns the Option Key
     */
    public function optionKey(): string
    {
        return $this->optionKey;
    }
    
    
    /**
     * Returns the Option Value
     */
    public function optionValue(): string
    {
        return $this->optionValue;
    }
    
    
    /**
     * Returns the content of this Value Object as an array
     */
    public function toArray(): array
    {
        return [
            "optionKey"   => $this->optionKey,
            "optionValue" => $this->optionValue
        ];
    }
}