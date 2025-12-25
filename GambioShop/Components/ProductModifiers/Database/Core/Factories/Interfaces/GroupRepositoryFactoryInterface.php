<?php
/*--------------------------------------------------------------------------------------------------
    GroupRepositoryFactoryInterface.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\Factories\Interfaces;

use Gambio\Shop\ProductModifiers\Groups\Repositories\GroupRepositoryInterface;

/**
 * Interface GroupRepositoryFactoryInterface
 * @package Gambio\Shop\ProductModifiers\Database\Core\Factories\Interfaces
 */
interface GroupRepositoryFactoryInterface
{
    /**
     * @return GroupRepositoryInterface
     */
    public function createRepository(): GroupRepositoryInterface;
    
    
}