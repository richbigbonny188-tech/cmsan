<?php
/* --------------------------------------------------------------
 ConfigurationFinderBuilder.php 2020-04-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\App\Creation;

use Gambio\Core\Configuration\App\NamespaceConfigurationFinder;
use Gambio\Core\Configuration\Services\ConfigurationFinder;

/**
 * Class ConfigurationFinderBuilder
 * @package Gambio\Core\Configuration\Builder
 *
 * @codeCoverageIgnore
 */
class ConfigurationFinderBuilder
{
    /**
     * @var ConfigurationFinder
     */
    private $finder;
    
    
    /**
     * ConfigurationFinderBuilder constructor.
     *
     * @param ConfigurationFinder $finder
     */
    public function __construct(ConfigurationFinder $finder)
    {
        $this->finder = $finder;
    }
    
    
    /**
     * Builds a namespace configuration finder instance.
     *
     * @param string $namespace
     *
     * @return NamespaceConfigurationFinder
     */
    public function buildNamespaceFinder(string $namespace): NamespaceConfigurationFinder
    {
        return new NamespaceConfigurationFinder($this->finder, $namespace);
    }
}