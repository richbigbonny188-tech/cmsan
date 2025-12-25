<?php
/**
 * ZipExtractHelper.php 2019-12-11
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2019 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

namespace Gambio\StyleEdit\Core\Helpers;

use ZipArchive;
use InvalidArgumentException;
use Gambio\StyleEdit\Core\Helpers\Interfaces\ArchiveExtractInterface;

class ZipExtractHelper implements ArchiveExtractInterface
{
    
    /**
     * @var ZipArchive
     */
    private $zip;
    
    
    public function __construct()
    {
        $this->zip = new ZipArchive;
    }
    
    
    /**
     * @inheritDoc
     */
    public function extractArchive($resourceFile, string $extractPath) : string
    {
        $file = $this->zip->open($resourceFile);
        if ($file !== true) {
            throw new InvalidArgumentException("Invalid file type.");
        }
        
        $this->zip->extractTo($extractPath);
        $this->zip->close();
        
        return $extractPath;
    }
    
}