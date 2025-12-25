<?php
/*--------------------------------------------------------------
   ProductImageBaseNameFactory.php 2020-07-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class ProductImageBaseNameFactory
 */
class ProductImageBaseNameFactory implements ProductImageBaseNameFactoryInterface
{
    /**
     * @var OriginalImagesPath
     */
    protected $originalImagesPath;
    
    
    /**
     * ProductImageBaseNameFactory constructor.
     *
     * @param OriginalImagesPath $originalImagesPath
     */
    public function __construct(OriginalImagesPath $originalImagesPath)
    {
        $this->originalImagesPath = $originalImagesPath;
    }
    
    
    /**
     * @param string $filename
     *
     * @return ProductImageBaseName
     */
    public function create(string $filename): ProductImageBaseName
    {
        $value = urldecode($filename);
        $value = str_replace($this->originalImagesPath->value(), '', $value);
        
        return new ProductImageBaseName($value);
    }
}