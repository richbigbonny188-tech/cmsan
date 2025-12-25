<?php
/* --------------------------------------------------------------
   ManufacturerWriteServiceInterface.inc.php 2017-11-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ManufacturerWriteServiceInterface
 *
 * @category   System
 * @package    Manufacturer
 * @subpackage Interfaces
 */
interface ManufacturerWriteServiceInterface
{
    /**
     * Saves manufacturer entity in database.
     *
     * @param \ManufacturerInterface $manufacturer Manufacturer unit to be saved.
     *
     * @return $this|\ManufacturerWriteServiceInterface Same instance for chained method calls.
     */
    public function save(ManufacturerInterface $manufacturer);
    
    
    /**
     * Saves an existing image file with giving name.
     *
     * @param \ExistingFile       $sourceFile        existing file to save.
     *
     * @param \FilenameStringType $preferredFilename filename to saving file.
     *
     * @return $this|\ManufacturerWriteServiceInterface Same instance for chained method calls.
     */
    public function saveImage(ExistingFile $sourceFile, FilenameStringType $preferredFilename);
    
    
    /**
     * If the file is existing ,the filename get an unique id as prefix
     *
     * @param \FilenameStringType $preferredFilename filename of uploaded file.
     *
     * @return \FilenameStringType filename with or without unique id.
     */
    public function unifyFilename(FilenameStringType $preferredFilename);
    
    
    /**
     * Deletes manufacturer entity from database.
     *
     * @param \ManufacturerInterface $manufacturer Manufacturer unit to be deleted.
     *
     * @return $this|\ManufacturerWriteServiceInterface Same instance for chained method calls.
     */
    public function delete(ManufacturerInterface $manufacturer);
    
    
    /**
     * Creates manufacturer entity.
     *
     * @return \Manufacturer New manufacturer entity.
     */
    public function createManufacturer();
}