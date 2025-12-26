<?php
/* --------------------------------------------------------------
   NamesAndDescriptionsUpdated.php 2020-10-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\Model\Events;

use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupDescriptions;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupId;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupNames;

/**
 * Class NamesAndDescriptionsUpdated
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model\Events
 * @codeCoverageIgnore
 */
class NamesAndDescriptionsUpdated
{
    /**
     * @var AccessGroupId
     */
    private $groupId;
    
    /**
     * @var AccessGroupNames
     */
    private $names;
    
    /**
     * @var AccessGroupDescriptions
     */
    private $descriptions;
    
    
    /**
     * NamesAndDescriptionsUpdated constructor.
     *
     * @param AccessGroupId           $groupId
     * @param AccessGroupNames        $names
     * @param AccessGroupDescriptions $descriptions
     */
    private function __construct(AccessGroupId $groupId, AccessGroupNames $names, AccessGroupDescriptions $descriptions)
    {
        $this->groupId      = $groupId;
        $this->names        = $names;
        $this->descriptions = $descriptions;
    }
    
    
    /**
     * @param AccessGroupId           $groupId
     * @param AccessGroupNames        $names
     * @param AccessGroupDescriptions $descriptions
     *
     * @return NamesAndDescriptionsUpdated
     */
    public static function create(
        AccessGroupId $groupId,
        AccessGroupNames $names,
        AccessGroupDescriptions $descriptions
    ): NamesAndDescriptionsUpdated {
        return new self($groupId, $names, $descriptions);
    }
    
    
    /**
     * @return AccessGroupId
     */
    public function accessGroupId(): AccessGroupId
    {
        return $this->groupId;
    }
    
    
    /**
     * @return AccessGroupNames
     */
    public function names(): AccessGroupNames
    {
        return $this->names;
    }
    
    
    /**
     * @return AccessGroupDescriptions
     */
    public function descriptions(): AccessGroupDescriptions
    {
        return $this->descriptions;
    }
}