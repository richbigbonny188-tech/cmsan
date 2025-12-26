<?php
/**
 * DeleteServiceImageListFactoryInterface.php 2020-1-24
 * Last Modified: 1/24/20, 4:45 PM
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

namespace Gambio\ProductImageList\DeleteService\Interfaces;

use Gambio\ProductImageList\Interfaces\ProductImageListDeleteServiceInterface;

interface ProductImageListDeleteServiceFactoryInterface
{
    /**
     * @return ProductImageListDeleteServiceInterface
     */
    public function createService() : ProductImageListDeleteServiceInterface;
    
}