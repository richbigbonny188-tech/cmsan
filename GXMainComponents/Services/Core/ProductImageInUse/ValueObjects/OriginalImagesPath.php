<?php
/*--------------------------------------------------------------
   OriginalImagesPath.php 2020-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class OriginalImagesPath
 */
class OriginalImagesPath
{
    /**
     * @var string
     */
    protected $path;
    
    
    /**
     * OriginalImagesPath constructor.
     */
    public function __construct()
    {
        $this->path = str_replace('/', DIRECTORY_SEPARATOR, 'images/product_images/original_images/');
    }
    
    
    public function value(): string
    {
        return $this->path;
    }
}