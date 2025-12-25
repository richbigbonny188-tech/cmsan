<?php
/*
 * --------------------------------------------------------------
 *   ServiceProvider.php 2021-03-16
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2021 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\SampleData;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Shop\SampleData\SampleDataService as SampleDataServiceInterface;
use Gambio\Shop\SampleData\Services\SampleDataService;

/**
 * Class ServiceProvider
 * @package Gambio\Shop\SampleData
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides() : array
    {
        return [
            SampleDataServiceInterface::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register() : void
    {
        $this->application->registerShared(SampleDataServiceInterface::class, SampleDataService::class)
                          ->addArgument(Connection::class);
    }
}