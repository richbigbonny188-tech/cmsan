<?php

/*--------------------------------------------------------------------------------------------------
    ViewSettingsService.php 2019-09-12
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class ViewSettingsService
 */
class ViewSettingsService implements ViewSettingsServiceInterface
{
    /**
     * @var ViewSettingsReader
     */
    protected $reader;
    
    
    /**
     * ViewSettingsService constructor.
     *
     * @param ViewSettingsReaderInterface $reader
     */
    public function __construct(ViewSettingsReaderInterface $reader)
    {
        $this->reader = $reader;
    }
    
    
    /**
     * @return ViewSettings
     */
    public function get(): ViewSettings
    {
        return $this->reader->get();
    }
    
}