<?php
/*--------------------------------------------------------------------------------------------------
    ImagePath.php 2022-04-15
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Presentation\Implementations\Image\ValueObjects;

use Gambio\Shop\ProductModifiers\Presentation\Implementations\Image\Exceptions\InvalidFilePathException;
use Gambio\Shop\ProductModifiers\Presentation\Implementations\Image\Exceptions\InvalidUrlException;

/**
 * Class ImagePath
 * @package Gambio\Shop\ProductModifiers\Presentation\Implementations\Image\ValueObjects
 */
class ImagePath
{
    /**
     * @var string
     */
    private $public;
    /**
     * @var string
     */
    private $real;
    
    /**
     * ImagePath constructor.
     *
     * @param string $public
     * @param string $real
     *
     * @throws InvalidFilePathException
     */
    public function __construct(string $public, string $real)
    {
        if (!file_exists($real)) {
            if ($real === urldecode($real)) {
                trigger_error('The image path: "' . $real . '" is not a valid file path', E_USER_WARNING);
            }
            
            $real = urldecode($real);
            
            if (!file_exists($real)) {
                trigger_error('The image path: "' . $real . '" is not a valid file path', E_USER_WARNING);
            }
        }
        
        $this->public = $public;
        $this->real   = $real;
    }
    
    
    /**
     * @return string
     */
    public function real(): string
    {
        return $this->real;
    }
    
    
    /**
     * @return string
     */
    public function public(): string
    {
        return $this->public;
    }
    
}