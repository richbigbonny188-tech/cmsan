<?php
/* --------------------------------------------------------------
 FavouritesHashList.php 2020-03-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Models\Cached;

use function in_array;

/**
 * Class FavouritesHashList
 * @package Gambio\Admin\Layout\Menu\Models\Cached
 */
class FavouritesHashList
{
    /**
     * @var array
     */
    private $favourites;
    
    
    /**
     * FavouritesHashList constructor.
     *
     * @param array $favourites
     */
    private function __construct(array $favourites)
    {
        $this->favourites = $favourites;
    }
    
    
    /**
     * Factory method for FavouritesHashList.
     *
     * @param array $favourites
     *
     * @return static
     */
    public static function fromArray(array $favourites): self
    {
        return new static($favourites);
    }
    
    
    /**
     * Checks if given menu item is marked as favourite.
     *
     * @param MenuItem $item
     *
     * @return bool
     */
    public function isFavourite(MenuItem $item): bool
    {
        return in_array($item->id(), $this->favourites, true);
    }
}