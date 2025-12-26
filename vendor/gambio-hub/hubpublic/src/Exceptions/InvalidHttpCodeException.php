<?php
/* --------------------------------------------------------------
   InvalidHttpCodeException.php 2016-11-11
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2016 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Exceptions;

/**
 * Class InvalidHttpCodeException
 *
 * Use this exception i.e. if a provided HTTP result code is out of range
 *
 * @package HubPublic\Exceptions
 */
class InvalidHttpCodeException extends HubWarningException
{

}
