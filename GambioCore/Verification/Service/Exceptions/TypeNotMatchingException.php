<?php
/*--------------------------------------------------------------
   TypeNotMatchingException.php 2023-03-16
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
 * Class TypeNotMatchingException
 *
 * @package Gambio\Shop\Modules\ProductListing\Submodules\Verification\Service\Exceptions
 */
class TypeNotMatchingException extends VerificationException
{
    public const CODE = 1;
    
    
    /**
     * @param string     $expected
     * @param string     $actual
     * @param string|int $key
     *
     * @return TypeNotMatchingException
     */
    #[Pure]
    public static function create(mixed $expected, mixed $actual, string|int $key): TypeNotMatchingException
    {
        $message = 'Key "%s" is of type "%s". Expected: %s';
        $message = sprintf($message, $key, $actual, $expected);
        
        return new static($message, static::CODE);
    }
}