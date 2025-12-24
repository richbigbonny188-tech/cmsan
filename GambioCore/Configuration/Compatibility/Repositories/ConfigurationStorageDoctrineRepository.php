<?php
/* --------------------------------------------------------------
 LegacyStorageRepository.php 2020-01-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 15 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\Compatibility\Repositories;

use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\Repositories\Storage\NamespaceConverter;
use Gambio\Core\Configuration\Compatibility\Repositories\Storage\ConfigurationStorageReader;
use Gambio\Core\Configuration\Compatibility\Repositories\Storage\ConfigurationStorageWriter;

/**
 * Class LegacyStorageRepository
 * @package Gambio\Core\Configuration\Repositories
 */
class ConfigurationStorageDoctrineRepository implements ConfigurationStorageRepository
{
    /**
     * @var ConfigurationStorageReader
     */
    private $reader;
    
    /**
     * @var ConfigurationStorageWriter
     */
    private $writer;
    
    /**
     * @var NamespaceConverter
     */
    private $namespaceConverter;
    
    
    /**
     * LegacyStorageRepository constructor.
     *
     * @param ConfigurationStorageReader $reader
     * @param ConfigurationStorageWriter $writer
     * @param NamespaceConverter         $namespaceConverter
     */
    public function __construct(ConfigurationStorageReader $reader, ConfigurationStorageWriter $writer, NamespaceConverter $namespaceConverter)
    {
        $this->reader             = $reader;
        $this->writer             = $writer;
        $this->namespaceConverter = $namespaceConverter;
    }
    
    
    /**
     * @inheritDoc
     */
    public function get(string $key): string
    {
        return $this->reader->get($key) ?? '';
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAll(string $prefix = ''): array
    {
        return $this->reader->getAll($prefix);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllTree(string $prefix = ''): array
    {
        return $this->namespaceConverter->fromFlatToTree($this->getAll($prefix));
    }
    
    
    /**
     * @inheritDoc
     */
    public function is(string $key): bool
    {
        return $this->reader->is($key);
    }
    
    
    /**
     * @inheritDoc
     */
    public function set(string $key, string $value): void
    {
        $this->writer->set($key, $value);
    }
    
    
    /**
     * @inheritDoc
     */
    public function setAll(array $configTree): void
    {
        $flatConfigs = $this->namespaceConverter->fromTreeToFlat($configTree);
        
        foreach ($flatConfigs as $key => $value) {
            $this->set($key, $value);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function delete(string $key): void
    {
        $this->writer->delete($key);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAll(string $prefix = ''): void
    {
        $this->writer->deleteAll($prefix);
    }
}