<?php
/*--------------------------------------------------------------
   ProductListingVerificationServiceFactory.php 2023-05-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use Gambio\Core\Application\ValueObjects\Environment;
use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Cache\Services\SafeCache;
use Gambio\Core\Verification\Service\VerificationService;

/**
 * Class ProductListingVerificationServiceFactory
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class ProductListingVerificationServiceInitializer
{
    private bool $initialized = false;
    private SafeCache $cache;
    
    public function __construct(
        private Environment $environment,
        CacheFactory $factory,
        private VerificationService $service
    ) {
        $this->cache = $factory->createCacheFor('post-installation');
    }
    
    
    /**
     * @return VerificationService
     */
    public function getService(): VerificationService
    {
        if ($this->initialized === false) {
            
            $this->init();
        }
        
        return $this->service;
    }
    
    
    /**
     * @return void
     */
    private function init(): void
    {
        if ($this->isProduction()) {
            //  ExceptionTransmitter checks internally if users have consented,
            //  can be loaded in every production environment
            $this->service->enableModeTransmission();
        } else {
            //  VerificationService will print errors and halt further execution
            $this->service->enableModePrint();
        }
        
        if ($this->isFirstExecutionAfterInstallation()) {
            //  tax zones, etc. are set up on first run of the shop, the shop will be reloaded afterwards.
            //  Legacy product listing data will be generated incorrect so there is no point in verifying any result
            $this->service->disableModeLog();
            $this->service->disableModePrint();
            $this->service->disableModeTransmission();
        }
        
        $this->initialized = true;
    }
    
    /**
     * @return bool
     */
    private function isProduction(): bool
    {
        return $this->environment->isDev() === false;
    }
    
    
    /**
     * @return bool
     */
    private function isFirstExecutionAfterInstallation(): bool
    {
        return $this->cache->has('executed');
    }
}