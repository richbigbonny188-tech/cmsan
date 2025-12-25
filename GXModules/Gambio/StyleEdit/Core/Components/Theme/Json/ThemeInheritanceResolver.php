<?php
/*--------------------------------------------------------------------------------------------------
    ThemeInheritanceResolver.php 2019-10-11
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme\Json;

use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\Core\Json\InheritanceHandler;
use Gambio\StyleEdit\Core\Json\Interfaces\InheritanceResolverInterface;

/**
 * Class ThemeExtendsPropertyResolver
 * @package Gambio\StyleEdit\Core\Json
 */
class ThemeInheritanceResolver implements InheritanceResolverInterface
{
    /**
     * @var FileIO
     */
    protected $fileIO;
    
    
    /**
     * ThemeExtendsPropertyResolver constructor.
     *
     * @param FileIO $fileIO
     */
    public function __construct(FileIO $fileIO)
    {
        $this->fileIO = $fileIO;
    }
    
    
    /**
     * @param $settings
     *
     * @param $sourceFilename
     *
     * @return string
     * @throws \Exception
     */
    public function resolveInheritanceFileName($settings, $sourceFilename): string
    {
        $inheritSettings = is_object($settings) ? ($settings->from ?? 'PARENT') : $settings;
        $dirname         = dirname($sourceFilename,2) . DIRECTORY_SEPARATOR;
        
        if (strtoupper($inheritSettings) === 'PARENT') {
            $sourceObject = $this->fileIO->read($sourceFilename);
            
            return $dirname . $sourceObject->extends . DIRECTORY_SEPARATOR . 'theme.json';
        } else {
            return $dirname . $inheritSettings . DIRECTORY_SEPARATOR . 'theme.json';
        }
    }
    
    
    /**
     * @param $settings
     *
     * @param $sourceFilename
     *
     * @return string
     */
    public function resolveInheritanceType($settings, $sourceFilename): string
    {
        return is_object($settings) ? ($settings->type ?? InheritanceHandler::MERGE) : InheritanceHandler::MERGE;
    }
    
    
    /**
     * @return string
     */
    public function jsonExtensionFolder(): string
    {
        return 'theme_extensions/';
    }

    /**
     * @param $settings
     * @param $resourcePath
     * @param $sourceFilename
     * @return mixed
     */
    public function getResourceRelativePath($settings, $resourcePath, $sourceFilename)
    {
        // TODO: Implement getResourceRelativePath() method.
    }
}