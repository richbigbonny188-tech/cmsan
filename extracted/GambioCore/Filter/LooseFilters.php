<?php
/* --------------------------------------------------------------
   LooseFilters.php 2022-05-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filter;

use Webmozart\Assert\Assert;

/**
 * Class LooseFilters
 *
 * @package Gambio\Core\Filter
 */
class LooseFilters implements Filters
{
    protected bool $useAndConcatenation;
    
    /**
     * @var Filter[]
     */
    protected array $filters;
    
    
    /**
     * LooseFilters constructor.
     *
     * @param bool   $useAndConcatenation
     * @param Filter ...$filters
     */
    private function __construct(bool $useAndConcatenation, Filter ...$filters)
    {
        $this->useAndConcatenation = $useAndConcatenation;
        $this->filters             = $filters;
    }
    
    
    /**
     * @param Filter ...$filters
     *
     * @return static
     */
    public static function create(Filter ...$filters)
    {
        return new static(true, ...$filters);
    }
    
    
    /**
     * @param Filter ...$filters
     *
     * @return static
     */
    public static function createWithAndConcatenation(Filter ...$filters)
    {
        return new static(true, ...$filters);
    }
    
    
    /**
     * @param Filter ...$filters
     *
     * @return static
     */
    public static function createWithOrConcatenation(Filter ...$filters)
    {
        return new static(false, ...$filters);
    }
    
    
    /**
     * @param array $filterMap
     * @param bool  $useAndConcatenation
     *
     * @return static
     */
    public static function createFromMap(array $filterMap, bool $useAndConcatenation = true)
    {
        Assert::isMap($filterMap, 'Provided filters array need to be a map.');
        Assert::allRegex($filterMap,
                         self::OPERATION_AND_VALUE_STRING_PATTERN,
                         'Provided filtern don\'t match expected pattern: ' . self::OPERATION_AND_VALUE_STRING_PATTERN);
        
        $filters = [];
        foreach ($filterMap as $attribute => $filter) {
            $value     = $filter;
            $operation = (strpos($value, '*') !== false) ? 'like' : 'eq';
            if (strpos($filter, '|') !== false) {
                $value     = substr($filter, strpos($filter, '|') + 1);
                $operation = substr($filter, 0, strpos($filter, '|'));
            }
            
            $filters[] = LooseFilter::create($attribute, $operation, $value);
        }
        
        if ($useAndConcatenation) {
            return static::createWithAndConcatenation(...$filters);
        }
        
        return static::createWithOrConcatenation(...$filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function filters(): array
    {
        return $this->filters;
    }
    
    
    /**
     * @inheritDoc
     */
    public function useAndConcatenation(): bool
    {
        return $this->useAndConcatenation;
    }
}