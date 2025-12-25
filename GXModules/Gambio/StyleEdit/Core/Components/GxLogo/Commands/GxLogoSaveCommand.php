<?php
/* --------------------------------------------------------------
  GxLogoSaveCommand.php 2019-08-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\GxLogo\Commands;

use Gambio\StyleEdit\Core\Options\Commands\AbstractSaveCommand;
use GmConfigurationServiceInterface;

/**
 * Class GxLogoSaveCommand
 */
class GxLogoSaveCommand extends AbstractSaveCommand
{
    /**
     * @var GmConfigurationServiceInterface
     */
    protected $service;
    
    /**
     * @var string
     */
    protected const GM_LOGO_CONFIGURATION_KEY = 'GM_LOGO_SHOP';
    
    /**
     * @var string
     */
    protected const GM_LOGO_WEBPATH = 'images/logos/';
    
    
    /**
     * GxLogoSaveCommand constructor.
     *
     * @param GmConfigurationServiceInterface $service
     */
    public function __construct(GmConfigurationServiceInterface $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * Execute the command
     */
    public function execute(): void
    {
        $newValue = $this->option->value();
        $newValue = str_replace(self::GM_LOGO_WEBPATH, '', $newValue);
        
        $gxInstallPath = DIR_WS_CATALOG;
        if ($gxInstallPath !== '/' && strpos($gxInstallPath, '/') === 0) {
            $gxInstallPath = substr($gxInstallPath, 1);
            $newValue      = str_replace($gxInstallPath, '', $newValue);
        }
        
        $gmLogoConfiguration = $this->service->getConfigurationByKey(self::GM_LOGO_CONFIGURATION_KEY);
        $gmLogoConfiguration->setValue($newValue);
        
        $this->service->updateGmConfiguration($gmLogoConfiguration);
    }
    
    
    /**
     * Execute the command
     */
    public function rollback(): void
    {
        
    }
}