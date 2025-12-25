<?php
/* --------------------------------------------------------------
   UpdateContentsSortOrderCommand.php 2021-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Options\Commands;

use Gambio\StyleEdit\Core\Command\CommandInterface;
use Gambio\StyleEdit\Core\Options\Entities\OptionInterface;

/**
 * Updates the sort order of contents
 */
class UpdateContentsSortOrderCommand implements CommandInterface
{
    /**
     * @var array
     */
    private $contentGroupSortOrder = [];
    
    /**
     * @var array
     */
    private $rollback = [];
    
    
    /**
     * @param int $contentGroupId
     * @param int $sortOrder
     */
    public function add(int $contentGroupId, int $sortOrder): void
    {
        $this->contentGroupSortOrder[$contentGroupId] = $sortOrder;
    }
    
    
    public function execute(): void
    {
        foreach ($this->contentGroupSortOrder as $contentGroupId => $sortOrder) {
            $result = xtc_db_query("SELECT
                                        `content_id`,
                                        `sort_order`
                                    FROM `content_manager`
                                    WHERE `content_group` = $contentGroupId");
            while ($row = xtc_db_fetch_array($result)) {
                $this->rollback[$row['content_id']] = $row['sort_order'];
            }
            
            xtc_db_query("UPDATE `content_manager` SET `sort_order` = $sortOrder WHERE `content_group` = $contentGroupId");
        }
    }
    
    
    public function rollback(): void
    {
        foreach ($this->rollback as $contentId => $sortOrder) {
            xtc_db_query("UPDATE `content_manager` SET `sort_order` = $sortOrder WHERE `content_id` = $contentId");
        }
    }
    
    
    /**
     * @param OptionInterface $option
     */
    public function setOption(OptionInterface $option): void
    {
        // skip
    }
}