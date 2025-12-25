<?php
/* --------------------------------------------------------------
   UserFriendlyErrorPageErrorHandlerService.php 2021-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\UserFriendlyErrorPage\Services;

/**
 * Interface UserFriendlyErrorPageErrorHandlerService
 *
 * @package Gambio\Admin\Modules\UserFriendlyErrorPage\Services
 */
interface UserFriendlyErrorPageErrorHandlerService
{
    /**
     * Sets HTTP header to redirect to the user friendly error page and returns generated error code.
     *
     * @return string
     */
    public function redirectToUserFriendlyErrorPage(): string;
    
    
    /**
     * Returns active state of this module.
     *
     * @return bool
     */
    public function isModuleActive(): bool;
}