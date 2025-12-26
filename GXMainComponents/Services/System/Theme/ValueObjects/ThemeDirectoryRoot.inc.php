<?php
/* --------------------------------------------------------------
   ThemeDirectoryRoot.inc.php 2019-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ThemeDirectoryRoot
 */
class ThemeDirectoryRoot implements ThemeDirectoryRootInterface
{
    /**
     * @var string
     */
    protected $path;
    
    
    /**
     * ThemeDirectoryRoot constructor.
     *
     * @param \ExistingDirectory $root Absolute path to theme directories root.
     */
    public function __construct(ExistingDirectory $root)
    {
        $this->path = $root->getAbsolutePath();
    }
    
    
    /**
     * Named constructor of ThemeDirectoryRoot.
     *
     * @param \ExistingDirectory $root Absolute path to theme directories root.
     *
     * @return \ThemeDirectoryRoot New instance.
     */
    public static function create(ExistingDirectory $root)
    {
        return MainFactory::create(static::class, $root);
    }
    
    
    /**
     * Returns an absolute path, which represents a theme directory.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
    
    /**
     * Appends the given path to the internal theme path and returns a new ThemeDirectoryRoot instance.
     *
     * @param string $path Path to append.
     *
     * @return \ThemeDirectoryRootInterface New instance.
     */
    public function withPath($path)
    {
        $path = new ExistingDirectory(rtrim($this->path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $path);
        
        return static::create($path);
    }
    
    
    /**
     * @param string $path path to be checked
     *
     * @return mixed
     */
    public function hasPath($path)
    {
        return is_dir(rtrim($this->path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $path);
    }
}