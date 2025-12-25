<?php
/*--------------------------------------------------------------------------------------------------
    Validator.php 2019-10-14
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme;
use Gambio\StyleEdit\Core\Components\Theme\Validator;


class UploadValidator extends Validator
{
    
    /**
     * @inheritDoc
     */
    protected function getThemesFolder(): string
    {
        return $this->styleEditConfiguration->tmpFolderPath();
    }
    
    /**
     * @inheritDoc
     */
    protected function validateThemeJson() : void
    {
        $themeJsonPath = $this->themeName . DIRECTORY_SEPARATOR . 'theme.json';
        
        if (!$this->filesystemThemes->has($themeJsonPath)) {
            
            $this->themeCanBeOpenedInStyleEdit4 = false;
        }
        
        return;
    }
}