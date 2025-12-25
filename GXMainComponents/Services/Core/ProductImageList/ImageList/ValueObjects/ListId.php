<?php
/* --------------------------------------------------------------
  ListId.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ImageList\ValueObjects;

use JsonSerializable;

/**
 * Class ListId
 * @package Gambio\ProductImageList\ImageList\ValueObjects
 */
class ListId implements JsonSerializable
{
    /**
     * @var int
     */
    protected $listId;
    
    
    /**
     * ListId constructor.
     *
     * @param int $listId
     */
    public function __construct(int $listId)
    {
        $this->listId = $listId;
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->listId;
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