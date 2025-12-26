<?php
/*--------------------------------------------------------------
   OptionUpdateSet.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption;

use ArrayIterator;
use IteratorAggregate;
use Webmozart\Assert\Assert;

/**
 * Class representing an option update set.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption
 */
class OptionUpdateSet implements IteratorAggregate
{
    /**
     * Values.
     *
     * @var array
     */
    private $values;
    
    
    /**
     * Constructor.
     *
     * @param array $values Values.
     */
    private function __construct(array $values)
    {
        Assert::isMap($values);
        
        $this->values = $values;
    }
    
    
    /**
     * Create instance.
     *
     * @param array $updateSet Values.
     *
     * @return OptionUpdateSet Instance.
     */
    public static function create(array $updateSet): self
    {
        return new self($updateSet);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }
    
    
    /**
     * Return as array.
     *
     * @return array Values as array.
     */
    public function toArray(): array
    {
        return $this->values;
    }
}