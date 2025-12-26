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

namespace Gambio\Admin\Modules\Product\App;

use Gambio\Admin\Modules\Product\Services\AdditionalOptionReadService as AdditionalOptionReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptions;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services as Submodule;

/**
 * Class AdditionalOptionReadServiceProxy
 *
 * @package Gambio\Admin\Modules\Product\App\Proxies
 */
class AdditionalOptionReadService implements AdditionalOptionReadServiceInterface
{
    public function __construct(private Submodule\AdditionalOptionReadService $submoduleReadService) { }
    
    
    /**
     * @inheritDoc
     */
    public function getAdditionalOptionsByProductId(int $productId): AdditionalOptions
    {
        return $this->submoduleReadService->getAdditionalOptionsByProductId($productId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdditionalOptionById(int $additionalOptionId): AdditionalOption
    {
        return $this->submoduleReadService->getAdditionalOptionById($additionalOptionId);
    }
}