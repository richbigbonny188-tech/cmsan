<?php
/*--------------------------------------------------------------------
 InvalidValueIdsSpecifiedException.php 2020-3-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\Id\Exceptions;

use Exception;

/**
 * Class InvalidValueIdsSpecifiedException
 * @package Gambio\Shop\Attributes\Representation\Id\Exceptions
 */
class InvalidValueIdsSpecifiedException extends Exception
{
    /**
     * @return static
     */
    public static function missingAttributesIds(): self
    {
        return new static('Missing attribute ids');
    }
    
    
    /**
     * @param array $values
     *
     * @return static
     */
    public static function incorrectValues(array $values): self
    {
        $message = 'The option ids( ' . implode(', ', $values) . ') do not exsits';
        return new static($message);
    }
}