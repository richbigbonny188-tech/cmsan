<?php
/* --------------------------------------------------------------
   ThemeDirectory.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ThemeDirectory
 */
class ThemeDirectory implements ThemeDirectoryInterface
{
    /**
     * @var \ThemeDirectoryRootInterface
     */
    protected $root;
    
    /**
     * @var string[]
     */
    protected $files = [];
    
    /**
     * @var \ThemeDirectoryCollection|null
     */
    protected $children;
    
    
    /**
     * ThemeDirectory constructor.
     *
     * @param \ThemeDirectoryRootInterface $directoryRoot Absolute path to root of theme directory.
     */
    public function __construct(ThemeDirectoryRootInterface $directoryRoot)
    {
        $this->root  = $directoryRoot;
        $iterator    = new IteratorIterator(new DirectoryIterator($this->root->getPath()));
        $directories = [];
        
        foreach ($iterator as $dir) {
            /** @var \DirectoryIterator $dir */
            if ($dir->isFile()) {
                $this->files[] = $dir->getFilename();
            }
            if ($dir->isDir() && !$dir->isDot()) {
                $root          = ThemeDirectoryRoot::create(new ExistingDirectory($dir->getPathname()));
                $directories[] = ThemeDirectory::create($root);
            }
        }
        if (count($directories) > 0) {
            $this->children = ThemeDirectoryCollection::collect(...$directories);
        }
    }
    
    
    /**
     * Named constructor of ThemeDirectory.
     *
     * @param \ThemeDirectoryRootInterface $directoryRoot Absolute path to root of theme directory.
     *
     * @return \ThemeDirectory New instance.
     */
    public static function create(ThemeDirectoryRootInterface $directoryRoot)
    {
        return MainFactory::create(static::class, $directoryRoot);
    }
    
    
    /**
     * Returns the theme directories root path.
     *
     * @return \ThemeDirectoryRootInterface
     */
    public function getRoot()
    {
        return $this->root;
    }
    
    
    /**
     * Returns a list of all files in the current theme (non-recursively).
     *
     * @return string[]
     */
    public function getFiles()
    {
        return $this->files;
    }
    
    
    /**
     * Returns a list of all directories in the current theme (non-recursively).
     *
     * @return \ThemeDirectoryCollection|null
     */
    public function getChildren()
    {
        return $this->children;
    }
}