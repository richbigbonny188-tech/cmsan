<?php
/* --------------------------------------------------------------
   CategoryRepositoryInterface.php 2020-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Services\Interfaces;

use Gambio\Admin\Modules\Configuration\Model\Collections\Categories;

/**
 * Interface CategoryRepositoryInterface
 *
 * @package Gambio\Admin\Modules\Configuration\Services\Interfaces
 */
interface CategoryRepositoryInterface
{
    /**
     * @return Categories
     */
    public function getAllCategories(): Categories;
}