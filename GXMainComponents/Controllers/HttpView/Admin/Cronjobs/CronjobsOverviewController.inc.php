<?php
/* --------------------------------------------------------------
   CronjobsOverviewController.inc.php 2022-10-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CronjobsOverviewController extends AdminHttpViewController
{
    /**
     * @var \LanguageTextManager
     */
    protected $languageTextManager;
    
    /**
     * @var \NonEmptyStringType
     */
    protected $title;
    
    /**
     * @var \ExistingFile
     */
    protected $template;
    
    
    /**
     * Initialize Controller
     */
    public function init()
    {
        $this->languageTextManager = MainFactory::create('LanguageTextManager', 'cronjobs', $_SESSION['languages_id']);
        $this->title               = new NonEmptyStringType($this->languageTextManager->get_text('title'));
        $this->template            = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                                             . '/html/content/cronjobs/overview.html'));
    }
    
    
    /**
     * Renders the cronjob configuration ui.
     *
     * @return \HttpControllerResponseInterface
     */
    public function actionDefault()
    {
        $assets = MainFactory::create('AssetCollection');
        $assets->add(MainFactory::create('Asset', 'admin_buttons.lang.inc.php'));
        $assets->add(MainFactory::create('Asset', 'cronjobs.lang.inc.php'));
        $assets->add(MainFactory::create('Asset', 'cronjob_check_permissions.lang.inc.php'));
        $assets->add(MainFactory::create('Asset', 'cronjob_delete_guest_accounts.lang.inc.php'));
        $assets->add(MainFactory::create('Asset', 'cronjob_delete_logs.lang.inc.php'));
        $assets->add(MainFactory::create('Asset', 'cronjob_create_sitemap.lang.inc.php'));
        $assets->add(MainFactory::create('Asset', 'cronjob_image_processing.lang.inc.php'));
        $assets->add(MainFactory::create('Asset', 'cronjob_currency_rates.lang.inc.php'));
    
        $gxmodulesIterator = new \GlobIterator(DIR_FS_CATALOG . '/GXModules/*/*/*/TextPhrases/*/cronjob_*.lang.inc.php');
        $gxmodulesSectionFiles = [];
        /** @var SplFileInfo $gxmodulesFile */
        foreach ($gxmodulesIterator as $gxmodulesFile) {
            $gxmodulesSectionFiles[] = $gxmodulesFile->getBasename(); 
        }
        $gxmodulesSectionFiles = array_unique($gxmodulesSectionFiles);
        foreach($gxmodulesSectionFiles as $gxmodulesSectionFile) {
            $assets->add(MainFactory::create('Asset', $gxmodulesSectionFile));
        }
    
    
        return MainFactory::create('AdminLayoutHttpControllerResponse', $this->title, $this->template, null, $assets);
    }
}