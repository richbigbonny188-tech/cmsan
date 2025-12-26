<?php
/* --------------------------------------------------------------
   StaticSeoUrlServiceFactory.inc.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StaticSeoUrlServiceFactory
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Factories
 */
class StaticSeoUrlServiceFactory extends AbstractStaticSeoUrlServiceFactory
{
    /**
     * Database connection.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var \StaticSeoUrlRepository
     */
    protected $repository;
    
    /**
     * @var \StaticSeoUrlRepositoryWriter
     */
    protected $writer;
    
    /**
     * @var \StaticSeoUrlRepositoryReader
     */
    protected $reader;
    
    /**
     * @var \StaticSeoUrlRepositoryDeleter
     */
    protected $deleter;
    
    /**
     * @var \StaticSeoUrlContentRepository
     */
    protected $contentRepository;
    
    
    /**
     * @var \StaticSeoUrlContentRepositoryWriter
     */
    protected $contentWriter;
    
    /**
     * @var \StaticSeoUrlContentRepositoryReader
     */
    protected $contentReader;
    
    /**
     * @var \StaticSeoUrlContentRepositoryDeleter
     */
    protected $contentDeleter;
    
    
    /**
     * StaticSeoUrlServiceFactory constructor.
     *
     * @param CI_DB_query_builder $db Database connection.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**)* Creates a staticSeoUrl read service.
     *
     * @return StaticSeoUrlReadService
     */
    public function createStaticSeoUrlReadService()
    {
        return MainFactory::create('StaticSeoUrlReadService', $this->_createRepository());
    }
    
    
    /**
     * Creates a staticSeoUrl write service.
     *
     * @return StaticSeoUrlWriteService
     */
    public function createStaticSeoUrlWriteService()
    {
        return MainFactory::create('StaticSeoUrlWriteService', $this->_createRepository());
    }
    
    
    /**
     * Creates a new instance of a StaticSeoUrlRepository object.
     * Consecutive calls provide the same object.
     *
     * @return \StaticSeoUrlRepository
     */
    protected function _createRepository()
    {
        if (null === $this->repository) {
            $this->repository = MainFactory::create('StaticSeoUrlRepository',
                                                    $this->_createWriter(),
                                                    $this->_createReader(),
                                                    $this->_createDeleter(),
                                                    $this->_createContentRepository());
        }
        
        return $this->repository;
    }
    
    
    /**
     * Creates a new instance of a StaticSeoUrlRepositoryWriter object.
     * Consecutive calls provide the same object.
     *
     * @return \StaticSeoUrlRepositoryWriter
     */
    protected function _createWriter()
    {
        if (null === $this->writer) {
            $this->writer = MainFactory::create('StaticSeoUrlRepositoryWriter', $this->db);
        }
        
        return $this->writer;
    }
    
    
    /**
     * Creates a new instance of a StaticSeoUrlRepositoryReader object.
     * Consecutive calls provide the same object.
     *
     * @return \StaticSeoUrlRepositoryReader
     */
    protected function _createReader()
    {
        if (null === $this->reader) {
            $this->reader = MainFactory::create('StaticSeoUrlRepositoryReader', $this->db);
        }
        
        return $this->reader;
    }
    
    
    /**
     * Creates a new instance of a StaticSeoUrlRepositoryDeleter object.
     * Consecutive calls provide the same object.
     *
     * @return \StaticSeoUrlRepositoryDeleter
     */
    protected function _createDeleter()
    {
        if (null === $this->deleter) {
            $this->deleter = MainFactory::create('StaticSeoUrlRepositoryDeleter', $this->db);
        }
        
        return $this->deleter;
    }
    
    
    /**
     * Creates a new instance of a StaticSeoUrlContentRepository object.
     * Consecutive calls provide the same object.
     *
     * @return \StaticSeoUrlContentRepository
     */
    protected function _createContentRepository()
    {
        if (null === $this->contentRepository) {
            $this->contentRepository = MainFactory::create('StaticSeoUrlContentRepository',
                                                           $this->_createContentWriter(),
                                                           $this->_createContentReader(),
                                                           $this->_createContentDeleter());
        }
        
        return $this->contentRepository;
    }
    
    
    /**
     * Creates a new instance of a StaticSeoUrlContentRepositoryWriter object.
     * Consecutive calls provide the same object.
     *
     * @return \StaticSeoUrlContentRepositoryWriter
     */
    protected function _createContentWriter()
    {
        if (null === $this->contentWriter) {
            $this->contentWriter = MainFactory::create('StaticSeoUrlContentRepositoryWriter', $this->db);
        }
        
        return $this->contentWriter;
    }
    
    
    /**
     * Creates a new instance of a StaticSeoUrlContentRepositoryReader object.
     * Consecutive calls provide the same object.
     *
     * @return \StaticSeoUrlContentRepositoryReader
     */
    protected function _createContentReader()
    {
        if (null === $this->contentReader) {
            $this->contentReader = MainFactory::create('StaticSeoUrlContentRepositoryReader', $this->db);
        }
        
        return $this->contentReader;
    }
    
    
    /**
     * Creates a new instance of a StaticSeoUrlContentRepositoryDeleter object.
     * Consecutive calls provide the same object.
     *
     * @return \StaticSeoUrlContentRepositoryDeleter
     */
    protected function _createContentDeleter()
    {
        if (null === $this->contentDeleter) {
            $this->contentDeleter = MainFactory::create('StaticSeoUrlContentRepositoryDeleter', $this->db);
        }
        
        return $this->contentDeleter;
    }
    
}