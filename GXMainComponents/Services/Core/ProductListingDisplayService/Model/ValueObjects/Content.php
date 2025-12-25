<?php
/* --------------------------------------------------------------
  Content.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects;

/**
 * Class Content
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects
 */
class Content
{
    /**
     * @param int    $id
     * @param int    $groupId
     * @param string $title
     */
    public function __construct(
        private int    $id,
        private int    $groupId,
        private string $title
    )
    {
    }


    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }


    /**
     * @return int
     */
    public function groupId(): int
    {
        return $this->groupId;
    }


    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }
}