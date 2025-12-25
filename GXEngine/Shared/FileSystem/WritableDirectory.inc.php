<?php
/* --------------------------------------------------------------
   WritableDirectory.inc.php 2018-01-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WritableDirectory
 *
 * @category   System
 * @package    Shared
 * @subpackage FileSystem
 */
class WritableDirectory extends ExistingDirectory
{
    /**
     * WritableDirectory constructor.
     *
     * @param string $absoluteDirPath
     *
     * @throws InvalidArgumentException
     */
    public function __construct($absoluteDirPath)
    {
        parent::__construct($absoluteDirPath);
        
        // Check whether directory is writable.
        if (!is_writable($this->value)) {
            throw new InvalidArgumentException('"' . $absoluteDirPath . '" is not writable');
        }
    }
}