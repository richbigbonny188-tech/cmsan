<?php
/*--------------------------------------------------------------
   VerificationService.php 2023-05-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\Verification\Service;

use Gambio\Core\Verification\Service\Exceptions\VerificationExceptionStack;
use stdClass;

/**
 * Interface VerificationService
 *
 * @package Gambio\Shop\Modules\ProductListing\Submodules\Verification\Service
 */
interface VerificationService
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
     * Reports given verification exception
     *
     * @param VerificationExceptionStack $stack
     * @param array|stdClass             $context additional context for the report
     * @param string                     $module  name that being reported on
     *
     * @return void
     */
    public function report(
        VerificationExceptionStack $stack,
        array|stdClass             $context = [],
        string                     $module = 'VerificationService'
    ): void;
    
    
    /**
     * Enables the log reporting mode.
     */
    public function enableModeLog(): void;
    
    
    /**
     * Enables the print reporting mode.
     */
    public function enableModePrint(): void;
    
    
    /**
     * Enables the transmission reporting mode.
     */
    public function enableModeTransmission(): void;
    
    
    /**
     * Disables the log reporting mode.
     */
    public function disableModeLog(): void;
    
    
    /**
     * Disables the print reporting mode.
     */
    public function disableModePrint(): void;
    
    
    /**
     * Disables the transmission reporting mode.
     */
    public function disableModeTransmission(): void;
    
    
    /**
     * Checks if the log reporting mode is enabled.
     *
     * @return bool
     */
    public function isModeLogEnabled(): bool;
    
    
    /**
     * Checks if the print reporting mode is enabled.
     *
     * @return bool
     */
    public function isModePrintEnabled(): bool;
    
    
    /**
     * Checks if the transmission reporting mode is enabled.
     *
     * @return bool
     */
    public function isModeTransmissionEnabled(): bool;
}