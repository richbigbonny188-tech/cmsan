<?php

/*--------------------------------------------------------------------------------------------------
    VariantDirectories.inc.php 2019-06-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class VariantDirectories
 */
class VariantDirectories extends BaseThemeDirectories implements VariantDirectoriesInterface
{
    use CustomThemeDirectoriesTrait;
    
    /**
     * Theme ID
     *
     * @var ThemeId
     */
    protected $id;
    
    
    /**
     * VariantDirectories constructor.
     *
     * @param ThemeDirectoryRootInterface $root
     */
    public function __construct(ThemeDirectoryRootInterface $root)
    {
        parent::__construct($root);
        $name               = end(explode(DIRECTORY_SEPARATOR, $root->getPath()));
        $this->id           = new ThemeId($name);
        $this->prefix       = $name;
        $this->customHtml   = $this->tryBuildThemeDirectory($root, 'html' . DIRECTORY_SEPARATOR . 'custom');
        $this->customJs     = $this->tryBuildThemeDirectory($root, 'javascripts' . DIRECTORY_SEPARATOR . 'custom');
        $this->jsExtensions = $this->tryBuildThemeDirectory($root, 'javascripts' . DIRECTORY_SEPARATOR . 'extensions');
        $this->customStyles = $this->tryBuildThemeDirectory($root, 'styles' . DIRECTORY_SEPARATOR . 'custom');
    }
    
    
    /**
     * @param ThemeDirectoryRootInterface $root
     *
     * @return bool|mixed|VariantDirectories
     */
    public static function create(ThemeDirectoryRootInterface $root)
    {
        return MainFactory::create(static::class, $root);
    }
    
    
    /**
     * @param ThemeDirectoryRootInterface $root
     * @param ThemeId                     $themeId
     *
     * @return VariantDirectories
     */
    public static function createWithCustomPrefix(ThemeDirectoryRootInterface $root, ThemeId $themeId)
    {
        
        $result         = self:: create($root);
        $result->prefix = $themeId->getId() . ucfirst($result->getPrefix());
        
        return $result;
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
}