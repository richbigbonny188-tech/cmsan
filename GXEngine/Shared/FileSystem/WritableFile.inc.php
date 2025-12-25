<?php
/* --------------------------------------------------------------
   WriteableFile.inc.php 2016-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WritableFile
 *
 * @category   System
 * @package    Shared
 * @subpackage FileSystem
 */
class WritableFile extends ExistingFile
{
    /**
     * WritableFile constructor.
     *
     * @param string $absoluteFilePath
     *
     * @throws InvalidArgumentException
     */
    public function __construct($absoluteFilePath)
    {
        parent::__construct(MainFactory::create('NonEmptyStringType', $absoluteFilePath));
        
        // Check whether file is writable.
        if (!is_writable($this->value)) {
            throw new InvalidArgumentException("'$absoluteFilePath' is not writable");
        }
    }
}