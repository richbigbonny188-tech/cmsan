<?php
/* --------------------------------------------------------------
 DoctrineQbServiceProvider.php 2022-10-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\ServiceProviders;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Exception;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use PDO;
use TestsConfig;
use function file_exists;

/**
 * Class DoctrineQbServiceProvider
 *
 * @package Gambio\Core\Application\ServiceProviders
 */
class DoctrineQbServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            Connection::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function register(): void
    {
        $config   = $this->getDatabaseConfig();
        $db       = explode(':', $config['DB_SERVER']);
        $dbHost   = $db[0];
        $dbPort   = isset($db[1]) && is_numeric($db[1]) ? $db[1] : '';
        $dbSocket = isset($db[1]) && !is_numeric($db[1]) ? 'unix_socket=' . $db[1] . ';' : '';
        $dbUser   = $config['DB_SERVER_USERNAME'];
        $dbName   = $this->lookupDatabaseName($config['DB_DATABASE']);
        $dbPass   = $config['DB_SERVER_PASSWORD'];
        
        $connectionParams = [
            'dbname'   => $dbName,
            'user'     => $dbUser,
            'password' => $dbPass,
            'host'     => $dbHost,
            'port'     => $dbPort,
            'charset'  => 'UTF8',
            'driver'   => 'pdo_mysql',
            'driverOptions' => [
                PDO::ATTR_STRINGIFY_FETCHES => true,
            ]
        ];
        
        if ($dbSocket) {
            $connectionParams['unix_socket'] = $dbSocket;
        }
        
        $evm = new EventManager();
        $evm->addEventSubscriber(new DoctrineSqlModePostConnectEvent());
        
        $connection = DriverManager::getConnection($connectionParams, null, $evm);
        $this->application->registerShared(Connection::class, $connection);
        
        if ($evm->hasListeners(Events::postConnect)) {
            $eventArgs = new ConnectionEventArgs($connection);
            $evm->dispatchEvent(Events::postConnect, $eventArgs);
        }
    }
    
    
    protected function lookupDatabaseName(string $database): string
    {
        if (isset($_SERVER['HTTP_X_GXDB'])
            && file_exists(__DIR__ . '/../../../.dev-environment')) {
            return $_SERVER['HTTP_X_GXDB'];
        }
        
        return $database;
    }
    
    
    /**
     * @return array
     */
    private function getDatabaseConfig(): array
    {
        if (!defined('DB_SERVER')) {
            if (file_exists(__DIR__ . '/../../../includes/local/configure.php')) {
                include_once __DIR__ . '/../../../includes/local/configure.php';
            } elseif (file_exists(__DIR__ . '/../../../includes/configure.php')) {
                include_once __DIR__ . '/../../../includes/configure.php';
            }
        }
        
        $config = [
            'DB_SERVER'          => defined('DB_SERVER') ? DB_SERVER : '',
            'DB_SERVER_USERNAME' => defined('DB_SERVER_USERNAME') ? DB_SERVER_USERNAME : '',
            'DB_SERVER_PASSWORD' => defined('DB_SERVER_PASSWORD') ? DB_SERVER_PASSWORD : '',
            'DB_DATABASE'        => defined('DB_DATABASE') ? DB_DATABASE : '',
        ];
        
        if ($config['DB_DATABASE'] === '') {
            return $this->testsConfigFallback();
        }
        
        return $config;
    }
    
    
    /**
     * @return array
     */
    private function testsConfigFallback(): array
    {
        if (file_exists($testsConfig = __DIR__ . '/../../../../tests/tests.config.inc.php')) {
            require_once $testsConfig;
            
            return [
                'DB_SERVER'          => TestsConfig::DB_HOST,
                'DB_SERVER_USERNAME' => TestsConfig::DB_USER,
                'DB_SERVER_PASSWORD' => TestsConfig::DB_PASSWORD,
                'DB_DATABASE'        => TestsConfig::DB_NAME(),
            ];
        }
        
        return [
            'DB_SERVER'          => '',
            'DB_SERVER_USERNAME' => '',
            'DB_SERVER_PASSWORD' => '',
            'DB_DATABASE'        => '',
        ];
    }
}
