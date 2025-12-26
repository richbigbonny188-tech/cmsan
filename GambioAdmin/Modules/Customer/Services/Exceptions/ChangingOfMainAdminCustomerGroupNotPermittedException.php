<?php
/*--------------------------------------------------------------
   ChangingOfMainAdminCustomerGroupNotPermittedException.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services\Exceptions;

/**
 * Class ChangingOfMainAdminCustomerGroupNotPermittedException
 *
 * @package Gambio\Admin\Modules\Customer\Services\Exceptions
 */
class ChangingOfMainAdminCustomerGroupNotPermittedException extends StorageOfCustomerFailedException
{
    /**
     * @return ChangingOfMainAdminCustomerGroupNotPermittedException
     */
    public static function create(): ChangingOfMainAdminCustomerGroupNotPermittedException
    {
        return new self('The admin with the id "1" can\'t be put in a different customer group');
    }
}