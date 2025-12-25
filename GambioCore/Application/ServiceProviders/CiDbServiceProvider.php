<?php
/* --------------------------------------------------------------
 CiDbServiceProvider.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\ServiceProviders;

use CI_DB_query_builder;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use TestsConfig;

require_once __DIR__ . '/../../../vendor/gambio/codeigniter-db/CIDB.php';

/**
 * Class CiDbServiceProvider
 *
 * @package Gambio\Core\Application\ServiceProviders
 */
class CiDbServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CI_DB_query_builder::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $config = $this->getDatabaseConfig();
        $dbHost = $config['DB_SERVER'];
        $dbUser = $config['DB_SERVER_USERNAME'];
        $dbPass = $config['DB_SERVER_PASSWORD'];
        $dbName = $config['DB_DATABASE'];
        
        $dsn = 'mysqli://' . $dbUser . ':' . $dbPass . '@' . $dbHost . '/' . $dbName;
        $this->application->registerShared(CI_DB_query_builder::class, CIDB($dsn));
    }
    
    
    /**
     * @return array
     */
    private function getDatabaseConfig(): array
    {
        $config           = [];
        $configureContent = file_get_contents(__DIR__ . '/../../../includes/configure.php');
        preg_match_all("/define\('(DB\_\w*)\',\s?\'(.*)'\);/m", $configureContent, $matches);
        foreach ($matches[1] as $index => $key) {
            $config[$key] = $matches[2][$index];
        }
        
        if (count($config) === 0) {
            return $this->fallbackToTestConfig();
        }
        
        return $config;
    }
    
    
    /**
     * @return array
     */
    private function fallbackToTestConfig(): array
    {
        $testsConfigPath = __DIR__ . '/../../../../tests/tests.config.inc.php';
        if (!file_exists($testsConfigPath)) {
            // the credentials should fail
            return [
                'DB_SERVER'          => 'localhost',
                'DB_SERVER_USERNAME' => 'user',
                'DB_SERVER_PASSWORD' => 'password',
                'DB_DATABASE'        => 'db_name',
            ];
        }
        require_once $testsConfigPath;
        
        return [
            'DB_SERVER'          => TestsConfig::DB_HOST,
            'DB_SERVER_USERNAME' => TestsConfig::DB_USER,
            'DB_SERVER_PASSWORD' => TestsConfig::DB_PASSWORD,
            'DB_DATABASE'        => TestsConfig::DB_NAME(),
        ];
    }
}