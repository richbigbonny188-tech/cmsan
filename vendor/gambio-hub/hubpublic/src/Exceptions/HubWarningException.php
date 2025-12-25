<?php
/* --------------------------------------------------------------
   HubWarningException.php 2017-07-13
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
 * Class HubWarningException
 *
 * This exception represents a warning hub exception. Use this exception, if a error occurs which is at a warning level.
 *
 * @package HubPublic\Exceptions
 */
class HubWarningException extends HubException implements HubExceptionErrorLevelInterface
{
    /**
     * Error level.
     *
     * @var string
     */
    private $errorLevel = 'warning';
    
    
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
