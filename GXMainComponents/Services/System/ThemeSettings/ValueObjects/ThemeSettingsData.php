<?php
/*--------------------------------------------------------------------------------------------------
    ThemeSettingsData.php 2020-02-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\GX\Services\System\ThemeSettings\ValueObjects;


use ExistingDirectory;
use ExistingFile;
use Gambio\GX\Services\System\ThemeSettings\ValueObjects\Interfaces\ThemeSettingsDataInterface;

class ThemeSettingsData implements ThemeSettingsDataInterface
{
    /**
     * @var ExistingFile
     */
    protected $filename;
    
    /**
     * ThemeSettingsData constructor.
     *
     * @param ExistingFile $themeSettingsFile
     */
    public function __construct(ExistingFile $themeSettingsFile)
    {
        $this->filename = $themeSettingsFile;
    
    }

    /**
     * @inheritDoc
     */
    public function filename(): ExistingFile
    {
        return $this->filename;
    }
    
}