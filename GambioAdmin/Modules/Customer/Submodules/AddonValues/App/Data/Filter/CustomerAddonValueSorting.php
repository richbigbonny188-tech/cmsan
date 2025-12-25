<?php
/* --------------------------------------------------------------
   CustomerAddonValueSorting.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data\Filter;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Filter\CustomerAddonValueSorting as CustomerAddonValueSortingInterface;
use Gambio\Core\Filter\SqlSorting;

/**
 * Class CustomerAddonValueSorting
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\App\Data\Filter
 * @codeCoverageIgnore
 */
class CustomerAddonValueSorting extends SqlSorting implements CustomerAddonValueSortingInterface
{
    /**
     * @inheritDoc
     */
    public static function attributeColumnMapping(): array
    {
        return [
            'key'   => 'addon_key',
            'value' => 'addon_value',
        ];
    }
}