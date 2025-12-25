<?php
/* --------------------------------------------------------------
   AfterbuyOrderTrackingRepository.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderTracking\App\Data;

use Gambio\Admin\Modules\Language\Model\Exceptions\LanguageNotFoundException;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Admin\Modules\ParcelService\Model\ParcelService;
use Gambio\Admin\Modules\ParcelService\Services\Exceptions\ParcelServiceNotFoundException;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceReadService;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\ParcelServiceDetails;
use Gambio\Admin\Modules\TrackingCode\Services\Exceptions\CreationOfTrackingCodesFailedException;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeWriteService;
use Gambio\Core\Language\Services\LanguageService;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\AfterbuyLogger;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotEnabledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotInstalledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderExport\Exceptions\AfterbuyResponseException;
use GXModules\Gambio\Afterbuy\OrderTracking\Exceptions\SyncTrackingLinksFailedException;
use GXModules\Gambio\Afterbuy\OrderTracking\Model\AfterbuyOrderTrackingCode;
use GXModules\Gambio\Afterbuy\OrderTracking\Model\AfterbuyOrderTrackingCodes;
use Throwable;

/**
 * Class AfterbuyOrderTrackingRepository
 *
 * @package GXModules\Gambio\Afterbuy\OrderTracking\App\Data
 */
