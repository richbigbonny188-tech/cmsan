<?php
/* --------------------------------------------------------------
   MainTheme.inc.php 2019-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class MainTheme
 */
class MainTheme implements MainThemeInterface
{
    /**
     * @var \ThemeId
     */
    protected $id;
    
    /**
     * @var \MainThemeDirectoriesInterface
     */
    protected $directories;
    
    
    /**
     * MainTheme constructor.
     *
     * @param \ThemeId                       $id          Theme identifier (the themes name).
     * @param \MainThemeDirectoriesInterface $directories Theme directories.
     */
    public function __construct(ThemeId $id, MainThemeDirectoriesInterface $directories)
    {
        $this->id          = $id;
        $this->directories = $directories;
    }
    
    
    /**
     * Named constructor of MainTheme.
     *
     * @param \ThemeId                       $id          Theme identifier (the themes name).
     * @param \MainThemeDirectoriesInterface $directories Theme directories.
     *
     * @return \MainTheme New instance.
     */
    public static function create(ThemeId $id, MainThemeDirectoriesInterface $directories)
    {
        return MainFactory::create(static::class, $id, $directories);
    }
    
    
    /**
     * Returns the root theme id.
     *
     * @return \ThemeId
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Returns the config theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getConfig()
    {
        return $this->directories->getConfig();
    }
    
    
    /**
     * Returns the html theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getHtml()
    {
        return $this->directories->getHtml();
    }
    
    
    /**
     * Returns the fonts theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getFonts()
    {
        return $this->directories->getFonts();
    }
    
    
    /**
     * Returns the js theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getJs()
    {
        return $this->directories->getJs();
    }
    
    
    /**
     * Returns the styles theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getStyles()
    {
        return $this->directories->getStyles();
    }
    
    
    /**
     * Returns the style edit theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getStyleEdit()
    {
        return $this->directories->getStyleEdit();
    }
    
    
    /**
     * Returns the images theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getImages()
    {
        return $this->directories->getImages();
    }
    
    
    /**
     * Returns the variants directory
     *
     * @return ThemeDirectory|null
     */
    public function getVariants()
    {
        return $this->directories->getVariants();
    }
    
    
    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->directories->getPrefix();
    }
    
    
    /**
     * Returns the directory root.
     *
     * @return ThemeDirectoryRootInterface
     */
    public function getRoot()
    {
        return $this->directories->getRoot();
    }
}