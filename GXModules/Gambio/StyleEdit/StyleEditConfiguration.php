<?php
/* --------------------------------------------------------------
	StyleEditController.inc.php 2019-03-14
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
--------------------------------------------------------------
*/

namespace Gambio\StyleEdit;

use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\SingletonStrategyInterface;

/**
 * Class StyleEditConfiguration
 * @package Gambio\StyleEdit
 */
class StyleEditConfiguration implements SingletonStrategyInterface
{
    /**
     * @var string
     */
    protected $themesFolderPath;
    
    /**
     * @var string
     */
    protected $uploadFolderPath;
    
    /**
     * @var string
     */
    protected $tmpFolderPath;
    
    /**
     * @var string
     */
    protected $publicFolderPath;
    
    /**
     * @var string
     */
    protected $baseFolderPath;
    
    
    /**
     * StyleEditConfiguration constructor.
     */
    public function __construct()
    {
        $this->baseFolderPath   = realpath(__DIR__ . '/../../../') . DIRECTORY_SEPARATOR;
        $this->themesFolderPath = realpath(__DIR__ . '/../../../themes/') . DIRECTORY_SEPARATOR;
        $this->publicFolderPath = realpath(__DIR__ . '/../../../public') . DIRECTORY_SEPARATOR;
        $this->uploadFolderPath = realpath(__DIR__ . '/../../../uploads/') . DIRECTORY_SEPARATOR;
        $this->tmpFolderPath    = realpath($this->uploadFolderPath . '/tmp/') . DIRECTORY_SEPARATOR;
    }
    
    
    /**
     * @return string
     */
    public function themesFolderPath(): string
    {
        return $this->themesFolderPath;
    }
    
    
    /**
     * @return string
     */
    public function baseFolderPath(): string
    {
        return $this->baseFolderPath;
    }
    
    
    /**
     * @return string
     */
    public function uploadFolderPath(): string
    {
        return $this->uploadFolderPath;
    }
    
    
    /**
     * @return string
     */
    public function publicFolderPath()
    {
        return $this->publicFolderPath;
    }
    
    
    /**
     * @return string
     */
    public function tmpFolderPath(): string
    {
        return $this->tmpFolderPath;
    }
}