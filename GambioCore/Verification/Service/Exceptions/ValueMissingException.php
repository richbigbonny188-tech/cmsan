<?php
/*--------------------------------------------------------------
   ValueMissingException.php 2023-03-28
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
 * Class ValueMissingException
 *
 * @package Gambio\Shop\Modules\ProductListing\Submodules\Verification\Service\Exceptions
 */
class ValueMissingException extends VerificationException
{
    public const CODE = 5;
    
    
    /**
     * @param string|int $key
     *
     * @return ValueMissingException
     */
    #[Pure]
    public static function create(string|int $key): ValueMissingException
    {
        $message = 'Key "%s" is not set';
        $message = sprintf($message, $key);
        
        return new static($message, static::CODE);
    }
}