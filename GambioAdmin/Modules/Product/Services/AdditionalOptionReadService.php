<?php
/*--------------------------------------------------------------
   AdditionalOptionReadServiceProxy.php 2023-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Services;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptions;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\AdditionalOptionDoesNotExistException;

/**
 * Interface AdditionalOptionReadServiceProxy
 *
 * @package Gambio\Admin\Modules\Product\Services\Proxies
 */
interface AdditionalOptionReadService
{
    /**
     * @param int $productId
     *
     * @return AdditionalOptions
     */
    public function getAdditionalOptionsByProductId(int $productId): AdditionalOptions;
    
    
    /**
     * @param int $additionalOptionId
     *
     * @return AdditionalOption
     *
     * @throws AdditionalOptionDoesNotExistException
     */
    public function getAdditionalOptionById(int $additionalOptionId): AdditionalOption;
}