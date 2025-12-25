<?php
/*--------------------------------------------------------------
   ArraySizeNotMatchingException.php 2023-04-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\Verification\Service\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class ArrayLengthNotMatchingException
 *
 * @package Gambio\Core\Verification\Service\Exceptions
 */
class ArrayLengthNotMatchingException extends VerificationException
{
    public const CODE = 6;
    
    
    /**
     * @param array $expected
     * @param array $actual
     *
     * @return ArrayLengthNotMatchingException
     */
    #[Pure]
    public static function create(array $expected, array $actual): ArrayLengthNotMatchingException
    {
        $message = '<b>Expected to have a size of "%s". Got: %s</b>';
        $message = sprintf($message, count($expected), count($actual));
        
        return new static($message, static::CODE);
    }
}