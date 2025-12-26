<?php
/*--------------------------------------------------------------------
 PostInstallationMalibuActivator.inc.php 2020-3-2
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

/**
 * Class PostInstallationMalibuActivator
 */
class PostInstallationMalibuActivator extends PostInstallationMalibuActivator_parent
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var ThemeService
     */
    protected $themeService;
    
    
    /**
     * @throws Exception
     */
    public function proceed()
    {
        parent::proceed();
        $this->initMalibuActivator();
        
        if ($this->malibuIsActivated()) {
            
            $this->reActivateMalibu();
        }
    }
    
    
    /**
     * @return bool
     */
    protected function malibuIsActivated(): bool
    {
        $result = $this->queryBuilder->select('value')
            ->from('gx_configurations')
            ->where(['key' => 'configuration/CURRENT_THEME'])
            ->get()
            ->result_array();
        
        return count($result) === 1 && current($result)['value'] === 'Malibu';
    }
    
    
    /**
     * @throws Exception
     */
    protected function reActivateMalibu(): void
    {
        $this->themeService->activateTheme('Malibu');
    }
    
    protected function initMalibuActivator(): void
    {
        $this->queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->themeService = StaticGXCoreLoader::getService('Theme');
    }
}