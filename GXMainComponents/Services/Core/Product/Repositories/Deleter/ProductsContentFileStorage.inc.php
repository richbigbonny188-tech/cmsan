<?php

/* --------------------------------------------------------------
   ProductsContentFileStorage.inc.php 2017-05-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ProductsContentFileStorage extends AbstractFileStorage
{
    /**
     * ProductsContentFileStorage constructor.
     *
     * Overridden parent constructor, no arguments required.
     */
    public function __construct()
    {
        parent::__construct(MainFactory::create('WritableDirectory', (DIR_FS_DOCUMENT_ROOT . 'media/products')));
    }
    
    
    /**
     * Validates the provided file.
     *
     * @param ExistingFile $sourceFile The file to validate.
     *
     * @return AbstractFileStorage Same instance for chained method calls.
     * @throws InvalidArgumentException
     *
     */
    protected function _validateFile(ExistingFile $sourceFile)
    {
        // no validation required, transitional solution.
        return $this;
    }
    
    
    /**
     * Validates the provided filename.
     *
     * @param FilenameStringType $filename The filename to validate.
     *
     * @return AbstractFileStorage Same instance for chained method calls.
     * @throws InvalidArgumentException
     *
     */
    protected function _validateFilename(FilenameStringType $filename)
    {
        // no validation required, transitional solution.
        return $this;
    }
}