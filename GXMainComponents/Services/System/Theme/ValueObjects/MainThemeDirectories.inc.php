<?php
/* --------------------------------------------------------------
   MainThemeDirectories.inc.php 2019-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class MainThemeDirectories
 */
class MainThemeDirectories extends VariableThemeDirectories implements MainThemeDirectoriesInterface
{
    /**
     * @var \ThemeDirectory
     */
    protected $fonts;
    
    /**
     * @var \ThemeDirectory
     */
    protected $variant;
    
    
    /**
     * MainThemeDirectories constructor.
     *
     * @param \ThemeDirectoryRootInterface $root Path to root of theme.
     */
    public function __construct(ThemeDirectoryRootInterface $root)
    {
        parent::__construct($root);
        $this->root    = $root;
        $this->fonts   = ThemeDirectory::create($root->withPath('fonts'));
        $this->variant = $this->tryBuildThemeDirectory($root, 'variants');
    }
    
    
    /**
     * Named constructor of MainThemeDirectories.
     *
     * @param \ThemeDirectoryRootInterface $root
     *
     * @return \MainThemeDirectories New instance.
     */
    public static function create(ThemeDirectoryRootInterface $root)
    {
        return MainFactory::create(static::class, $root);
    }
    
    
    /**
     * Returns the fonts theme directory.
     *
     * @return \ThemeDirectoryInterface
     */
    public function getFonts()
    {
        return $this->fonts;
    }
    
    
    /**
     * Returns the variants directory
     *
     * @return ThemeDirectory|null
     */
    public function getVariants()
    {
        return $this->variant;
    }
}