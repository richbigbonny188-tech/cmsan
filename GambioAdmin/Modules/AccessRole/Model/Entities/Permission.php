<?php
/* --------------------------------------------------------------
   Permission.php 2020-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Model\Entities;

use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\GroupId;

/**
 * Class Permission
 *
 * @package Gambio\Admin\Modules\AccessRole\Model\Entities
 */
class Permission
{
    /**
     * @var GroupId
     */
    private $groupId;
    
    /**
     * @var bool
     */
    private $readingGranted;
    
    /**
     * @var bool
     */
    private $writingGranted;
    
    /**
     * @var bool
     */
    private $deletingGranted;
    
    
    /**
     * Permission constructor.
     *
     * @param GroupId $groupId
     * @param bool    $readingGranted
     * @param bool    $writingGranted
     * @param bool    $deletingGranted
     */
    private function __construct(GroupId $groupId, bool $readingGranted, bool $writingGranted, bool $deletingGranted)
    {
        $this->groupId         = $groupId;
        $this->readingGranted  = $readingGranted;
        $this->writingGranted  = $writingGranted;
        $this->deletingGranted = $deletingGranted;
    }
    
    
    /**
     * @param GroupId $groupId
     * @param bool    $readingGranted
     * @param bool    $writingGranted
     * @param bool    $deletingGranted
     *
     * @return Permission
     */
    public static function create(
        GroupId $groupId,
        bool $readingGranted,
        bool $writingGranted,
        bool $deletingGranted
    ): Permission {
        return new self($groupId, $readingGranted, $writingGranted, $deletingGranted);
    }
    
    
    /**
     * @return int
     */
    public function groupId(): int
    {
        return $this->groupId->value();
    }
    
    
    /**
     * @return bool
     */
    public function readingGranted(): bool
    {
        return $this->readingGranted;
    }
    
    
    /**
     * @return bool
     */
    public function writingGranted(): bool
    {
        return $this->writingGranted;
    }
    
    
    /**
     * @return bool
     */
    public function deletingGranted(): bool
    {
        return $this->deletingGranted;
    }
}