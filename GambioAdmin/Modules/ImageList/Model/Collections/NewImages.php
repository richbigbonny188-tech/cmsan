<?php
/*--------------------------------------------------------------
   NewImages.php 2021-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\NewImage;
use IteratorAggregate;
use Traversable;

/**
 * Class NewImages
 * @package Gambio\Admin\Modules\ImageList\Model\Collections
 */
class NewImages implements IteratorAggregate
{
    /**
     * @var NewImage[]
     */
    private $newImages;
    
    
    /**
     * NewImages constructor.
     *
     * @param NewImage[] $newImages
     */
    private function __construct(array $newImages)
    {
        $this->newImages = $newImages;
    }
    
    
    /**
     * @param NewImage ...$newImages
     *
     * @return NewImages
     */
    public static function create(NewImage ...$newImages): NewImages
    {
        return new self($newImages);
    }
    
    
    /**
     * @param NewImage ...$newImages
     *
     * @return NewImages
     */
    public function with(NewImage ...$newImages): NewImages
    {
        return new self(array_merge($this->newImages, $newImages));
    }
    
    
    /**
     * @return Traversable|NewImage[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->newImages);
    }
}