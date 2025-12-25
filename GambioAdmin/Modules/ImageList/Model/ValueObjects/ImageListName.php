<?php
/*--------------------------------------------------------------
   ImageListName.php 2021-09-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ImageListName
 * @package Gambio\Admin\Modules\ImageList\Model\ValueObjects
 */
class ImageListName
{
    /**
     * @var string
     */
    private $imageListName;
    
    
    /**
     * @param string $imageListName
     */
    private function __construct(string $imageListName)
    {
        
        $this->imageListName = $imageListName;
    }
    
    
    /**
     * @param string $imageListName
     *
     * @return ImageListName
     */
    public static function create(string $imageListName): ImageListName
    {
        Assert::stringNotEmpty($imageListName, "Attribute: 'name' can't be empty");
        
        return new self($imageListName);
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->imageListName;
    }
}