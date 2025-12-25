<?php
/* --------------------------------------------------------------
   AfterbuyProductsCronjobTask.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\InsertionOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\StorageOfProductVariantsFailed;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\CreationOfAdditionalFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\CreationOfAdditionalProductFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\DeletionOfAdditionalProductFieldsFailedException;
use GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\AfterbuyXMLService;
use GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\Exceptions\XMLException;
use GXModules\Gambio\Afterbuy\Admin\Classes\Catalogs\AfterbuyCatalogRepository;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\AfterbuyProductImporter;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\Exceptions\ProductImportException;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ProductImportRunner;

/**
 * Class AfterbuyProductsCronjobTask
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes
 */
class AfterbuyProductsCronjobTask extends AbstractCronjobTask
{
    /**
     * @param float $cronjobStartAsMicrotime
     *
     * @return Closure
     */
    public function getCallback($cronjobStartAsMicrotime): Closure
    {
        return function () {
            $this->logInfo('AfterbuyProductsCronjobTask::getCallback() called');
            
            $this->logger->lastRun();
            
            if (!$this->moduleIsInstalledAndActive()) {
                return true;
            }
            
            /** @var GambioAfterbuyConfigurationStorage $configurationStorage */
            $configurationStorage = $this->dependencies->getDependencies()['ConfigurationStorage'];
            
            $this->logInfo('Updating products');
            $this->updateProducts();
            
            $this->logger->log(['AfterbuyProducts CronjobTask finished' => date('c')]);
            $this->logger->lastSuccess();
            
            return true;
        };
    }
    
    
    /**
     * @return void
     *
     * @throws EntityNotFoundException
     * @throws XMLException
     * @throws ProductImportException
     * @throws InsertionOfProductVariantsFailed
     * @throws StorageOfProductVariantsFailed
     * @throws CreationOfAdditionalFieldFailedException
     * @throws CreationOfAdditionalProductFieldFailedException
     * @throws DeletionOfAdditionalProductFieldsFailedException
     */
    protected function updateProducts(): void
    {
        $this->logInfo("Updating products via cron");
        
        /** @var AfterbuyXMLService $xmlService */
        $abService = $this->dependencies->getDependencies()['XMLService'];
        
        /** @var GambioAfterbuyConfigurationStorage $configurationStorage */
        $configurationStorage = $this->dependencies->getDependencies()['ConfigurationStorage'];
        
        /** @var AfterbuyCatalogRepository $catalogRepository */
        $catalogRepository = $this->dependencies->getDependencies()['CatalogRepository'];
        
        /** @var AfterbuyProductImporter $importer */
        $importer = $this->dependencies->getDependencies()['ProductImporter'];
        
        $importRunner = new ProductImportRunner($configurationStorage, $abService, $catalogRepository, $importer);
        $importRunner->setLogger(AfterbuyLogger::createLogger());
        $importRunner->run();
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