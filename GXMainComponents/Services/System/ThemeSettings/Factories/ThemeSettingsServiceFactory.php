<?php
/*--------------------------------------------------------------------
 ThemeSettingsServiceFactory.php 2020-3-2
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

namespace Gambio\GX\Services\System\ThemeSettings\Factories;

use CacheControl;
use CI_DB_query_builder;
use ErrorException;
use FilesystemAdapter;
use Gambio\GX\Services\System\ThemeSettings\Factories\Interfaces\ThemeSettingsFactoryInterface;
use Gambio\GX\Services\System\ThemeSettings\Factories\Interfaces\ThemeSettingsServiceFactoryInterface;
use Gambio\GX\Services\System\ThemeSettings\Interfaces\ThemeInitialisationServiceInterface;
use Gambio\GX\Services\System\ThemeSettings\Interfaces\ThemeSettingsServiceInterface;
use Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces\ThemeSettingsReaderInterface;
use Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces\ThemeSettingsRepositoryInterface;
use Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces\ThemeSettingsWriterInterface;
use Gambio\GX\Services\System\ThemeSettings\Repositories\ThemeSettingsReader;
use Gambio\GX\Services\System\ThemeSettings\Repositories\ThemeSettingsRepository;
use Gambio\GX\Services\System\ThemeSettings\Repositories\ThemeSettingsWriter;
use Gambio\GX\Services\System\ThemeSettings\ThemeSettingsService;
use Gambio\StyleEdit\Core\ExternalServices\StyleEdit4ThemeInitialisationService;
use ThemeServiceInterface;

/**
 * Class ThemeSettingsServiceFactory
 * @todo create unit test for this class
 */
class ThemeSettingsServiceFactory implements ThemeSettingsServiceFactoryInterface
{
    /**
     * @var FilesystemAdapter
     */
    protected $adapter;
    
    /**
     * @var ThemeSettingsRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var ThemeSettingsFactoryInterface
     */
    protected $factory;
    
    /**
     * @var ThemeSettingsReaderInterface
     */
    protected $reader;
    
    /**
     * @var ThemeSettingsWriterInterface
     */
    protected $writer;
    
    /**
     * @var ThemeSettingsServiceInterface
     */
    protected $service;
    
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var CacheControl
     */
    protected $cacheControl;
    
    /**
     * @var ThemeServiceInterface
     */
    protected $themeService;
    
    /**
     * @var ThemeInitialisationServiceInterface
     */
    protected $themeInitialisationService;
    
    
    /**
     * ThemeSettingsServiceFactory constructor.
     *
     * @param FilesystemAdapter     $adapter
     * @param CI_DB_query_builder   $queryBuilder
     * @param CacheControl          $cacheControl
     * @param ThemeServiceInterface $themeService
     */
    public function __construct(
        FilesystemAdapter $adapter,
        CI_DB_query_builder $queryBuilder,
        CacheControl $cacheControl,
        ThemeServiceInterface $themeService
    ) {
        $this->adapter      = $adapter;
        $this->queryBuilder = $queryBuilder;
        $this->cacheControl = $cacheControl;
        $this->themeService = $themeService;
    }
    
    
    /**
     * @return ThemeSettingsRepositoryInterface
     */
    public function repository(): ThemeSettingsRepositoryInterface
    {
        if ($this->repository === null) {
            
            $this->repository = new ThemeSettingsRepository($this->writer(), $this->reader(), $this->factory());
        }
        
        return $this->repository;
    }
    
    
    /**
     * @return ThemeSettingsFactoryInterface
     */
    public function factory(): ThemeSettingsFactoryInterface
    {
        if ($this->factory === null) {
            
            $this->factory = new ThemeSettingsFactory;
        }
        
        return $this->factory;
    }
    
    
    /**
     * @return ThemeSettingsReaderInterface
     */
    public function reader(): ThemeSettingsReaderInterface
    {
        if ($this->reader === null) {
            
            $this->reader = new ThemeSettingsReader($this->adapter);
        }
        
        return $this->reader;
    }
    
    
    /**
     * @return ThemeSettingsWriterInterface
     */
    public function writer(): ThemeSettingsWriterInterface
    {
        if ($this->writer === null) {
            
            $this->writer = new ThemeSettingsWriter($this->adapter, $this->queryBuilder);
        }
        
        return $this->writer;
    }
    
    
    /**
     * @return ThemeSettingsServiceInterface
     * @throws ErrorException
     */
    public function service(): ThemeSettingsServiceInterface
    {
        if ($this->service === null) {

            $this->service = new ThemeSettingsService($this->repository(), $this->cacheControl, $this->themeService);
        }
        
        return $this->service;
    }

}