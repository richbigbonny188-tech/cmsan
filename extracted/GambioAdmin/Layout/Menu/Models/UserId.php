<?php
/* --------------------------------------------------------------
 UserId.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Models;

use Gambio\Core\Application\ValueObjects\UserPreferences;
use UnexpectedValueException;

/**
 * Class UserId
 * @package Gambio\Admin\Layout\Menu\Models
 */
class UserId
{
    /**
     * @var UserPreferences
     */
    private $userPreferences;
    
    
    /**
     * UserId constructor.
     *
     * @param UserPreferences $userPreferences
     */
    public function __construct(UserPreferences $userPreferences)
    {
        $this->userPreferences = $userPreferences;
    }
    
    
    /**
     * @return int
     */
    public function userId(): int
    {
        $userId = $this->userPreferences->userId();
        if (!$userId) {
            throw new UnexpectedValueException('User id was not found');
        }
        
        return $userId;
    }
}