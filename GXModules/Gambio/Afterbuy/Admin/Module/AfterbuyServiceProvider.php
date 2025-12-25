<?php
/* --------------------------------------------------------------
   AfterbuyServiceProvider.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Module;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceReadService;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeWriteService;
use Gambio\Core\Application\DependencyInjection\AbstractModuleServiceProvider;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Language\Services\LanguageService;
use GuzzleHttp\Client;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\ABInformationService;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\AfterbuyLogger;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\Data\AfterbuyGlobal\AfterbuyGlobalReader;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\Data\AfterbuyGlobal\AfterbuyGlobalRepository as AfterbuyGlobalRepositoryImpl;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\Data\Sender\AfterbuyRequestSender;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\Data\Sender\AfterbuyXmlResponseValidator;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Service\AfterbuyGlobalRepository;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Service\AfterbuyInformationService;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\AfterbuyOrderInvoiceReader;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\AfterbuyOrderPaymentInfoMapper;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\AfterbuyOrderPaymentInfoReader;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\AfterbuyOrderReader;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\AfterbuyOrderRepository;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\AfterbuyOrderShippingAddressMapper;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\AfterbuyOrderShippingInfoMapper;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\AfterbuyOrderShippingReader;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\AfterbuyOrderTotalReader;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\AfterbuyTaxHelper;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\OrderExportStorage;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\OrderExportRepository;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\OrderXmlApiRepository;
use GXModules\Gambio\Afterbuy\OrderExport\App\OrderExportService;
use GXModules\Gambio\Afterbuy\OrderExport\App\OrderXmlApiService;
use GXModules\Gambio\Afterbuy\OrderExport\Service\AfterbuyOrderExportService;
use GXModules\Gambio\Afterbuy\OrderExport\Service\AfterbuyOrderXmlApiService;
use GXModules\Gambio\Afterbuy\OrderExport\Service\Data\AfterbuyOrderXmlApiRepository;
use GXModules\Gambio\Afterbuy\OrderStatus\App\CheckPaidStatusService;
use GXModules\Gambio\Afterbuy\OrderStatus\App\Data\OrderStatusHistoryReader;
use GXModules\Gambio\Afterbuy\OrderStatus\App\Data\OrderStatusPaidReader;
use GXModules\Gambio\Afterbuy\OrderStatus\App\Data\OrderStatusPaidRepository;
use GXModules\Gambio\Afterbuy\OrderStatus\Service\AfterbuyCheckPaidStatusService;
use GXModules\Gambio\Afterbuy\OrderTracking\App\Data\AfterbuyOrderTrackingReader;
use GXModules\Gambio\Afterbuy\OrderTracking\App\Data\AfterbuyOrderTrackingRepository;
use GXModules\Gambio\Afterbuy\OrderTracking\App\Data\AfterbuyOrderTrackingWriter;
use GXModules\Gambio\Afterbuy\OrderTracking\App\OrderTrackingLinkService;
use GXModules\Gambio\Afterbuy\OrderTracking\Service\AfterbuyOrderTrackingLinkService;
use GXModules\Gambio\Afterbuy\ShopApi\App\Data\OrderIdMapping\OrderIdMappingRepository;
use GXModules\Gambio\Afterbuy\ShopApi\App\Data\OrderIdMapping\OrderIdMappingResponseParser;
use GXModules\Gambio\Afterbuy\ShopApi\App\Data\OrderIdMapping\OrderIdMappingWriter;
use GXModules\Gambio\Afterbuy\ShopApi\App\OrderIdMappingService;
use GXModules\Gambio\Afterbuy\ShopApi\Service\AfterbuyOrderIdMappingService;
use GXModules\Gambio\Afterbuy\ShopApi\Service\Data\AfterbuyOrderIdMappingRepository;

/**
 * Class AfterbuyServiceProvider
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Module
 */
