<?php
/* --------------------------------------------------------------
   ExistingFile.inc.php 2016-09-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ExistingFile
 *
 * @category   System
 * @package    Shared
 * @subpackage FileSystem
 */
class ExistingFile extends PathType
{
    /**
     * ExistingFile constructor.
     *
     * @param NonEmptyStringType $absoluteFilePath
     *
     * @throws InvalidArgumentException
     */
    public function __construct(NonEmptyStringType $absoluteFilePath)
    {
        parent::__construct($absoluteFilePath->asString());
        
        // Check for file existence.
        if (!is_file($this->value)) {
            throw new InvalidArgumentException('"' . $absoluteFilePath->asString() . '" is not a valid file path.');
        }
    }
    
    
    /**
     * Returns the absolute file path.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->value;
    }
}