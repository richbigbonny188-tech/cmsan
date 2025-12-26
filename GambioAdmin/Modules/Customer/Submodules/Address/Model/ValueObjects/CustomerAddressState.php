<?php
/*--------------------------------------------------------------------------------------------------
    CustomerAddressState.php 2022-10-28
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
class CustomerAddressState
{
    /**
     * @var int
     */
    private int $id;
    
    
    /**
     * @var string
     */
    private string $name;
    
    
    /**
     * @param int    $id
     * @param string $name
     */
    private function __construct(int $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
    
    
    /**
     * @param int    $id
     * @param string $name
     *
     * @return CustomerAddressState
     */
    public static function create(int $id, string $name): CustomerAddressState
    {
        return new self($id, $name);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name];
    }
    
    
}