<?php
/*--------------------------------------------------------------------
 gm_set_conf.inc.php 2020-2-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 * @param $gm_conf_key
 * @param $gm_conf_value
 *
 * @deprecated This method shouldn't be used for new domains and exist for compatibility purposes.
 */
function gm_set_conf($gm_conf_key, $gm_conf_value)
{
    $legacyContainer = LegacyDependencyContainer::getInstance();
    
    /** @var ConfigurationService $service */
    $service = $legacyContainer->get(ConfigurationService::class);
    
    $prefix = 'gm_configuration/';
    $gmKey  = "{$prefix}{$gm_conf_key}";
    
    $service->save($gmKey, $gm_conf_value);
}