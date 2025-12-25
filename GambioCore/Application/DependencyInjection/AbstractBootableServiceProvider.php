<?php
/* --------------------------------------------------------------
 AbstractBootableServiceProvider.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\DependencyInjection;

use Gambio\Core\Application\DependencyInjection\Abstraction\LeagueBootableServiceProvider;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Class AbstractBootableServiceProvider
 *
 * @package Gambio\Core\Application\DependencyInjection
 */
abstract class AbstractBootableServiceProvider extends AbstractServiceProvider implements BootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function toLeagueServiceProvider(): ServiceProviderInterface
    {
        return new LeagueBootableServiceProvider($this);
    }
}