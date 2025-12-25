<?php
/* --------------------------------------------------------------
   ThemeDirectoryCollection.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ThemeDirectoryCollection
 */
class ThemeDirectoryCollection implements IteratorAggregate
{
    /**
     * @var \ThemeDirectory[]
     */
    protected $items;
    
    
    /**
     * ThemeDirectoryCollection constructor.
     *
     * @param \ThemeDirectory ...$themeDirectories Theme directories to be collected.
     */
    public function __construct(ThemeDirectory ...$themeDirectories)
    {
        if (count($themeDirectories) === 0) {
            throw new InvalidArgumentException('At least one arg of type "ThemeDirectory" must be provided!');
        }
        
        $this->items = $themeDirectories;
    }
    
    
    /**
     * Named constructor of ThemeDictoryCollection.
     *
     * @param \ThemeDirectory ...$themeDirectories Theme directories to be collected.
     *
     * @return \ThemeDirectoryCollection New instance.
     */
    public static function collect(ThemeDirectory ...$themeDirectories)
    {
        return MainFactory::create(static::class, ...$themeDirectories);
    }
    
    
    /**
     * Returns an array iterator of the internal collected theme directory instances.
     * This method is used php internally, so instances of this class can be used in foreach loops.
     *
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->items);
    }
}