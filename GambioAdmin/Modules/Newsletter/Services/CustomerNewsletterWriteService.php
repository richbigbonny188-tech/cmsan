<?php
/*--------------------------------------------------------------
   CustomerNewsletterWriteService.php 2022-11-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\Services;

/**
 * Interface CustomerNewsletterWriteService
 *
 * @package Gambio\Admin\Modules\Newsletter\Services
 */
interface CustomerNewsletterWriteService
{
    /**
     * Subscribes the given customer from newsletters.
     *
     * @param int $customerId
     * @param int $adminId
     *
     * @return void
     */
    public function subscribe(int $customerId, int $adminId): void;
    
    
    /**
     * Unsubscribes the given customer from newsletters.
     *
     * @param int $customerId
     *
     * @return void
     */
    public function unsubscribe(int $customerId): void;
}