<?php

/*--------------------------------------------------------------------------------------------------
    VariableThemeDirectories.inc.php 2019-06-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class VariableThemeDirectories
 */
class VariableThemeDirectories extends BaseThemeDirectories implements VariableThemeDirectoriesInterface
{
    /**
     * @var \ThemeDirectory
     */
    protected $config;
    
    /**
     * variants directory
     *
     * @var ThemeDirectory|null
     */
    protected $variants;
    
    /**
     * @var \ThemeDirectory
     */
    protected $styleEdit;
    
    
    /**
     * VariableThemeDirectories constructor.
     *
     * @param ThemeDirectoryRootInterface $root
     */
    public function __construct(ThemeDirectoryRootInterface $root)
    {
        parent::__construct($root);
        $this->config    = ThemeDirectory::create($root->withPath('config'));
        $this->styleEdit = $this->tryBuildThemeDirectory($root, 'styles' . DIRECTORY_SEPARATOR . 'styleedit');
        $this->variants  = $this->tryBuildThemeDirectory($root, 'variants');
    }
    
    
    /**
     * Returns the variants directory
     *
     * @return ThemeDirectory
     */
    public function getVariants()
    {
        return $this->variants;
    }
    
    
    /**
     * Returns the style edit theme directory.
     *
     * @return \ThemeDirectory|null
     */
    public function getStyleEdit()
    {
        return $this->styleEdit;
    }
    
    
    /**
     * Returns the config theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getConfig()
    {
        return $this->config;
    }
    
}