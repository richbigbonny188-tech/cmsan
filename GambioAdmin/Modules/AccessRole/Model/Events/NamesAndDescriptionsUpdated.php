<?php
/* --------------------------------------------------------------
   NamesAndDescriptionsUpdated.php 2020-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Model\Events;

use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleDescriptions;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleId;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleNames;

/**
 * Class NamesAndDescriptionsUpdated
 *
 * @package Gambio\Admin\Modules\AccessRole\Model\Events
 * @codeCoverageIgnore
 */
class NamesAndDescriptionsUpdated
{
    /**
     * @var AccessRoleId
     */
    private $roleId;
    
    /**
     * @var AccessRoleNames
     */
    private $names;
    
    /**
     * @var AccessRoleDescriptions
     */
    private $descriptions;
    
    
    /**
     * NamesAndDescriptionsUpdated constructor.
     *
     * @param AccessRoleId           $roleId
     * @param AccessRoleNames        $names
     * @param AccessRoleDescriptions $descriptions
     */
    private function __construct(
        AccessRoleId $roleId,
        AccessRoleNames $names,
        AccessRoleDescriptions $descriptions
    ) {
        $this->roleId       = $roleId;
        $this->names        = $names;
        $this->descriptions = $descriptions;
    }
    
    
    /**
     * @param AccessRoleId           $roleId
     * @param AccessRoleNames        $names
     * @param AccessRoleDescriptions $descriptions
     *
     * @return NamesAndDescriptionsUpdated
     */
    public static function create(
        AccessRoleId $roleId,
        AccessRoleNames $names,
        AccessRoleDescriptions $descriptions
    ): NamesAndDescriptionsUpdated {
        return new self($roleId, $names, $descriptions);
    }
    
    
    /**
     * @return AccessRoleId
     */
    public function accessRoleId(): AccessRoleId
    {
        return $this->roleId;
    }
    
    
    /**
     * @return AccessRoleNames
     */
    public function names(): AccessRoleNames
    {
        return $this->names;
    }
    
    
    /**
     * @return AccessRoleDescriptions
     */
    public function descriptions(): AccessRoleDescriptions
    {
        return $this->descriptions;
    }
}