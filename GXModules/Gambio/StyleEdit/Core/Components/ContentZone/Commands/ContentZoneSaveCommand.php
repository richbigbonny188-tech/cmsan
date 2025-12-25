<?php
/*--------------------------------------------------------------------------------------------------
    ContentZoneSaveCommand.php 2019-07-12
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\ContentZone\Commands;

use Gambio\StyleEdit\Core\Components\ContentZone\Entities\ContentZoneOption;
use Gambio\StyleEdit\Core\Components\ContentZone\Services\ContentZoneService;
use Gambio\StyleEdit\Core\Components\ContentZone\Utility\SingletonPublicCacheClearer;
use Gambio\StyleEdit\Core\Components\Theme\StyleEditThemeService;
use Gambio\StyleEdit\Core\Options\Commands\AbstractSaveCommand;
use Exception;
use Gambio\StyleEdit\Core\SingletonPrototype;

/**
 * Class ContentZoneSaveCommand
 * @package Gambio\StyleEdit\Core\Components\ContentZone\Commands
 */
class ContentZoneSaveCommand extends AbstractSaveCommand
{
    
    /**
     * @var \Gambio\StyleEdit\Core\Components\Variant\Services\VariantService
     */
    protected $receiver;
    
    /**
     * @var ContentZoneService
     */
    protected $service;
    
    
    /**
     * ContentZoneSaveCommand constructor.
     *
     * @param ContentZoneService $service
     *
     * @throws Exception
     */
    public function __construct(ContentZoneService $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @throws Exception
     */
    public function execute(): void
    {
        $this->saveOption();
    }
    
    
    /**
     * @throws Exception
     */
    protected function saveOption(): void
    {
        $this->service->save($this->getOption());
    }
    
    
    /**
     * @return ContentZoneOption
     */
    protected function getOption(): ContentZoneOption
    {
        return $this->option;
    }
    
    
    /**
     * Execute the command
     */
    public function rollback(): void
    {
        // TODO: Implement rollback() method.
    }
}
