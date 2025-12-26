<?php
/* --------------------------------------------------------------
   Feature.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects;

/**
 * Class Feature
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects
 */
class Feature
{
    /**
     * @var string
     */
    private string $Name;
    
    
    /**
     * @var string
     */
    private string $Value;
    
    
    /**
     * @var int|null
     */
    private ?int $ID;
    
    
    /**
     * @param string   $Name
     * @param string   $Value
     * @param int|null $ID
     */
    public function __construct(string $Name, string $Value, ?int $ID = null)
    {
        $this->Name  = $Name;
        $this->Value = $Value;
        $this->ID    = $ID;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            'Name'  => $this->Name,
            'Value' => $this->Value,
        ];
        if ($this->ID !== null) {
            $array['ID'] = $this->ID;
        }
        
        return $array;
    }
    
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->Name;
    }
    
    
    /**
     * @param string $Name
     */
    public function setName(string $Name): void
    {
        $this->Name = $Name;
    }
    
    
    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->Value;
    }
    
    
    /**
     * @param string $Value
     */
    public function setValue(string $Value): void
    {
        $this->Value = $Value;
    }
    
    
    /**
     * @return int|null
     */
    public function getID(): ?int
    {
        return $this->ID;
    }
    
    
    /**
     * @param int|null $ID
     */
    public function setID(?int $ID): void
    {
        $this->ID = $ID;
    }
    
    
}