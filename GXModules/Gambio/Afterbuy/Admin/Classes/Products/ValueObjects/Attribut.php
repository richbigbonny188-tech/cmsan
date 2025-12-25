<?php
/* --------------------------------------------------------------
   Attribut.php 2023-10-18
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
 * Class Attribut
 *
 * A product attribute.
 *
 * Note: The class is intentionally named “Attribut”, not “Attribute”; the spelling reflects the name of the XML tag
 * found in GetShopProducts responses.
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects
 */
class Attribut
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
     * @var int
     */
    private int $Type;
    
    
    /**
     * @var bool
     */
    private bool $Required;
    
    
    /**
     * @var int
     */
    private int $Position;
    
    
    /**
     * @param string $Name
     * @param string $Value
     * @param int    $Type
     * @param bool   $Required
     * @param int    $Position
     */
    public function __construct(string $Name, string $Value, int $Type, bool $Required, int $Position)
    {
        $this->Name     = $Name;
        $this->Value    = $Value;
        $this->Type     = $Type;
        $this->Required = $Required;
        $this->Position = $Position;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'AttributName'     => $this->Name,
            'AttributValue'    => $this->Value,
            'AttributType'     => $this->Type,
            'AttributRequired' => $this->Required ? '-1' : '0', // yeah, like, … dunno.
        ];
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
     * @return int
     */
    public function getType(): int
    {
        return $this->Type;
    }
    
    
    /**
     * @param int $Type
     */
    public function setType(int $Type): void
    {
        $this->Type = $Type;
    }
    
    
    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->Required;
    }
    
    
    /**
     * @param bool $Required
     */
    public function setRequired(bool $Required): void
    {
        $this->Required = $Required;
    }
    
    
    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->Position;
    }
    
    
    /**
     * @param int $Position
     */
    public function setPosition(int $Position): void
    {
        $this->Position = $Position;
    }
    
    
}
