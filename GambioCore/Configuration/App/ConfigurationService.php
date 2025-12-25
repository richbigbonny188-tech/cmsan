<?php
/* --------------------------------------------------------------
 ConfigurationService.php 2020-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\App;

use Gambio\Core\Configuration\App\Creation\ConfigurationFactory;
use Gambio\Core\Configuration\Model\Interfaces\Configuration;
use Gambio\Core\Configuration\Model\Interfaces\LanguageDependentConfiguration;
use Gambio\Core\Configuration\Services\ConfigurationRepository;
use Gambio\Core\Configuration\Services\ConfigurationService as Service;

/**
 * Class ConfigurationService
 * @package Gambio\Core\Configuration\Services
 */
class ConfigurationService implements Service
{
    /**
     * @var ConfigurationRepository
     */
    private $repository;
    
    /**
     * @var ConfigurationFactory
     */
    private $factory;
    
    
    /**
     * ConfigurationService constructor.
     *
     * @param ConfigurationRepository $configurationRepository
     * @param ConfigurationFactory    $configurationFactory
     */
    public function __construct(
        ConfigurationRepository $configurationRepository,
        ConfigurationFactory $configurationFactory
    ) {
        $this->repository = $configurationRepository;
        $this->factory    = $configurationFactory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function find(string $key): ?Configuration
    {
        return $this->repository->find($key);
    }
    
    
    /**
     * @inheritDoc
     */
    public function findLanguageDependent(string $key, string $languageCode): ?LanguageDependentConfiguration
    {
        return $this->repository->findLanguageDependent($key, $languageCode);
    }
    
    
    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return $this->repository->has($key);
    }
    
    
    /**
     * @inheritDoc
     */
    public function hasLanguageDependent(string $key, string $languageCode): bool
    {
        return $this->repository->hasLanguageDependent($key, $languageCode);
    }
    
    
    /**
     * @inheritDoc
     */
    public function save(string $key, ?string $value): void
    {
        $configuration = $this->factory->createConfiguration($key, $value);
        
        $this->saveConfiguration($configuration);
    }
    
    
    /**
     * @inheritDoc
     */
    public function saveBulk(array $configurations): void
    {
        foreach ($configurations as $key => $value) {
            $configuration = $this->factory->createConfiguration($key, $value);
            
            $this->saveConfiguration($configuration);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function saveLanguageDependent(string $key, string $languageCode, ?string $value): void
    {
        $configuration = $this->factory->createLanguageDependent($key, $languageCode, $value);
        
        $this->saveLanguageDependentConfiguration($configuration);
    }
    
    
    /**
     * @inheritDoc
     */
    public function saveLanguageDependentBulk(array $configurations): void
    {
        foreach ($configurations as $configurationData) {
            $configuration = $this->factory->createLanguageDependentFromArray($configurationData);
            
            $this->saveLanguageDependentConfiguration($configuration);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function delete(string ...$keys): void
    {
        $this->repository->delete(...$keys);
    }
    
    
    /**
     * Either adds or updates the given configuration.
     *
     * @param LanguageDependentConfiguration $configuration
     */
    private function saveLanguageDependentConfiguration(LanguageDependentConfiguration $configuration): void
    {
        if ($this->repository->hasLanguageDependent(
            $configuration->key(),
            $configuration->languageCode()
        )) {
            $this->repository->updateLanguageDependent($configuration);
        } else {
            $this->repository->addLanguageDependent($configuration);
        }
    }
    
    
    /**
     * Either adds or updates the given configuration.
     *
     * @param Configuration $configuration
     */
    private function saveConfiguration(Configuration $configuration): void
    {
        $this->repository->has($configuration->key()) ? $this->repository->update(
            $configuration
        ) : $this->repository->add($configuration);
    }
}