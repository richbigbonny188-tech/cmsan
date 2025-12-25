<?php
/* --------------------------------------------------------------
   DoctrineSqlModePostConnectEvent.php 2022-12-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\ServiceProviders;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Exception;
use function Gambio\Core\Application\env;

class DoctrineSqlModePostConnectEvent implements EventSubscriber
{
    /**
     * @return void
     * @throws Exception
     */
    public function postConnect(ConnectionEventArgs $args)
    {
        include_once __DIR__ . '/../env.php';
        
        $sqlMode   = env('MYSQL_SQL_MODE', '');
        $overwrite = env('MYSQL_OVERWRITE_SQL_MODE', true);
        
        
        mysqli_report(MYSQLI_REPORT_OFF);
        
        if ($overwrite) {
            $args->getConnection()->executeQuery(sprintf('SET SESSION sql_mode="%s"', $sqlMode));
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::postConnect];
    }
}
