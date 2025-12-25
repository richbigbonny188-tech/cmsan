<?php
/*--------------------------------------------------------------
   WidgetOptions.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption;
use InvalidArgumentException;
use IteratorAggregate;
use Webmozart\Assert\Assert;

/**
 * Class representing a collection of options for a widget.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Collections
 */
class WidgetOptions implements IteratorAggregate
{
    /**
     * Values.
     *
     * @var WidgetOption[]
     */
    private $values;
    
    
    /**
     * Constructor.
     *
     * @param array $values Values.
     */
    private function __construct(array $values)
    {
        $this->values = $values;
    }
    
    
    /**
     * Create instance.
     *
     * @param WidgetOption ...$options Values.
     *
     * @return WidgetOptions Instance.
     */
    public static function create(WidgetOption ...$options): self
    {
        return new self($options);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }
    
    
    /**
     * Return option by ID.
     *
     * @param string $id Option ID.
     *
     * @return WidgetOption Option with provided ID.
     */
    public function getById(string $id): WidgetOption
    {
        Assert::stringNotEmpty($id);
        
        /**
         * @var WidgetOption $option
         */
        foreach ($this->values as $option) {
            if ($id === $option->id()->value()) {
                return $option;
            }
        }
        
        throw new InvalidArgumentException("Option not found");
    }
    
    
    /**
     * Validate option update set.
     *
     * @param array $updateSet Widget's option update set.
     */
    public function validateUpdateSet(array $updateSet): void
    {
        $ids = array_map(function (WidgetOption $option) {
            return $option->id()->value();
        },
            $this->values);
        
        foreach (array_keys($updateSet) as $id) {
            Assert::inArray($id, $ids);
        }
    }
}