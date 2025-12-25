<?php
/* --------------------------------------------------------------
   AdminReadService.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Admin\Services;

use Gambio\Admin\Modules\Admin\Model\Admin;
use Gambio\Admin\Modules\Admin\Model\Collections\Admins;
use Gambio\Admin\Modules\Admin\Services\Exceptions\AdminDoesNotExistException;

/**
 * Interface AdminReadService
 *
 * @package Gambio\Admin\Modules\Admin\Services
 */
interface AdminReadService
{
    /**
     * Returns all available admins.
     *
     * @return Admins
     */
    public function getAdmins(): Admins;
    
    
    /**
     * Returns a specific admin based on the provided admin ID.
     *
     * @param int $id
     *
     * @return Admin
     *
     * @throws AdminDoesNotExistException
     */
    public function getAdminById(int $id): Admin;
}