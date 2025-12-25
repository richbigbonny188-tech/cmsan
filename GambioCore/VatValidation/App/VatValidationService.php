<?php
/* --------------------------------------------------------------
   VatValidationService.php 2024-05-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\VatValidation\App;

use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Logging\LoggerBuilder;
use Gambio\Core\VatValidation\App\Validators\EuViesValidator;
use Gambio\Core\VatValidation\App\Validators\ManualVatIdValidator;
use Gambio\Core\VatValidation\Services\VatValidationService as VatValidationServiceInterface;
use Psr\Log\LoggerInterface;
use SoapClient;

/**
 * Class VatValidationService
 *
 * @package Gambio\Core\VatValidation\App
 */
class VatValidationService implements VatValidationServiceInterface
{
    private bool            $liveCheckEnabled;
    private EuViesValidator $euViesValidator;
    private LoggerInterface $logger;
    
    /**
     * @var ManualVatIdValidator[]
     */
    private array $manualValidators;
    
    
    /**
     * @param ConfigurationFinder $configurationFinder
     * @param EuViesValidator     $euViesValidator
     * @param LoggerBuilder       $loggerFactory
     */
    public function __construct(
        ConfigurationFinder $configurationFinder,
        EuViesValidator     $euViesValidator,
        LoggerBuilder       $loggerFactory
    ) {
        $this->liveCheckEnabled = class_exists(SoapClient::class)
                                  && $configurationFinder->get('configuration/ACCOUNT_COMPANY_VAT_LIVE_CHECK', 'true')
                                     === 'true';
        $this->euViesValidator  = $euViesValidator;
        $this->logger           = $loggerFactory->changeNamespace('vat-validation')->omitRequestData()->build();
        $this->manualValidators = [];
    }
    
    
    /**
     * @inheritDoc
     */
    public function validateVatId(string $vatId): bool
    {
        if (($vatId = $this->cleanupVatId($vatId)) === '') {
            return false;
        }
        
        $countryIsoCode = strtolower(substr($vatId, 0, 2));
        if (array_key_exists($countryIsoCode, $this->manualValidators) === false) {
            // showing a warning here has the potential to mercilessly spam the logs on automated registering
            // and even when a user types a faulty VATID manually, so this would not help anyone.
            // A potential solution to keep the best of both worlds: Add a new parameter controlling whether this
            // warning will be shown, give it a default of true and disable it only on registration.
            //$this->logger->warning('No validator available for given country.',
            //                       ['vatId' => $vatId, 'countryIsoCode' => $countryIsoCode]);
            
            return false;
        }
        
        $isValidFormat = $this->manualValidators[$countryIsoCode]->validateVatId($vatId);
        if ($isValidFormat && $this->liveCheckEnabled) {
            return $this->euViesValidator->validateVatId($vatId);
        } else {
            return $isValidFormat;
        }
    }
    
    
    /**
     * @param string $vatId
     *
     * @return string
     */
    private function cleanupVatId(string $vatId): string
    {
        return trim(str_replace([' ', '-', '/', '\\', '.', ':', ','], '', $vatId));
    }
    
    
    /**
     * @param ManualVatIdValidator $manualVatIdValidator
     *
     * @return void
     */
    public function registerVatIdValidator(ManualVatIdValidator $manualVatIdValidator): void
    {
        $countryIsoCode = trim(strtolower($manualVatIdValidator->validatedCountryIsoCode()));
        
        $this->manualValidators[$countryIsoCode] = $manualVatIdValidator;
    }
    
    
    /**
     * @inheritDoc
     */
    public function isVatIdEmpty(string $vatId): bool
    {
        return empty($this->cleanupVatId($vatId));
    }
}