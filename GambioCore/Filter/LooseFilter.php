<?php
/* --------------------------------------------------------------
   LooseFilter.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filter;

use Webmozart\Assert\Assert;

/**
 * Class LooseFilter
 *
 * @package Gambio\Core\Filter
 */
class LooseFilter implements Filter
{
    /**
     * @var string
     */
    protected $attribute;
    
    /**
     * @var string
     */
    protected $operation;
    
    /**
     * @var string
     */
    protected $value;
    
    
    /**
     * LooseFilter constructor.
     *
     * @param string $attribute
     * @param string $operation
     * @param string $value
     */
    protected function __construct(string $attribute, string $operation, string $value)
    {
        $this->attribute = $attribute;
        $this->operation = $operation;
        $this->value     = $value;
    }
    
    
    /**
     * @param string $attribute
     * @param string $operation
     * @param string $value
     *
     * @return LooseFilter
     */
    public static function create(string $attribute, string $operation, string $value): LooseFilter
    {
        Assert::oneOf($operation,
                      self::ALLOWED_OPERATIONS,
                      'Invalid operation provided. Must be one of: ' . implode(', ', self::ALLOWED_OPERATIONS));
        
        return new static($attribute, $operation, $value);
    }
    
    
    /**
     * @inheritDoc
     */
    public function attribute(): string
    {
        return $this->attribute;
    }
    
    
    /**
     * @inheritDoc
     */
    public function operation(): string
    {
        return $this->operation;
    }
    
    
    /**
     * @inheritDoc
     */
    public function value(): string
    {
        return $this->value;
    }
}