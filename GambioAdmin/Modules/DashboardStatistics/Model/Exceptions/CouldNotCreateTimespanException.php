<?php
/*--------------------------------------------------------------
   CouldNotCreateTimespanException.php 2021-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\Exceptions;

use InvalidArgumentException;
use Throwable;

class CouldNotCreateTimespanException extends InvalidArgumentException
{
    /**
     * Create instance.
     */
    public static function invalidDatetimeString(
        string $value,
        array $supportedTypes,
        Throwable $previous = null
    ): self {
        
        $supportedTypes = array_map(static function (string $type) {
            return '"' . $type . '"';
        },
            $supportedTypes);
        
        $lastType = array_pop($supportedTypes);
        $message  = 'Time span could not be created with the value "' . $value . '". ';
        $message  .= 'Only ' . implode(', ', $supportedTypes) . ' and ' . $lastType . ' are supported';
        
        return new self($message, 1, $previous);
    }
}