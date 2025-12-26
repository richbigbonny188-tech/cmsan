<?php

/* --------------------------------------------------------------
   AdminAccessGroupItem.inc.php 2020-03-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessGroupItem
 *
 * A Group is a collection pages/controllers,
 * that represent a semantic unit for the administration of our shop system.
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Entities
 */
class AdminAccessGroupItem implements AdminAccessGroupItemInterface
{
    /**
     * @var array
     */
    protected $allowedTypes;
    
    /**
     * @var int
     */
    protected $groupId;
    
    /**
     * @var string
     */
    protected $identifier;
    
    /**
     * @var string
     */
    protected $type;
    
    
    /**
     * Returns the group id.
     *
     * @return int Group id.
     */
    public function __construct(IntType $groupId, NonEmptyStringType $identifier, NonEmptyStringType $type)
    {
        $this->allowedTypes = ['PAGE', 'CONTROLLER', 'AJAX_HANDLER', 'ROUTE'];
        if (!in_array(strtoupper($type->asString()), $this->allowedTypes)) {
            throw new InvalidArgumentException('Item type must be one of the following: ' . implode(', ',
                                                                                                    $this->allowedTypes));
        }
        
        $this->groupId    = $groupId->asInt();
        $this->identifier = $identifier->asString();
        $this->type       = strtoupper($type->asString());
    }
    
    
    /**
     * Returns the group id.
     *
     * @return int Group id.
     */
    public function getGroupId()
    {
        return $this->groupId;
    }
    
    
    /**
     * Returns the group item identifier.
     *
     * @return string Group item identifier.
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    
    /**
     * Returns the group item type.
     *
     * @return string Group item type.
     */
    public function getType()
    {
        return $this->type;
    }
    
    
    /**
     * Sets the group id.
     *
     * @param int Group id.
     *
     * @return AdminAccessGroupItemInterface
     */
    public function setGroupId(IntType $value)
    {
        $this->groupId = $value->asInt();
        
        return $this;
    }
    
    
    /**
     * Sets the group item identifier.
     *
     * @param NonEmptyStringType Group item identifier.
     *
     * @return AdminAccessGroupItemInterface
     */
    public function setIdentifier(NonEmptyStringType $value)
    {
        $this->identifier = $value->asString();
        
        return $this;
    }
    
    
    /**
     * Sets the group item type.
     *
     * @param NonEmptyStringType Group item type.
     *
     * @return AdminAccessGroupItemInterface
     */
    public function setType(NonEmptyStringType $value)
    {
        $this->allowedTypes = ['PAGE', 'CONTROLLER', 'AJAX_HANDLER', 'ROUTE'];
        if (!in_array(strtoupper($value->asString()), $this->allowedTypes)) {
            throw new InvalidArgumentException('Item type must be one of the following: ' . implode(', ',
                                                                                                    $this->allowedTypes));
        }
        
        $this->type = strtoupper($value->asString());
        
        return $this;
    }
}
