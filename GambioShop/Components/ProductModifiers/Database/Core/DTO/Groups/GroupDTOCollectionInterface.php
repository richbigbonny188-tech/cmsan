<?php
/*--------------------------------------------------------------------------------------------------
    GroupDTOCollectionInterface.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups;

use Countable;
use Gambio\Shop\ProductModifiers\Groups\ValueObjects\GroupIdentifierInterface;
use IteratorAggregate;
use JsonSerializable;

/**
 * Interface GroupDTOCollectionInterface
 * @package Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups
 */
interface GroupDTOCollectionInterface extends IteratorAggregate, Countable, JsonSerializable
{
    /**
     * @param GroupIdentifierInterface $id
     *
     * @return mixed
     */
    public function getById(GroupIdentifierInterface $id): GroupDTO;
    
}