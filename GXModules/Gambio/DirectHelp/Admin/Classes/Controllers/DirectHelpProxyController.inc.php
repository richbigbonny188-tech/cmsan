<?php

/* --------------------------------------------------------------
   DirectHelpProxyController.inc.php 2018-10-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class representing the proxy to the online manual pages
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class DirectHelpProxyController extends AdminHttpViewController
{
    /**
     * Forum link factory
     *
     * @var DirectHelpForumLinkFactory
     */
    protected $forumLinkFactory;
    
    /**
     * Online manual page link factory
     *
     * @var DirectHelpManualPageLinkFactory
     */
    protected $manualPageLinkFactory;
    
    /**
     * Shop version
     *
     * @var string
     */
    protected $shopVersion;
    
    /**
     * Database connection
     *
     * @var CI_DB_query_builder
     */
    protected $database;
    
    
    /**
     * Initialize the controller
     */
    public function init()
    {
        $this->database = StaticGXCoreLoader::getDatabaseQueryBuilder();
        
        $shopVersion = new NonEmptyStringType($this->shopVersion());
        
        $mappingFileStorage = MainFactory::create('DirectHelpManualMappingFileStorage',
                                                  new IntType(DirectHelpManualPageLinkConfiguration::MAX_LOCAL_MAPPING_FILE_TTL),
                                                  new NonEmptyStringType(DirectHelpManualPageLinkConfiguration::LOCAL_MAPPING_FILE_LOCATION),
                                                  new NonEmptyStringType(DirectHelpManualPageLinkConfiguration::REMOTE_MAPPING_FILE_LOCATION),
                                                  $shopVersion);
        
        $this->forumLinkFactory = MainFactory::create('DirectHelpForumLinkFactory',
                                                      new NonEmptyStringType(DirectHelpForumLinkConfiguration::FORUM_LOCATION),
                                                      new IntType(DirectHelpForumLinkConfiguration::MIN_RANDOM_NUMBER),
                                                      new IntType(DirectHelpForumLinkConfiguration::MAX_RANDOM_NUMBER));
        
        $this->manualPageLinkFactory = MainFactory::create('DirectHelpManualPageLinkFactory',
                                                           $mappingFileStorage,
                                                           $shopVersion);
    }
    
    
    /**
     * Send URL to appropriate manual page as response
     *
     * @returns HttpControllerResponse
     */
    public function actionDefault()
    {
        $languageTextManager = MainFactory::create('LanguageTextManager');
        $tooltip             = $languageTextManager->get_text('tooltip', 'directhelp') ? : '';
        $link                = '';
        
        try {
            $origin = new NonEmptyStringType($this->_getQueryParameter('origin'));
            $link   = $this->manualPageLinkFactory->linkByOrigin($origin);
        } catch (Exception $exception) {
        }
        
        $data = [
            'link'    => $link,
            'tooltip' => $tooltip
        ];
        
        return MainFactory::create('JsonHttpControllerResponse', $data);
    }
    
    
    /**
     * Redirect to manual pages
     *
     * @return RedirectHttpControllerResponse
     */
    public function actionGoToManual()
    {
        $url = $this->manualPageLinkFactory->linkToRootPage();
        
        try {
            $term = new NonEmptyStringType($this->_getQueryParameter('search'));
            $url  = $this->manualPageLinkFactory->linkBySearchTerm($term);
        } catch (Exception $exception) {
        }
        
        return MainFactory::create('RedirectHttpControllerResponse', $url);
    }
    
    
    /**
     * Redirect to forum
     *
     * @return RedirectHttpControllerResponse
     */
    public function actionGoToForum()
    {
        $url = $this->forumLinkFactory->linkToRootPage();
        
        try {
            $term = new NonEmptyStringType($this->_getQueryParameter('search'));
            $url  = $this->forumLinkFactory->linkBySearchTerm($term);
        } catch (Exception $exception) {
        }
        
        return MainFactory::create('RedirectHttpControllerResponse', $url);
    }
    
    
    /**
     * Return current shop version
     *
     * @return string
     */
    protected function shopVersion()
    {
        if ($this->shopVersion === null) {
            $shopVersion = $this->database->select('version')
                               ->from('version_history')
                               ->where('type != "update"')
                               ->order_by('history_id', 'desc')
                               ->get()
                               ->row_array()['version'];
            
            $processedShopVersion = str_replace(' ', '_', $shopVersion);
            
            $this->shopVersion = $processedShopVersion;
        }
        
        return $this->shopVersion;
    }
}