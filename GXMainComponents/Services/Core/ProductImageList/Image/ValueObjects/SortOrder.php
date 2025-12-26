<?php
/* --------------------------------------------------------------
  SortOrder.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\Image\ValueObjects;

use JsonSerializable;

/**
 * Class SortOrder
 * @package Gambio\ProductImageList\Image\ValueObjects
 */
class SortOrder implements JsonSerializable
{
    /**
     * @var int
     */
    protected $sortOrder;
    
    
    /**
     * SortOrder constructor.
     *
     * @param int $sortOrder
     */
    public function __construct(int $sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->sortOrder;
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