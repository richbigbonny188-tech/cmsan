<?php
/* --------------------------------------------------------------
   UserConfiguration.php 2021-05-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\UserConfiguration\Model;

use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;
use Gambio\Core\UserConfiguration\Model\Events\UserConfigurationValueUpdated;
use Gambio\Core\UserConfiguration\Model\ValueObjects\UserConfigurationKey;
use Gambio\Core\UserConfiguration\Model\ValueObjects\UserId;

/**
 * Class UserConfiguration
 *
 * @package Gambio\Core\UserConfiguration\Model
 */
class UserConfiguration extends AbstractEventRaisingEntity
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
     * UserConfiguration constructor.
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
     * @return UserConfiguration
     */
    public static function create(UserId $userId, UserConfigurationKey $key, string $value): UserConfiguration
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
    
    
    /**
     * @param string $newValue
     */
    public function changeValue(string $newValue): void
    {
        $this->value = $newValue;
        
        $this->raiseEvent(UserConfigurationValueUpdated::create($this->userId, $this->key, $newValue));
    }
}