<?php

/* --------------------------------------------------------------
   QuantityUnitRepository.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuantityUnitRepository
 *
 * @category   System
 * @package    QuantityUnit
 * @subpackage Repositories
 */
class QuantityUnitRepository implements QuantityUnitRepositoryInterface
{
    /**
     * @var \QuantityUnitStorageInterface
     */
    protected $storage;
    
    /**
     * @var \QuantityUnitFactory
     */
    protected $factory;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * QuantityUnitRepository constructor.
     *
     * @param \QuantityUnitStorageInterface $storage
     * @param \QuantityUnitFactory          $factory
     * @param \LanguageProvider             $languageProvider
     */
    public function __construct(
        QuantityUnitStorageInterface $storage,
        QuantityUnitFactory $factory,
        LanguageProvider $languageProvider
    ) {
        $this->storage          = $storage;
        $this->factory          = $factory;
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * Returns quantity unit entity by the given id.
     *
     * @param \IdType $quantityUnitId IdType of entity to be returned.
     *
     * @return \QuantityUnitInterface
     */
    public function getById(IdType $quantityUnitId)
    {
        $data         = $this->storage->getById($quantityUnitId);
        $quantityUnit = $this->factory->createEntity();
        
        $quantityUnit->setId($quantityUnitId);
        
        foreach ($data['names'] as $languageId => $name) {
            $languageCode = $this->languageProvider->getCodeById(new IdType($languageId));
            $quantityUnit->setName(new StringType($name), $languageCode);
        }
        
        return $quantityUnit;
    }
    
    
    /**
     * Returns all quantity unit as collection.
     *
     * @return \QuantityUnitCollection Quantity unit collection.
     */
    public function getAll()
    {
        $quantityUnitData = $this->storage->getAll();
        $collection       = $this->factory->createCollection();
        foreach ($quantityUnitData as $data) {
            $quantityUnit = $this->factory->createEntity();
            $quantityUnit->setId(new IdType($data['id']));
            
            foreach ($data['names'] as $languageId => $name) {
                $languageCode = $this->languageProvider->getCodeById(new IdType($languageId));
                $quantityUnit->setName(new StringType($name), $languageCode);
            }
            $collection->addItem($quantityUnit);
        }
        
        return $collection;
    }
    
    
    /**
     * Saves quantity unit entity in database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit to be save.
     *
     * @return \QuantityUnitRepositoryInterface Same instance for chained method calls.
     */
    public function save(QuantityUnitInterface $quantityUnit)
    {
        if ($quantityUnit->getId() === 0) {
            $this->storage->save($quantityUnit);
        } else {
            $this->storage->update($quantityUnit);
        }
        
        return $this;
    }
    
    
    /**
     * Deletes quantity unit entity from database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit entity to be deleted.
     *
     * @return \QuantityUnitRepositoryInterface Same instance for chained method calls.
     */
    public function delete(QuantityUnitInterface $quantityUnit)
    {
        $this->storage->delete($quantityUnit);
        
        return $this;
    }
    
    
    /**
     * Creates Quantity unit entity.
     *
     * @return \GXEngineQuantityUnit New quantity unit entity.
     */
    public function createQuantityUnit()
    {
        return $this->factory->createEntity();
    }
}