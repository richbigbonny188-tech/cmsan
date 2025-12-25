<?php
/*--------------------------------------------------------------
   ImageTitles.php 2021-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageTitle;
use IteratorAggregate;
use Traversable;

/**
 * Class ImageTitles
 * @package Gambio\Admin\Modules\ImageList\Model\Collections
 * @codeCoverageIgnore
 */
class ImageTitles implements IteratorAggregate
{
    /**
     * @var ImageTitle[]
     */
    private $titles;
    
    
    /**
     * ImageTitles constructor.
     *
     * @param ImageTitle[] $titles
     */
    public function __construct(array $titles)
    {
        $this->titles = $titles;
    }
    
    
    /**
     * @param ImageTitle ...$titles
     *
     * @return ImageTitles
     */
    public static function create(ImageTitle ...$titles): ImageTitles
    {
        return new self($titles);
    }
    
    
    /**
     * @return Traversable|ImageTitle[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->titles);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (ImageTitle $title): array {
            return $title->toArray();
        },
            $this->titles);
    }
}