<?php
/* --------------------------------------------------------------
 NamespaceConfigurationFinder.php 2020-04-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\App;

use Gambio\Core\Configuration\Services\ConfigurationFinder as Finder;

/**
 * Class NamespaceConfigurationFinder
 * @package Gambio\Core\Configuration\Services
 */
class NamespaceConfigurationFinder implements Finder
{
    /**
     * @var Finder
     */
    private $configurationFinder;
    
    /**
     * @var string
     */
    private $namespace;
    
    
    /**
     * NamespaceConfigurationFinder constructor.
     *
     * @param Finder $configurationFinder
     * @param string $namespace
     */
    public function __construct(Finder $configurationFinder, string $namespace)
    {
        $this->configurationFinder = $configurationFinder;
        $this->namespace           = $namespace;
    }
    
    
    /**
     * @inheritDoc
     */
    public function get(string $key, string $default = null): ?string
    {
        $key = "{$this->namespace}/{$key}";
        
        return $this->configurationFinder->get($key) ?? $default;
    }
}