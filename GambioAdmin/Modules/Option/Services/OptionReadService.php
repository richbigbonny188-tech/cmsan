<?php
/*--------------------------------------------------------------
   OptionReadService.php 2020-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Option\Services;

use Gambio\Admin\Modules\Option\Model\Collections\Options;
use Gambio\Admin\Modules\Option\Model\Option;
use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;

/**
 * Interface OptionReadService
 *
 * @package Gambio\Admin\Modules\Option\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains will be refactored into
 *             submodules too. All important changes will be documented in the developer journal as soon as they are
 *             implemented.
 */
interface OptionReadService
{
    /**
     * Returns a specific option based on the given option ID.
     *
     * @param int $optionId
     *
     * @return Option
     *
     * @throws OptionDoesNotExistException
     */
    public function getOptionById(int $optionId): Option;
    
    
    /**
     * Returns a collection of all options.
     *
     * @return Options
     */
    public function getAllOptions(): Options;
}