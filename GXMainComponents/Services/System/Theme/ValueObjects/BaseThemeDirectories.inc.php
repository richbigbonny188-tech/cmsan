<?php
/*--------------------------------------------------------------------------------------------------
    BaseThemeDirectories.inc.php 2019-06-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class BaseThemeDirectories
 */
class BaseThemeDirectories implements BaseThemeDirectoriesInterface
{
    /**
     * Fonts directory
     *
     * @var ThemeDirectory|null
     */
    protected $fonts;
    
    /**
     * @var \ThemeDirectory
     */
    protected $html;
    
    /**
     * @var \ThemeDirectory
     */
    protected $js;
    
    /**
     * @var \ThemeDirectory
     */
    protected $styles;
    
    
    /**
     * @var \ThemeDirectory
     */
    protected $images;
    
    /**
     * @var string
     */
    protected $prefix;
    
    /**
     * @var ThemeDirectoryRootInterface
     */
    protected $root;
    
    
    /**
     * BaseThemeDirectories constructor.
     *
     * @param ThemeDirectoryRootInterface $root
     */
    public function __construct(ThemeDirectoryRootInterface $root)
    {
        $rootPath     = explode(DIRECTORY_SEPARATOR, $root->getPath());
        $prefix       = end($rootPath);
        $this->prefix = $prefix;
        $this->root   = $root;
        $this->html   = $this->tryBuildThemeDirectory($root, 'html' . DIRECTORY_SEPARATOR . 'system');
        $this->js     = $this->tryBuildThemeDirectory($root, 'javascripts' . DIRECTORY_SEPARATOR . 'system');
        $this->styles = $this->tryBuildThemeDirectory($root, 'styles' . DIRECTORY_SEPARATOR . 'system');
        $this->images = $this->tryBuildThemeDirectory($root, 'images');
        $this->fonts  = $this->tryBuildThemeDirectory($root, 'fonts');
    }
    
    
    /**
     * Tries to create an instance of ThemeDirectory by the given root- and sub path.
     * If no directory was found, null will be returned.
     *
     * @param \ThemeDirectoryRootInterface $root Path to root directory of theme.
     * @param string                       $path Sub directory path.
     *
     * @return null|\ThemeDirectory Theme directory instance if given path exists.
     */
    protected function tryBuildThemeDirectory(ThemeDirectoryRootInterface $root, $path)
    {
        $a = rtrim($root->getPath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $path;
        
        return is_dir($a) ? ThemeDirectory::create($root->withPath($path)) : null;
    }
    
    
    /**
     * Returns the html theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getHtml()
    {
        return $this->html;
    }
    
    
    /**
     * Returns the js theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getJs()
    {
        return $this->js;
    }
    
    
    /**
     * Returns the styles theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getStyles()
    {
        return $this->styles;
    }
    
    
    /**
     * Returns the images directory.
     *
     * @return \ThemeDirectory|null
     */
    public function getImages()
    {
        return $this->images;
    }
    
    
    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
    
    
    /**
     * Returns the fonts directory.
     *
     * @return \ThemeDirectory|null
     */
    public function getFonts()
    {
        return $this->fonts;
    }
    
    
    /**
     * Returns the directory root.
     *
     * @return ThemeDirectoryRootInterface
     */
    public function getRoot()
    {
        return $this->root;
    }
}