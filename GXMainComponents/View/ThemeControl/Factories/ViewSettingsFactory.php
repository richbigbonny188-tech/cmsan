<?php

/*--------------------------------------------------------------------------------------------------
    ViewSettingsFactory.php 2019-09-12
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class ViewSettingsFactory
 */
class ViewSettingsFactory
{
    protected $viewSettingsService;
    
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * ViewSettingsFactory constructor.
     *
     * @param CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @return ViewSettingsReaderInterface
     */
    public function createViewSettingsReader(): ViewSettingsReaderInterface
    {
        return MainFactory::create(ViewSettingsReader::class, $this->db);
    }
    
    
    /**
     * @return ViewSettingsService
     */
    public function service(): ViewSettingsServiceInterface
    {
        if ($this->viewSettingsService === null) {
            $this->viewSettingsService = MainFactory::create(ViewSettingsService::class,
                                                             $this->createViewSettingsReader());
        }
        
        return $this->viewSettingsService;
    }
}