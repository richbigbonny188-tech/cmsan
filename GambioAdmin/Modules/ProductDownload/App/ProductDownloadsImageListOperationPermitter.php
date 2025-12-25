<?php
/*--------------------------------------------------------------
   ProductDownloadsImageListOperationPermitter.php 2023-06-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductDownload\App;

use Gambio\Admin\Modules\ImageList\Model\ImageList;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ImageList\Services\ImageListOperationPermitter;

/**
 * Class ProductDownloadsImageListOperationPermitter
 *
 * @package Gambio\Admin\Modules\ProductDownload\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11.
 */
class ProductDownloadsImageListOperationPermitter implements ImageListOperationPermitter
{
    public function __construct(
        private \Gambio\Admin\Modules\Product\Submodules\Download\App\ProductDownloadsImageListOperationPermitter $permitter
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsCreations(string ...$imageListNames): bool
    {
        return $this->permitter->permitsCreations(...$imageListNames);
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsStorages(ImageList ...$imageList): bool
    {
        return $this->permitter->permitsStorages(...$imageList);
    }
    
    
    /**
     * @inheritDoc
     */
    public function permitsDeletions(ImageListId ...$ids): bool
    {
        return $this->permitter->permitsDeletions(...$ids);
    }
}