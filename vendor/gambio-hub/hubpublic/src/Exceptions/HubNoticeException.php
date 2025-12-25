<?php
/* --------------------------------------------------------------
   HubNoticeException.php 2017-07-13
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
 * Class HubNoticeException
 *
 * This exception represents a notice hub exception. Use this exception if a error occurs, which is at a notice level.
 *
 * @package HubPublic\Exceptions
 */
class HubNoticeException extends HubException implements HubExceptionErrorLevelInterface
{
    /**
     * Error level.
     *
     * @var string
     */
    private $errorLevel = 'notice';
    
    
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
