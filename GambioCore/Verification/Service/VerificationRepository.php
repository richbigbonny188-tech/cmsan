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

namespace Gambio\Core\Verification\Service;

use Gambio\Core\Verification\Service\Exceptions\VerificationExceptionStack;

/**
 * Interface VerificationRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\Submodules\Verification\Service
 */
interface VerificationRepository
{
    /**
     * Compares the expected and actual array and gathers all the differences,
     * if there are any, in an VerificationExceptionStack
     *
     * if any difference is spotted a VerificationExceptionStack will be thrown
     *
     * @param array $expected
     * @param array $actual
     *
     * @return void
     *
     * @throws VerificationExceptionStack contains all detected differences
     */
    public function verify(array $expected, array $actual): void;
    
    
    /**
     * Displays verification exception on the loaded page
     *
     * @param VerificationExceptionStack $stack
     *
     * @return void
     */
    public function printReport(VerificationExceptionStack $stack): void;
    
    
    /**
     * stores $stack in a log file
     *
     * @param VerificationExceptionStack $stack
     * @param array                      $context
     * @param string                     $module name that being reported on
     *
     * @return void
     */
    public function logReport(VerificationExceptionStack $stack, array $context, string $module): void;
    
    
    /**
     * Transmit exception to external logging service
     *
     * @param VerificationExceptionStack $stack
     * @param array                      $context
     *
     * @return void
     */
    public function transmitReport(VerificationExceptionStack $stack, array $context): void;
}