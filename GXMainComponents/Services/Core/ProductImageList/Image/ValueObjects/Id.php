<?php
/* --------------------------------------------------------------
  Id.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\Image\ValueObjects;

use JsonSerializable;

/**
 * Class Id
 * @package Gambio\ProductImageList\Image\ValueObjects
 */
class Id implements JsonSerializable
{
    /**
     * @var int
     */
    protected $id;
    
    
    /**
     * Id constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->id;
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