<?php
/**
 * ThemeSectionJavascriptCacheBuilder.inc.php 2021-01-21
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2021 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

/**
 * Class ThemeSectionJavascriptCacheBuilder
 */
class ThemeSectionJavascriptCacheBuilder
{
    /**
     * ThemeSectionJavascriptCacheBuilder constructor.
     */
    public function __construct()
    {
        if (file_exists($this->resultDirectoryPath()) === false
            && !mkdir($concurrentDirectory = $this->resultDirectoryPath(), 0777,true)
            && !is_dir($concurrentDirectory)) {
            
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }
    
    
    /**
     * @param string   $section
     * @param string[] $scriptPaths
     *
     * @return string
     */
    public function createCacheFile(string $section, array $scriptPaths): string
    {
        $section       = $section === '' ? 'global' : strtolower($section);
        $fileName      = $this->resultDirectoryPath() . $section . '.js';
        $scriptContent = '';
        
        if (file_exists($fileName) === false) {
            
            foreach ($scriptPaths as $path) {
                
                $scriptContent .= file_get_contents($path);
            }
            
            file_put_contents($fileName, $scriptContent);
            chmod($fileName, 0777);
        }
        
        return $fileName;
    }
    
    
    /**
     * @return string
     */
    protected function resultDirectoryPath(): string
    {
        return dirname(__DIR__, 3) . '/public/theme/javascripts/system/Build/';
    }
}