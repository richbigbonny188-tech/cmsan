<?php
/* --------------------------------------------------------------
   LanguageSorting.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\App\Data\Filter;

use Gambio\Core\Filter\SqlSorting;

/**
 * Class LanguageSorting
 *
 * @package Gambio\Admin\Modules\Language\App\Data\Filter
 * @codeCoverageIgnore
 */
class LanguageSorting extends SqlSorting
{
    /**
     * @inheritDoc
     */
    public static function attributeColumnMapping(): array
    {
        return [
            'id'          => 'languages_id',
            'code'        => 'code',
            'name'        => 'name',
            'charset'     => 'language_charset',
            'directory'   => 'directory',
            'status'      => 'status',
            'statusAdmin' => 'status_admin',
            'sortOrder'   => 'sort_order',
        ];
    }
}