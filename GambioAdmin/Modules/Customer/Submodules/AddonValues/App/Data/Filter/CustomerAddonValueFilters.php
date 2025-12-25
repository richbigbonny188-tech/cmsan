<?php
/* --------------------------------------------------------------
   CustomerAddonValueFilters.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data\Filter;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Filter\CustomerAddonValueFilters as CustomerAddonValueFiltersInterface;
use Gambio\Core\Filter\SqlFilters;

/**
 * Class CustomerAddonValueFilters
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\App\Data\Filter
 * @codeCoverageIgnore
 */
class CustomerAddonValueFilters extends SqlFilters implements CustomerAddonValueFiltersInterface
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
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForLikeOperation(): array
    {
        return [
            'key',
            'value',
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForNumericOperations(): array
    {
        return [];
    }
}