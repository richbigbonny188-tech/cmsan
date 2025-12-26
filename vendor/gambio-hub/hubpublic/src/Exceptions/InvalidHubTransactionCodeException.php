<?php
/* --------------------------------------------------------------
   InvalidHubTransactionCodeException.php 2017-07-14
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2017 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Exceptions;

/**
 * Class InvalidHubTransactionCodeException
 *
 * Use this exception i.e. if the provided hub transaction code is invalid or malformed.
 *
 * @package HubPublic\Exceptions
 */
class InvalidHubTransactionCodeException extends HubWarningException
{

}
