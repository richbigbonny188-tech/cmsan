<?php
/* --------------------------------------------------------------
   AfterbuyStockupdateCronjobTask.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\AfterbuyXMLService;
use GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\Exceptions\XMLException;
use GXModules\Gambio\Afterbuy\Admin\Classes\LoggerAdapter;
use GXModules\Gambio\Afterbuy\Admin\Classes\ProductsQuantityUpdateService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AfterbuyStockupdateCronjobTask
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes
 */
class AfterbuyStockupdateCronjobTask extends AbstractCronjobTask
{
    /**
     * @param float $cronjobStartAsMicrotime
     *
     * @return Closure
     */
    public function getCallback($cronjobStartAsMicrotime): Closure
    {
        return function () {
            $this->logInfo('AfterbuyStockupdateCronjobTask::getCallback() called');
            
            $this->logger->lastRun();
            
            if (!$this->moduleIsInstalledAndActive()) {
                return true;
            }
            
            $this->logInfo('Updating product quantities');
            //$this->updateProductsQuantities();
            $productsQuantityUpdateRunner = $this->dependencies->getDependencies()['ProductsQuantitiesUpdateRunner'];
            $productsQuantityUpdateRunner->setLogger(new LoggerAdapter($this->logger));
            $productsQuantityUpdateRunner->run();
            
            $this->logger->log(['AfterbuyStockupdate CronjobTask finished' => date('c')]);
            $this->logger->lastSuccess();
            
            return true;
        };
    }
    
    
    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    protected function updateProductsQuantities(): void
    {
        /** @var GambioAfterbuyConfigurationStorage $configurationStorage */
        $configurationStorage = $this->dependencies->getDependencies()['ConfigurationStorage'];
        $lastSyncDateTime     = new DateTimeImmutable($configurationStorage->get('last_qty_sync'));
        $newLastSyncDateTime  = new DateTimeImmutable();
        $oneHourAgo           = new DateTimeImmutable('1 hour ago');
        if ($lastSyncDateTime->getTimestamp() < $oneHourAgo->getTimestamp()) {
            $salesSince = $lastSyncDateTime;
        } else {
            $salesSince = $oneHourAgo;
        }
        $this->logInfo("Syncing quantity updates since {$salesSince->format('c')}");
        /** @var AfterbuyXMLService $xmlService */
        $xmlService = $this->dependencies->getDependencies()['XMLService'];
        $xmlService->setLogger(AfterbuyLogger::createLogger());
        try {
            $page = 0;
            do {
                $page++;
                $updatedProducts = $xmlService->getShopProductsModifiedSinceRange($salesSince, 10, 0, true, $page);
                if ((int)$updatedProducts->getTotalNumberOfEntries() === 0) {
                    $this->logInfo("No updated products found at Afterbuy.");
                } else {
                    $this->logInfo("Processing " . count($updatedProducts->getProducts())
                                   . " products received from Afterbuy");
                    /** @var ProductsQuantityUpdateService $productsQuantitiesUpdateService */
                    $productsQuantitiesUpdateService = $this->dependencies->getDependencies()['ProductsQuantitiesUpdateService'];
                    $productsQuantitiesUpdateService->setLogger(new LoggerAdapter($this->logger));
                    $productSyncType = $configurationStorage->get('product_sync_type');
                    $productsQuantitiesUpdateService->updateProductQuantities($productSyncType,
                        ...
                                                                              $updatedProducts->getProducts());
                }
            } while ($updatedProducts->getTotalNumberOfPages() > $page);
            
            $configurationStorage->set('last_qty_sync', $newLastSyncDateTime->format('c'));
        } catch (XMLException $e) {
            $this->logError("ERROR: {$e->getMessage()}");
        }
    }
    
    
    /**
     * @param string $message
     *
     * @return void
     */
    protected function logInfo(string $message): void
    {
        $this->logger->log(['message' => $message, 'level' => 'info']);
    }
    
    
    /**
     * @param string $message
     *
     * @return void
     */
    protected function logError(string $message): void
    {
        $this->logger->logError(['message' => $message, 'level' => 'error']);
    }
    
    
    /**
     * @return bool
     */
    protected function moduleIsInstalledAndActive(): bool
    {
        $configurationFinder = $this->dependencies->getDependencies()['ConfigurationFinder'];
        $installedConfig     = (bool)$configurationFinder->get('gm_configuration/MODULE_CENTER_GAMBIOAFTERBUY_INSTALLED');
        $activeConfig        = (bool)$configurationFinder->get('modules/gambio/afterbuy/active');
        
        return $installedConfig && $activeConfig;
    }
}