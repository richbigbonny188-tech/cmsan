<?php
/*--------------------------------------------------------------
   AdditionalProductFieldReadService.php 2021-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\Services;

use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections\AdditionalProductFields;

/**
 * Interface AdditionalProductFieldReadService
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\Services
 */
interface AdditionalProductFieldReadService
{
    /**
     * @param int $productId
     *
     * @return AdditionalProductFields
     */
    public function getAdditionalProductFields(int $productId): AdditionalProductFields;
}