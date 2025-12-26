<?php
/* --------------------------------------------------------------
   FileSystemDetailsRepository.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Repositories;

use Gambio\AdminFeed\Services\ShopInformation\Mapper\FileSystemDetailsMapper;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\FileSystemDetails;

/**
 * Class FileSystemDetailsRepository
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Repositories
 */
class FileSystemDetailsRepository
{
    /**
     * @var FileSystemDetailsMapper
     */
    private $mapper;
    
    
    /**
     * FileSystemDetailsRepository constructor.
     *
     * @param FileSystemDetailsMapper $mapper
     */
    public function __construct(FileSystemDetailsMapper $mapper)
    {
        $this->mapper = $mapper;
    }
    
    
    /**
     * Returns the file system details.
     *
     * @return FileSystemDetails
     */
    public function getFileSystemDetails()
    {
        return $this->mapper->getFileSystemDetails();
    }
}