<?php
/* --------------------------------------------------------------
   InvalidKeyException.php 2017-07-14
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
 * Class InvalidKeyException
 *
 * Basic key exception. All other key exceptions (e.g. the InvalidHubSessionKeyException) have to be an
 * instance of this exception.
 *
 * @package HubPublic\Exceptions
 */
class InvalidKeyException extends HubNoticeException
{

}
