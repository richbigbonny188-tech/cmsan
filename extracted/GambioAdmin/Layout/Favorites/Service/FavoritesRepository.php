<?php
/* --------------------------------------------------------------
 FavoritesRepository.php 2023-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Favorites\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class FavoritesRepository
 *
 * @package Gambio\Admin\Layout\Favorites\Service
 */
class FavoritesRepository
{
    private const TABLE_NAME = 'gm_admin_favorites';
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var UserPreferences
     */
    private $userPreferences;
    
    
    /**
     * FavoritesRepository constructor.
     *
     * @param Connection      $connection
     * @param UserPreferences $userPreferences
     */
    public function __construct(Connection $connection, UserPreferences $userPreferences)
    {
        $this->connection      = $connection;
        $this->userPreferences = $userPreferences;
    }
    
    
    /**
     * Checks if menu item is marked as favorites.
     *
     * @param string $menuItemId
     *
     * @return bool
     * @throws Exception
     */
    public function has(string $menuItemId): bool
    {
        $qb = $this->connection->createQueryBuilder();
        
        $favEqId      = "link_key = {$qb->createNamedParameter($menuItemId)}";
        $customerEqId = "customers_id = {$qb->createNamedParameter($this->userPreferences->userId())}";
        $result       = $qb->select('*')
            ->from(static::TABLE_NAME)
            ->where($favEqId)
            ->andWhere($customerEqId)
            ->executeQuery()
            ->fetchAllAssociative();
        
        return count($result) > 0;
    }
    
    
    /**
     * Adds the menu item to the favorites.
     *
     * @param string $menuItemId
     *
     * @throws DBALException
     */
    public function add(string $menuItemId): void
    {
        $data = [
            'link_key'     => $menuItemId,
            'customers_id' => $this->userPreferences->userId(),
        ];
        $this->connection->insert(static::TABLE_NAME, $data);
    }
    
    
    /**
     * Deletes the menu item from favorites.
     *
     * @param string $menuItemId
     *
     * @throws DBALException
     */
    public function delete(string $menuItemId): void
    {
        $identifier = [
            'link_key'     => $menuItemId,
            'customers_id' => $this->userPreferences->userId(),
        ];
        $this->connection->delete(static::TABLE_NAME, $identifier);
    }
}