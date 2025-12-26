<?php
/* --------------------------------------------------------------
   CategoryDoesNotExist.php 2020-08-18
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
 * Class CategoryDoesNotExist
 *
 * @package Gambio\Admin\Modules\Configuration\App\Exceptions
 */
class CategoryDoesNotExist extends Exception
{
    /**
     * @param string $id
     *
     * @return CategoryDoesNotExist
     */
    public static function withId(string $id): CategoryDoesNotExist
    {
        return new self('Category with ID "' . $id . '" does not exist.');
    }
}