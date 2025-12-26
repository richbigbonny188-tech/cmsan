<?php

/* --------------------------------------------------------------
   ProductImageFileStorage.inc.php 2021-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductImageFileStorage
 *
 * @category   System
 * @package    Product
 * @subpackage Storages
 */
class ProductImageFileStorage extends ImageFileStorage
{
    /**
     * Settings
     *
     * @var EnvProductImageFileStorageSettings
     */
    protected $settings;
    
    /**
     * Original Dir (The original_images folder).
     *
     * @var WritableDirectory
     */
    protected $originalDir;
    
    /**
     * Processing
     *
     * @var ProductImageProcessingInterface
     */
    protected $processing;
    
    /**
     * @var ProductImageInUseServiceInterface
     */
    protected $productImageInUseService;
    
    
    /**
     * @throws DirectoryCreationFailedException
     */
    public function __construct(
        ProductImagePathsSettingsInterface $settings,
        ProductImageProcessingInterface    $processing
    ) {
        $originalPath = $settings->getProductOriginalImagesDirPath();
        if (!file_exists($originalPath)) {
            if (@mkdir($originalPath, 0777, true) === false) {
                throw new DirectoryCreationFailedException("Directory " . $originalPath
                                                           . " does not exists and could not be created automatically");
            }
        }
        
        $this->originalDir = MainFactory::create('WritableDirectory', $settings->getProductOriginalImagesDirPath());
        parent::__construct($this->originalDir);
        $this->settings   = $settings;
        $this->processing = $processing;
    }
    
    
    /**
     * Import File
     *
     * Saves an image to a writable directory.
     *
     * @param ExistingFile       $sourceFile        The source file to import.
     * @param FilenameStringType $preferredFilename The preferred name of the file to be saved.
     *
     * @return string Preferred filename
     * @throws InvalidArgumentException If the provided source file of the preferred filename is not valid.
     *
     */
    public function importFile(ExistingFile $sourceFile, FilenameStringType $preferredFilename)
    {
        $filename = parent::importFile($sourceFile, $preferredFilename);
        
        $filename = new FilenameStringType($filename);
        
        $this->processImage($filename);
        
        return $filename->asString();
    }
    
    
    /**
     * Rename File
     *
     * Renames an existing image file.
     *
     * @param FilenameStringType $oldName The old name of the file.
     * @param FilenameStringType $newName The new name of the file.
     *
     * @return ProductImageFileStorage Same instance for chained method calls.
     * @throws InvalidArgumentException If a file with the preferred name already exists.
     * @throws FileNotFoundException If processed product image does not exists.
     *
     * @throws InvalidArgumentException If the file that should be renamed does not exists.
     */
    public function renameFile(FilenameStringType $oldName, FilenameStringType $newName)
    {
        parent::renameFile($oldName, $newName);
        
        // Product images folders excluding the original images folder because it will
        // be handled in the parent renameFile method.
        $productImageFoldersExcludingOriginalImages = [
            $this->settings->getProductGalleryImagesDirPath(),
            $this->settings->getProductInfoImagesDirPath(),
            $this->settings->getProductPopupImagesDirPath(),
            $this->settings->getProductThumbnailImagesDirPath()
        ];
        
        foreach ($productImageFoldersExcludingOriginalImages as $folder) {
            if (!file_exists($folder . $oldName->asString())) {
                throw new FileNotFoundException($oldName->asString() . ' does not exist in ' . $folder);
            }
            
            rename($folder . $oldName->asString(), $folder . $newName->asString());
        }
        
        return $this;
    }
    
    
    /**
     * Delete File
     *
     * Deletes an existing file.
     *
     * @param FilenameStringType $filename The file to delete.
     *
     * @return ProductImageFileStorage Same instance for chained method calls.
     * @throws InvalidArgumentException If the provided filename is not valid.
     *
     */
    public function deleteFile(FilenameStringType $filename)
    {
        if ($this->productImageInUseService()->imageIsInUse($filename->asString())) {
            
            $this->addInfoMessageToStack($filename);
            
            return $this;
        }
        
        parent::deleteFile($filename);
        
        // Product images folders, excluding the original images folder, because it will
        // be handled by the parent deleteFile method.
        $productImageFoldersExcludingOriginalImages = [
            $this->settings->getProductGalleryImagesDirPath(),
            $this->settings->getProductInfoImagesDirPath(),
            $this->settings->getProductPopupImagesDirPath(),
            $this->settings->getProductThumbnailImagesDirPath()
        ];
        
        foreach ($productImageFoldersExcludingOriginalImages as $folder) {
            $filepath = $folder . $filename->asString();
            if (file_exists($filepath) && !is_dir($filepath)) {
                
                unlink($folder . $filename->asString());
            }
        }
        
        return $this;
    }
    
    
    /**
     * Processes an image for the front end.
     *
     * @param FilenameStringType $productImage
     *
     * @return ProductImageFileStorage Same instance for chained method calls.
     */
    public function processImage(FilenameStringType $productImage)
    {
        $this->processing->proceedImage($productImage);
        
        return $this;
    }
    
    
    /**
     * @return ProductImageInUseServiceInterface
     */
    public function productImageInUseService(): ProductImageInUseServiceInterface
    {
        if ($this->productImageInUseService === null) {
    
            $this->productImageInUseService = StaticGXCoreLoader::getService('ProductImageInUse');
        }
        
        return $this->productImageInUseService;
    }
    
    
    protected function addInfoMessageToStack(FilenameStringType $filename)
    {
        /** @var LanguageTextManager $textManager */
        $textManager = MainFactory::create('LanguageTextManager', 'admin_image_in_use');
        $infoMessage = $textManager->get_text('in_use_in_list_info_message');
        $infoMessage = sprintf($infoMessage, $filename->asString());
    
        $GLOBALS['messageStack']->add_session($infoMessage, 'warning');
    }
}