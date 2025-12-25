<?php
/*--------------------------------------------------------------
   SqlSearch.php 2023-06-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\Filter;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class SqlSearch
 *
 * @package Gambio\Core\Filter
 */
abstract class SqlSearch extends LooseSearch
{
    /**
     * @return array
     */
    abstract public static function attributeColumnMapping(): array;
    
    /**
     * @return array
     */
    abstract public static function attributesForLikeOperation(): array;
    
    /**
     * @param QueryBuilder $query
     */
    public function applyToQuery(QueryBuilder $query): void
    {
        foreach (static::attributesForLikeOperation() as $attribute) {
    
            if (array_key_exists($attribute, static::attributeColumnMapping()) === false) {
                
                continue;
            }
            
            $column    = static::attributeColumnMapping()[$attribute];
            $value     = '%' . $this->keyword() . '%';
            $parameter = ':criteria_search_' . str_replace('.', '_', $attribute);
            $expr      = $query->expr()->like($column, $parameter);
            
            $query->orWhere($expr)->setParameter(ltrim($parameter, ':'), $value);
        }
    }
}