<?php
/* --------------------------------------------------------------
   TaxCalculationModeTypeFactory.php 2021-05-28
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
 * Class TaxCalculationModeTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class TaxCalculationModeTypeFactory implements TypeFactory
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
                'value' => 'None',
                'text'  => 'None',
            ],
            [
                'value' => 'Standard',
                'text'  => 'Standard',
            ],
            [
                'value' => 'Credit Note',
                'text'  => 'Credit Note',
            ],
        ];
        
        return Type::create('dropdown', $params);
    }
}