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

namespace Gambio\Admin\Modules\DSGVO\Models\Entities;

use Gambio\Admin\Modules\DSGVO\Models\Interfaces\AdminLog as Log;

/**
 * Class AdminLog
 * @package Gambio\Admin\Modules\DSGVO\Models\Entities
 */
class AdminLog implements Log
{
    /**
     * @var string
     */
    private $sessionId;
    
    /**
     * @var int
     */
    private $customerId;
    
    /**
     * @var string
     */
    private $firstname;
    
    /**
     * @var string
     */
    private $lastname;
    
    /**
     * @var string
     */
    private $email;
    
    
    /**
     * AdminLog constructor.
     *
     * @param string $sessionId
     * @param int    $customerId
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     */
    public function __construct(string $sessionId, int $customerId, string $firstname, string $lastname, string $email)
    {
        $this->sessionId  = $sessionId;
        $this->customerId = $customerId;
        $this->firstname  = $firstname;
        $this->lastname   = $lastname;
        $this->email      = $email;
    }
    
    
    /**
     * @inheritDoc
     */
    public function sessionId(): string
    {
        return $this->sessionId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function customerId(): int
    {
        return $this->customerId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function firstName(): string
    {
        return $this->firstname;
    }
    
    
    /**
     * @inheritDoc
     */
    public function lastName(): string
    {
        return $this->lastname;
    }
    
    
    /**
     * @inheritDoc
     */
    public function email(): string
    {
        return $this->email;
    }
}