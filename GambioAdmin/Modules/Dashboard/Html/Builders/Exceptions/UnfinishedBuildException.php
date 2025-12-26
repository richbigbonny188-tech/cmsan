<?php
/*--------------------------------------------------------------
   UnfinishedBuildException.php 2020-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\Html\Builders\Exceptions;

use Exception;
use Gambio\Admin\Modules\Dashboard\Html\ValueObjects\EndpointUrl;
use Throwable;

/**
 * Class UnfinishedBuildException
 * @package Gambio\Admin\Modules\Dashboard\Html\Builders\Exceptions
 */
class UnfinishedBuildException extends Exception
{
    /**
     * @param string         $property
     * @param Throwable|null $previous
     *
     * @return static
     */
    final public static function missingProperty(string $property, Throwable $previous = null): self
    {
        $message = 'Could not build ' . EndpointUrl::class . ' Property "' . $property . '" is missing';
        
        return new static($message, 1, $previous);
    }
}