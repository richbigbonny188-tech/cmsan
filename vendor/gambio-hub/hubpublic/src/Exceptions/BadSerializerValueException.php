<?php
/* --------------------------------------------------------------
   BadSerializerValueException.php 2017-07-14
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
 * Class BadSerializerValueException
 *
 * Use this exception i.e. if the JSON string is malformed, empty or is missing information for the serializers.
 *
 * @package HubPublic\Exceptions
 */
class BadSerializerValueException extends HubWarningException
{

}
