<?php
/*--------------------------------------------------------------
   ImageAltTitles.php 2021-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageAltTitle;
use IteratorAggregate;
use Traversable;

/**
 * Class ImageAltTitles
 * @package Gambio\Admin\Modules\ImageList\Model\Collections
 * @codeCoverageIgnore
 */
class ImageAltTitles implements IteratorAggregate
{
    /**
     * @var ImageAltTitle[]
     */
    private $titles;
    
    
    /**
     * ImageAltTitles constructor.
     *
     * @param ImageAltTitle[] $titles
     */
    private function __construct(array $titles)
    {
        $this->titles = $titles;
    }
    
    
    /**
     * @param ImageAltTitle ...$titles
     *
     * @return ImageAltTitles
     */
    public static function create(ImageAltTitle ...$titles): ImageAltTitles
    {
        return new self($titles);
    }
    
    
    /**
     * @return Traversable|ImageAltTitle[]
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
        return array_map(static function (ImageAltTitle $title): array {
            return $title->toArray();
        },
            $this->titles);
    }
}