class AfterbuyOrderTrackingRepository
{
    private const DEFAULT_LANGUAGE_ID = '2';
    
    
    public const  CONFIGURATION_AB_TRACKING_SYNC = 'last_tracking_sync';
    
    
    /**
     * @var AfterbuyOrderTrackingReader
     */
    private AfterbuyOrderTrackingReader $reader;
    
    
    /**
     * @var AfterbuyOrderTrackingWriter
     */
    private AfterbuyOrderTrackingWriter $writer;
    
    
    /**
     * @var AfterbuyLogger
     */
    private AfterbuyLogger $logger;
    
    
    /**
     * @var TrackingCodeWriteService
     */
    private TrackingCodeWriteService $trackingCodeWriteService;
    
    
    /**
     * @var ParcelServiceReadService
     */
    private ParcelServiceReadService $parcelServiceReadService;
    
    
    /**
     * @var LanguageService
     */
    private LanguageService $languageService;
    
    
    /**
     * AfterbuyOrderTrackingRepository constructor.
     *
     * @param AfterbuyOrderTrackingReader $reader
     * @param AfterbuyOrderTrackingWriter $writer
     * @param AfterbuyLogger              $logger
     * @param TrackingCodeWriteService    $trackingCodeWriteService
     * @param ParcelServiceReadService    $parcelServiceReadService
     * @param LanguageService             $languageService
     */
    public function __construct(
        AfterbuyOrderTrackingReader $reader,
        AfterbuyOrderTrackingWriter $writer,
        AfterbuyLogger              $logger,
        TrackingCodeWriteService    $trackingCodeWriteService,
        ParcelServiceReadService    $parcelServiceReadService,
        LanguageService             $languageService
    ) {
        $this->reader                   = $reader;
        $this->writer                   = $writer;
        $this->logger                   = $logger;
        $this->trackingCodeWriteService = $trackingCodeWriteService;
        $this->parcelServiceReadService = $parcelServiceReadService;
        $this->languageService          = $languageService;
    }
    
    
    /**
     * Returns a list of order tracking codes from the Afterbuy XML-API.
     *
     * It is required to set up Afterbuy in the shop system, otherwise either an `AfterbuyNotInstalledException`
     * or `AfterbuyNotEnabledException` exception is thrown.
     *
     * Additionally, the method might throw an `AfterbuyResponseException` if the request to the Afterbuy XML-API
     * fails for some reason.
     *
     * @param string $lastTrackingSync
     *
     * @return AfterbuyOrderTrackingCodes
     * @throws AfterbuyNotInstalledException|AfterbuyNotEnabledException
     * @throws AfterbuyResponseException
     */
    public function getTrackingCodes(string $lastTrackingSync): AfterbuyOrderTrackingCodes
    {
        return $this->reader->getTrackingCodes($lastTrackingSync);
    }
    
    
    /**
     * Returns a list of afterbuy tracking codes for the given order.
     *
     * It is required to set up Afterbuy in the shop system, otherwise either an `AfterbuyNotInstalledException`
     * or `AfterbuyNotEnabledException` exception is thrown.
     *
     * Additionally, the method might throw an `AfterbuyResponseException` if the request to the Afterbuy XML-API
     * fails for some reason.
     *
     * @param OrderId $orderId
     *
     * @return AfterbuyOrderTrackingCodes
     * @throws AfterbuyNotInstalledException|AfterbuyNotEnabledException
     * @throws AfterbuyResponseException
     */
    public function findTrackingCodesByOrderId(OrderId $orderId): ?AfterbuyOrderTrackingCodes
    {
        return $this->reader->findTrackingCodesByOrderId($orderId);
    }
    
    
    /**
     * @param AfterbuyOrderTrackingCode $trackingCode
     *
     * @throws SyncTrackingLinksFailedException
     */
    public function saveTrackingCode(AfterbuyOrderTrackingCode $trackingCode): void
    {
        if (!$this->reader->trackingLinkExists($trackingCode)) {
            $this->logger->debug("saveTrackingCode {$trackingCode->trackingCode()}");
            $trackingLinkShippingMethods = $this->getAfterbuyTrackingSyncShippingMethods();
            
            $this->createTrackingCode($trackingCode, $trackingLinkShippingMethods);
            $this->writer->updateOrderStatusWithTrackingConfiguration($trackingCode->orderId());
        }
    }
    
    
    /**
     * Creates a tracking code for the order.
     *
     * @param AfterbuyOrderTrackingCode $trackingCode
     * @param array                     $trackingLinkShippingMethods
     *
     * @throws SyncTrackingLinksFailedException
     */
    private function createTrackingCode(
        AfterbuyOrderTrackingCode $trackingCode,
        array                     $trackingLinkShippingMethods
    ): void {
        $shopLanguage = $this->getCurrentLanguage();
        
        $trackingLink = $trackingCode->trackingLink();
        if (!empty($trackingLink)) {
            $parcelService = $this->getParcelServiceByTrackingLink($trackingLink, $shopLanguage->code());
            if ($parcelService !== null) {
                $this->logger->debug("Found parcel service {$parcelService->name()} ({$parcelService->id()}) for order {$trackingCode->orderId()} by link: {$trackingLink}");
                $details = ParcelServiceDetails::create($parcelService->id(),
                                                        $shopLanguage->code(),
                                                        $parcelService->name(),
                                                        $trackingLink,
                                                        '',
                                                        $trackingCode->shippingMethod());
                
                $this->createCode($trackingCode, $details);
            }
        } else {
            $configurationKey = array_search($trackingCode->shippingMethod(), $trackingLinkShippingMethods, true);
            if ($configurationKey === false) {
                return;
            }
            $parcelService = $this->getParcelServiceFromKey($configurationKey);
            
            $url = str_replace('{TRACKING_NUMBER}',
                               $trackingCode->trackingCode(),
                               $parcelService->url($shopLanguage->code()));
            
            $details = ParcelServiceDetails::create($parcelService->id(),
                                                    $shopLanguage->code(),
                                                    $parcelService->name(),
                                                    $url,
                                                    '',
                                                    $trackingCode->shippingMethod());
            $this->createCode($trackingCode, $details);
        }
    }
    
    
    /**
     * Creates the tracking code and handles possible errors.
     *
     * @param AfterbuyOrderTrackingCode $trackingCode
     * @param ParcelServiceDetails      $details
     */
    private function createCode(AfterbuyOrderTrackingCode $trackingCode, ParcelServiceDetails $details): void
    {
        try {
            $this->trackingCodeWriteService->createTrackingCode($trackingCode->orderId(),
                                                                $trackingCode->trackingCode(),
                                                                $details,
                                                                false);
        } catch (CreationOfTrackingCodesFailedException $e) {
            $message = "Failed to create tracking code for order: {$trackingCode->orderId()}\nError: {$e->getMessage()}";
            $context = [
                'trackingCode'  => [
                    'orderId'        => $trackingCode->orderId(),
                    'trackingCode'   => $trackingCode->trackingCode(),
                    'shippingMethod' => $trackingCode->shippingMethod(),
                ],
                'parcelService' => $details->toArray(),
                'exception'     => $this->getThrowableContext($e),
            ];
            
            $this->logger->error($message, $context);
        }
    }
    
    
    /**
     * Returns a parcel service.
     * Throws an exception of the parcel service was not found.
     *
     * @param string $configurationKey
     *
     * @return ParcelService
     * @throws SyncTrackingLinksFailedException
     */
    private function getParcelServiceFromKey(string $configurationKey): ParcelService
    {
        $parcelServiceId = str_replace('tracking_sync_shipping_methods_', '', $configurationKey);
        try {
            $parcelService = $this->parcelServiceReadService->getParcelServiceById((int)$parcelServiceId);
        } catch (ParcelServiceNotFoundException $e) {
            $message = "Parcel service with id '$parcelServiceId' not found.\nError: {$e->getMessage()}";
            throw new SyncTrackingLinksFailedException($message, $e->getCode(), $e);
        }
        
        return $parcelService;
    }
    
    
    /**
     * Returns a ParcelService if it can be identified by a tracking link, null otherwise.
     *
     * @param string $trackingLink
     * @param string $languageCode
     *
     * @return ParcelService|null
     */
    private function getParcelServiceByTrackingLink(string $trackingLink, string $languageCode): ?ParcelService
    {
        $trackingLinkParts = parse_url($trackingLink);
        if (!is_array($trackingLinkParts)) {
            return null;
        }
        $parcelServices = $this->parcelServiceReadService->getParcelServices();
        foreach ($parcelServices as $parcelService) {
            $serviceUrl      = $parcelService->url($languageCode);
            $serviceUrlParts = parse_url($serviceUrl);
            if (!is_array($serviceUrlParts)) {
                continue;
            }
            if ($serviceUrlParts['host'] === $trackingLinkParts['host']) {
                return $parcelService;
            }
        }
        
        return null;
    }
    
    
    /**
     * Extracts context information from any throwable type, used for logging.
     *
     * @param Throwable $t
     *
     * @return array
     */
    private function getThrowableContext(Throwable $t): array
    {
        $context = [
            'message' => $t->getMessage(),
            'code'    => $t->getCode(),
            'file'    => $t->getFile(),
            'line'    => $t->getLine(),
            'trace'   => $t->getTrace(),
        ];
        if ($previous = $t->getPrevious()) {
            $context['previous'] = $this->getThrowableContext($previous);
        }
        
        return $context;
    }
    
    
    /**
     * Checks if an order exists for the tracking codes order id.
     *
     * @param AfterbuyOrderTrackingCode $trackingCode
     *
     * @return bool
     */
    public function orderExists(AfterbuyOrderTrackingCode $trackingCode): bool
    {
        return $this->reader->orderExists($trackingCode);
    }
    
    
    /**
     * Returns a list of all afterbuy tracking link synchronization configurations.
     *
     * When removing the prefix `tracking_sync_shipping_methods_` from the array key, you
     * get the parcel service provider id of the shop system.
     * The result will be cached, so subsequent calls
     *
     * @return array
     */
    public function getAfterbuyTrackingSyncShippingMethods(): array
    {
        return $this->reader->getAfterbuyTrackingSyncShippingMethods();
    }
    
    
    /**
     * Returns the current shop language.
     *
     * @return Language
     * @throws SyncTrackingLinksFailedException
     */
    private function getCurrentLanguage(): Language
    {
        $languageId = $_SESSION['languages_id'] ?? self::DEFAULT_LANGUAGE_ID;
        $languageId = (int)$languageId;
        
        try {
            return $this->languageService->getLanguageById($languageId);
        } catch (LanguageNotFoundException $e) {
            $message = "Shop language not found.\nError: {$e->getMessage()}";
            throw new SyncTrackingLinksFailedException($message, $e->getCode(), $e);
        }
    }
}
