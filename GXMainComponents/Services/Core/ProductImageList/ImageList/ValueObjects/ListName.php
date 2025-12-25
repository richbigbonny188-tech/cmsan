<?php
/* --------------------------------------------------------------
  ListName.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ImageList\ValueObjects;

use JsonSerializable;

/**
 * Class ListName
 * @package Gambio\ProductImageList\ImageList\ValueObjects
 */
class ListName implements JsonSerializable
{
    /**
     * @var string
     */
    protected $name;
    
    
    /**
     * ListName constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->name;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->value();
    }
}