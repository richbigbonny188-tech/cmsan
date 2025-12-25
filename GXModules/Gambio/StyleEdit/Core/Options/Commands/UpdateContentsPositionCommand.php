<?php
/* --------------------------------------------------------------
   UpdateContentsPositionCommand.php 2021-08-20
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
use PagePositionMapper;
use PHPMailer\PHPMailer\Exception;

/**
 * Updates the position of contents
 */
class UpdateContentsPositionCommand implements CommandInterface
{
    /**
     * @var array
     */
    private $contentPositions = [];
    
    /**
     * @var array
     */
    private $rollback = [];
    
    
    /**
     * @param int    $contentGroupId
     * @param string $pageGroupId
     */
    public function add(int $contentGroupId, string $pageGroupId): void
    {
        try {
            $this->contentPositions[$contentGroupId] = PagePositionMapper::getPagePositionForDatabase($pageGroupId);
        } catch (Exception $e) {
            // skip
        }
    }
    
    
    public function execute(): void
    {
        foreach ($this->contentPositions as $contentGroupId => $item) {
            $position = $item['position'];
            $fileFlag = $item['fileFlag'];
            
            $result = xtc_db_query("SELECT
                                           `content_id`,
                                           `content_position`,
                                           `file_flag`
                                    FROM `content_manager`
                                    WHERE `content_group` = $contentGroupId");
            while ($row = xtc_db_fetch_array($result)) {
                $this->rollback[$row['content_id']] = $row['sort_order'];
            }
            
            xtc_db_query("UPDATE `content_manager`
                            SET
                                `content_position` = '$position',
                                `file_flag` = $fileFlag
                            WHERE `content_group` = $contentGroupId");
        }
    }
    
    
    public function rollback(): void
    {
        foreach ($this->rollback as $contentId => $item) {
            $position = $item['position'];
            $fileFlag = $item['fileFlag'];
            
            xtc_db_query("UPDATE `content_manager`
                            SET
                                `content_position` = '$position',
                                `file_flag` = $fileFlag
                            WHERE `content_id` = $contentId");
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