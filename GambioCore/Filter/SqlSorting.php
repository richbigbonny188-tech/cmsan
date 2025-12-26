<?php
/* --------------------------------------------------------------
   SqlSorting.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filter;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class SqlSorting
 *
 * @package Gambio\Core\Filter
 */
abstract class SqlSorting extends LooseSorting
{
    /**
     * @return array
     */
    abstract public static function attributeColumnMapping(): array;
    
    
    /**
     * @param QueryBuilder $query
     */
    public function applyToQuery(QueryBuilder $query): void
    {
        foreach (explode(',', $this->sorting ?? '') as $attribute) {
            $trimmedAttribute = trim($attribute, '-+');
            if (array_key_exists($trimmedAttribute, static::attributeColumnMapping()) === false) {
                continue;
            }
            
            $order  = (strpos($attribute, '-') === 0) ? 'desc' : 'asc';
            $column = static::attributeColumnMapping()[$trimmedAttribute];
            
            $query->addOrderBy($column, $order);
        }
    }
}