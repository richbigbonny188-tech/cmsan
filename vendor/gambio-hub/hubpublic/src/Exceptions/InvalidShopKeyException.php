<?php
/* --------------------------------------------------------------
   InvalidShopKeyException.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Exceptions;

/**
 * Class InvalidShopKeyException
 *
 * Use this exception i.e. if the provided shop key is invalid.
 *
 * @package HubPublic\Exceptions
 */
class InvalidShopKeyException extends HubNoticeException
{
}
