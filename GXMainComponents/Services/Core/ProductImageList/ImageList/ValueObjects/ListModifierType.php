<?php
/* --------------------------------------------------------------
  ListEntityType.php 2020-02-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\ImageList\ValueObjects;

/**
 * Class ListEntityType
 * @package Gambio\ProductImageList\ImageList\ValueObjects
 */
class ListModifierType
{
    /**
     * @var string
     */
    protected $type;
    
    
    /**
     * ListEntityType constructor.
     *
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->type;
    }
    
}