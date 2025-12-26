<?php
/* --------------------------------------------------------------
  ConfigurationServiceFactory.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\Configuration\Factories;

use FilesystemAdapter;
use Gambio\StyleEdit\Core\Services\Configuration\Converter\Interfaces\StyleEdit3ToStyleEdit4ConverterInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Converter\StyleEdit3ToStyleEdit4Converter;
use Gambio\StyleEdit\Core\Services\Configuration\Factories\Interfaces\ConfigurationFactoryInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Factories\Interfaces\ConfigurationServiceFactoryInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Interfaces\ConfigurationServiceInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Repositories\Interfaces\ConfigurationJsonReaderInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Repositories\Interfaces\ConfigurationRepositoryInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Repositories\ConfigurationJsonReader;
use Gambio\StyleEdit\Core\Services\Configuration\Repositories\ConfigurationRepository;
use Gambio\StyleEdit\Core\Services\Configuration\ConfigurationService;
use phpDocumentor\Reflection\DocBlock\Tag;

/**
 * Class ConfigurationServiceFactory
 */
class ConfigurationServiceFactory implements ConfigurationServiceFactoryInterface
{
    /**
     * @var FilesystemAdapter
     */
    protected $themeFilesystem;
    
    /**
     * @var ConfigurationJsonReaderInterface
     */
    protected $jsonReader;
    
    /**
     * @var ConfigurationRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var ConfigurationFactoryInterface
     */
    protected $factory;
    
    /**
     * @var StyleEdit3ToStyleEdit4ConverterInterface
     */
    protected $converter;
    
    /**
     * @var ConfigurationServiceInterface
     */
    protected $service;
    
    
    /**
     * ConfigurationServiceFactory constructor.
     *
     * @param FilesystemAdapter $themeFilesystem
     */
    public function __construct(FilesystemAdapter $themeFilesystem)
    {
        $this->themeFilesystem = $themeFilesystem;
    }
    
    
    /**
     * @return FilesystemAdapter
     */
    public function themeFilesystem(): FilesystemAdapter
    {
        return $this->themeFilesystem;
    }
    
    
    /**
     * @return ConfigurationJsonReaderInterface
     */
    public function jsonReader(): ConfigurationJsonReaderInterface
    {
        if ($this->jsonReader === null) {
            
            $this->jsonReader = new ConfigurationJsonReader($this->themeFilesystem());
        }
        
        return $this->jsonReader;
    }
    
    
    /**
     * @return ConfigurationRepositoryInterface
     */
    public function repository(): ConfigurationRepositoryInterface
    {
        if ($this->repository === null) {
            
            $this->repository = new ConfigurationRepository($this->jsonReader(), $this->factory(), $this->converter());
        }
        
        return $this->repository;
    }
    
    
    /**
     * @return ConfigurationFactoryInterface
     */
    public function factory(): ConfigurationFactoryInterface
    {
        if ($this->factory === null) {
            
            $this->factory = new ConfigurationFactory;
        }
        
        return $this->factory;
    }
    
    
    /**
     * @return ConfigurationServiceInterface
     */
    public function service(): ConfigurationServiceInterface
    {
        if ($this->service === null) {
            
            $this->service = new ConfigurationService($this->repository());
        }
        
        return $this->service;
    }
    
    
    /**
     * @return StyleEdit3ToStyleEdit4ConverterInterface
     */
    public function converter(): StyleEdit3ToStyleEdit4ConverterInterface
    {
        if ($this->converter === null) {
            
            $this->converter = new StyleEdit3ToStyleEdit4Converter;
        }
        
        return $this->converter;
    }
}