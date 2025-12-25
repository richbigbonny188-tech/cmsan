<?php
/* --------------------------------------------------------------
 ConfigurationQbWriter.php  2023-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Exception;
use Gambio\Core\Configuration\Model\Events\GroupCheckUpdated;
use Gambio\Core\Configuration\Model\Interfaces\Configuration;
use Gambio\Core\Configuration\Model\Interfaces\LanguageDependentConfiguration;
use Psr\EventDispatcher\EventDispatcherInterface;
use function Gambio\Core\Logging\logger;

/**
 * Class ConfigurationWriter
 *
 * @package Gambio\Core\ConfigurationBak\Repository
 */
class ConfigurationWriter
{
    public const  KEY_GROUP_CHECK = 'configuration/GROUP_CHECK';
    private const TABLE_NAME      = 'gx_configurations';
    private const TABLE_LANG_NAME = 'gx_lang_configurations';
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    
    
    /**
     * ConfigurationWriter constructor.
     *
     * @param Connection               $connection
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        Connection               $connection,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->connection      = $connection;
        $this->eventDispatcher = $eventDispatcher;
    }
    
    
    /**
     * Updates a configuration.
     *
     * @param Configuration $item
     */
    public function update(Configuration $item): void
    {
        if ($item->key() === self::KEY_GROUP_CHECK) {
            $this->eventDispatcher->dispatch(new GroupCheckUpdated($item->value() ?? 'false'));
        }
        
        if ($item instanceof Configuration) {
            $this->updateConfiguration($item);
        }
    }
    
    
    /**
     * Updates a language dependent database configuration value.
     *
     * @param LanguageDependentConfiguration $configuration
     */
    public function updateLanguageDependent(LanguageDependentConfiguration $configuration): void
    {
        $set = [$this->connection->quoteIdentifier('value') => $configuration->value()];
        
        try {
            $identifier = [
                $this->connection->quoteIdentifier('key') => $configuration->key(),
                'language_id'                             => $this->languageCodeToId($configuration->languageCode()),
            ];
            $this->connection->update(self::TABLE_LANG_NAME, $set, $identifier);
        } catch (DBALException $e) {
            logger()->notice('Failed to execute db query', $this->exceptionToArray($e));
        }
    }
    
    
    /**
     * Updates a configuration.
     *
     * @param Configuration $configuration
     */
    private function updateConfiguration(Configuration $configuration): void
    {
        $set = [$this->connection->quoteIdentifier('value') => $configuration->value()];
        
        try {
            $this->connection->update(self::TABLE_NAME, $set, $this->identifier($configuration->key()));
        } catch (DBALException $e) {
            logger()->notice('Failed to execute db query', $this->exceptionToArray($e));
        }
    }
    
    
    /**
     * Adds a new configuration.
     *
     * @param Configuration $item
     */
    public function add(Configuration $item): void
    {
        $data = [
            $this->connection->quoteIdentifier('key')   => $item->key(),
            $this->connection->quoteIdentifier('value') => $item->value(),
        ];
        
        try {
            $this->connection->insert(self::TABLE_NAME, $data);
        } catch (DBALException $e) {
            logger()->notice('Failed to execute db query', $this->exceptionToArray($e));
        }
    }
    
    
    /**
     * Adds a new language dependent database configuration value.
     *
     * @param LanguageDependentConfiguration $configuration
     */
    public function addLanguageDependent(LanguageDependentConfiguration $configuration): void
    {
        try {
            $data = [
                $this->connection->quoteIdentifier('key')         => $configuration->key(),
                $this->connection->quoteIdentifier('value')       => $configuration->value(),
                $this->connection->quoteIdentifier('language_id') => $this->languageCodeToId($configuration->languageCode()),
            ];
            $this->connection->insert(self::TABLE_LANG_NAME, $data);
        } catch (DBALException $e) {
            logger()->notice('Failed to execute db query', $this->exceptionToArray($e));
        }
    }
    
    
    /**
     * Deletes configurations by key name.
     *
     * @param string ...$keys
     *
     */
    public function delete(string ...$keys): void
    {
        // just loop through, so i can use prepared statement instead of insecure WHERE IN
        // (in this case with arbitrary input keys)
        foreach ($keys as $key) {
            try {
                $this->connection->delete(self::TABLE_NAME, $this->identifier($key));
            } catch (\Doctrine\DBAL\Exception $e) {
                logger()->notice('Failed to execute db query', $this->exceptionToArray($e));
            }
        }
    }
    
    
    /**
     * Returns an identifier array that can be used by the connection to create a where condition.
     * (Format: ['`key`' => $key])
     *
     * @param string $key
     *
     * @return array
     */
    private function identifier(string $key): array
    {
        return [$this->connection->quoteIdentifier('key') => $key];
    }
    
    
    /**
     * Converts an exception in an array.
     *
     * @param Exception $e
     *
     * @return array
     */
    private function exceptionToArray(Exception $e): array
    {
        return [
            'message' => $e->getMessage(),
            'trace'   => $e->getTrace(),
            'code'    => $e->getCode(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ];
    }
    
    
    /**
     * Converts the language code into a language id.
     *
     * @param string $languageCode
     *
     * @return string|null
     * @throws \Doctrine\DBAL\Exception
     */
    private function languageCodeToId(string $languageCode): ?string
    {
        $qb       = $this->connection->createQueryBuilder();
        $langData = $qb->select('languages_id')
            ->from('languages')
            ->where("code = {$qb->createNamedParameter($languageCode)}")
            ->executeQuery()
            ->fetchAssociative();
        
        return $langData['languages_id'] ?? null;
    }
}