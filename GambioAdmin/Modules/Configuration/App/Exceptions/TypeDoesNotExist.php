<?php
/* --------------------------------------------------------------
   TypeDoesNotExist.php 2020-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Exceptions;

use Exception;

/**
 * Class TypeDoesNotExist
 *
 * @package Gambio\Admin\Modules\Configuration\App\Exceptions
 */
class TypeDoesNotExist extends Exception
{
    /**
     * @param string $id
     *
     * @return TypeDoesNotExist
     */
    public static function withId(string $id): TypeDoesNotExist
    {
        return new self('Type with ID "' . $id . '" does not exist.');
    }
}