<?php
/*--------------------------------------------------------------
   ProductVariantsImageListOperationPermitter.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App;

use Gambio\Admin\Modules\ImageList\Model\ImageList;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ImageList\Services\ImageListOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsReader;

/**
 * Class ProductVariantsImageListOperationPermitter
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App
 */
class ProductVariantsImageListOperationPermitter implements ImageListOperationPermitter
{
    /**
     * ProductVariantsImageListOperationPermitter constructor.
     *
     * @param ProductVariantsReader $reader
     */
    public function __construct(private ProductVariantsReader $reader) { }
    
    
    /**
     * @inheritDoc
     */
    public function permitsCreations(string ...$imageListNames): bool
    {
        return true;
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsStorages(ImageList ...$imageList): bool
    {
        return true;
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsDeletions(ImageListId ...$ids): bool
    {
        $callback = static fn(ImageListId $id): int => $id->value();
        $ids      = array_map($callback, $ids);
        
        return $this->reader->imageListsAreAssignedToAProductVariant(...$ids) === false;
    }
}