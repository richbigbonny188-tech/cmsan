<?php
/* --------------------------------------------------------------
   CustomerAddonValueKey.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class CustomerAddonValueKey
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Model\ValueObjects
 */
class CustomerAddonValueKey
{
    private string $value;
    
    
    /**
     * @param string $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @param string $value
     *
     * @return CustomerAddonValueKey
     */
    public static function create(string $value): CustomerAddonValueKey
    {
        Assert::notWhitespaceOnly($value, 'Customer addon value key can not be whitespace only.');
        
        return new self($value);
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
}