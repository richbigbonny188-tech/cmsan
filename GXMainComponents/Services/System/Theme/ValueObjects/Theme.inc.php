<?php

/* --------------------------------------------------------------
   Theme.php 2019-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

/**
 * Class Theme
 */
class Theme implements ThemeInterface
{
    /**
     * Theme ID
     *
     * @var ThemeId
     */
    protected $id;
    
    /**
     * Theme directories
     *
     * @var ThemeDirectoriesInterface
     */
    protected $directories;
    
    /**
     * Parent theme
     *
     * @var null|Theme
     */
    protected $parentTheme;
    
    
    /**
     * Theme constructor.
     *
     * @param ThemeId                   $id          Theme ID
     * @param ThemeDirectoriesInterface $directories Theme directories
     * @param ThemeInterface|null       $parentTheme Parent theme
     */
    public function __construct(
        ThemeId $id,
        ThemeDirectoriesInterface $directories,
        ThemeInterface $parentTheme = null
    ) {
        $this->id          = $id;
        $this->directories = $directories;
        $this->parentTheme = $parentTheme;
    }
    
    
    /**
     * Create theme instance
     *
     * @param ThemeId                   $id          Theme ID
     * @param ThemeDirectoriesInterface $directories Theme directories
     * @param ThemeInterface|null       $parentTheme Parent theme
     *
     * @return Theme New instance.
     */
    public static function create(
        ThemeId $id,
        ThemeDirectoriesInterface $directories,
        ThemeInterface $parentTheme = null
    ) {
        return MainFactory::create(static::class, $id, $directories, $parentTheme);
    }
    
    
    /**
     * Return whether there is a parent theme
     *
     * @return bool
     */
    public function hasParent()
    {
        return ($this->parentTheme !== null);
    }
    
    
    /**
     * Convert to main theme
     *
     * @return \MainThemeInterface
     */
    public function toMainTheme()
    {
        if ($this->hasParent()) {
            throw new \RuntimeException('Only possible if there is no parent');
        }
        
        return MainTheme::create($this->id, MainThemeDirectories::create($this->getRoot()));
    }
    
    
    /**
     * Returns the theme id
     *
     * @return ThemeId
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Return the parent theme
     *
     * @return Theme|null
     */
    public function getParentTheme()
    {
        return $this->parentTheme;
    }
    
    
    /**
     * Returns the directory. root
     *
     * @return ThemeDirectoryRootInterface
     */
    public function getRoot()
    {
        return $this->directories->getRoot();
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
     * Returns the HTML directory.
     *
     * @return ThemeDirectory|null
     */
    public function getHtml()
    {
        return $this->directories->getHtml();
    }
    
    
    /**
     * Returns the scripts directory.
     *
     * @return ThemeDirectory|null
     */
    public function getJs()
    {
        return $this->directories->getJs();
    }
    
    
    /**
     * Returns the styles directory.
     *
     * @return ThemeDirectory|null
     */
    public function getStyles()
    {
        return $this->directories->getStyles();
    }
    
    
    /**
     * Returns the style edit theme directory.
     *
     * @return \ThemeDirectory|null
     */
    public function getStyleEdit()
    {
        return $this->directories->getStyleEdit();
    }
    
    
    /**
     * Returns the images directory.
     *
     * @return \ThemeDirectory|null
     */
    public function getImages()
    {
        return $this->directories->getImages();
    }
    
    
    /**
     * Returns the fonts directory.
     *
     * @return \ThemeDirectory|null
     */
    public function getFonts()
    {
        return $this->directories->getFonts();
    }
    
    
    /**
     * Returns the custom HTML directory.
     *
     * @return ThemeDirectory|null
     */
    public function getCustomHtml()
    {
        return $this->directories->getCustomHtml();
    }
    
    
    /**
     * Returns the custom scripts directory.
     *
     * @return ThemeDirectory|null
     */
    public function getCustomJs()
    {
        return $this->directories->getCustomJs();
    }
    
    
    /**
     * Returns the custom styles directory.
     *
     * @return ThemeDirectory|null
     */
    public function getCustomStyles()
    {
        return $this->directories->getCustomStyles();
    }
    
    
    /**
     * Returns the script extension directory
     *
     * @return ThemeDirectory|null
     */
    public function getJsExtensions()
    {
        return $this->directories->getJsExtensions();
    }
    
    
    /**
     * Returns the variants directory
     *
     * @return ThemeDirectoriesInterface|null
     */
    public function getVariants()
    {
        return $this->directories->getVariants();
    }
    
    
    /**
     * Returns the variants directory
     *
     * @return ThemeDirectoriesInterface|null
     */
    public function getPrefix()
    {
        return $this->directories->getPrefix();
    }
}