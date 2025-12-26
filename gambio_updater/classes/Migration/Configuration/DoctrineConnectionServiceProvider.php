<?php
/* --------------------------------------------------------------
 DoctrineConnectionServiceProvider.php 2023-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use League\Container\ServiceProvider\AbstractServiceProvider;

class DoctrineConnectionServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        Connection::class
    ];
    
    
    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function register(): void
    {
        $server   = explode(':', DB_SERVER);
        $host     = $server[0];
        $port     = isset($server[1]) && is_numeric($server[1]) ? 'port=' . $server[1] . ';' : '';
        $socket   = isset($server[1]) && !is_numeric($server[1]) ? 'unix_socket=' . $server[1] . ';' : '';
        $user     = DB_SERVER_USERNAME;
        $name     = DB_DATABASE;
        $password = DB_SERVER_PASSWORD;
        
        $connectionParams = [
            'dbname'   => $name,
            'user'     => $user,
            'password' => $password,
            'host'     => $host,
            'port'     => $port,
            'charset'  => 'UTF8',
            'driver'   => 'pdo_mysql',
            'driverOptions' => [
                PDO::ATTR_STRINGIFY_FETCHES => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT
            ]
        ];
        
        if ($socket) {
            $connectionParams['unix_socket'] = $socket;
        }
        
        $connection = DriverManager::getConnection($connectionParams);
        $connection->executeQuery('SET SESSION sql_mode=""');
        
        $this->leagueContainer->share(Connection::class, $connection);
    }
}