<?php
/*--------------------------------------------------------------
   ItemDetails.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Model\ValueObjects;

class ItemDetails
{
    /**
     * @var string
     */
    private string $itemNumber;
    
    /**
     * @var string
     */
    private string $title;
    
    /**
     * @var string
     */
    private string $image;
    
    
    /**
     * Constructor
     */
    private function __construct(string $itemNumber, string $title, string $image)
    {
        $this->itemNumber = $itemNumber;
        $this->title      = $title;
        $this->image      = $image;
    }
    
    
    /**
     * Creates a new instance if ItemDetails
     */
    public static function create(string $itemNumber, string $title, string $image): self
    {
        return new self($itemNumber, $title, $image);
    }
    
    
    /**
     * Returns the Item Number
     */
    public function itemNumber(): string
    {
        return $this->itemNumber;
    }
    
    
    /**
     * Returns the Title
     */
    public function title(): string
    {
        return $this->title;
    }
    
    
    /**
     * Returns the image URL
     */
    public function image(): string
    {
        return $this->image;
    }
    
    
    /**
     * Returns an array containing the class members
     */
    public function toArray(): array
    {
        return [
            "itemNumber" => $this->itemNumber,
            "title"      => $this->title,
            "image"      => $this->image
        ];
    }
}