<?php
/* --------------------------------------------------------------
   ManufacturerRepository.inc.php 2019-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ManufacturerRepository
 *
 * @category   System
 * @package    Manufacturer
 * @subpackage Repositories
 */
class ManufacturerRepository implements ManufacturerRepositoryInterface
{
    /**
     * @var \ManufacturerFactory
     */
    protected $factory;
    
    /**
     * @var \ManufacturerReaderInterface
     */
    protected $reader;
    
    /**
     * @var \ManufacturerWriterInterface
     */
    protected $writer;
    
    /**
     * @var \ManufacturerDeleterInterface
     */
    protected $deleter;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * ManufacturerRepository constructor.
     *
     * @param \ManufacturerFactory          $factory
     * @param \ManufacturerReaderInterface  $reader
     * @param \ManufacturerWriterInterface  $writer
     * @param \ManufacturerDeleterInterface $deleter
     * @param \LanguageProvider             $languageProvider
     */
    public function __construct(
        ManufacturerFactory $factory,
        ManufacturerReaderInterface $reader,
        ManufacturerWriterInterface $writer,
        ManufacturerDeleterInterface $deleter,
        LanguageProvider $languageProvider
    ) {
        $this->factory          = $factory;
        $this->reader           = $reader;
        $this->writer           = $writer;
        $this->deleter          = $deleter;
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * Returns all manufacturer as collection.
     *
     * @return \ManufacturerCollection Manufacturer collection.
     * @throws Exception
     */
    public function getAll()
    {
        $manufacturerData = $this->reader->getAll();
        
        return $this->_mapReaderDataToCollection($manufacturerData);
    }
    
    
    /**
     * Searches for manufacturer entities that respects the given search condition and returns their data as a
     * collection.
     *
     * @param \ManufacturerSearchCondition $searchCondition
     * @param \Pager|null                  $pager   (Optional) Pager object with pagination information
     * @param array                        $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ManufacturerCollection Manufacturer collection.
     * @throws Exception
     */
    public function search(ManufacturerSearchCondition $searchCondition, \Pager $pager = null, array $sorters = [])
    {
        $manufacturerData = $this->reader->search($searchCondition, $pager, $sorters);
        
        return $this->_mapReaderDataToCollection($manufacturerData);
    }
    
    
    /**
     * Returns manufacturer entity by the given id.
     *
     * @param \IdType $manufacturerId IdType of entity to be returned.
     *
     * @return \ManufacturerInterface
     * @throws \EntityNotFoundException If no record was found with provided manufacturer entity id.
     */
    public function getById(IdType $manufacturerId)
    {
        $data         = $this->reader->getById($manufacturerId);
        $manufacturer = $this->factory->createEntity();
        
        $manufacturer->setId($manufacturerId);
        $manufacturer->setName(new StringType($data['name']));
        if ($data['image']) {
            $manufacturer->setImage(new StringType($data['image']));
        }
        $manufacturer->setDateAdded(new \DateTime($data['dateAdded']));
        $manufacturer->setLastModified(new \DateTime($data['lastModified']));
        
        foreach ($data['url'] as $languageId => $title) {
            $languageCode = $this->languageProvider->getCodeById(new IdType($languageId));
            $manufacturer->setUrl(new StringType($title), $languageCode);
        }
        
        return $manufacturer;
    }
    
    
    /**
     * Saves manufacturer entity from database.
     *
     * @param \ManufacturerInterface $manufacturer Manufacturer entity to be saved.
     *
     * @return \ManufacturerRepositoryInterface Same instance for chained method calls.
     */
    public function save(ManufacturerInterface $manufacturer)
    {
        if ($manufacturer->getId() === 0) {
            $this->writer->store($manufacturer);
        } else {
            $this->writer->update($manufacturer);
        }
        
        return $this;
    }
    
    
    /**
     * Deletes manufacturer entity from database.
     *
     * @param \ManufacturerInterface $manufacturer Manufacturer entity to be deleted.
     *
     * @return \ManufacturerRepositoryInterface Same instance for chained method calls.
     */
    public function delete(ManufacturerInterface $manufacturer)
    {
        $this->deleter->delete($manufacturer);
        
        return $this;
    }
    
    
    /**
     * creates manufacturer entity.
     *
     * @return \Manufacturer New manufacturer entity.
     */
    public function createManufacturer()
    {
        return $this->factory->createEntity();
    }
    
    
    /**
     * @param $manufacturerData
     *
     * @return \ManufacturerCollection
     * @throws Exception
     */
    protected function _mapReaderDataToCollection($manufacturerData)
    {
        $collection = $this->factory->createCollection();
        
        foreach ($manufacturerData as $data) {
            $manufacturer = $this->factory->createEntity();
            $manufacturer->setId(new IdType($data['id']));
            $manufacturer->setName(new StringType($data['name']));
            if ($data['image']) {
                $manufacturer->setImage(new StringType($data['image']));
            }
            $manufacturer->setDateAdded(new \DateTime($data['dateAdded']));
            $manufacturer->setLastModified(new \DateTime($data['lastModified']));
            
            foreach ($data['url'] as $languageId => $title) {
                $languageCode = $this->languageProvider->getCodeById(new IdType($languageId));
                $manufacturer->setUrl(new StringType($title), $languageCode);
            }
            $collection->addItem($manufacturer);
        }
        
        return $collection;
    }
}