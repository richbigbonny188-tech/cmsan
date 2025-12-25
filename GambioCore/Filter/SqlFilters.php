<?php
/* --------------------------------------------------------------
   SqlFilters.php 2023-06-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filter;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class SqlFilters
 *
 * @package Gambio\Core\Filter
 */
abstract class SqlFilters extends LooseFilters
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
     * @return array
     */
    abstract public static function attributesForNumericOperations(): array;
    
    
    /**
     * @param QueryBuilder $query
     */
    public function applyToQuery(QueryBuilder $query): void
    {
        foreach ($this->filters as $filter) {
            $attribute = $filter->attribute();
            if (array_key_exists($attribute, static::attributeColumnMapping()) === false) {
                continue;
            }
            
            $operation = $filter->operation();
            $column    = static::attributeColumnMapping()[$attribute];
            $value     = $filter->value();
            $parameter = ':criteria_filter_' . str_replace('.', '_', $column);
            
            if ($operation === 'like' && in_array($attribute, static::attributesForLikeOperation(), true)) {
                $value      = str_replace(['%', '*'], ['\%', '%'], $value);
                $filterExpr = $query->expr()->like($column, $parameter);
            } elseif ($operation === 'gt' && in_array($attribute, static::attributesForNumericOperations(), true)) {
                $filterExpr = $query->expr()->gt($column, $parameter);
            } elseif ($operation === 'gte' && in_array($attribute, static::attributesForNumericOperations(), true)) {
                $filterExpr = $query->expr()->gte($column, $parameter);
            } elseif ($operation === 'lt' && in_array($attribute, static::attributesForNumericOperations(), true)) {
                $filterExpr = $query->expr()->lt($column, $parameter);
            } elseif ($operation === 'lte' && in_array($attribute, static::attributesForNumericOperations(), true)) {
                $filterExpr = $query->expr()->lte($column, $parameter);
            } elseif ($operation === 'neq') {
                $filterExpr = $query->expr()->neq($column, $parameter);
            } else {
                $filterExpr = $query->expr()->eq($column, $parameter);
            }
            
            if ($this->useAndConcatenation) {
                $query->andWhere($filterExpr)->setParameter(ltrim($parameter, ':'), $value);
            } else {
                $query->orWhere($filterExpr)->setParameter(ltrim($parameter, ':'), $value);
            }
        }
    }
}