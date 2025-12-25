<?php
/* --------------------------------------------------------------
   ThemeId.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ThemeId
 */
class ThemeId
{
    /**
     * @var string
     */
    protected $id;
    
    
    /**
     * ThemeId constructor.
     *
     * @param $themeId
     */
    public function __construct($themeId)
    {
        $this->id = $themeId;
    }
    
    
    /**
     * Named constructor of ThemeId.
     *
     * @param string $themeId Identifier for themes.
     *
     * @return \ThemeId New instance.
     */
    public static function create($themeId)
    {
        return MainFactory::create(static::class, $themeId);
    }
    
    
    /**
     * Returns the theme id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Checks if the given theme id is equal to this one.
     *
     * @param \ThemeId $id
     *
     * @return bool
     */
    public function equals(ThemeId $id)
    {
        return $this->id === $id->getId();
    }
}
