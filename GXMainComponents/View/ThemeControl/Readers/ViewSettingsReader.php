<?php
/*--------------------------------------------------------------------------------------------------
    ViewSettingsReader.php 2021-05-04
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
MainFactory::load_class('ViewSettingsReaderInterface');
/**
 * Class ViewSettingsReader
 */
class ViewSettingsReader implements ViewSettingsReaderInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    /**
     * @var ViewSettings
     */
    protected $settings;
    
    
    /**
     * ViewSettingsReader constructor.
     *
     * @param CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @return ViewSettings
     */
    public function get(): ViewSettings
    {
        if ($this->settings === null) {
            $dbSettings = ['type' => 'theme', 'name' => 'Honeygrid'];
            
            $themeConfig = $this->db->select('value')
                ->from('gx_configurations')
                ->where('key', 'configuration/CURRENT_THEME')
                ->get()
                ->row_array();
            
            if ($themeConfig !== null && $themeConfig['value'] !== ''
                && is_dir(DIR_FS_CATALOG . 'themes/' . $themeConfig['value'])) {
                $dbSettings['type'] = 'theme';
                $dbSettings['name'] = $themeConfig['value'];
            }
            
            $this->settings = new ViewSettings($dbSettings['type'], $dbSettings['name']);
        }
        
        return $this->settings;
    }
    
}