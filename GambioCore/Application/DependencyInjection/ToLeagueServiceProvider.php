<?php
/* --------------------------------------------------------------
 ToLeagueServiceProvider.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\DependencyInjection;

use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Interface ToLeagueServiceProvider
 *
 * @package Gambio\Core\Application\DependencyInjection
 */
interface ToLeagueServiceProvider
{
    /**
     * Creates a league container package compatible service provider interface.
     *
     * @return ServiceProviderInterface
     */
    public function toLeagueServiceProvider(): ServiceProviderInterface;
}