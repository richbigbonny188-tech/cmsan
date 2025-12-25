<?php
/* --------------------------------------------------------------
  ListEntityId.php 2020-02-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\ImageList\ValueObjects;

/**
 * Class ListEntityId
 * @package Gambio\ProductImageList\ImageList\ValueObjects
 */
class ListModifierId
{
    /**
     * @var int
     */
    protected $id;
    
    
    /**
     * ListEntityId constructor.
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
    
}