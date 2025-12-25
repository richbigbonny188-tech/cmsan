<?php
/* --------------------------------------------------------------
  MagnalisterModuleCenterModule.inc.php 2023-04-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class MagnalisterModuleCenterModule
 *
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class MagnalisterModuleCenterModule extends AbstractModuleCenterModule
{
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('magnalister_title');
        $this->description = $this->languageTextManager->get_text('magnalister_description');
        $this->sortOrder   = 531353;
    }
    
    
    /**
     * Installs the module
     */
    public function install()
    {
        parent::install();
        
        $this->magnalisterOrderColumn(true);
        
        $this->db->insert('gx_configurations',
                          [
                              'key'             => 'configuration/MODULE_MAGNALISTER_STATUS',
                              'value'           => 'True',
                              'sort_order'      => '1',
                          ]);
        
        return xtc_href_link('admin.php', 'do=EmbeddedModule/magnalister&update=true');
    }
    
    
    /**
     * Adds or removes the magnalister column in the orders overview.
     *
     * @param bool $blInstall Flag for adding (true) or removing (false) the magnalister column in user_configuration
     *                        table.
     *
     * @deprecated This method should be replaced when Gambio provides a generic service method.
     *             https://tracker.gambio-server.net/issues/48136
     *
     */
    protected function magnalisterOrderColumn($blInstall)
    {
        $userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
        $activeColumns            = json_decode(str_replace('\\"',
                                                            '"',
                                                            $userConfigurationService->getUserConfiguration(new IdType(1),
                                                                                                            'ordersOverviewSettingsColumns')),
                                                true);
        if (!empty($activeColumns) && is_array($activeColumns)) {
            $magnaActiveColumns = [];
            if ($blInstall) { // add magnalister column
                foreach ($activeColumns as $iColumn => $sColumn) {
                    if ($sColumn == 'magnalister') {// already setted
                        $magnaActiveColumns = [];
                        break;
                    }
                    $magnaActiveColumns[] = $sColumn;
                    if ($iColumn == 0) {
                        $magnaActiveColumns[] = 'magnalister';
                    }
                }
            } else {
                if (in_array('magnalister', $activeColumns)) {// remove magnalister column
                    $magnaActiveColumns = $activeColumns;
                    unset($magnaActiveColumns[array_search('magnalister', $magnaActiveColumns)]);
                    $magnaActiveColumns = array_values($magnaActiveColumns); // rebuild index for clean json array
                }
            }
            if (!empty($magnaActiveColumns)) {
                $userConfigurationService->setUserConfiguration(new IdType(1),
                                                                'ordersOverviewSettingsColumns',
                                                                str_replace('"',
                                                                            '\\"',
                                                                            json_encode($magnaActiveColumns)));
            }
        }
    }
    
    
    /**
     * Uninstalls the module
     */
    public function uninstall()
    {
        parent::uninstall();
        
        $this->magnalisterOrderColumn(false);
        $this->db->where_in('key', 'configuration/MODULE_MAGNALISTER_STATUS')->delete('gx_configurations');
    }
}