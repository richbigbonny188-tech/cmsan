<?php
/* --------------------------------------------------------------
   InvalidHubSessionKeyException.php 2016-11-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Exceptions;

/**
 * Class InvalidHubSessionKeyException
 *
 * Use this exception i.e. if provided hub session key is invalid or malformed.
 *
 * @package HubPublic\Exceptions
 */
class InvalidHubSessionKeyException extends InvalidKeyException
{

}
