<?php
/* --------------------------------------------------------------
   ListingSortDirection.php 2022-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\Model\ValueObjects;

/**
 * Class ListingSortDirection
 *
 * @package Gambio\Shop\Modules\ProductListing\Model\ValueObjects
 */
class ListingSortDirection
{
    /**
     * @var string
     */
    private string $direction;
    
    
    /**
     * ListingSortDirection constructor.
     *
     * @param string $direction
     */
    private function __construct(string $direction)
    {
        $this->direction = $direction;
    }
    
    
    /**
     * Factory method to create a listing direction in ascending order.
     *
     * @return static
     */
    public static function asc(): self
    {
        return new static('asc');
    }
    
    
    /**
     * Factory method to create a listing direction in descending order.
     *
     * @return static
     */
    public static function desc(): self
    {
        return new static('desc');
    }
    
    
    /**
     * @return string
     */
    public function direction(): string
    {
        return $this->direction;
    }
}
