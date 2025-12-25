<?php
/* --------------------------------------------------------------
 AbstractModuleServiceProvider.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\DependencyInjection;

/**
 * Class AbstractModuleServiceProvider
 *
 * This abstract service provider is especially for external modules. This way we might be able to split the
 * behavior for our own service provider but keep the behavior for external modules.
 *
 * @package Gambio\Core\Application\DependencyInjection
 */
abstract class AbstractModuleServiceProvider extends AbstractServiceProvider implements ModuleServiceProvider
{
}