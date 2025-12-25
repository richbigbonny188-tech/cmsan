<?php
/* --------------------------------------------------------------
  StyleEditApplication.php 2021-08-12
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\GX;

/**
 * Class StyleEditApplication
 */
class StyleEditApplication extends \Gambio\GX\Application
{
    /**
     * @return bool
     */
    protected function isLanguageCodeForUrlsActivated(): bool
    {
        return false;
    }
    
    public function redirectToFrontEnd(): void
    {
        xtc_redirect(xtc_href_link(FILENAME_DEFAULT, '', ''));
    }
    
    public function redirectTemplateSettings(): void
    {
        $templateSettingUrl = xtc_href_link(FILENAME_DEFAULT, '', '') . 'admin/admin.php?do=TemplateConfiguration';
        xtc_redirect($templateSettingUrl);
    }
    
    
    protected function startSession()
    {
        parent::startSession();
        
        if (isset($_SESSION['customers_status']['customers_status_id'])
            && $_SESSION['customers_status']['customers_status_id'] === '0') {
            $_SESSION['style_edit_started'] = true;
        }
    }
}