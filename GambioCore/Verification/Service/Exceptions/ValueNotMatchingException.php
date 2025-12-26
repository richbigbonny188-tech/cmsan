<?php
/*--------------------------------------------------------------
   ValueNotMatchingException.php 2023-03-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\Verification\Service\Exceptions;

use JetBrains\PhpStorm\Pure;
use stdClass;

/**
 * Class ValueNotMatchingException
 *
 * @package Gambio\Shop\Modules\ProductListing\Submodules\Verification\Service\Exceptions
 */
class ValueNotMatchingException extends VerificationException
{
    public const CODE_PRIMITIVE  = 2;
    public const CODE_COLLECTION = 3;
    public const CODE_OBJECT     = 4;
    public const CODE_BOOL       = 5;
    
    
    /**
     * @param string     $expected
     * @param string     $actual
     * @param string|int $key
     *
     * @return ValueNotMatchingException
     */
    #[Pure]
    public static function forPrimitive(mixed $expected, mixed $actual, string|int $key): ValueNotMatchingException
    {
        if (is_bool($expected) && is_bool($actual)) {
            return static::forBoolean($expected, $actual, $key);
        }
        
        $message = 'Content of key "%s" differs.' . PHP_EOL .'Expected: %s' . PHP_EOL . 'Actual: %s';
        $message = sprintf($message, $key, (string)$expected, (string)$actual);
        
        return new static($message, static::CODE_PRIMITIVE);
    }
    
    
    /**
     * @param mixed      $expected
     * @param mixed      $actual
     * @param string|int $key
     *
     * @return ValueNotMatchingException
     */
    public static function forBoolean(bool $expected, bool $actual, string|int $key): ValueNotMatchingException
    {
        $message = 'Content of key "%s" differs.' . PHP_EOL .'Expected: %s' . PHP_EOL . 'Actual: %s';
        $message = sprintf($message, $key, $expected ? 'true' : 'false', $actual ? 'true' : 'false');
        
        return new static($message, static::CODE_BOOL);
    }
    
    /**
     * @param mixed      $expected
     * @param mixed      $actual
     * @param string|int $key
     *
     * @return ValueNotMatchingException
     */
    #[Pure]
    public static function forCollection(
        array|stdClass $expected,
        array|stdClass $actual,
        string|int $key
    ): ValueNotMatchingException {
    
        $expected = json_encode($expected, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        $actual   = json_encode($actual, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        
        $message = 'Content of key "%s" differs.' . PHP_EOL .' Expected: %s' . PHP_EOL . 'Actual: %s';
        $message = sprintf($message, $key, $expected, $actual);
    
        return new static($message, static::CODE_COLLECTION);
    }
    
    
    /**
     * @param string     $class
     * @param string|int $key
     *
     * @return ValueNotMatchingException
     */
    #[Pure]
    public static function forObject(string $class, string|int $key): ValueNotMatchingException
    {
        $message = 'Content of object "%s" differs for key "%s"';
        $message = sprintf($message, $class, $key);
        
        return new static($message, static::CODE_OBJECT);
    }
}