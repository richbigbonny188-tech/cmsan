<?php
/* --------------------------------------------------------------
   LanguageCriteria.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\App\Data\Filter;

use Gambio\Core\Filter\SqlFilters;

/**
 * Class LanguageFilters
 *
 * @package Gambio\Admin\Modules\Language\App\Data\Filter
 * @codeCoverageIgnore
 */
class LanguageFilters extends SqlFilters
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
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForLikeOperation(): array
    {
        return [
            'code'      => 'code',
            'name'      => 'name',
            'charset'   => 'language_charset',
            'directory' => 'directory',
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForNumericOperations(): array
    {
        return [
            'id',
            'status'      => 'status',
            'statusAdmin' => 'status_admin',
            'sortOrder'   => 'sort_order',
        ];
    }
}