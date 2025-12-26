<?php
/* --------------------------------------------------------------
   ShippingDestinationTypeFactory.php 2021-05-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Services\TypeFactories;

use Gambio\Admin\Modules\Configuration\Model\Entities\Type;

/**
 * Class ShippingDestinationTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class ShippingDestinationTypeFactory implements TypeFactory
{
    /**
     * @param array $params
     *
     * @return Type
     */
    public function createType(array $params): Type
    {
        $params['items'] = [
            [
                'value' => 'national',
                'text'  => 'national',
            ],
            [
                'value' => 'international',
                'text'  => 'international',
            ],
            [
                'value' => 'both',
                'text'  => 'both',
            ],
        ];
        
        return Type::create('dropdown', $params);
    }
}