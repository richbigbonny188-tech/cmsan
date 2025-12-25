<?php
/*--------------------------------------------------------------
   VerificationRepository.php 2023-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\Verification\App;

use Gambio\Core\ErrorHandling\Services\ExceptionTransmitter;
use Gambio\Core\Verification\App\Data\VerificationLogWriter;
use Gambio\Core\Verification\Service\Exceptions\ArrayLengthNotMatchingException;
use Gambio\Core\Verification\Service\Exceptions\ValueMissingException;
use Gambio\Core\Verification\Service\Exceptions\ValueNotMatchingException;
use Gambio\Core\Verification\Service\Exceptions\TypeNotMatchingException;
use Gambio\Core\Verification\Service\Exceptions\VerificationException;
use Gambio\Core\Verification\Service\Exceptions\VerificationExceptionStack;
use Gambio\Core\Verification\Service\VerificationRepository as VerificationRepositoryInterface;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class VerificationRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\Submodules\Verification\App
 */
class VerificationRepository implements VerificationRepositoryInterface
{
    /**
     * VerificationRepository constructor.
     *
     * @param VerificationLogWriter $writer
     * @param ExceptionTransmitter  $transmitter
     */
    public function __construct(
        private VerificationLogWriter $writer,
        private ExceptionTransmitter $transmitter
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function verify(array $expected, array $actual, string|int|null $subKey = null): void
    {
        $errors = [];
        
        if (count($actual) !== count($expected)) {
            
            $errors[] = ArrayLengthNotMatchingException::create($expected, $actual);
        }
        
        foreach (array_keys($expected) as $key) {
            
            try {
                $this->validateKeyExists($actual, $key, $subKey);
                $this->validateTypesMatch($expected, $actual, $key, $subKey);
                $this->validateContentsMatch($expected, $actual, $key, $subKey);
            } catch (ValueNotMatchingException $exception) {
                
                if ($exception->getCode() === ValueNotMatchingException::CODE_COLLECTION) {
                    
                    $stack = $this->getVerificationExceptionStack($expected[$key],
                                                                  $actual[$key],
                                                                  $subKey === null ? $key : "$subKey.$key");
                    
                    if ($stack) {
                        
                        foreach ($stack as $item) {
                            
                            $errors[] = $item;
                        }
                    }
                } else {
                    $errors[] = $exception;
                }
            } catch (VerificationException $exception) {
                $errors[] = $exception;
            }
        }
        
        if (empty($errors) === false) {
            
            throw VerificationExceptionStack::create(...$errors);
        }
    }
    
    
    /**
     * @param array      $expected
     * @param array      $actual
     * @param string|int $subKey
     *
     * @return VerificationExceptionStack|null
     * @codeCoverageIgnore
     */
    private function getVerificationExceptionStack(
        array      $expected,
        array      $actual,
        string|int $subKey
    ): ?VerificationExceptionStack {
        try {
            $this->verify($expected, $actual, $subKey);
            return null;
        } catch (VerificationExceptionStack $stack) {
            return $stack;
        }
    }
    
    
    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function printReport(VerificationExceptionStack $stack): void
    {
        echo "<pre>{$stack->getMessage()}</pre>";
    }
    
    
    /**
     * @inheritDoc
     */
    public function logReport(VerificationExceptionStack $stack, array $context, string $module): void
    {
        $this->writer->error($stack->getMessage(), $context, $module);
    }
    
    
    /**
     * @inheritDoc
     */
    public function transmitReport(VerificationExceptionStack $stack, array $context): void
    {
        $this->transmitter->handleException($stack, $context);
    }
    
    
    /**
     * Checks if array key is in $actual array. If not ContentMissingException is thrown
     *
     * @param array           $actual
     * @param string|int      $key
     * @param int|string|null $subKey
     *
     * @return void
     *
     * @throws ValueMissingException
     */
    private function validateKeyExists(array $actual, string|int $key, int|null|string $subKey = null): void
    {
        if (array_key_exists($key, $actual) === false) {
            
            throw ValueMissingException::create($subKey === null ? $key : "$subKey.$key");
        }
    }
    
    
    /**
     * Validates types of provided key are identically in $expected and $actual.
     * If not TypeNotMatchingException is thrown
     *
     * @param array           $expected
     * @param array           $actual
     * @param string|int      $key
     * @param int|string|null $subKey
     *
     * @return void
     *
     * @throws TypeNotMatchingException
     */
    private function validateTypesMatch(
        array           $expected,
        array           $actual,
        string|int      $key,
        int|null|string $subKey = null
    ): void {
        $expectedType = gettype($expected[$key]);
        $actualType   = gettype($actual[$key]);
        
        if ($expectedType !== $actualType) {
            
            throw TypeNotMatchingException::create($expectedType,
                                                   $actualType,
                                                   $subKey === null ? $key : "$subKey.$key");
        }
    }
    
    
    /**
     * Validates the contents of provided key are identically in both $expected and $actual
     * If not ContentNotMatchingException is thrown
     *
     * @param array           $expected
     * @param array           $actual
     * @param string|int      $key
     * @param string|int|null $subKey
     *
     * @return void
     *
     * @throws ValueNotMatchingException
     */
    private function validateContentsMatch(
        array           $expected,
        array           $actual,
        string|int      $key,
        string|int|null $subKey = null
    ): void {
        $expectedValue = $expected[$key];
        $actualValue   = $actual[$key];
        
        if (is_object($expectedValue)) {
            
            $this->validateObjectContentsMatch($expectedValue, $actualValue, $subKey === null ? $key : "$subKey.$key");
            
            return;
        }
        
        if ($expectedValue !== $actualValue) {
            
            if (is_array($expectedValue)) {
                
                throw ValueNotMatchingException::forCollection($expectedValue,
                                                               $actualValue,
                                                               $subKey === null ? $key : "$subKey.$key");
            }
            
            throw ValueNotMatchingException::forPrimitive($expectedValue,
                                                          $actualValue,
                                                          $subKey === null ? $key : "$subKey.$key");
        }
    }
    
    
    /**
     * Validates if the content of 2 objects are identically
     * If not ContentNotMatchingException is thrown
     *
     * @param object     $expected
     * @param object     $actual
     * @param string|int $key
     *
     * @return void
     *
     * @throws ValueNotMatchingException
     * @noinspection TypeUnsafeComparisonInspection
     */
    private function validateObjectContentsMatch(object $expected, object $actual, string|int $key): void
    {
        if ($expected != $actual) {
            
            throw ValueNotMatchingException::forObject($expected::class, $key);
        }
    }
}