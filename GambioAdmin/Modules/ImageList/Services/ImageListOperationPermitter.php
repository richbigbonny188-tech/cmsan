<?php
/*--------------------------------------------------------------
   ImageListOperationPermitter.php 2021-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Services;

use Gambio\Admin\Modules\ImageList\Model\ImageList;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageListId;

/**
 * Interface ImageListOperationPermitter
 * @package Gambio\Admin\Modules\ImageList\Services
 */
interface ImageListOperationPermitter
{
    /**
     * Checks the permission of the create operation.
     *
     * @param string ...$imageListNames
     *
     * @return bool
     */
    public function permitsCreations(string ...$imageListNames): bool;
    
    /**
     * Checks the permission of the store operation.
     *
     * @param ImageList ...$imageList
     *
     * @return bool
     */
    public function permitsStorages(ImageList ...$imageList): bool;
    
    /**
     * Checks the permission of the delete operation.
     *
     * @param ImageListId ...$ids
     *
     * @return bool
     */
    public function permitsDeletions(ImageListId ...$ids): bool;
}