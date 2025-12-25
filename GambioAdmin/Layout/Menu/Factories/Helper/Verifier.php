<?php
/* --------------------------------------------------------------
 Verifier.php 2020-10-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Factories\Helper;

use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Permission\Services\PermissionService;

/**
 * Class Verifier
 * @package Gambio\Admin\Layout\Menu\Permissions
 */
class Verifier
{
    /**
     * @var PermissionService
     */
    private $permissionService;
    
    /**
     * @var Utility
     */
    private $utility;
    
    /**
     * @var UserPreferences
     */
    private $userPreferences;
    
    
    /**
     * Verifier constructor.
     *
     * @param PermissionService $permissionService
     * @param Utility           $utility
     * @param UserPreferences   $userPreferences
     */
    public function __construct(
        PermissionService $permissionService,
        Utility $utility,
        UserPreferences $userPreferences
    ) {
        $this->permissionService = $permissionService;
        $this->utility           = $utility;
        $this->userPreferences   = $userPreferences;
    }
    
    
    /**
     * Checks if a user is allowed to see a menu item.
     *
     * @param string $url
     *
     * @return bool
     */
    public function isAllowed(string $url): bool
    {
        $type       = $this->utility->determineType($url);
        $identifier = $this->utility->determineIdentifier($url);
        
        return $this->permissionService->checkAdminPermission($this->userPreferences->userId(),
                                                              'read',
                                                              $type,
                                                              $identifier);
    }
}