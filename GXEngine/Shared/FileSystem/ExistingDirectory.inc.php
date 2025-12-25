<?php
/* --------------------------------------------------------------
   ExistingDirectory.inc.php 2016-09-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ExistingDirectory
 *
 * @category   System
 * @package    Shared
 * @subpackage FileSystem
 */
class ExistingDirectory extends PathType
{
    /**
     * ExistingDirectory constructor.
     *
     * @param string $absoluteDirPath
     *
     * @throws InvalidArgumentException
     */
    public function __construct($absoluteDirPath)
    {
        parent::__construct($absoluteDirPath);
        
        // Check for directory existence.
        if (!is_dir($this->value)) {
            throw new InvalidArgumentException("'$absoluteDirPath' is not a valid directory path");
        }
    }
    
    
    /**
     * Returns the absolute directory path.
     *
     * @return string
     */
    public function getDirPath()
    {
        return $this->value;
    }
}