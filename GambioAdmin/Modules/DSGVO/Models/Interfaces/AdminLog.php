<?php
/* --------------------------------------------------------------
 AdminLog.php 2020-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\DSGVO\Models\Interfaces;

/**
 * Interface AdminLog
 * @package Gambio\Admin\Modules\DSGVO\Models
 */
interface AdminLog
{
    /**
     * @return string
     */
    public function sessionId(): string;
    
    
    /**
     * @return int
     */
    public function customerId(): int;
    
    
    /**
     * @return string
     */
    public function firstName(): string;
    
    
    /**
     * @return string
     */
    public function lastName(): string;
    
    
    /**
     * @return string
     */
    public function email(): string;
}