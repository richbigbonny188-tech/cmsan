<?php
/* --------------------------------------------------------------
   MultiLanguage.php 2023-10-18
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
 * Class MultiLanguage
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects
 */
class MultiLanguage
{
    /**
     * @var string
     */
    private string $Name;
    
    /**
     * @var string|null
     */
    private ?string $Alias = null;
    
    /**
     * @var string|null
     */
    private ?string $Beschreibung = null;
    
    /**
     * @var string|null
     */
    private ?string $Kurzbeschreibung = null;
    
    /**
     * @var string|null
     */
    private ?string $ust = null;
    
    /**
     * @var string|null
     */
    private ?string $Variationsname = null;
    
    
    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->Name = $name;
    }
    
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->Name;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            'Name'             => $this->Name,
            'Alias'            => $this->Alias,
            'Beschreibung'     => $this->Beschreibung,
            'Kurzbeschreibung' => $this->Kurzbeschreibung,
            'ust'              => $this->ust,
            'Variationsname'   => $this->Variationsname,
        ];
        
        return array_filter($array, static fn($x) => !empty($x));
    }
    
    
    /**
     * @param string $Name
     */
    public function setName(string $Name): void
    {
        $this->Name = $Name;
    }
    
    
    /**
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->Alias;
    }
    
    
    /**
     * @param string|null $Alias
     */
    public function setAlias(?string $Alias): void
    {
        $this->Alias = $Alias;
    }
    
    
    /**
     * @return string|null
     */
    public function getBeschreibung(): ?string
    {
        return $this->Beschreibung;
    }
    
    
    /**
     * @param string|null $Beschreibung
     */
    public function setBeschreibung(?string $Beschreibung): void
    {
        $this->Beschreibung = $Beschreibung;
    }
    
    
    /**
     * @return string|null
     */
    public function getKurzbeschreibung(): ?string
    {
        return $this->Kurzbeschreibung;
    }
    
    
    /**
     * @param string|null $Kurzbeschreibung
     */
    public function setKurzbeschreibung(?string $Kurzbeschreibung): void
    {
        $this->Kurzbeschreibung = $Kurzbeschreibung;
    }
    
    
    /**
     * @return string|null
     */
    public function getUst(): ?string
    {
        return $this->ust;
    }
    
    
    /**
     * @param string|null $ust
     */
    public function setUst(?string $ust): void
    {
        $this->ust = $ust;
    }
    
    
    /**
     * @return string|null
     */
    public function getVariationsname(): ?string
    {
        return $this->Variationsname;
    }
    
    
    /**
     * @param string|null $Variationsname
     */
    public function setVariationsname(?string $Variationsname): void
    {
        $this->Variationsname = $Variationsname;
    }
    
}