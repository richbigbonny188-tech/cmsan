<?php
/* --------------------------------------------------------------
  JanolawModuleCenterModule.inc.php 2015-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class JanolawModuleCenterModule
 *
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class JanolawModuleCenterModule extends AbstractModuleCenterModule
{
    /**
     * @var array $configurationKeys
     */
    protected $configurationKeys = [];
    
    
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('janolaw_title');
        $this->description = $this->languageTextManager->get_text('janolaw_description');
        $this->sortOrder   = 68184;
        
        $this->configurationKeys = [
            'configuration/MODULE_GAMBIO_JANOLAW_STATUS',
            'configuration/MODULE_GAMBIO_JANOLAW_USER_ID',
            'configuration/MODULE_GAMBIO_JANOLAW_SHOP_ID',
            'configuration/MODULE_GAMBIO_JANOLAW_USE_IN_PDF'
        ];
    }
    
    
    /**
     * Installs the module
     */
    public function install()
    {
        parent::install();
        
        foreach ($this->_getDefaultConfigurationData() as $configuration) {
            $this->db->insert('gx_configurations', $configuration);
        }
    }
    
    
    /**
     * Uninstalls the module
     */
    public function uninstall()
    {
        parent::uninstall();
        
        $this->db->where_in('key', $this->configurationKeys)->delete('gx_configurations');
    }
    
    
    /**
     * Get array of default janolaw configuration
     *
     * @return array
     */
    protected function _getDefaultConfigurationData()
    {
        return [
            [
                'key'             => 'configuration/MODULE_GAMBIO_JANOLAW_SHOP_ID',
                'value'           => '12345',
                'sort_order'      => '1',
            ],
            [
                'key'             => 'configuration/MODULE_GAMBIO_JANOLAW_USER_ID',
                'value'           => '12345',
                'sort_order'      => '1',
            ],
            [
                'key'             => 'configuration/MODULE_GAMBIO_JANOLAW_STATUS',
                'value'           => 'True',
                'sort_order'      => '1',
            ],
            [
                'key'             => 'configuration/MODULE_GAMBIO_JANOLAW_USE_IN_PDF',
                'value'           => 'True',
                'sort_order'      => '2',
            ]
        ];
    }
}