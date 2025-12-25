<?php
/* --------------------------------------------------------------
   ThemeSettings.inc.php 2018-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ThemeSettings
 */
class ThemeSettings implements ThemeSettingsInterface
{
    /**
     * @var \ThemeDirectoryRootInterface
     */
    protected $source;
    
    /**
     * @var \ThemeDirectoryRootInterface
     */
    protected $destination;
    
    
    /**
     * ThemeSettings constructor.
     *
     * @param \ThemeDirectoryRootInterface $source      Root path of themes source directory.
     * @param \ThemeDirectoryRootInterface $destination Root path of themes destination directory.
     */
    public function __construct(ThemeDirectoryRootInterface $source, ThemeDirectoryRootInterface $destination)
    {
        $this->source      = $source;
        $this->destination = $destination;
    }
    
    
    /**
     * Named constructor of theme settings.
     *
     * @param \ThemeDirectoryRootInterface $source      Root path of themes source directory.
     * @param \ThemeDirectoryRootInterface $destination Root path of themes destination directory.
     *
     * @return \ThemeSettings New instance.
     */
    public static function create(ThemeDirectoryRootInterface $source, ThemeDirectoryRootInterface $destination)
    {
        return MainFactory::create(static::class, $source, $destination);
    }
    
    
    /**
     * Returns the path to the root of the theme's source directory.
     *
     * @return \ThemeDirectoryRootInterface
     */
    public function getSource()
    {
        return $this->source;
    }
    
    
    /**
     * Returns the path to the root of the theme's destination directory.
     *
     * @return \ThemeDirectoryRootInterface
     */
    public function getDestination()
    {
        return $this->destination;
    }
}