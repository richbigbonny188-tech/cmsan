<?php
/* --------------------------------------------------------------
   PathType.inc.php 2016-09-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PathType
 *
 * @category   System
 * @package    Shared
 * @subpackage FileSystem
 */
class PathType extends NonEmptyStringType
{
    /**
     * PathType constructor.
     *
     * @param string $absolutePath
     *
     * @throws InvalidArgumentException
     */
    public function __construct($absolutePath)
    {
        parent::__construct($absolutePath);
        
        $this->value = $this->_getRealPath($absolutePath);
    }
    
    
    /**
     * @param $path
     *
     * @return string
     */
    protected function _getRealPath($path)
    {
        if (strpos($path, 'vfs://') === 0) {
            return $path;
        }
        
        return realpath($path);
    }
    
    
    /**
     * Returns the absolute path.
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->value;
    }
}