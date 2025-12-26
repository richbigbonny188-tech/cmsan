<?php
/* --------------------------------------------------------------
   InvalidAuthHashException.php 2017-07-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Exceptions;

/**
 * Class InvalidAuthHashException
 *
 * Use this exception i.e. if provided authorization hash is invalid or malformed.
 *
 * @package HubPublic\Exceptions
 */
class InvalidAuthHashException extends HubWarningException
{

}
