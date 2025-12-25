<?php
/* --------------------------------------------------------------
   AfterbuyCatalogsCronjobDependencies.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\AfterbuyXMLService;
use GXModules\Gambio\Afterbuy\Admin\Classes\Catalogs\AfterbuyCatalogRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AfterbuyCatalogsCronjobDependencies
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes
 */
class AfterbuyCatalogsCronjobDependencies extends AbstractCronjobDependencies
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
        $dbConnection                       = LegacyDependencyContainer::getInstance()->get(Connection::class);
        $catalogRepository                  = new AfterbuyCatalogRepository($dbConnection);
        
        return [
            'ConfigurationStorage' => $gambioAfterbuyConfigurationStorage,
            'ConfigurationFinder'  => $configurationFinder,
            'XMLService'           => new AfterbuyXMLService($partnerToken, $accountToken),
            'CatalogRepository'    => $catalogRepository,
        ];
    }
}
