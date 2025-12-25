<?php
/* --------------------------------------------------------------
   AfterbuyStockupdateCronjobDependencies.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use Gambio\Core\Configuration\Services\ConfigurationFinder;
use GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\AfterbuyXMLService;
use GXModules\Gambio\Afterbuy\Admin\Classes\ProductsQuantityUpdateRunner;
use GXModules\Gambio\Afterbuy\Admin\Classes\ProductsQuantityUpdateService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AfterbuyStockupdateCronjobDependencies
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes
 */
class AfterbuyStockupdateCronjobDependencies extends AbstractCronjobDependencies
{
    
    /**
     * @return array
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getDependencies(): array
    {
        /** @var GambioAfterbuyConfigurationStorage $gambioAfterbuyConfigurationStorage */
        $gambioAfterbuyConfigurationStorage = MainFactory::create('GambioAfterbuyConfigurationStorage');
        $configurationFinder                = LegacyDependencyContainer::getInstance()->get(ConfigurationFinder::class);
        $partnerToken                       = $gambioAfterbuyConfigurationStorage->get('partner_token');
        $accountToken                       = $gambioAfterbuyConfigurationStorage->get('account_token');
        $moduleConfiguration                = new GambioAfterbuyConfigurationStorage();
        $productsQuantitiesUpdateService    = new ProductsQuantityUpdateService(StaticGXCoreLoader::getDatabaseQueryBuilder());
        
        $xmlService                     = new AfterbuyXMLService($partnerToken, $accountToken);
        $productsQuantitiesUpdateRunner = new ProductsQuantityUpdateRunner($moduleConfiguration,
                                                                           $xmlService,
                                                                           $productsQuantitiesUpdateService);
        
        return [
            'ConfigurationFinder'            => $configurationFinder,
            'ProductsQuantitiesUpdateRunner' => $productsQuantitiesUpdateRunner,
        ];
    }
}