class AfterbuyServiceProvider extends AbstractModuleServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            // services
            AfterbuyOrderIdMappingService::class,
            AfterbuyOrderXmlApiService::class,
            AfterbuyCheckPaidStatusService::class,
            AfterbuyOrderTrackingLinkService::class,
            AfterbuyOrderExportService::class,
            AfterbuyInformationService::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->registerCommon();
        $this->registerXmlApiService();
        $this->registerOrderExportService();
        $this->registerOrderIdMappingService();
        $this->registerOrderStatusPaidService();
        $this->registerOrderTrackingService();
    }
    
    
    /**
     * Registers common afterbuy components.
     */
    private function registerCommon(): void
    {
        $this->application->registerShared(AfterbuyInformationService::class, ABInformationService::class)
            ->addArgument(AfterbuyGlobalRepository::class);
        $this->application->registerShared(AfterbuyGlobalRepository::class, AfterbuyGlobalRepositoryImpl::class)
            ->addArgument(AfterbuyGlobalReader::class);
        $this->application->registerShared(AfterbuyLogger::class)->addArgument(AfterbuyGlobalReader::class);
        
        $this->application->registerShared(AfterbuyGlobalReader::class)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class)
            ->addArgument(ConfigurationFinder::class);
    }
    
    
    /**
     * Registers the afterbuy order export service.
     */
    private function registerXmlApiService(): void
    {
        $this->application->registerShared(AfterbuyOrderXmlApiService::class,
                                           OrderXmlApiService::class)
            ->addArgument(AfterbuyOrderXmlApiRepository::class)
            ->addArgument(AfterbuyLogger::class);
        $this->application->registerShared(AfterbuyOrderXmlApiRepository::class,
                                           OrderXmlApiRepository::class)
            ->addArgument(AfterbuyGlobalRepository::class)
            ->addArgument(AfterbuyOrderRepository::class)
            ->addArgument(AfterbuyRequestSender::class);
        $this->application->registerShared(AfterbuyOrderRepository::class)
            ->addArgument(AfterbuyOrderReader::class)
            ->addArgument(AfterbuyOrderShippingAddressMapper::class)
            ->addArgument(AfterbuyOrderPaymentInfoMapper::class)
            ->addArgument(AfterbuyOrderShippingInfoMapper::class)
            ->addArgument(ConfigurationFinder::class)
            ->addArgument(AfterbuyTaxHelper::class);
        $this->application->registerShared(AfterbuyRequestSender::class)
            ->addArgument(Client::class)
            ->addArgument(AfterbuyXmlResponseValidator::class)
            ->addArgument(AfterbuyLogger::class);
        $this->application->registerShared(Client::class);
        $this->application->registerShared(AfterbuyXmlResponseValidator::class)->addArgument(AfterbuyLogger::class);
        
        $this->application->registerShared(AfterbuyOrderReader::class)
            ->addArgument(Connection::class)
            ->addArgument(AfterbuyLogger::class);
        $this->application->registerShared(AfterbuyOrderInvoiceReader::class)->addArgument(Connection::class);
        $this->application->registerShared(AfterbuyOrderShippingAddressMapper::class);
        $this->application->registerShared(AfterbuyOrderPaymentInfoMapper::class)
            ->addArgument(AfterbuyOrderPaymentInfoReader::class);
        $this->application->registerShared(AfterbuyOrderShippingInfoMapper::class)
            ->addArgument(AfterbuyOrderShippingReader::class);
        $this->application->registerShared(AfterbuyOrderPaymentInfoReader::class)
            ->addArgument(ConfigurationFinder::class)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class)
            ->addArgument(AfterbuyOrderTotalReader::class)
            ->addArgument(Connection::class)
            ->addArgument(AfterbuyLogger::class);
        $this->application->registerShared(AfterbuyOrderTotalReader::class)->addArgument(Connection::class);
        $this->application->registerShared(AfterbuyOrderShippingReader::class)
            ->addArgument(ConfigurationFinder::class)
            ->addArgument(AfterbuyOrderTotalReader::class)
            ->addArgument(Connection::class);
        $this->application->registerShared(AfterbuyOrderTotalReader::class)->addArgument(Connection::class);
        $this->application->registerShared(AfterbuyTaxHelper::class)
            ->addArgument(Connection::class,);
    }
    
    
    /**
     * Registers all components to make the `AfterbuyOrderExportService` available.
     */
    private function registerOrderExportService(): void
    {
        $this->application->registerShared(AfterbuyOrderExportService::class, OrderExportService::class)
            ->addArgument(OrderExportRepository::class);
        
        $this->application->registerShared(OrderExportRepository::class)
            ->addArgument(AfterbuyOrderReader::class)
            ->addArgument(OrderExportStorage::class);
        $this->application->registerShared(OrderExportStorage::class)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
    }
    
    
    /**
     * Registers the `AfterbuyOrderIdMappingService`, which is accessible through the DI-Container with all
     * necessary components.
     */
    private function registerOrderIdMappingService(): void
    {
        $this->application->registerShared(AfterbuyOrderIdMappingService::class, OrderIdMappingService::class)
            ->addArgument(AfterbuyOrderIdMappingRepository::class);
        
        $this->application->registerShared(AfterbuyOrderIdMappingRepository::class, OrderIdMappingRepository::class)
            ->addArgument(OrderIdMappingResponseParser::class)
            ->addArgument(OrderIdMappingWriter::class);
        
        $this->application->registerShared(OrderIdMappingResponseParser::class);
        $this->application->registerShared(OrderIdMappingWriter::class)->addArgument(Connection::class);
    }
    
    
    /**
     * Registers the Afterbuy "check paid status" service.
     */
    private function registerOrderStatusPaidService(): void
    {
        $this->application->registerShared(AfterbuyCheckPaidStatusService::class, CheckPaidStatusService::class)
            ->addArgument(OrderStatusPaidRepository::class);
        $this->application->registerShared(OrderStatusPaidRepository::class)
            ->addArgument(OrderStatusPaidReader::class)
            ->addArgument(OrderStatusHistoryReader::class)
            ->addArgument(AfterbuyLogger::class);
        $this->application->registerShared(OrderStatusPaidReader::class)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class)
            ->addArgument(Connection::class);
        $this->application->registerShared(OrderStatusHistoryReader::class)->addArgument(Connection::class);
    }
    
    
    /**
     * Registers all components to make the `AfterbuyOrderTrackingLinkService` available.
     */
    private function registerOrderTrackingService(): void
    {
        $this->application->registerShared(AfterbuyOrderTrackingLinkService::class, OrderTrackingLinkService::class)
            ->addArgument(AfterbuyOrderTrackingRepository::class)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
        $this->application->registerShared(AfterbuyOrderTrackingRepository::class)
            ->addArgument(AfterbuyOrderTrackingReader::class)
            ->addArgument(AfterbuyOrderTrackingWriter::class)
            ->addArgument(AfterbuyLogger::class)
            ->addArgument(TrackingCodeWriteService::class)
            ->addArgument(ParcelServiceReadService::class)
            ->addArgument(LanguageService::class);
        $this->application->registerShared(AfterbuyOrderTrackingReader::class)
            ->addArgument(AfterbuyGlobalRepository::class)
            ->addArgument(AfterbuyRequestSender::class)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class)
            ->addArgument(Connection::class);
        $this->application->registerShared(AfterbuyOrderTrackingWriter::class)
            ->addArgument(Connection::class)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
    }
}