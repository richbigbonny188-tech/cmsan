<?php
/* --------------------------------------------------------------
  StyleEdit3ConfigurationServiceFactory.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Factories;

use FilesystemAdapter;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Factories\Interfaces\StyleEdit3ConfigurationFactoryInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Factories\Interfaces\StyleEdit3ConfigurationServiceFactoryInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Interfaces\StyleEdit3ConfigurationServiceInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\Interfaces\StyleEdit3ConfigurationRepositoryInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\Interfaces\StyleEdit3TemplateConfigurationReaderInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\Interfaces\StyleEdit3ThemeConfigurationReaderInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\StyleEdit3ConfigurationRepository;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\StyleEdit3TemplateConfigurationReader;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\StyleEdit3ThemeConfigurationReader;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\StyleEdit3ConfigurationService;

/**
 * Class StyleEdit3ConfigurationServiceFactory
 */
class StyleEdit3ConfigurationServiceFactory implements StyleEdit3ConfigurationServiceFactoryInterface
{
    /**
     * @var FilesystemAdapter
     */
    protected $shopRootFilesystem;
    
    /**
     * @var FilesystemAdapter
     */
    protected $themesFilesystem;
    
    /**
     * @var StyleEdit3ThemeConfigurationReaderInterface
     */
    protected $themeConfigurationReader;
    
    /**
     * @var StyleEdit3TemplateConfigurationReaderInterface
     */
    protected $templateConfigurationReader;
    
    /**
     * @var StyleEdit3ConfigurationFactoryInterface
     */
    protected $configurationFactory;
    
    /**
     * @var StyleEdit3ConfigurationRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var StyleEdit3ConfigurationServiceInterface
     */
    protected $service;
    
    
    /**
     * StyleEdit3ConfigurationServiceFactory constructor.
     *
     * @param FilesystemAdapter $shopRootFilesystem
     * @param FilesystemAdapter $themesFilesystem
     */
    public function __construct(FilesystemAdapter $shopRootFilesystem, FilesystemAdapter $themesFilesystem)
    {
        $this->shopRootFilesystem = $shopRootFilesystem;
        $this->themesFilesystem   = $themesFilesystem;
    }
    
    
    /**
     * @return FilesystemAdapter
     */
    public function shopRootFilesystem(): FilesystemAdapter
    {
        return $this->shopRootFilesystem;
    }
    
    
    /**
     * @return FilesystemAdapter
     */
    public function themesFilesystem(): FilesystemAdapter
    {
        return $this->themesFilesystem;
    }
    
    
    /**
     * @return StyleEdit3ThemeConfigurationReaderInterface
     */
    public function themeConfigurationReader(): StyleEdit3ThemeConfigurationReaderInterface
    {
        if ($this->themeConfigurationReader === null) {
            
            $this->themeConfigurationReader = new StyleEdit3ThemeConfigurationReader($this->themesFilesystem());
        }
        
        return $this->themeConfigurationReader;
    }
    
    
    /**
     * @return StyleEdit3TemplateConfigurationReaderInterface
     */
    public function templateConfigurationReader(): StyleEdit3TemplateConfigurationReaderInterface
    {
        if ($this->templateConfigurationReader === null) {
            
            $this->templateConfigurationReader = new StyleEdit3TemplateConfigurationReader($this->shopRootFilesystem());
        }
        
        return $this->templateConfigurationReader;
    }
    
    
    /**
     * @return StyleEdit3ConfigurationFactoryInterface
     */
    public function configurationFactory(): StyleEdit3ConfigurationFactoryInterface
    {
        if ($this->configurationFactory === null) {
            
            $this->configurationFactory = new StyleEdit3ConfigurationFactory($this->shopRootFilesystem());
        }
        
        return $this->configurationFactory;
    }
    
    
    /**
     * @return StyleEdit3ConfigurationRepositoryInterface
     */
    public function repository(): StyleEdit3ConfigurationRepositoryInterface
    {
        if ($this->repository === null) {
            
            $this->repository = new StyleEdit3ConfigurationRepository($this->themeConfigurationReader(),
                                                                      $this->templateConfigurationReader(),
                                                                      $this->configurationFactory());
        }
        
        return $this->repository;
    }
    
    
    /**
     * @return StyleEdit3ConfigurationServiceInterface
     */
    public function service(): StyleEdit3ConfigurationServiceInterface
    {
        if ($this->service === null) {
            
            $this->service = new StyleEdit3ConfigurationService($this->repository());
        }
        
        return $this->service;
    }
}