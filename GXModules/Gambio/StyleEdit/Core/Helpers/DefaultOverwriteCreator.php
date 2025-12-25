<?php
/* --------------------------------------------------------------
  DefaultOverwriteCreator.php 2019-10-15
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Helpers;

use FilesystemAdapter;
use Gambio\StyleEdit\Core\Json\InheritanceHandler;
use stdClass;

/**
 * Class DefaultOverwriteCreator
 */
class DefaultOverwriteCreator
{
    public const OVERWRITE_DIRECTORY_NAME = 'theme_extensions';
    
    /**
     * @var string
     */
    protected $themeId;
    
    /**
     * @var FilesystemAdapter
     */
    protected $themeFilesystem;
    
    /**
     * @var ConvertSettingsToDefaultValueThemeExtensions
     */
    protected $converter;
    
    
    /**
     * DefaultOverwriteCreator constructor.
     *
     * @param string                                       $themeId
     * @param FilesystemAdapter                            $themeFilesystem
     * @param ConvertSettingsToDefaultValueThemeExtensions $converter
     */
    public function __construct(
        string $themeId,
        FilesystemAdapter $themeFilesystem,
        ConvertSettingsToDefaultValueThemeExtensions $converter
    ) {
        $this->themeId         = $themeId;
        $this->themeFilesystem = $themeFilesystem;
        $this->converter       = $converter;
    }
    
    
    public function store(): void
    {
        foreach ($this->converter->convert() as $id => $default) {
            
            $overwritePath         = $this->themeId . DIRECTORY_SEPARATOR . self::OVERWRITE_DIRECTORY_NAME
                                     . DIRECTORY_SEPARATOR . $id . '.json';
            $value                 = new stdClass;
            $value->extension_type = InheritanceHandler::MERGE;
            $value->default        = $default;
            
            if ($this->themeFilesystem->has($overwritePath) === false) {
                
                $this->themeFilesystem->write($overwritePath, json_encode($value));
            } else {
                $this->themeFilesystem->delete($overwritePath);
                $this->themeFilesystem->write($overwritePath, json_encode($value));
            }
        }
    }
}