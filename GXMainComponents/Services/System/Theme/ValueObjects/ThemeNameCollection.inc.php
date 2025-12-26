<?php
/* --------------------------------------------------------------
   ThemeNameCollection.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ThemeNameCollection
 */
class ThemeNameCollection implements IteratorAggregate
{
    /**
     * @var ThemeNameInterface[]
     */
    protected $names;
    
    
    /**
     * ThemeNameCollection constructor.
     *
     * @param \ThemeName ...$names Theme names.
     */
    public function __construct(ThemeName ...$names)
    {
        if (count($names) === 0) {
            throw new InvalidArgumentException('At least one ThemeName must be provided');
        }
        
        $this->names = $names;
    }
    
    
    /**
     * Named constructor of ThemeNameCollection.
     *
     * @param \ThemeName ...$names Theme names.
     *
     * @return \ThemeNameCollection New instance.
     */
    public static function collect(ThemeName ...$names)
    {
        return MainFactory::create(static::class, ...$names);
    }
    
    
    /**
     * Returns an array iterator of the internal collected theme name instances.
     * This method is used php internally, so instances of this class can be used in foreach loops.
     *
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->names);
    }
}