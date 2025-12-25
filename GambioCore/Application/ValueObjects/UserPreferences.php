<?php
/* --------------------------------------------------------------
 UserPreferences.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\ValueObjects;

/**
 * Class UserPreferences
 *
 * @package Gambio\Core\Application\ValueObjects
 */
class UserPreferences
{
    /**
     * @var int|null
     */
    private $userId;
    
    /**
     * @var int
     */
    private $languageId;
    
    
    /**
     * UserPreferences constructor.
     *
     * @param int|null $customerId
     * @param int      $languageId
     */
    public function __construct(?int $customerId, int $languageId)
    {
        $this->userId     = $customerId;
        $this->languageId = $languageId;
    }
    
    
    /**
     * The customer id is maybe not set.
     * If authenticated returns true, customerId must return a value.
     *
     * @return int|null
     */
    public function userId(): ?int
    {
        return $this->userId;
    }
    
    
    /**
     * Checks if current user is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return null !== $this->userId;
    }
    
    
    /**
     * Returns a language id of the shop system.
     *
     * @return int
     */
    public function languageId(): int
    {
        return $this->languageId;
    }
}