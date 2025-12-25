<?php

/*--------------------------------------------------------------------------------------------------
    ThemeDBSettings.php 2023-03-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class ViewSettings
 */
class ViewSettings
{
    protected $type;
    protected $name;
    
    
    /**
     * ThemeDBSettings constructor.
     *
     * @param string $type
     * @param string $name
     */
    public function __construct(string $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;
    }
    
    
    /**
     * @return bool
     */
    public function isThemeSystemActive(): bool
    {
        return $this->type === 'theme';
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
}