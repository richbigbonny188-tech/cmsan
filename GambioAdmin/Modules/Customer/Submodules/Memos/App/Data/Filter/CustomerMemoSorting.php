<?php
/* --------------------------------------------------------------
  CustomerMemoSorting.php 2022-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data\Filter;

use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Filter\CustomerMemoSorting as CustomerMemoSortingInterface;
use Gambio\Core\Filter\SqlSorting;

/**
 * Class CustomerMemoSorting
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data\Filter
 * @codeCoverageIgnore
 */
class CustomerMemoSorting extends SqlSorting implements CustomerMemoSortingInterface
{
    /**
     * @inheritDoc
     */
    public static function attributeColumnMapping(): array
    {
        return [
            'id'            => 'customers_memo.memo_id',
            'creatorId'     => 'customers_memo.poster_id',
            'content'       => 'customers_memo.memo_text',
            'creationTime'  => 'customers_memo.memo_date',
            'updatedAtTime' => 'customers_memo.last_modified',
        ];
    }
}