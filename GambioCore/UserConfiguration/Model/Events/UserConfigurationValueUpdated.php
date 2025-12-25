<?php
/* --------------------------------------------------------------
   UserConfigurationValueUpdated.php 2021-05-21
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
 * Class UserConfigurationValueUpdated
 *
 * @package Gambio\Core\UserConfiguration\Model\Events
 * @codeCoverageIgnore
 */
class UserConfigurationValueUpdated
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
    private $newValue;
    
    
    /**
     * UserConfigurationValueUpdated constructor.
     *
     * @param UserId               $userId
     * @param UserConfigurationKey $key
     * @param string               $newValue
     */
    private function __construct(UserId $userId, UserConfigurationKey $key, string $newValue)
    {
        $this->userId   = $userId;
        $this->key      = $key;
        $this->newValue = $newValue;
    }
    
    
    /**
     * @param UserId               $userId
     * @param UserConfigurationKey $key
     * @param string               $newValue
     *
     * @return UserConfigurationValueUpdated
     */
    public static function create(
        UserId $userId,
        UserConfigurationKey $key,
        string $newValue
    ): UserConfigurationValueUpdated {
        return new self($userId, $key, $newValue);
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
    public function newValue(): string
    {
        return $this->newValue;
    }
}