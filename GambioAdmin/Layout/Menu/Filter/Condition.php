<?php
/* --------------------------------------------------------------
 Condition.php 2020-01-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 29 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Filter;

/**
 * Class Condition
 * @package Gambio\Admin\Layout\Menu\Models
 */
final class Condition implements FilterCondition, FilterConditionArguments
{
    /**
     * @var string
     */
    private $filter;
    
    /**
     * @var array
     */
    private $args;
    
    
    /**
     * Condition constructor.
     *
     * @param string $filter
     * @param array  $args
     */
    private function __construct(string $filter, array $args)
    {
        $this->filter = $filter;
        $this->args   = $args;
    }
    
    
    /**
     * Factory method for condition.
     *
     * @param string $filter
     * @param array  $args
     *
     * @return static
     */
    public static function create(string $filter, array $args): self
    {
        return new static($filter, $args);
    }
    
    
    /**
     * @return string
     */
    public function filter(): string
    {
        return $this->filter;
    }
    
    
    /**
     * @return array
     */
    public function args(): array
    {
        return $this->args;
    }
}