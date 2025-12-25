<?php
/* --------------------------------------------------------------
   ApplicationStyleEditApi.inc.php 2021-04-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\GX;
require_once __DIR__ . '/Application.inc.php';
require_once __DIR__.'/FakeSessionHandler.inc.php';

use MainFactory;
use StaticGXCoreLoader;

class ApplicationStyleEditApi extends Application
{

    protected function startSession()
    {
        parent::startSession();
        
        // The API only needs to read the session so it can be closed for writing avoiding session lock problems.
        session_write_close();
    }

    protected function setSessionObjects(){}

    protected function handlePageSpecificRequests() {}

    protected function setUpFrontend(){
        $currentTheme = StaticGXCoreLoader::getThemeControl()->getCurrentTheme();
        $GLOBALS['coo_template_control'] = MainFactory::create_object('TemplateControl', [$currentTheme], true);
    }
}
