<?php
/*--------------------------------------------------------------
   DeletionOfMainAdminNotPermittedException.php 2022-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services\Exceptions;

use Exception;

/**
 * Class DeletionOfMainAdminNotPermittedException
 *
 * @package Gambio\Admin\Modules\Customer\Services\Exceptions
 */
class DeletionOfMainAdminNotPermittedException extends Exception
{
    /**
     * @return DeletionOfMainAdminNotPermittedException
     */
    public static function create(): DeletionOfMainAdminNotPermittedException
    {
        return new self('The admin with the id "1" can\'t be deleted');
    }
}