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

namespace Gambio\Core\Verification\App;

use Gambio\Core\Verification\Service\Exceptions\VerificationExceptionStack;
use Gambio\Core\Verification\Service\VerificationRepository as VerificationRepositoryInterface;
use Gambio\Core\Verification\Service\VerificationService as VerificationServiceInterface;
use stdClass;

/**
 * Class VerificationService
 *
 * @package Gambio\Shop\Modules\ProductListing\Submodules\Verification\App
 */
class VerificationService implements VerificationServiceInterface
{
    /**
     * Reports data in a log file
     */
    private const REPORTING_MODE_LOG = 1;
    
    /**
     * Reports data by printing it
     */
    private const REPORTING_MODE_PRINT = 2;
    
    /**
     * Reports data by transmitting it to an external service
     */
    private const REPORTING_MODE_TRANSMISSION = 3;
    
    private array $reportingModes;
    
    
    /**
     * VerificationService constructor.
     *
     * @param VerificationRepositoryInterface $repository
     */
    public function __construct(private VerificationRepositoryInterface $repository)
    {
        $this->reportingModes = [self::REPORTING_MODE_LOG];
    }
    
    
    /**
     * @inheritDoc
     */
    public function verify(array $expected, array $actual): void
    {
        $this->repository->verify($expected, $actual);
    }
    
    
    /**
     * @inheritDoc
     */
    public function report(
        VerificationExceptionStack $stack,
        array|stdClass             $context = [],
        string                     $module = 'VerificationService'
    ): void {
        if ($this->reportingModeIsEnabled(self::REPORTING_MODE_LOG)) {
            
            $this->repository->logReport($stack, (array)$context, $module);
        }
        
        if ($this->reportingModeIsEnabled(self::REPORTING_MODE_TRANSMISSION)) {
            
            $this->repository->transmitReport($stack, (array)$context);
        }
        
        if ($this->reportingModeIsEnabled(self::REPORTING_MODE_PRINT)) {
            
            $this->repository->printReport($stack);
        }
    }
    
    
    /**
     * @param int $reportingMode
     *
     * @return bool
     */
    private function reportingModeIsEnabled(int $reportingMode): bool
    {
        return in_array($reportingMode, $this->reportingModes, true);
    }
    
    
    /**
     * @inheritDoc
     */
    public function enableModeLog(): void
    {
        if (!in_array(self::REPORTING_MODE_LOG, $this->reportingModes, true)) {
            $this->reportingModes[] = self::REPORTING_MODE_LOG;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function enableModePrint(): void
    {
        if (!in_array(self::REPORTING_MODE_PRINT, $this->reportingModes, true)) {
            $this->reportingModes[] = self::REPORTING_MODE_PRINT;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function enableModeTransmission(): void
    {
        if (!in_array(self::REPORTING_MODE_TRANSMISSION, $this->reportingModes, true)) {
            $this->reportingModes[] = self::REPORTING_MODE_TRANSMISSION;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function disableModeLog(): void
    {
        $index = array_search(self::REPORTING_MODE_LOG, $this->reportingModes, true);
        if ($index !== false) {
            unset($this->reportingModes[$index]);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function disableModePrint(): void
    {
        $index = array_search(self::REPORTING_MODE_PRINT, $this->reportingModes, true);
        if ($index !== false) {
            unset($this->reportingModes[$index]);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function disableModeTransmission(): void
    {
        $index = array_search(self::REPORTING_MODE_TRANSMISSION, $this->reportingModes, true);
        if ($index !== false) {
            unset($this->reportingModes[$index]);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function isModeLogEnabled(): bool
    {
        return in_array(self::REPORTING_MODE_LOG, $this->reportingModes, true);
    }
    
    
    /**
     * @inheritDoc
     */
    public function isModePrintEnabled(): bool
    {
        return in_array(self::REPORTING_MODE_PRINT, $this->reportingModes, true);
    }
    
    
    /**
     * @inheritDoc
     */
    public function isModeTransmissionEnabled(): bool
    {
        return in_array(self::REPORTING_MODE_TRANSMISSION, $this->reportingModes, true);
    }
}