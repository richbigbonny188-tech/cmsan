<?php

/* --------------------------------------------------------------
   ContentServiceFactory.inc.php 2019-04-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentServiceFactory
 *
 * This class represents a factory for content services
 *
 * @category   System
 * @package    Content
 */
class ContentServiceFactory implements ContentServiceFactoryInterface
{
    /**
     * @var ContentIdResolverInterface
     */
    protected $contentIdentificationResolver;
    /**
     * @var ContentDeleter
     */
    protected $deleter;
    /**
     * @var ContentValueObjectFactory
     */
    protected $factory;
    /**
     * @var ContentIdentificationFactory
     */
    protected $identificationFactory;
    /**
     * CodeIgniter QueryBuilder
     *
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    /**
     * @var ContentReadService
     */
    protected $readService;
    /**
     * @var ContentReader
     */
    protected $reader;
    /**
     * Content repository
     *
     * @var ContentRepository
     */
    protected $repository;
    /**
     * Content write service
     *
     * @var ContentWriteService
     */
    protected $writeService;
    /**
     * Content writer
     *
     * @var ContentWriter
     */
    protected $writer;
    
    
    /**
     * ContentServiceFactory constructor
     *
     * @param CI_DB_query_builder $queryBuilder CodeIgniter QueryBuilder instance
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Return a content write service
     *
     * @return ContentWriteService
     */
    public function createWriteService(): ContentWriteService
    {
        if ($this->writeService === null) {
            $this->writeService = MainFactory::create(ContentWriteService::class, $this->repository());
        }
        
        return $this->writeService;
    }
    
    
    /**
     * Return a content repository
     *
     * @return ContentRepository
     */
    protected function repository(): ContentRepository
    {
        if ($this->repository === null) {
            $this->repository = MainFactory::create(ContentRepository::class,
                                                    $this->writer(),
                                                    $this->reader(),
                                                    $this->deleter(),
                                                    $this->factory(),
                                                    $this->identificationFactory());
        }
        
        return $this->repository;
    }
    
    
    /**
     * Return a content writer
     *
     * @return ContentWriter
     */
    protected function writer(): ContentWriter
    {
        if ($this->writer === null) {
            $this->writer = MainFactory::create(ContentWriter::class,
                                                $this->queryBuilder,
                                                $this->contentIdentificationResolver());
        }
        
        return $this->writer;
    }
    
    
    /**
     * @return ContentReader
     */
    public function reader(): ContentReader
    {
        if ($this->reader === null) {
            $this->reader = MainFactory::create(ContentReader::class,
                                                $this->queryBuilder,
                                                $this->identificationFactory(),
                                                $this->contentIdentificationResolver());
        }
        
        return $this->reader;
    }
    
    
    /**
     * Create content deleter.
     *
     * @return ContentDeleter
     */
    public function deleter(): ContentDeleter
    {
        if ($this->deleter === null) {
            $this->deleter = MainFactory::create(ContentDeleter::class,
                                                 $this->queryBuilder,
                                                 $this->contentIdentificationResolver());
        }
        
        return $this->deleter;
    }
    
    
    /**
     * @return ContentValueObjectFactory
     */
    protected function factory(): ContentValueObjectFactory
    {
        if ($this->factory === null) {
            
            $this->factory = MainFactory::create(ContentValueObjectFactory::class,
                                                 MainFactory::create(LanguageProvider::class, $this->queryBuilder));
        }
        
        return $this->factory;
    }
    
    
    /**
     * @return ContentIdResolverInterface
     */
    protected function contentIdentificationResolver(): ContentIdResolverInterface
    {
        if ($this->contentIdentificationResolver === null) {
            $this->contentIdentificationResolver = MainFactory::create(ContentIdResolver::class, $this->queryBuilder);
        }
        
        return $this->contentIdentificationResolver;
    }
    
    
    /**
     * @return ContentIdentificationFactoryInterface
     */
    protected function identificationFactory(): ContentIdentificationFactoryInterface
    {
        if ($this->identificationFactory === null) {
            $this->identificationFactory = MainFactory::create(ContentIdentificationFactory::class);
        }
        
        return $this->identificationFactory;
    }
    
    
    /**
     * @return ContentReadService
     */
    public function createReadService(): ContentReadService
    {
        if ($this->readService === null) {
            
            $this->readService = MainFactory::create(ContentReadService::class, $this->repository());
        }
        
        return $this->readService;
    }
}