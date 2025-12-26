<?php
/*--------------------------------------------------------------
   ImageLists.php 2021-05-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\ImageList\Model\ImageList;
use IteratorAggregate;
use Traversable;

/**
 * Class ImageLists
 * @package Gambio\Admin\Modules\ImageList\Model\Collections
 * @codeCoverageIgnore
 */
class ImageLists implements IteratorAggregate
{
    /**
     * @var ImageList[]
     */
    private $imageLists;
    
    
    /**
     * Images constructor.
     *
     * @param ImageList[] $imageLists
     */
    private function __construct(array $imageLists)
    {
        $this->imageLists = [];
        
        foreach ($imageLists as $imageList) {
            
            $this->imageLists[$imageList->id()] = $imageList;
        }
    }
    
    
    /**
     * @param ImageList ...$imageLists
     *
     * @return ImageLists
     */
    public static function create(ImageList ...$imageLists): ImageLists
    {
        return new self($imageLists);
    }
    
    /**
     * @return Traversable|ImageList[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->imageLists);
    }
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = array_map(static function (ImageList $list): array {
            return $list->toArray();
        },
            $this->imageLists);
        
        return array_values($data);
    }
}