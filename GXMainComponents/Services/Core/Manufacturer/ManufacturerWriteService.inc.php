<?php
/* --------------------------------------------------------------
   ManufacturerWriteService.inc.php 2018-01-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ManufacturerWriteService
 *
 * @category   System
 * @package    Manufacturer
 */
class ManufacturerWriteService implements ManufacturerWriteServiceInterface
{
    /**
     * @var \ManufacturerRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var \ImageFileStorage
     */
    protected $storage;
    
    
    public function __construct(ManufacturerRepositoryInterface $repository, ImageFileStorage $storage)
    {
        $this->repository = $repository;
        $this->storage    = $storage;
    }
    
    
    /**
     * Saves manufacturer entity in database.
     *
     * @param \ManufacturerInterface $manufacturer Manufacturer unit to be saved.
     *
     * @return $this|\ManufacturerWriteServiceInterface Same instance for chained method calls.
     */
    public function save(ManufacturerInterface $manufacturer)
    {
        $this->repository->save($manufacturer);
        
        return $this;
    }
    
    
    /**
     * Saves an existing image file with giving name.
     *
     * @param \ExistingFile       $sourceFile        existing file to save.
     *
     * @param \FilenameStringType $preferredFilename filename to saving file.
     *
     * @return $this |\ManufacturerWriteServiceInterface Same instance for chained method calls.
     */
    public function saveImage(ExistingFile $sourceFile, FilenameStringType $preferredFilename)
    {
        $this->storage->importFile($sourceFile, $preferredFilename);
        
        return $this;
    }
    
    
    /**
     * If the file is existing ,the filename get an unique id as prefix
     *
     * @param \FilenameStringType $preferredFilename filename of uploaded file.
     *
     * @return \FilenameStringType filename with or without unique id.
     */
    public function unifyFilename(FilenameStringType $preferredFilename)
    {
        if ($this->storage->fileExists($preferredFilename)) {
            return new FilenameStringType(uniqid() . $preferredFilename->asString());
        }
        
        return $preferredFilename;
    }
    
    
    /**
     * Delete an existing image from filesystem.
     *
     * @param \IdType $id manufacturer id to get the image name.
     *
     * @return $this
     */
    public function deleteImage(IdType $id)
    {
        $existingImage = MainFactory::create('FilenameStringType',
                                             basename($this->repository->getById($id)->getImage()));
        
        $this->storage->deleteFile($existingImage);
        
        return $this;
    }
    
    
    /**
     * Deletes manufacturer entity from database.
     *
     * @param \ManufacturerInterface $manufacturer Manufacturer unit to be deleted.
     *
     * @return $this|\ManufacturerWriteServiceInterface Same instance for chained method calls.
     */
    public function delete(ManufacturerInterface $manufacturer)
    {
        $this->repository->delete($manufacturer);
        
        return $this;
    }
    
    
    /**
     * Creates manufacturer entity.
     *
     * @return \Manufacturer New manufacturer entity.
     */
    public function createManufacturer()
    {
        return $this->repository->createManufacturer();
    }
}
