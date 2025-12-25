<?php

/* --------------------------------------------------------------
  StaticSeoUrlContentRepository.inc.php 2017-05-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('StaticSeoUrlContentRepositoryInterface');

/**
 * Class StaticSeoUrlRepository
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Repositories
 */
class StaticSeoUrlContentRepository implements StaticSeoUrlContentRepositoryInterface
{
    /**
     * @var StaticSeoUrlContentRepositoryWriterInterface
     */
    protected $writer;
    
    /**
     * @var StaticSeoUrlContentRepositoryReaderInterface
     */
    protected $reader;
    
    /**
     * @var StaticSeoUrlContentRepositoryDeleterInterface
     */
    protected $deleter;
    
    
    /**
     * StaticSeoUrlContentRepository constructor.
     *
     * @param StaticSeoUrlContentRepositoryWriterInterface  $writer
     * @param StaticSeoUrlContentRepositoryReaderInterface  $reader
     * @param StaticSeoUrlContentRepositoryDeleterInterface $deleter
     */
    public function __construct(
        StaticSeoUrlContentRepositoryWriterInterface $writer,
        StaticSeoUrlContentRepositoryReaderInterface $reader,
        StaticSeoUrlContentRepositoryDeleterInterface $deleter
    ) {
        $this->writer  = $writer;
        $this->reader  = $reader;
        $this->deleter = $deleter;
    }
    
    
    /**
     * Returns a StaticSeoUrlContentCollection instance by the given staticSeoUrl ID.
     *
     * @param IdType $staticSeoUrlId
     *
     * @return StaticSeoUrlContentCollection
     */
    public function getByStaticSeoUrlId(IdType $staticSeoUrlId)
    {
        return $this->reader->getByStaticSeoUrlId($staticSeoUrlId);
    }
    
    
    /**
     * Returns a StaticSeoUrlContent instance by the given staticSeoUrlContent ID.
     *
     * @param IdType $staticSeoUrlContentId
     *
     * @return StaticSeoUrlContentInterface
     */
    public function getById(IdType $staticSeoUrlContentId)
    {
        return $this->reader->getById($staticSeoUrlContentId);
    }
    
    
    /**
     * Stores a StaticSeoUrlContent to the database.
     *
     * @param IdType                       $staticSeoUrlId
     * @param StaticSeoUrlContentInterface $staticSeoUrlContent
     *
     * @return StaticSeoUrlContentRepositoryInterface Same instance for method chaining.
     */
    public function store(IdType $staticSeoUrlId, StaticSeoUrlContentInterface $staticSeoUrlContent)
    {
        $staticSeoUrlContentId = new IdType($this->writer->store($staticSeoUrlId, $staticSeoUrlContent));
        $staticSeoUrlContent->setId($staticSeoUrlContentId);
        
        return $this;
    }
    
    
    /**
     * Deletes a StaticSeoUrlContent by the given staticSeoUrlContent ID.
     *
     * @param IdType $staticSeoUrlContentId
     *
     * @return StaticSeoUrlContentRepositoryInterface Same instance for method chaining.
     */
    public function deleteStaticSeoUrlContentById(IdType $staticSeoUrlContentId)
    {
        $this->deleter->deleteById($staticSeoUrlContentId);
        
        return $this;
    }
}