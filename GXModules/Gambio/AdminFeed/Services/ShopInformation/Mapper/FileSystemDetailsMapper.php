<?php
/* --------------------------------------------------------------
   FileSystemDetailsMapper.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Mapper;

use Gambio\AdminFeed\Services\ShopInformation\Reader\FileSystemDetailsReader;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\FileSystemDetails;

/**
 * Class FileSystemDetailsMapper
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Mapper
 */
class FileSystemDetailsMapper
{
    /**
     * @var FileSystemDetailsReader
     */
    private $reader;
    
    
    /**
     * FileSystemDetailsMapper constructor.
     *
     * @param FileSystemDetailsReader $reader
     */
    public function __construct(FileSystemDetailsReader $reader)
    {
        $this->reader = $reader;
    }
    
    
    /**
     * Returns the file system details.
     *
     * @return FileSystemDetails
     */
    public function getFileSystemDetails()
    {
        return FileSystemDetails::create($this->reader->getUserMods(),
                                         $this->reader->getGxModules(),
                                         $this->reader->getDangerousTools(),
                                         $this->reader->getReceiptFiles(),
                                         $this->reader->doesGlobalUsermodDirectoryExist(),
                                         $this->reader->doesUpmDirectoryExist());
    }
}