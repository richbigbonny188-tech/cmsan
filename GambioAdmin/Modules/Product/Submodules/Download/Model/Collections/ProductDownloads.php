<?php
/*--------------------------------------------------------------------
 ProductDownloads.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections;

use ArrayIterator;
use Exception;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload;
use IteratorAggregate;
use Traversable;

/**
 * Class ProductDownloads
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections
 */
class ProductDownloads implements IteratorAggregate
{
    /**
     * ProductDownloads constructor.
     *
     * @param ProductDownload[] $downloads
     */
    private function __construct(private array $downloads) { }
    
    
    /**
     * @param ProductDownload ...$productDownloads
     *
     * @return ProductDownloads
     */
    public static function create(ProductDownload ...$productDownloads): ProductDownloads
    {
        return new static($productDownloads);
    }
    
    
    /**
     * @return Traversable|ProductDownload[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->downloads);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $callback = fn(ProductDownload $download): array => $download->toArray();
        
        return array_map($callback, $this->downloads);
    }
}