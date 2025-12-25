<?php
/*--------------------------------------------------------------------------------------------------
    GroupCollectionInterface.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Groups\Collections;

use Countable;
use Gambio\Shop\ProductModifiers\Groups\GroupInterface;
use Gambio\Shop\ProductModifiers\Groups\ValueObjects\GroupIdentifierInterface;
use IteratorAggregate;
use JsonSerializable;

/**
 * Interface GroupCollectionInterface
 * @package Gambio\Shop\ProductModifiers\Groups\Collections
 */
interface GroupCollectionInterface extends IteratorAggregate, Countable, JsonSerializable
{
    /**
     * @param GroupInterface $group
     *
     * @return mixed
     */
    public function addGroup(GroupInterface $group);
    
    
    /**
     * @param GroupCollectionInterface $groups
     *
     * @return mixed
     */
    public function addGroups(GroupCollectionInterface $groups);
    
    
    /**
     * @param int $index
     *
     * @return mixed
     */
    public function get(int $index): GroupInterface;
    
    
    /**
     * @param GroupIdentifierInterface $id
     *
     * @return GroupInterface
     */
    public function getById(GroupIdentifierInterface $id): GroupInterface;
}