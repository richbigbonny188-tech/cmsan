<?php
/*--------------------------------------------------------------------------------------------------
    CustomerAddressCountry.php 2022-09-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects;

/**
 *
 */
class CustomerAddressCountry
{
    /**
     * @var string
     */
    private string $name;
    
    /**
     * @var string|null
     */
    private ?string $isoCode2;
    
    
    /**
     * @param string      $name
     * @param string|null $isoCode2
     */
    private function __construct(string $name, ?string $isoCode2 = '')
    {
        $this->name     = $name;
        $this->isoCode2 = $isoCode2;
    }
    
    
    /**
     * @param string      $name
     * @param string|null $isoCode2
     *
     * @return CustomerAddressCountry
     */
    public static function create(string $name, ?string $isoCode2 = ''): CustomerAddressCountry
    {
        return new self($name, $isoCode2);
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return string|null
     */
    public function isoCode2(): ?string
    {
        return $this->isoCode2;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return ['name' => $this->name, 'isoCode2' => $this->isoCode2];
    }
}