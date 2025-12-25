<?php

/* --------------------------------------------------------------
  StaticSeoUrlRepository.inc.php 2017-05-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('StaticSeoUrlRepositoryInterface');

/**
 * Class StaticSeoUrlRepository
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Repositories
 */
class StaticSeoUrlRepository implements StaticSeoUrlRepositoryInterface
{
    /**
     * @var StaticSeoUrlRepositoryWriterInterface
     */
    protected $writer;
    
    /**
     * @var  StaticSeoUrlRepositoryReaderInterface
     */
    protected $reader;
    
    /**
     * @var  StaticSeoUrlRepositoryDeleterInterface
     */
    protected $deleter;
    
    /**
     * @var  StaticSeoUrlContentRepositoryInterface
     */
    protected $staticSeoUrlContentRepository;
    
    
    /**
     * StaticSeoUrlRepository constructor.
     *
     * @param StaticSeoUrlRepositoryWriterInterface  $writer
     * @param StaticSeoUrlRepositoryReaderInterface  $reader
     * @param StaticSeoUrlRepositoryDeleterInterface $deleter
     * @param StaticSeoUrlContentRepositoryInterface $staticSeoUrlContentRepository
     */
    public function __construct(
        StaticSeoUrlRepositoryWriterInterface $writer,
        StaticSeoUrlRepositoryReaderInterface $reader,
        StaticSeoUrlRepositoryDeleterInterface $deleter,
        StaticSeoUrlContentRepositoryInterface $staticSeoUrlContentRepository
    ) {
        $this->writer                        = $writer;
        $this->reader                        = $reader;
        $this->deleter                       = $deleter;
        $this->staticSeoUrlContentRepository = $staticSeoUrlContentRepository;
    }
    
    
    /**
     * Returns a StaticSeoUrlCollection with all existing StaticSeoUrl objects.
     *
     * @return StaticSeoUrlCollection
     */
    public function getAll()
    {
        $staticSeoUrlCollection = $this->reader->getAll();
        
        /** @var StaticSeoUrlInterface $staticSeoUrl */
        foreach ($staticSeoUrlCollection->getArray() as $staticSeoUrl) {
            $staticSeoUrl->setStaticSeoUrlContentCollection($this->_getStaticSeoUrlContentCollection(new IdType($staticSeoUrl->getId())));
        }
        
        return $staticSeoUrlCollection;
    }
    
    
    /**
     * Returns a StaticSeoUrl instance by the given staticSeoUrl ID.
     *
     * @param IdType $staticSeoUrlId ID of the staticSeoUrl to get.
     *
     * @return StaticSeoUrlInterface
     */
    public function getById(IdType $staticSeoUrlId)
    {
        $staticSeoUrl = $this->reader->getById($staticSeoUrlId);
        $staticSeoUrl->setStaticSeoUrlContentCollection($this->_getStaticSeoUrlContentCollection(new IdType($staticSeoUrl->getId())));
        
        return $staticSeoUrl;
    }
    
    
    /**
     * Stores a StaticSeoUrl to the database.
     *
     * @param StaticSeoUrlInterface $staticSeoUrl StaticSeoUrl to store.
     *
     * @return StaticSeoUrlRepositoryInterface Same instance for method chaining.
     * @throws InvalidArgumentException If no staticSeoUrl was found by the given ID.
     *
     */
    public function store(StaticSeoUrlInterface $staticSeoUrl)
    {
        $staticSeoUrlId = new IdType($this->writer->store($staticSeoUrl));
        $staticSeoUrl->setId($staticSeoUrlId);
        
        foreach ($staticSeoUrl->getStaticSeoUrlContentCollection()->getArray() as $staticSeoUrlContent) {
            $this->staticSeoUrlContentRepository->store($staticSeoUrlId, $staticSeoUrlContent);
        }
        
        return $this;
    }
    
    
    /**
     * Deletes a StaticSeoUrl by the given staticSeoUrl ID.
     *
     * @param IdType $staticSeoUrlId ID of the staticSeoUrl to delete.
     *
     * @return StaticSeoUrlRepositoryInterface Same instance for method chaining.
     * @throws InvalidArgumentException If no staticSeoUrl was found by the given ID.
     *
     */
    public function deleteStaticSeoUrlById(IdType $staticSeoUrlId)
    {
        $staticSeoUrl = $this->getById($staticSeoUrlId);
        $this->deleter->deleteById($staticSeoUrlId);
        
        foreach ($staticSeoUrl->getStaticSeoUrlContentCollection()->getArray() as $staticSeoUrlContent) {
            $this->staticSeoUrlContentRepository->deleteStaticSeoUrlContentById(new IdType($staticSeoUrlContent->getId()));
        }
        
        return $this;
    }
    
    
    /**
     * Returns a StaticSeoUrlContentCollection with all existing StaticSeoUrlContent objects by the given staticSeoUrl
     * ID.
     *
     * @param IdType $staticSeoUrlId ID of the staticSeoUrl to fetch the staticSeoUrlContent collection from.
     *
     * @return StaticSeoUrlContentCollection
     */
    protected function _getStaticSeoUrlContentCollection(IdType $staticSeoUrlId)
    {
        return $this->staticSeoUrlContentRepository->getByStaticSeoUrlId($staticSeoUrlId);
    }
}