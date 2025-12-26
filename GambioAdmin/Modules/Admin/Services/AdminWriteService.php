<?php
/* --------------------------------------------------------------
   AdminWriteService.php 2021-05-14
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
use Gambio\Admin\Modules\Admin\Model\Collections\AdminIds;
use Gambio\Admin\Modules\Admin\Services\Exceptions\StorageOfAdminsFailedException;

/**
 * Interface AdminWriteService
 *
 * @package Gambio\Admin\Modules\Admin\Services
 */
interface AdminWriteService
{
    /**
     * Stores (creates or updates) provided admins and return their IDs.
     *
     * @param Admin ...$admins
     *
     * @return AdminIds
     *
     * @throws StorageOfAdminsFailedException
     */
    public function storeAdmins(Admin ...$admins): AdminIds;
}