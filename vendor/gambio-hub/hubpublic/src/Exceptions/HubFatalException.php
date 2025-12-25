<?php
/* --------------------------------------------------------------
   HubFatalException.php 2017-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Exceptions;

use HubPublic\Exceptions\Interfaces\HubExceptionErrorLevelInterface;

/**
 * Class HubFatalException
 *
 * This exception represents a fatal hub exception. Use this exception if a fatal error occurs.
 *
 * @package HubPublic\Exceptions
 */
class HubFatalException extends HubException implements HubExceptionErrorLevelInterface
{
    /**
     * Error level.
     *
     * @var string
     */
    private $errorLevel = 'fatal';
    
    
    /**
     * Return the error level.
     *
     * @return string
     */
    public function getErrorLevel(): string
    {
        return $this->errorLevel;
    }
}
