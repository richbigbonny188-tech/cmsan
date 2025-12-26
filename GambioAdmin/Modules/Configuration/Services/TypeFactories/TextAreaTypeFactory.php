<?php
/* --------------------------------------------------------------
   TextAreaTypeFactory.php 2020-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Services\TypeFactories;

use Gambio\Admin\Modules\Configuration\Model\Entities\Type;

/**
 * Class TextAreaTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class TextAreaTypeFactory implements TypeFactory
{
    /**
     * @param array $params
     *
     * @return Type
     */
    public function createType(array $params): Type
    {
        return Type::create('textarea', $params);
    }
}