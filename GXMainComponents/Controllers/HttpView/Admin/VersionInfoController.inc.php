<?php
/* --------------------------------------------------------------
   VersionInfoController.inc.php 2017-03-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

class VersionInfoController extends AdminHttpViewController
{
    public function actionDefault()
    {
        $languageTextManager = MainFactory::create('LanguageTextManager', 'version_history', $_SESSION['languages_id']);
        $title               = new NonEmptyStringType($languageTextManager->get_text('PAGE_TITLE'));
        $template            = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                                       . '/html/content/version_history.html'));
        
        /** @var VersionInfoService $versionInfoService */
        $versionInfoService = StaticGXCoreLoader::getService('VersionInfo');
        $versionInfoData    = MainFactory::create('KeyValueCollection',
                                                  ['version_info_items' => $versionInfoService->getAllVersionInfoItems()]);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $versionInfoData);
    }
}