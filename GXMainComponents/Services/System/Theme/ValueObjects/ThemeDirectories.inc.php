<?php

/* --------------------------------------------------------------
   ThemeDirectories.inc.php 2019-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

/**
 * Class ThemeDirectories
 */
class ThemeDirectories extends VariableThemeDirectories implements ThemeDirectoriesInterface
{
    use CustomThemeDirectoriesTrait;
    /**
     * Directory root
     *
     * @var ThemeDirectoryRootInterface
     */
    protected $root;
    
    
    /**
     * ThemeDirectories constructor.
     *
     * @param \ThemeDirectoryRootInterface $root Path to root directory of theme.
     */
    public function __construct(ThemeDirectoryRootInterface $root)
    {
        parent::__construct($root);
        $this->root         = $root;
        $this->config       = ThemeDirectory::create($root->withPath('config'));
        $this->customHtml   = $this->tryBuildThemeDirectory($root, 'html' . DIRECTORY_SEPARATOR . 'custom');
        $this->customJs     = $this->tryBuildThemeDirectory($root, 'javascripts' . DIRECTORY_SEPARATOR . 'custom');
        $this->jsExtensions = $this->tryBuildThemeDirectory($root, 'javascripts' . DIRECTORY_SEPARATOR . 'extensions');
        $this->customStyles = $this->tryBuildThemeDirectory($root, 'styles' . DIRECTORY_SEPARATOR . 'custom');
    }
    
    
    /**
     * Named constructor of ThemeDirectories.
     *
     * @param \ThemeDirectoryRootInterface $root Path to root directory of theme.
     *
     * @return \ThemeDirectoriesInterface New instance.
     */
    public static function create(ThemeDirectoryRootInterface $root)
    {
        return MainFactory::create(static::class, $root);
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
     * Return the directory root
     *
     * @return ThemeDirectoryRootInterface
     */
    public function getRoot()
    {
        return $this->root;
    }
    
    
}