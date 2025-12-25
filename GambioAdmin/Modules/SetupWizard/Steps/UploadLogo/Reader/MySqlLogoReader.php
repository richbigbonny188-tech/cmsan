<?php
/*--------------------------------------------------------------
   MySqlLogoReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\Reader;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\DTO\CurrentLogoDTO;

/**
 * Class MySqlLogoReader
 *
 * @package Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\Reader
 */
class MySqlLogoReader implements UploadLogoReader
{
    /**
     * @var Connection
     */
    protected $connection;
    
    
    /**
     * MySqlDataAccessObject constructor.
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
    public function currentLogo(): CurrentLogoDTO
    {
        $queryBuilder      = $this->connection->createQueryBuilder();
        $currentLogoResult = $queryBuilder->select('`value`')
            ->from('gx_configurations')
            ->where('`key`="gm_configuration/GM_LOGO_SHOP"')
            ->executeQuery()
            ->fetchAllAssociative();
        
        return new CurrentLogoDTO(current($currentLogoResult)['value']);
    }
}