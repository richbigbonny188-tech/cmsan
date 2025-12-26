<?php

/* --------------------------------------------------------------
   ResponsiveFilemanagerController.inc.php 2017-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2017 Gambio GmbH
   --------------------------------------------------------------
*/

/**
 * Class ResponsiveFilemanagerController
 *
 * @category System
 * @package  HttpViewControllers
 */
class ResponsiveFilemanagerController extends AdminHttpViewController
{
    /**
     * Current language, fetched from the session.
     *
     * @var string
     */
    protected $lang = '';
    
    /**
     * URL to the file manager dialog
     *
     * @var string $filemanagerURL
     */
    protected $filemanagerURL = '../ResponsiveFilemanager/filemanager/filemanager.php?type=0&sub_folder=&popup=0&relative_url=1&page=responsive_filemanager';
    
    
    /**
     * Initializes the controller
     *
     * @param HttpContextInterface $httpContext
     */
    public function proceed(HttpContextInterface $httpContext)
    {
        $this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/');
        
        parent::proceed($httpContext);
    }
    
    
    /**
     * Default actions, renders the file manager page.
     *
     * @return \AdminLayoutHttpControllerResponse
     */
    public function actionDefault()
    {
        $languageTextManager = MainFactory::create('LanguageTextManager', 'file_manager', $_SESSION['languages_id']);
        
        $title    = new NonEmptyStringType($languageTextManager->get_text('HEADING_TITLE'));
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                            . '/html/content/filemanager/filemanager.html'));
        
        $lang                 = $_SESSION['languages_id'] === "2" ? 'de' : 'en_EN';
        $this->filemanagerURL .= '&lang=' . $lang;
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'url'          => $this->filemanagerURL,
                                        'is_available' => $this->_isFileManagerAvailable()
                                    ]);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, null);
    }
    
    
    /**
     * Checks if the responsive file manager is available
     *
     * @return bool true|false If responsive file manager is available|not available
     */
    protected function _isFileManagerAvailable()
    {
        $filemanagerAvailable = false;
        if (is_dir(DIR_FS_CATALOG . 'ResponsiveFilemanager')) {
            $filemanagerAvailable = true;
        }
        
        return $filemanagerAvailable;
    }
}