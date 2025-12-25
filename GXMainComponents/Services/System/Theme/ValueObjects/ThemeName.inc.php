<?php
/* --------------------------------------------------------------
   ThemeName.inc.php 2018-11-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ThemeName
 */
class ThemeName implements ThemeNameInterface
{
    /**
     * @var string
     */
    protected $name;
    
    
    /**
     * ThemeName constructor.
     *
     * @param \StringType $name Theme name.
     */
    public function __construct(StringType $name)
    {
        $this->name = $name->asString();
    }
    
    
    /**
     * Named constructor of theme name.
     *
     * @param string $name Theme name.
     *
     * @return \ThemeName New instance.
     */
    public static function create($name)
    {
        return MainFactory::create(static::class, new StringType($name));
    }
    
    
    /**
     * Returns the theme name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}