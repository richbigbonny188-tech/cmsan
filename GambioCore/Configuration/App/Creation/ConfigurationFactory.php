<?php
/* --------------------------------------------------------------
   ConfigurationFactory.php 2020-07-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Configuration\App\Creation;

use Gambio\Core\Configuration\Model\Configuration;
use Gambio\Core\Configuration\Model\LanguageDependentConfiguration;
use Webmozart\Assert\Assert;

/**
 * Class ConfigurationFactory
 * @package Gambio\Core\Configuration
 * @codeCoverageIgnore
 */
class ConfigurationFactory
{
    /**
     * Creates a new configuration with the given data.
     *
     * @param array $data
     *
     * @return Configuration
     */
    public function createConfigurationFromArray(array $data): Configuration
    {
        Assert::keyExists($data, 'key');
        $sortOrder = $data['sortOrder'] ?? null;
        
        return new Configuration($data['key'], $data['value'] ?? null, (int)$sortOrder);
    }
    
    
    /**
     * Creates a new configuration instance.
     *
     * @param string      $key
     * @param string|null $value
     * @param int|null    $sortOrder
     *
     * @return Configuration
     */
    public function createConfiguration(string $key, ?string $value, int $sortOrder = null): Configuration
    {
        return new Configuration($key, $value, $sortOrder);
    }
    
    
    /**
     * Creates a language dependent configuration with the given data.
     *
     * @param array $data
     *
     * @return LanguageDependentConfiguration
     */
    public function createLanguageDependentFromArray(array $data): LanguageDependentConfiguration
    {
        Assert::keyExists($data, 'key');
        Assert::keyExists($data, 'languageCode');
        $sortOrder = $data['sortOrder'] ?? null;
        
        return new LanguageDependentConfiguration(
            $data['key'], $data['languageCode'], $data['value'] ?? null, (int)$sortOrder
        );
    }
    
    
    /**
     * Creates a language dependent configuration value.
     *
     * @param string      $key
     * @param string      $languageCode
     * @param string|null $value
     * @param int|null    $sortOrder
     *
     * @return LanguageDependentConfiguration
     */
    public function createLanguageDependent(
        string $key,
        string $languageCode,
        ?string $value,
        int $sortOrder = null
    ): LanguageDependentConfiguration {
        return new LanguageDependentConfiguration($key, $languageCode, $value, $sortOrder);
    }
}
