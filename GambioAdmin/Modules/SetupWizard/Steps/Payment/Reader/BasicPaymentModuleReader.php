<?php
/* --------------------------------------------------------------
 BasicPaymentModuleReader.php 2023-06-09
 Gambio GmbH
 http://www.gambio.de

 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\Payment\Reader;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\SetupWizard\Steps\Payment\DTO\HubClientKeyDto;
use Gambio\Admin\Modules\SetupWizard\Steps\Payment\DTO\InstalledPaymentModulesDto;

/**
 * Class BasicPaymentModuleReader
 * @package Gambio\Admin\Modules\SetupWizard\Steps\Payment\Reader
 */
class BasicPaymentModuleReader implements PaymentModuleReader
{
    /**
     * @var Connection
     */
    protected $connection;
    
    
    /**
     * BasicPaymentModuleReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function paymentMethods(): InstalledPaymentModulesDto
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        
        $result = $queryBuilder->select('`value`')
            ->from('gx_configurations')
            ->where('`key`="configuration/MODULE_PAYMENT_INSTALLED"')
            ->executeQuery()
            ->fetchAllAssociative();
        
        return new InstalledPaymentModulesDto(current($result)['value'] ?? '');
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function hubClientKey(): HubClientKeyDto
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        
        $result = $queryBuilder->select('`value`')
            ->from('gx_configurations')
            ->where('`key`="gm_configuration/GAMBIO_HUB_CLIENT_KEY"')
            ->executeQuery()
            ->fetchAllAssociative();
        
        return new HubClientKeyDto(current($result)['value'] ?? '');
    }
}