<?php
/*--------------------------------------------------------------
   VerificationExceptionStack.php 2023-03-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\Verification\Service\Exceptions;

use ArrayIterator;
use Exception;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

/**
 * Class VerificationExceptionStack
 *
 * @package Gambio\Shop\Modules\ProductListing\Submodules\Verification\Service\Exceptions
 */
class VerificationExceptionStack extends Exception implements IteratorAggregate
{
    /**
     * VerificationExceptionStack constructor.
     *
     * @param VerificationException[] $stack
     */
    private function __construct(private array $stack)
    {
        parent::__construct($this->createMessage());
    }
    
    
    /**
     * @param VerificationException ...$exceptions
     *
     * @return static
     */
    public static function create(VerificationException ...$exceptions): static
    {
        return empty($exceptions) === false ? new static($exceptions) : throw new InvalidArgumentException(static::class . " can't be empty");
    }
    
    /**
     * @return Traversable|VerificationException[]
     * @noinspection PhpDocSignatureInspection
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->stack);
    }


    /**
     * Combines messages of all Exceptions in stack into a single message
     *
     * @return string
     */
    private function createMessage(): string
    {
        $fn       = static fn(VerificationException $e): string => $e::class . PHP_EOL . $e->getMessage();
        $messages = array_map($fn, $this->stack);

        return PHP_EOL . implode(PHP_EOL . PHP_EOL, $messages);
    }
}