<?php
/*--------------------------------------------------------------------------------------------------
    CustomStyleService.php 2019-10-24
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Style;

use Exception;
use Gambio\StyleEdit\Core\Options\Entities\FieldSet;
use Gambio\StyleEdit\Core\Repositories\SettingsRepository;
use Gambio\StyleEdit\Core\Services\Configuration\Repositories\ConfigurationRepository;
use Gambio\StyleEdit\Core\SingletonPrototype;
use stdClass;

/**
 * Class CustomScssService
 * @package Gambio\StyleEdit\Core\Components\Style
 */
class CustomScssService
{
    
    /**
     * @return array
     *
     * @throws Exception
     */
    public function getCustomStyles()
    {
        $configRepository  = SingletonPrototype::instance()->get(SettingsRepository::class);
        $configurationList = $configRepository->configurationsList()->jsonSerialize();
        
        $options = [];
        
        array_map(function ($element) use (&$options) {
            if ($element->group() === 'custom') {
                $options[] = $element->jsonSerialize();
            }
        },
            $configurationList);
        
        $fieldsetObj          = new stdClass;
        $fieldsetObj->id      = 'styles';
        $fieldsetObj->options = $options;
        
        return FieldSet::createFromJsonObject($fieldsetObj, $configRepository)->jsonSerialize();
    }
}