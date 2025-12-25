<?php
/*--------------------------------------------------------------
   AbstractProductOptionAction.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Core\Application\Http\AbstractAction;

/**
 * Class AbstractAdditionalOptionAction
 *
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json
 * @codeCoverageIgnore
 */
abstract class AbstractAdditionalOptionAction extends AbstractAction
{
    private ?Connection $connection;
    
    
    /**
     * @param Connection $connection
     */
    protected function setConnection(Connection $connection): void
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    protected function downloadProductOptionIds(): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('products_attributes_id')
            ->distinct()
            ->from('products_attributes_download')
            ->executeQuery()
            ->fetchAllAssociative();
        
        return array_map('intval', array_column($result, 'products_attributes_id'));
    }
}