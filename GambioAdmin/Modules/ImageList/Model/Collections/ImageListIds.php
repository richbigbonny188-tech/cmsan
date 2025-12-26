<?php
/*--------------------------------------------------------------
   ImageListIds.php 2021-05-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use IteratorAggregate;
use Traversable;

/**
 * Class ImageListIds
 * @package Gambio\Admin\Modules\ImageList\Model\Collections
 * @codeCoverageIgnore
 */
class ImageListIds implements IteratorAggregate
{
    
    /**
     * @var ImageListId[]
     */
    private $imageListIds;
    
    
    /**
     * ImageListIds constructor.
     *
     * @param ImageListId[] $imageListIds
     */
    private function __construct(array $imageListIds)
    {
        $this->imageListIds = [];
        
        foreach ($imageListIds as $imageListId) {
            
            $this->imageListIds[$imageListId->value()] = $imageListId;
        }
    }
    
    
    /**
     * @param ImageListId ...$imageListIds
     *
     * @return ImageListIds
     */
    public static function create(ImageListId ...$imageListIds): ImageListIds
    {
        return new self($imageListIds);
    }
    
    
    /**
     * @return Traversable|ImageListId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->imageListIds);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $imageIds = array_map(static function (ImageListId $id): int {
            return $id->value();
        },
            $this->imageListIds);
    
        return array_values($imageIds);
    }
}