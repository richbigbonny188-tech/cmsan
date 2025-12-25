<?php
/* --------------------------------------------------------------
   AfterbuyCronjobDependencies.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AfterbuyCronjobDependencies
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes
 */
class AfterbuyCronjobDependencies extends AbstractCronjobDependencies
{
    
    /**
     * @return array
     *
     * @throws CronjobConfigurationNotFoundException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getDependencies(): array
    {
        /** @var GambioAfterbuyConfigurationStorage $gambioAfterbuyConfigurationStorage */
        $gambioAfterbuyConfigurationStorage = MainFactory::create('GambioAfterbuyConfigurationStorage');
        $configurationFinder                = LegacyDependencyContainer::getInstance()->get(ConfigurationFinder::class);
        
        return [
            'ConfigurationStorage' => $gambioAfterbuyConfigurationStorage,
            'ConfigurationFinder'  => $configurationFinder,
            'active'               => $this->storage->get('Afterbuy', 'active'),
        ];
    }
}
