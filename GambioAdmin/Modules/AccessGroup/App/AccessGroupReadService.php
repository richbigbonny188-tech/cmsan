<?php
/* --------------------------------------------------------------
   AccessGroupReadService.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\App;

use Gambio\Admin\Modules\AccessGroup\App\Data\AccessGroupRepository;
use Gambio\Admin\Modules\AccessGroup\Model\AccessGroup;
use Gambio\Admin\Modules\AccessGroup\Model\Collections\AccessGroups;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupItem;
use Gambio\Admin\Modules\AccessGroup\Services\AccessGroupFactory;
use Gambio\Admin\Modules\AccessGroup\Services\AccessGroupReadService as AccessGroupReadServiceInterface;
use Gambio\Admin\Modules\AccessGroup\Services\Exceptions\AccessGroupDoesNotExistException;
use RuntimeException;

/**
 * Class AccessGroupReadService
 *
 * @package Gambio\Admin\Modules\AccessGroup\App
 */
class AccessGroupReadService implements AccessGroupReadServiceInterface
{
    private const UNKNOWN_ITEM_DESCRIPTOR = 'unknown-admin-access-item';
    
    /**
     * @var AccessGroupRepository
     */
    private $repository;
    
    /**
     * @var AccessGroupFactory
     */
    private $factory;
    
    
    /**
     * GroupService constructor.
     *
     * @param AccessGroupRepository $repository
     * @param AccessGroupFactory    $factory
     */
    public function __construct(AccessGroupRepository $repository, AccessGroupFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAccessGroups(): AccessGroups
    {
        return $this->repository->getAccessGroups();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAccessGroupById(int $groupId): AccessGroup
    {
        return $this->repository->getAccessGroupById($this->factory->createAccessGroupId($groupId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAccessGroupByTypeAndDescriptor(string $type, string $descriptor): AccessGroup
    {
        return $this->repository->getAccessGroupByItem($this->factory->createAccessGroupItem($type, $descriptor));
    }
    
    
    /**
     * @inheritDoc
     *
     * @note If this logic needs to be extended, e.g. for another type, then it should be refactored and some
     *       strategy pattern (or so) should be implemented. Currently I would assume, that this logic would
     *       become simpler, because the types `PAGE`, `CONTROLLER`, and `AJAX_HANDLER` will be removed.
     */
    public function findAccessGroupByTypeAndDescriptor(string $type, string $descriptor): AccessGroup
    {
        try {
            return $this->repository->getAccessGroupByItem($this->factory->createAccessGroupItem($type, $descriptor));
        } catch (AccessGroupDoesNotExistException $e) {
            $moreGenericDescriptor = rtrim($descriptor, '/');
            if ($type === AccessGroupItem::PAGE_TYPE || $type === AccessGroupItem::AJAX_HANDLER_TYPE
                || (strrpos($moreGenericDescriptor, '/') > 0) === false) {
                return $this->getAccessGroupForUnknownItemsByType($type);
            }
            $moreGenericDescriptor = substr($moreGenericDescriptor, 0, strrpos($moreGenericDescriptor, '/'));
            
            return $this->findAccessGroupByTypeAndDescriptor($type, $moreGenericDescriptor);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAccessGroupForUnknownItemsByType(string $type): AccessGroup
    {
        try {
            return $this->repository->getAccessGroupByItem($this->factory->createAccessGroupItem($type,
                                                                                                 self::UNKNOWN_ITEM_DESCRIPTOR));
        } catch (AccessGroupDoesNotExistException $e) {
            throw new RuntimeException('Missing Admin Access group for unknown items of type "' . $type . '".');
        }
    }
}