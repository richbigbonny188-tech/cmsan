<?php
/* --------------------------------------------------------------
   AfterbuyCatalog.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Catalogs\ValueObjects;

/**
 * Class AfterbuyCatalog
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Catalogs\ValueObjects
 */
class AfterbuyCatalog
{
    /**
     * @var int
     */
    private int $CatalogID;
    
    
    /**
     * @var int
     */
    private int $ParentID;
    
    
    /**
     * @var string
     */
    private string $Name;
    
    
    /**
     * @var string
     */
    private string $Description;
    
    
    /**
     * @var int
     */
    private int $Level;
    
    
    /**
     * @var int
     */
    private int $Position;
    
    
    /**
     * @var string
     */
    private string $AdditionalText;
    
    
    /**
     * @var bool
     */
    private bool $Show;
    
    
    /**
     * @var string
     */
    private string $Picture1;
    
    
    /**
     * @var string
     */
    private string $Picture2;
    
    
    /**
     * @var string
     */
    private string $TitlePicture;
    
    
    /**
     * @var array
     */
    private array $CatalogProducts = [];
    
    
    /**
     * @param int    $CatalogID
     * @param int    $ParentID
     * @param string $Name
     * @param string $Description
     * @param int    $Level
     * @param int    $Position
     * @param string $AdditionalText
     * @param bool   $Show
     * @param string $Picture1
     * @param string $Picture2
     * @param string $TitlePicture
     */
    public function __construct(
        int    $CatalogID,
        int    $ParentID,
        string $Name = '',
        string $Description = '',
        int    $Level = 0,
        int    $Position = 0,
        string $AdditionalText = '',
        bool   $Show = true,
        string $Picture1 = '',
        string $Picture2 = '',
        string $TitlePicture = ''
    ) {
        $this->CatalogID      = $CatalogID;
        $this->ParentID       = $ParentID;
        $this->Name           = $Name;
        $this->Description    = $Description;
        $this->Level          = $Level;
        $this->Position       = $Position;
        $this->AdditionalText = $AdditionalText;
        $this->Show           = $Show;
        $this->Picture1       = $Picture1;
        $this->Picture2       = $Picture2;
        $this->TitlePicture   = $TitlePicture;
    }
    
    
    /**
     * @param int $productID
     *
     * @return void
     */
    public function addProductID(int $productID): void
    {
        $this->CatalogProducts[] = $productID;
    }
    
    
    /**
     * @return array
     */
    public function getCatalogProducts(): array
    {
        return $this->CatalogProducts;
    }
    
    
    /**
     * @return int
     */
    public function getCatalogID(): int
    {
        return $this->CatalogID;
    }
    
    
    /**
     * @param int $CatalogID
     */
    public function setCatalogID(int $CatalogID): void
    {
        $this->CatalogID = $CatalogID;
    }
    
    
    /**
     * @return int
     */
    public function getParentID(): int
    {
        return $this->ParentID;
    }
    
    
    /**
     * @param int $ParentID
     */
    public function setParentID(int $ParentID): void
    {
        $this->ParentID = $ParentID;
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
    public function getDescription(): string
    {
        return $this->Description;
    }
    
    
    /**
     * @param string $Description
     */
    public function setDescription(string $Description): void
    {
        $this->Description = $Description;
    }
    
    
    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->Level;
    }
    
    
    /**
     * @param int $Level
     */
    public function setLevel(int $Level): void
    {
        $this->Level = $Level;
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
    
    
    /**
     * @return string
     */
    public function getAdditionalText(): string
    {
        return $this->AdditionalText;
    }
    
    
    /**
     * @param string $AdditionalText
     */
    public function setAdditionalText(string $AdditionalText): void
    {
        $this->AdditionalText = $AdditionalText;
    }
    
    
    /**
     * @return bool
     */
    public function isShow(): bool
    {
        return $this->Show;
    }
    
    
    /**
     * @param bool $Show
     */
    public function setShow(bool $Show): void
    {
        $this->Show = $Show;
    }
    
    
    /**
     * @return string
     */
    public function getPicture1(): string
    {
        return $this->Picture1;
    }
    
    
    /**
     * @param string $Picture1
     */
    public function setPicture1(string $Picture1): void
    {
        $this->Picture1 = $Picture1;
    }
    
    
    /**
     * @return string
     */
    public function getPicture2(): string
    {
        return $this->Picture2;
    }
    
    
    /**
     * @param string $Picture2
     */
    public function setPicture2(string $Picture2): void
    {
        $this->Picture2 = $Picture2;
    }
    
    
    /**
     * @return string
     */
    public function getTitlePicture(): string
    {
        return $this->TitlePicture;
    }
    
    
    /**
     * @param string $TitlePicture
     */
    public function setTitlePicture(string $TitlePicture): void
    {
        $this->TitlePicture = $TitlePicture;
    }
    
}