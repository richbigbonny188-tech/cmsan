<?php

/* --------------------------------------------------------------
   VPERepository.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VPERepository
 *
 * @category   System
 * @package    VPE
 * @subpackage Repositories
 */
class VPERepository implements VPERepositoryInterface
{
    /**
     * @var \VPEStorageInterface
     */
    protected $storage;
    
    /**
     * @var \VPEFactory
     */
    protected $factory;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * VPERepository constructor.
     *
     * @param \VPEStorageInterface $storage
     * @param \VPEFactory          $factory
     * @param \LanguageProvider    $languageProvider
     */
    public function __construct(VPEStorageInterface $storage, VPEFactory $factory, LanguageProvider $languageProvider)
    {
        $this->storage          = $storage;
        $this->factory          = $factory;
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * Returns VPE entity by the given id.
     *
     * @param \IdType $vpeId IdType of entity to be returned.
     *
     * @return \VPEInterface
     * @throws EntityNotFoundException
     */
    public function getById(IdType $vpeId)
    {
        
        $vpeData = $this->storage->getById($vpeId);
        $vpe     = null;
        if (is_array($vpeData) && count($vpeData['names'])) {
            $vpe = $this->factory->createEntity();
            
            $vpe->setId($vpeId);
            $vpe->setDefault(new BoolType($vpeData['default']));
            
            foreach ($vpeData['names'] as $key => $data) {
                $languageCode = $this->languageProvider->getCodeById(new IdType($key));
                $vpe->setName(new StringType($data), $languageCode);
            }
        } else {
            throw new EntityNotFoundException('Invalid VPE ID: ' . $vpeId);
        }
        
        return $vpe;
    }
    
    
    /**
     * Returns all VPE entities as collection.
     *
     * @return \VPECollection VPE collection.
     */
    public function getAll()
    {
        $vpeData    = $this->storage->getAll();
        $collection = $this->factory->createCollection();
        foreach ($vpeData as $data) {
            $vpe = $this->factory->createEntity();
            $vpe->setId(new IdType($data['id']));
            $vpe->setDefault(new BoolType($data['default']));
            
            foreach ($data['names'] as $languageId => $name) {
                $languageCode = $this->languageProvider->getCodeById(new IdType($languageId));
                $vpe->setName(new StringType($name), $languageCode);
            }
            $collection->addItem($vpe);
        }
        
        return $collection;
    }
    
    
    /**
     * Saves VPE entity in database.
     *
     * @param \VPEInterface $vpe Vpe entity to be save.
     *
     * @return \VPERepositoryInterface Same instance for chained method calls.
     */
    public function save(VPEInterface $vpe)
    {
        if ($vpe->getId() === 0) {
            $this->storage->save($vpe);
        } else {
            $this->storage->update($vpe);
        }
        
        return $this;
    }
    
    
    /**
     * Deletes VPE entity from database.
     *
     * @param \VPEInterface $vpe VPE entity to be deleted.
     *
     * @return \VPERepositoryInterface Same instance for chained method calls.
     */
    public function delete(VPEInterface $vpe)
    {
        $this->storage->delete($vpe);
        
        return $this;
    }
    
    
    /**
     * Creates VPE entity.
     *
     * @return \VPE New VPE entity.
     */
    public function createVPE()
    {
        return $this->factory->createEntity();
    }
}
