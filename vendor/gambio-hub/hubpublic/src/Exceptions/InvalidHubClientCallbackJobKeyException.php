<?php
/* --------------------------------------------------------------
   InvalidHubClientCallbackJobKeyException.php 2022-08-11
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
 * Class InvalidHubClientCallbackJobKeyException
 *
 * Use this exception i.e. if provided hub callback client job key is invalid or malformed.
 *
 * @package HubPublic\Exceptions
 */
class InvalidHubClientCallbackJobKeyException extends InvalidKeyException
{
    
}
