<?php
/*--------------------------------------------------------------------------------------------------
    PreviewSettingsService.php 2019-8-21
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme;

use Gambio\StyleEdit\Core\Components\Theme\Entities\PreviewThemeSettings;
use Gambio\StyleEdit\Core\Components\Theme\Repositories\PreviewSettingsRepository;
use Gambio\StyleEdit\Core\SingletonPrototype;
use ThemeContentsParser;

/**
 * Class PreviewSettingsService
 * @package Gambio\StyleEdit\Core\Components\Theme
 */
class PreviewSettingsService
{
    /**
     * @var PreviewSettingsRepository
     */
    protected $previewSettingsRepository;
    
    /**
     * PreviewSettingsService constructor.
     *
     * @param PreviewSettingsRepository $previewSettingsRepository
     */
    public function __construct(PreviewSettingsRepository $previewSettingsRepository)
    {
        $this->previewSettingsRepository = $previewSettingsRepository;
    }
    
    
    /**
     * @param PreviewThemeSettings $data
     *
     * @return bool|int
     * @throws \ReflectionException
     */
    public function save(PreviewThemeSettings $data)
    {
        return $this->previewSettingsRepository->save($data);
    }
}