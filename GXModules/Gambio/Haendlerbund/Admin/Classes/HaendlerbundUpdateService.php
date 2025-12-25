<?php
/* --------------------------------------------------------------
   HaendlerbundUpdateService.php 2022-09-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Haendlerbund\Admin\Classes;

use function Gambio\Core\Logging\logger;

class HaendlerbundUpdateService {
    /**
     * @var BatixService
     */
    private $batixService;
    
    /**
     * @var LegaltextUpdateService
     */
    private $legaltextUpdateService;

    const LOGGING_NAMESPACE = 'haendlerbund';
    
    /**
     * @var HaendlerbundConfigurationFinder
     */
    private $configurationFinder;
    
    
    public function __construct(BatixService $batixService, LegaltextUpdateService $legaltextUpdateService, HaendlerbundConfigurationFinder $configurationFinder)
    {
        $this->batixService           = $batixService;
        $this->legaltextUpdateService = $legaltextUpdateService;
        $this->configurationFinder    = $configurationFinder;
    }
    
    
    /**
     * Updates all legal texts.
     * 
     * @throws Exceptions\BatixServiceException
     * @throws Exceptions\LegaltextUpdateException
     */
    public function updateLegalTexts(): void
    {
        $docs = $this->batixService->getDocuments();
        logger(self::LOGGING_NAMESPACE)->info("documents_available:\n" . print_r($docs, true));
        $tosB2cAndB2b           = array_key_exists(BatixService::DID_TOS_B2C, $docs)
                                  && array_key_exists(BatixService::DID_TOS_B2B, $docs);
        $payAndShipB2cAndB2b    = array_key_exists(BatixService::DID_PAYMENT_AND_SHIPPING_B2C, $docs)
                                  && array_key_exists(BatixService::DID_PAYMENT_AND_SHIPPING_B2B, $docs);
    
        foreach ($docs as $did => $docTitle) {
            $documentLanguages = $this->batixService->getAvailableLanguages($did);
            logger(self::LOGGING_NAMESPACE)->info("Languages available for {$did}: " . implode(', ', $documentLanguages));
            if ($did === BatixService::DID_TOS_B2C && $this->configurationFinder->get('useTos', 'true') === 'true') {
                foreach ($documentLanguages as $language) {
                    $tosHtml = $this->batixService->getDocumentClasses($did, $language);
                    $tosText = $this->batixService->getDocumentPlain($did, $language);
                    if ($tosB2cAndB2b) {
                        $tosHtml .= $this->batixService->getDocumentClasses(BatixService::DID_TOS_B2B, $language);
                        $tosText .= $this->batixService->getDocumentPlain(BatixService::DID_TOS_B2B, $language);
                    }
                    $this->legaltextUpdateService->updateTermsOfService($tosHtml, $language, $docTitle);
                    $this->legaltextUpdateService->updateTermsOfServicePdf($tosText, $language, $docTitle);
                    logger(self::LOGGING_NAMESPACE)->info('updated: TOS (B2C' . ($tosB2cAndB2b ? ' + B2B' : '')
                                                        . "), {$language}, {$docTitle}");
                }
            }
    
            if ($tosB2cAndB2b === false && $did === BatixService::DID_TOS_B2B
                && $this->configurationFinder->get('useTos', 'true') === 'true') {
                foreach ($documentLanguages as $language) {
                    $tosHtml = $this->batixService->getDocumentClasses($did, $language);
                    $tosText = $this->batixService->getDocumentPlain($did, $language);
                    $this->legaltextUpdateService->updateTermsOfService($tosHtml, $language, $docTitle);
                    $this->legaltextUpdateService->updateTermsOfServicePdf($tosText, $language, $docTitle);
                    logger(self::LOGGING_NAMESPACE)->info("updated: TOS (B2B), {$language}, {$docTitle}");
                }
            }
    
            if ($did === BatixService::DID_PRIVACY
                && $this->configurationFinder->get('usePrivacy', 'true') === 'true') {
                foreach ($documentLanguages as $language) {
                    $privacyHtml = $this->batixService->getDocumentClasses($did, $language);
                    $this->legaltextUpdateService->updatePrivacyNotice($privacyHtml, $language, $docTitle);
                    logger(self::LOGGING_NAMESPACE)->info("updated: Privacy notice, {$language}, {$docTitle}");
                }
            }
        
            if ($did === BatixService::DID_IMPRINT && $this->configurationFinder->get('useImprint', 'true') === 'true') {
                foreach ($documentLanguages as $language) {
                    $imprintHtml = $this->batixService->getDocumentClasses($did, $language);
                    $this->legaltextUpdateService->updateImprint($imprintHtml, $language);
                    logger(self::LOGGING_NAMESPACE)->info("updated: Imprint, {$language}, {$docTitle}");
                }
            }
    
            if ($did === BatixService::DID_PAYMENT_AND_SHIPPING_B2C
                && $this->configurationFinder->get('usePaymentAndShipping', 'true') === 'true') {
                foreach ($documentLanguages as $language) {
                    $paymentHtml = $this->batixService->getDocumentClasses($did, $language);
                    if ($payAndShipB2cAndB2b) {
                        $paymentHtml .= $this->batixService->getDocumentClasses(BatixService::DID_PAYMENT_AND_SHIPPING_B2B,
                                                                          $language);
                    }
                    $this->legaltextUpdateService->updatePaymentAndShipping($paymentHtml, $language, $docTitle);
                    logger(self::LOGGING_NAMESPACE)->info("updated: Payment and Shipping (B2C), {$language}, {$docTitle}");
                }
            }
    
            if ($payAndShipB2cAndB2b === false && $did === BatixService::DID_PAYMENT_AND_SHIPPING_B2B
                && $this->configurationFinder->get('usePaymentAndShipping', 'true') === 'true') {
                foreach ($documentLanguages as $language) {
                    $paymentHtml = $this->batixService->getDocumentClasses($did, $language);
                    $this->legaltextUpdateService->updatePaymentAndShipping($paymentHtml, $language, $docTitle);
                    logger(self::LOGGING_NAMESPACE)->info("updated: Payment and Shipping (B2B'.($payAndShipB2cAndB2b ? ' + B2B' : '').'), {$language}, {$docTitle}");
                }
            }
        
            $withdrawalTexts = [];
            if ($did === BatixService::DID_WITHDRAWAL && $this->configurationFinder->get('useWithdrawal', 'true') === 'true') {
                foreach ($documentLanguages as $language) {
                    $withdrawalHtml             = $this->batixService->getDocumentClasses($did, $language);
                    $withdrawalText             = $this->batixService->getDocumentPlain($did, $language);
                    $withdrawalTexts[$language] = ($withdrawalTexts[$language] ?? '') . $docTitle . "\n\n"
                                                  . $withdrawalText . "\n\n";
                    $this->legaltextUpdateService->updateWithdrawal1($withdrawalHtml, $language, $docTitle);
                    logger(self::LOGGING_NAMESPACE)->info("updated: Withdrawal, {$language}, {$docTitle}");
                }
            }
        
            if ($did === BatixService::DID_WITHDRAWAL_DIGITAL_GOODS && $this->configurationFinder->get('useWithdrawal', 'true') === 'true') {
                foreach ($documentLanguages as $language) {
                    $withdrawalHtml = $this->batixService->getDocumentClasses($did, $language);
                    $withdrawalText             = $this->batixService->getDocumentPlain($did, $language);
                    $withdrawalTexts[$language] = ($withdrawalTexts[$language] ?? '') . $docTitle . "\n\n"
                                                  . $withdrawalText . "\n\n";
                    $this->legaltextUpdateService->updateWithdrawal2($withdrawalHtml, $language, $docTitle);
                    logger(self::LOGGING_NAMESPACE)->info("updated: Withdrawal, {$language}, {$docTitle}");
                }
            }
        
            if ($did === BatixService::DID_WITHDRAWAL_SERVICES && $this->configurationFinder->get('useWithdrawal', 'true') === 'true') {
                foreach ($documentLanguages as $language) {
                    $withdrawalHtml = $this->batixService->getDocumentClasses($did, $language);
                    $withdrawalText             = $this->batixService->getDocumentPlain($did, $language);
                    $withdrawalTexts[$language] = ($withdrawalTexts[$language] ?? '') . $docTitle . "\n\n"
                                                  . $withdrawalText . "\n\n";
                    $this->legaltextUpdateService->updateWithdrawal3($withdrawalHtml, $language, $docTitle);
                    logger(self::LOGGING_NAMESPACE)->info("updated: Withdrawal, {$language}, {$docTitle}");
                }
            }
        
            foreach ($withdrawalTexts as $language => $withdrawalText) {
                $this->legaltextUpdateService->updateWithdrawalPdf($withdrawalText, $language);
            }
        }
    }
}