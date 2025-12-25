<?php
/* --------------------------------------------------------------
 ConfigurationRepository.php 2020-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\App\Data;

use Gambio\Core\Configuration\App\Creation\ConfigurationFactory;
use Gambio\Core\Configuration\Model\Interfaces\Configuration;
use Gambio\Core\Configuration\Model\Interfaces\LanguageDependentConfiguration;
use Gambio\Core\Configuration\Services\ConfigurationRepository as Repository;

/**
 * Class ConfigurationRepository
 * @package Gambio\Core\Configuration\Repositories\Implementations
 */
class ConfigurationRepository implements Repository
{
    /**
     * @var ConfigurationReader
     */
    private $reader;
    
    /**
     * @var ConfigurationWriter
     */
    private $writer;
    
    /**
     * @var ConfigurationFactory
     */
    private $factory;
    
    
    /**
     * ConfigurationRepository constructor.
     *
     * @param ConfigurationReader  $reader
     * @param ConfigurationWriter  $writer
     * @param ConfigurationFactory $factory
     */
    public function __construct(ConfigurationReader $reader, ConfigurationWriter $writer, ConfigurationFactory $factory)
    {
        $this->reader  = $reader;
        $this->writer  = $writer;
        $this->factory = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function find(string $key): ?Configuration
    {
        $data = $this->reader->find($key);
        if (null === $data) {
            return null;
        }
        
        return $this->factory->createConfigurationFromArray($data);
    }
    
    
    /**
     * @inheritDoc
     */
    public function findLanguageDependent(string $key, string $languageCode): ?LanguageDependentConfiguration
    {
        $data = $this->reader->findLanguageDependent($key, $languageCode);
        if (null === $data) {
            return null;
        }
        
        return $this->factory->createLanguageDependentFromArray($data);
    }
    
    
    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return $this->reader->has($key);
    }
    
    
    /**
     * @inheritDoc
     */
    public function hasLanguageDependent(string $key, string $languageCode): bool
    {
        return $this->reader->hasLanguageDependent($key, $languageCode);
    }
    
    
    /**
     * @inheritDoc
     */
    public function update(Configuration $configuration): void
    {
        $this->writer->update($configuration);
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateLanguageDependent(LanguageDependentConfiguration $configuration): void
    {
        $this->writer->updateLanguageDependent($configuration);
    }
    
    
    /**
     * @inheritDoc
     */
    public function add(Configuration $configuration): void
    {
        $this->writer->add($configuration);
    }
    
    
    /**
     * @inheritDoc
     */
    public function addLanguageDependent(LanguageDependentConfiguration $configuration): void
    {
        $this->writer->addLanguageDependent($configuration);
    }
    
    
    /**
     * @inheritDoc
     */
    public function delete(string ...$keys): void
    {
        $this->writer->delete(...$keys);
    }
}