<?php
/* --------------------------------------------------------------
   ThemeReader.inc.php 2018-11-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ThemeReader
 */
class ThemeReader implements ThemeReaderInterface
{
    /**
     * Returns a theme instance by the given id and source directory.
     *
     * @param ThemeId                     $id     Theme id, the identifier is the theme name.
     * @param ThemeDirectoryRootInterface $source Path to themes source directory.
     *
     * @return ThemeInterface
     */
    public function getTheme(ThemeId $id, ThemeDirectoryRootInterface $source)
    {
        $themeRoot = $source->withPath($id->getId());
        
        $configFile = $themeRoot->getPath() . DIRECTORY_SEPARATOR . 'theme.json';
        
        if (!file_exists($configFile)) {
            throw new RuntimeException('Theme configuration file "' . $configFile . '" not found!');
        }
        $config = json_decode(file_get_contents($configFile), true);
        
        if (null === $config) {
            throw new RuntimeException('Theme configuration file is not a valid json!');
        }
    
        $parentTheme = array_key_exists('extends',
                                        $config)
                       && !empty($config['extends']) ? $parentTheme = $this->getTheme(ThemeId::create($config['extends']),
                                                                                      $source) : null;
        
        $themeDirectories = ThemeDirectories::create($themeRoot);
        
        return Theme::create($id, $themeDirectories, $parentTheme);
    }
    
    
    /**
     * Returns a list of names of all available themes.
     *
     * @param ThemeDirectoryRootInterface $source
     *
     * @return ThemeNameCollection
     */
    public function getAvailableThemes(ThemeDirectoryRootInterface $source)
    {
        $asd     = new IteratorIterator(new DirectoryIterator($source->getPath()));
        $exclude = ['.', '..'];
        $names   = [];
        
        foreach ($asd as $dir) {
            /** @var SplFileInfo $dir */
            if (!in_array($dir->getFilename(), $exclude) && $dir->isDir()) {
                $lookupConfig = $dir->getPathname() . '/theme.json';
                $lookupConfig = str_replace('/', DIRECTORY_SEPARATOR, $lookupConfig);
                
                if (file_exists($lookupConfig) && $configJson = json_decode(file_get_contents($lookupConfig), true)) {
                    if (array_key_exists('id', $configJson)) {
                        $names[$configJson['id']] = ThemeName::create($configJson['id']);
                    }
                }
            }
        }
        asort($names);
        $names = array_values($names);
        
        return ThemeNameCollection::collect(...$names);
    }
}