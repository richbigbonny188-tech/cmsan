<?php
/*--------------------------------------------------------------------------------------------------
    GroupMapperInterface.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\Mappers\Interfaces;

use Gambio\Shop\ProductModifiers\Database\Core\DTO\Groups\GroupDTO;
use Gambio\Shop\ProductModifiers\Groups\GroupInterface;

interface GroupMapperInterface
{
    /**
     * @param GroupDTO $dto
     *
     * @return GroupInterface
     */
    public function mapGroup(GroupDTO $dto): GroupInterface;
    
    
    /**
     * @param GroupMapperInterface $next
     */
    public function setNext(GroupMapperInterface $next): void;
    
}