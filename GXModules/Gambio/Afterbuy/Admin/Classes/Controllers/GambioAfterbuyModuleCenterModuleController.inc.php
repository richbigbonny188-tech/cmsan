<?php
/* --------------------------------------------------------------
   GambioAfterbuyModuleCenterModuleController.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\ProductVariant\Model\Exceptions\InsertionOfProductVariantsFailed;
use GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\AfterbuyXMLService;
use GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\Exceptions\XMLException;
use GXModules\Gambio\Afterbuy\Admin\Classes\Catalogs\AfterbuyCatalogRepository;
use GXModules\Gambio\Afterbuy\Admin\Classes\Catalogs\CatalogImportRunner;
use GXModules\Gambio\Afterbuy\Admin\Classes\Catalogs\Exceptions\CatalogImportException;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\AfterbuyProductImporter;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\Exceptions\ProductImportException;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ProductImportRunner;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ProductsMappingRepository;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\ProductImportRunnerStatus;
use GXModules\Gambio\Afterbuy\Admin\Classes\ProductsQuantityUpdateRunner;
use GXModules\Gambio\Afterbuy\Admin\Classes\ProductsQuantityUpdateService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class GambioAfterbuyModuleCenterModuleController
 *
 * @package GXModules/Gambio/Afterbuy/Admin/Classes/Controllers
 */
class GambioAfterbuyModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    /**
     * @var GambioAfterbuyConfigurationStorage
     */
    protected GambioAfterbuyConfigurationStorage $configuration;
    
    /**
     * @var AfterbuyLogger
     */
    protected AfterbuyLogger $logger;
    
    
    /**
     * @return void
     */
    protected function _init(): void
    {
        $this->pageTitle     = $this->languageTextManager->get_text('gambioafterbuy_title');
        $this->configuration = MainFactory::create('GambioAfterbuyConfigurationStorage');
        $this->logger        = MainFactory::create(AfterbuyLogger::class);
    }
    
    
    /**
     * @return AdminLayoutHttpControllerResponse|mixed|RedirectHttpControllerResponse
     * @throws \Exception
     */
    public function actionDefault()
    {
        $title    = new NonEmptyStringType($this->languageTextManager->get_text('gambioafterbuy_title'));
        $template = $this->getTemplateFile('Gambio/Afterbuy/Admin/Html/gambioafterbuy_configuration.html');
        
        if ($this->configuration->get('minimum_log_level') === 'debug') {
            $GLOBALS['messageStack']->add($this->languageTextManager->get_text('warning_debug_logging'), 'info');
        }
        
        $GLOBALS['messageStack']->add($this->languageTextManager->get_text('introductory_note_text'), 'info');
        
        $templateData = [
            'pageToken'                 => $_SESSION['coo_page_token']->generate_token(),
            'configuration'             => $this->configuration->get_all(),
            'cron_url'                  => xtc_catalog_href_link('shop.php',
                                                                 'do=AfterbuyCron/SendOrders&key='
                                                                 . LogControl::get_secure_token()),
            'action_save_configuration' => xtc_href_link('admin.php',
                                                         'do=GambioAfterbuyModuleCenterModule/SaveConfiguration'),
        ];
        $data         = MainFactory::create('KeyValueCollection', $templateData);
        
        $assets                      = new AssetCollection();
        $contentNavigationCollection = new ContentNavigationCollection([]);
        $contentNavigationCollection->add(new StringType($this->languageTextManager->get_text('gambioafterbuy_tab_config')),
                                          new StringType('admin.php?do=GambioAfterbuyModuleCenterModule'),
                                          new BoolType(true));
        $contentNavigationCollection->add(new StringType($this->languageTextManager->get_text('gambioafterbuy_tab_orderexport')),
                                          new StringType('admin.php?do=GambioAfterbuyModuleCenterModule/OrderExportConfiguration'),
                                          new BoolType(false));
        $contentNavigationCollection->add(new StringType($this->languageTextManager->get_text('gambioafterbuy_tab_import')),
                                          new StringType('admin.php?do=GambioAfterbuyModuleCenterModule/Import'),
                                          new BoolType(false));
        
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   $title,
                                   $template,
                                   $data,
                                   $assets,
                                   $contentNavigationCollection);
    }
    
    
    /**
     * @param array $newConfiguration
     *
     * @return void
     * @throws Exception
     */
    protected function saveConfiguration(array $newConfiguration): void
    {
        if (isset($newConfiguration['order_status_paid'])) {
            // ensures that 'order_status_not_paid' only contains different values than 'order_status_paid'
            // additionally, unset the whole 'order_status_not_paid' if 'treat all as paid' isset only
            if (in_array('-1', $newConfiguration['order_status_paid'], true)
                && count($newConfiguration['order_status_paid']) === 1) {
                $newConfiguration['order_status_not_paid'] = [];
            } elseif (is_array($newConfiguration['order_status_not_paid'])
                      && is_array($newConfiguration['order_status_paid'])) {
                $newConfiguration['order_status_not_paid'] = array_diff($newConfiguration['order_status_not_paid'],
                                                                        $newConfiguration['order_status_paid']);
            } else {
                $newConfiguration['order_status_not_paid'] = [];
            }
        }
        
        foreach ($newConfiguration as $key => $value) {
            $this->configuration->set($key, $value);
        }
    }
    
    
    /**
     * @return mixed|RedirectHttpControllerResponse
     * @throws Exception
     */
    public function actionSaveConfiguration()
    {
        $this->_validatePageToken();
        
        $newConfiguration = $this->_getPostData('configuration') ?? [];
        try {
            $this->saveConfiguration($newConfiguration);
        } catch (Exception $e) {
            $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('error_saving_configuration'),
                                                  'error');
        }
        
        $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('configuration_saved'), 'info');
        
        $abService = $this->getAfterbuyXMLService();
        if ($abService !== null) {
            try {
                $time           = $abService->getAfterbuyDatetime();
                $now            = new \DateTimeImmutable();
                $timeDifference = $time->getTimestamp() - $now->getTimestamp();
                if ($timeDifference > 0) {
                    $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('time_difference')
                                                          . "{$timeDifference}s",
                                                          'warning');
                }
            } catch (XMLException $e) {
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('check_tokens'), 'warning');
            }
        }
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=GambioAfterbuyModuleCenterModule'));
    }
    
    
    /**
     * @return AfterbuyXMLService|null
     */
    protected function getAfterbuyXMLService(): ?AfterbuyXMLService
    {
        $partnerToken = $this->configuration->get('partner_token');
        $accountToken = $this->configuration->get('account_token');
        if (!empty($partnerToken) && !empty($accountToken)) {
            return new AfterbuyXMLService($partnerToken, $accountToken);
        }
        
        return null;
    }
    
    
    /**
     * @return AdminLayoutHttpControllerResponse|mixed
     * @throws \Exception
     */
    public function actionOrderExportConfiguration()
    {
        $orderStatusReadService = StaticGXCoreLoader::getService('OrderStatus');
        $orderStatuses          = [];
        
        /** @var OrderStatusInterface $orderStatus */
        foreach ($orderStatusReadService->findAll() as $orderStatus) {
            $orderStatuses[] = [
                'id'   => (string)$orderStatus->getId(),
                'name' => $orderStatus->getName(MainFactory::create('LanguageCode',
                                                                    new StringType($_SESSION['language_code']))),
            ];
        }
        
        $title                       = new NonEmptyStringType($this->languageTextManager->get_text('gambioafterbuy_title_orderexport'));
        $template                    = $this->getTemplateFile('Gambio/Afterbuy/Admin/Html/gambioafterbuy_orderexport.html');
        $parcelServices              = $this->getParcelServiceData();
        $parcelServiceId             = $this->configuration->get('tracking_sync_parcel_service_id');
        $shippingMethods             = $this->configuration->get('tracking_sync_shipping_methods');
        $orderStatusTrackingSync     = $this->configuration->get('order_status_tracking_sync');
        $orderStatusShippingDate     = $this->configuration->get('order_status_shipping_date');
        $templateData                = [
            'pageToken'                        => $_SESSION['coo_page_token']->generate_token(),
            'configuration'                    => $this->configuration->get_all(),
            'order_statuses'                   => $orderStatuses,
            'order_status_tracking_sync'       => $orderStatusTrackingSync,
            'order_status_shipping_date'       => $orderStatusShippingDate,
            'parcel_services'                  => $parcelServices,
            'tracking_sync_parcel_service_id'  => $parcelServiceId,
            'tracking_sync_shipping_method'    => $shippingMethods,
            'last_tracking_sync_datetimelocal' => (new \DateTimeImmutable($this->configuration->get('last_tracking_sync')))->format('Y-m-d\TH:i:s'),
            'action_save_configuration'        => xtc_href_link('admin.php',
                                                                'do=GambioAfterbuyModuleCenterModule/SaveOrderExportConfiguration'),
        ];
        $data                        = MainFactory::create('KeyValueCollection', $templateData);
        $assets                      = new AssetCollection();
        $contentNavigationCollection = new ContentNavigationCollection([]);
        $contentNavigationCollection->add(new StringType($this->languageTextManager->get_text('gambioafterbuy_tab_config')),
                                          new StringType('admin.php?do=GambioAfterbuyModuleCenterModule'),
                                          new BoolType(false));
        $contentNavigationCollection->add(new StringType($this->languageTextManager->get_text('gambioafterbuy_tab_orderexport')),
                                          new StringType('admin.php?do=GambioAfterbuyModuleCenterModule/OrderExportConfiguration'),
                                          new BoolType(true));
        $contentNavigationCollection->add(new StringType($this->languageTextManager->get_text('gambioafterbuy_tab_import')),
                                          new StringType('admin.php?do=GambioAfterbuyModuleCenterModule/Import'),
                                          new BoolType(false));
        
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   $title,
                                   $template,
                                   $data,
                                   $assets,
                                   $contentNavigationCollection);
    }
    
    
    /**
     * @return array<array{id: string, name: string, isDefault: bool}>
     */
    protected function getParcelServiceData(): array
    {
        $parcelReadService = ParcelServiceServiceFactory::readService();
        try {
            $parcelServices = $parcelReadService->getAll();
        } catch (ParcelServiceCollectionNotFoundException $e) {
            $message = "Failed to get parcel services for afterbuy tracking link sync.\nError: {$e->getMessage()}";
            $context = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
            $this->logger->warning($message, $context);
            
            return [];
        }
        
        $data = [];
        foreach ($parcelServices as $parcelService) {
            $shippingMethodKey = "tracking_sync_shipping_methods_{$parcelService->id()}";
            /** @var ParcelServiceInterface $parcelService */
            $data[] = [
                'id'             => (string)$parcelService->id(),
                'name'           => $parcelService->name(),
                'isDefault'      => $parcelService->isDefault(),
                'shippingMethod' => $this->configuration->get($shippingMethodKey) ?? '',
            ];
        }
        
        return $data;
    }
    
    
    /**
     * @return mixed|RedirectHttpControllerResponse
     * @throws \Exception
     */
    public function actionSaveOrderExportConfiguration()
    {
        $this->_validatePageToken();
        
        $newConfiguration = $this->_getPostData('configuration') ?? [];
        
        try {
            $this->saveConfiguration($newConfiguration);
        } catch (Exception $e) {
            $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('error_saving_configuration'),
                                                  'error');
        }
        
        $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('configuration_saved'), 'info');
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php',
                                                 'do=GambioAfterbuyModuleCenterModule/OrderExportConfiguration'));
    }
    
    
    /**
     * @return AdminLayoutHttpControllerResponse|mixed
     * @throws Exception
     */
    public function actionImport()
    {
        $title    = new NonEmptyStringType($this->languageTextManager->get_text('gambioafterbuy_title_import'));
        $template = $this->getTemplateFile('Gambio/Afterbuy/Admin/Html/gambioafterbuy_import.html');
        
        $lastImportTimestamp = (int)$this->configuration->get('import_since_timestamp');
        $lastImportDateTime  = new \DateTime("@{$lastImportTimestamp}");
        $lastImportDateTime->setTimezone(new \DateTimeZone('Europe/Berlin'));
        
        $templateData = [
            'pageToken'                   => $_SESSION['coo_page_token']->generate_token(),
            'configuration'               => $this->configuration->get_all(),
            'last_qty_sync_datetimelocal' => (new \DateTimeImmutable($this->configuration->get('last_qty_sync')))->format('Y-m-d\TH:i:s'),
            'categories'                  => $this->getCategoriesTreeList(),
            'customer_groups'             => $this->getCustomerGroups(),
            'tax_classes'                 => $this->getTaxClasses(),
            'last_import_datetime'        => $lastImportDateTime->format('Y-m-d H:i:s'),
            'last_product_id'             => (int)$this->configuration->get('import_last_product_id'),
            'action_save_configuration'   => xtc_href_link('admin.php',
                                                           'do=GambioAfterbuyModuleCenterModule/SaveImportConfiguration'),
            'action_import_operations'    => xtc_href_link('admin.php',
                                                           'do=GambioAfterbuyModuleCenterModule/ImportOperation'),
            'product_import_config'       => json_encode([
                                                             'importStepUrl' => xtc_href_link('admin.php',
                                                                                              'do=GambioAfterbuyModuleCenterModule/ImportStep'),
                                                         ]),
        ];
        $data         = MainFactory::create('KeyValueCollection', $templateData);
        $assets       = new AssetCollection();
        $assetsBase   = '../GXModules/Gambio/Afterbuy';
        $assets->add(new Asset("${assetsBase}/Admin/Javascript/product_import.js"));
        $contentNavigationCollection = new ContentNavigationCollection([]);
        $contentNavigationCollection->add(new StringType($this->languageTextManager->get_text('gambioafterbuy_tab_config')),
                                          new StringType('admin.php?do=GambioAfterbuyModuleCenterModule'),
                                          new BoolType(false));
        $contentNavigationCollection->add(new StringType($this->languageTextManager->get_text('gambioafterbuy_tab_orderexport')),
                                          new StringType('admin.php?do=GambioAfterbuyModuleCenterModule/OrderExportConfiguration'),
                                          new BoolType(false));
        $contentNavigationCollection->add(new StringType($this->languageTextManager->get_text('gambioafterbuy_tab_import')),
                                          new StringType('admin.php?do=GambioAfterbuyModuleCenterModule/Import'),
                                          new BoolType(true));
        
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   $title,
                                   $template,
                                   $data,
                                   $assets,
                                   $contentNavigationCollection);
    }
    
    
    /**
     * @return mixed|RedirectHttpControllerResponse
     * @throws \Exception
     */
    public function actionSaveImportConfiguration()
    {
        $this->_validatePageToken();
        
        $newConfiguration = $this->_getPostData('configuration') ?? [];
        
        try {
            if (isset($newConfiguration['last_import_datetime'])) {
                $this->configuration->set('import_since_timestamp',
                                          strtotime($newConfiguration['last_import_datetime']));
                unset($newConfiguration['last_import_datetime']);
            }
            
            $this->saveConfiguration($newConfiguration);
        } catch (Exception $e) {
            $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('error_saving_configuration'),
                                                  'error');
        }
        
        $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('configuration_saved'), 'info');
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=GambioAfterbuyModuleCenterModule/Import'));
    }
    
    
    /**
     * @return mixed|RedirectHttpControllerResponse
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function actionImportOperation()
    {
        $this->_validatePageToken();
        
        $cmd = $this->_getPostData('cmd');
        
        if ($cmd === 'catalogs') {
            try {
                $this->importCatalogs();
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('catalogs_imported'),
                                                      'info');
            } catch (CatalogImportException $e) {
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('error_importing_catalogs')
                                                      . " {$e->getMessage()}",
                                                      'info');
            }
        } elseif ($cmd === 'products') {
            try {
                $this->importProducts();
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('products_imported'),
                                                      'info');
            } catch (\Exception $e) {
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('error_importing_products')
                                                      . ": {$e->getMessage()}",
                                                      'info');
            }
        } elseif ($cmd === 'update_quantities') {
            try {
                $this->updateQuantities();
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('quantities_updated'),
                                                      'info');
            } catch (\Exception $e) {
                $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('error_updating_quantities')
                                                      . ": {$e->getMessage()}",
                                                      'info');
            }
        } elseif ($cmd === 'products_reset') {
            $this->resetImportProductsTimestamp();
            $GLOBALS['messageStack']->add_session($this->languageTextManager->get_text('products_import_timestamp_reset'),
                                                  'info');
        } else {
            $GLOBALS['messageStack']->add_session("to be implemented: {$cmd}", 'info');
        }
        
        return MainFactory::create('RedirectHttpControllerResponse',
                                   xtc_href_link('admin.php', 'do=GambioAfterbuyModuleCenterModule/Import'));
    }
    
    
    /**
     * Triggers an import step; AJAX/JSON endpoint
     *
     * @return JsonHttpControllerResponse|mixed
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws InsertionOfProductVariantsFailed
     * @throws NotFoundExceptionInterface
     * @throws ProductImportException
     * @throws XMLException
     */
    public function actionImportStep()
    {
        ob_start();
        try {
            $importStatus       = $this->importProducts();
            $lastImportDatetime = new \DateTime('@' . $importStatus->getSince()->getTimestamp());
            $lastImportDatetime->setTimezone(new DateTimeZone('Europe/Berlin'));
            $lastImportDatetimeValue = $lastImportDatetime->format('Y-m-d\TH:i:s');
            if ((int)$importStatus->getTotalPages() === 0) {
                $response = [
                    'progress'           => 1.0,
                    'message'            => '100 %',
                    'lastimportdatetime' => $lastImportDatetimeValue,
                ];
            } else {
                $progress = $importStatus->getCurrentPage() / $importStatus->getTotalPages();
                $response = [
                    'progress'           => $importStatus->getCurrentPage() / $importStatus->getTotalPages(),
                    'message'            => (int)($progress * 100) . ' %',
                    'lastimportdatetime' => $lastImportDatetimeValue,
                ];
            }
            
            ob_clean();
            
            return MainFactory::create('JsonHttpControllerResponse', $response);
        } catch (\Exception $e) {
            $this->logger->error("Error importing products (actionImportStep) in {$e->getFile()}:{$e->getLine()}: {$e->getMessage()}\n{$e->getTraceAsString()}");
            ob_clean();
            
            return MainFactory::create('JsonHttpControllerResponse', [
                'progress'           => 1.0,
                'message'            => 'ERROR',
                'lastimportdatetime' => '',
            ]);
        }
    }
    
    
    /**
     * @return void
     * @throws CatalogImportException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    protected function importCatalogs(): void
    {
        $abService = $this->getAfterbuyXMLService();
        if ($abService === null) {
            throw new CatalogImportException("Could not fetch catalogs: credentials are not configured");
        }
        
        $dbConnection      = LegacyDependencyContainer::getInstance()->get(Connection::class);
        $catalogRepository = new AfterbuyCatalogRepository($dbConnection);
        $importRunner      = new CatalogImportRunner($this->configuration, $abService, $catalogRepository);
        $importRunner->setLogger(AfterbuyLogger::createLogger());
        $importRunner->run();
    }
    
    
    /**
     * @return void
     * @throws AfterbuyException
     */
    protected function updateQuantities(): void
    {
        $abService = $this->getAfterbuyXMLService();
        if ($abService === null) {
            throw new AfterbuyException('Could not update product quantities: credentials are not configured');
        }
        
        $productsQuantityUpdateService = new ProductsQuantityUpdateService(StaticGXCoreLoader::getDatabaseQueryBuilder());
        $productsQuantityUpdateRunner  = new ProductsQuantityUpdateRunner($this->configuration,
                                                                          $abService,
                                                                          $productsQuantityUpdateService);
        $productsQuantityUpdateRunner->setLogger(AfterbuyLogger::createLogger());
        $productsQuantityUpdateRunner->run();
    }
    
    
    /**
     * @return void
     */
    protected function resetImportProductsTimestamp(): void
    {
        $this->configuration->set('import_since_timestamp', 0);
    }
    
    
    /**
     * @return AfterbuyProductImporter
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getAfterbuyProductImporter(): AfterbuyProductImporter
    {
        $dbConnection        = LegacyDependencyContainer::getInstance()->get(Connection::class);
        $mappingRepository   = new ProductsMappingRepository($dbConnection);
        $moduleConfiguration = new GambioAfterbuyConfigurationStorage();
        $productWriteService = StaticGXCoreLoader::getService('ProductWrite');
        $productReadService  = StaticGXCoreLoader::getService('ProductRead');
        $importer            = new AfterbuyProductImporter($mappingRepository,
                                                           $moduleConfiguration,
                                                           $productWriteService,
                                                           $productReadService);
        
        return $importer;
    }
    
    
    /**
     * @return ProductImportRunnerStatus
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     * @throws ProductImportException
     * @throws XMLException
     * @throws InsertionOfProductVariantsFailed
     */
    protected function importProducts(): ProductImportRunnerStatus
    {
        $abService = $this->getAfterbuyXMLService();
        if ($abService === null) {
            throw new ProductImportException("Could not fetch products: credentials are not configured");
        }
        
        $dbConnection      = LegacyDependencyContainer::getInstance()->get(Connection::class);
        $catalogRepository = new AfterbuyCatalogRepository($dbConnection);
        $importer          = $this->getAfterbuyProductImporter();
        $importRunner      = new ProductImportRunner($this->configuration, $abService, $catalogRepository, $importer);
        $logger            = AfterbuyLogger::createLogger();
        $logger->info('Product import triggered by UI');
        $importRunner->setLogger($logger);
        
        return $importRunner->run();
    }
    
    
    /**
     * @param bool $includeAdminGroup
     * @param bool $includeDefaultGroup
     * @param bool $includeGuestGroup
     *
     * @return array
     */
    protected function getCustomerGroups(
        bool $includeAdminGroup = false,
        bool $includeDefaultGroup = false,
        bool $includeGuestGroup = false
    ): array {
        $adminGroupId   = defined('DEFAULT_CUSTOMERS_STATUS_ID_ADMIN') ? (int)constant('DEFAULT_CUSTOMERS_STATUS_ID_ADMIN') : 0;
        $guestGroupId   = defined('DEFAULT_CUSTOMERS_STATUS_ID_GUEST') ? (int)constant('DEFAULT_CUSTOMERS_STATUS_ID_GUEST') : 1;
        $defaultGroupId = defined('DEFAULT_CUSTOMERS_STATUS_ID') ? (int)constant('DEFAULT_CUSTOMERS_STATUS_ID') : 2;
        $excludedIds    = [];
        if ($includeAdminGroup === false) {
            $excludedIds[] = $adminGroupId;
        }
        if ($includeDefaultGroup === false) {
            $excludedIds[] = $defaultGroupId;
        }
        if ($includeGuestGroup === false) {
            $excludedIds[] = $guestGroupId;
        }
        $languageCode = new LanguageCode(new StringType($_SESSION['language_code']));
        /** @var CustomerGroupReadServiceInterface $groupRead */
        $groupRead = StaticGXCoreLoader::getService('CustomerGroupRead');
        $groups    = $groupRead->getAll();
        $groupsMap = [];
        /** @var CustomerGroupInterface $group */
        foreach ($groups as $group) {
            if (in_array($group->getId(), $excludedIds, true)) {
                continue;
            }
            $groupsMap[] = [
                'id'   => $group->getId(),
                'name' => $group->getName($languageCode),
            ];
        }
        
        return $groupsMap;
    }
    
    
    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    protected function getTaxClasses(): array
    {
        $taxClasses = [];
        
        $dbConn       = LegacyDependencyContainer::getInstance()->get(Connection::class);
        $taxClassRows = $dbConn->executeQuery('SELECT `tax_class_id`, `tax_class_title` FROM `tax_class` ORDER BY `tax_class_id`')
            ->fetchAllAssociative();
        foreach ($taxClassRows as $taxClassRow) {
            $taxClasses[] = [
                'id'    => $taxClassRow['tax_class_id'],
                'title' => $taxClassRow['tax_class_title'],
            ];
        }
        
        return $taxClasses;
    }
    
    
    /**
     * @param int $parentCategoryId
     * @param int $level
     *
     * @return array
     */
    protected function getCategoriesTreeList(int $parentCategoryId = 0, int $level = 0): array
    {
        $listPrefix   = '_';
        $languageCode = new LanguageCode(new StringType($_SESSION['language_code']));
        /** @var CategoryReadServiceInterface $categoryReadService */
        $categoryReadService = StaticGXCoreLoader::getService('CategoryRead');
        $categories          = $categoryReadService->getCategoryList($languageCode, new IdType($parentCategoryId));
        
        $categoriesTreeList = [];
        /** @var IdType $categoryId */
        foreach ($categories as $category) {
            $categoriesTreeList[] = [
                'id'   => $category->getCategoryId(),
                'name' => str_repeat($listPrefix, $level) . $category->getName($languageCode),
            ];
            $subCategories        = $this->getCategoriesTreeList($category->getCategoryId(), $level + 1);
            foreach ($subCategories as $subCategory) {
                $categoriesTreeList[] = $subCategory;
            }
        }
        
        return $categoriesTreeList;
    }
}
