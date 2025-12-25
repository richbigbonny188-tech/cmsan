<?php
/* --------------------------------------------------------------
   UserConfigurationCreated.php 2021-05-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\UserConfiguration\Model\Events;

use Gambio\Core\UserConfiguration\Model\ValueObjects\UserConfigurationKey;
use Gambio\Core\UserConfiguration\Model\ValueObjects\UserId;

/**
 * Class UserConfigurationCreated
 *
 * @package Gambio\Core\UserConfiguration\Model\Events
 * @codeCoverageIgnore
 */
class UserConfigurationCreated
{
    /**
     * @var UserId
     */
    private $userId;
    
    /**
     * @var UserConfigurationKey
     */
    private $key;
    
    /**
     * @var string
     */
    private $value;
    
    
    /**
     * UserConfigurationCreated constructor.
     *
     * @param UserId               $userId
     * @param UserConfigurationKey $key
     * @param string               $value
     */
    private function __construct(UserId $userId, UserConfigurationKey $key, string $value)
    {
        $this->userId = $userId;
        $this->key    = $key;
        $this->value  = $value;
    }
    
    
    /**
     * @param UserId               $userId
     * @param UserConfigurationKey $key
     * @param string               $value
     *
     * @return UserConfigurationCreated
     */
    public static function create(UserId $userId, UserConfigurationKey $key, string $value): UserConfigurationCreated
    {
        return new self($userId, $key, $value);
    }
    
    
    /**
     * @return int
     */
    public function userId(): int
    {
        return $this->userId->value();
    }
    
    
    /**
     * @return string
     */
    public function key(): string
    {
        return $this->key->value();
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
}