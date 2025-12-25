<?php
/*--------------------------------------------------------------------
 OptionAndOptionValueId.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class OptionAndOptionValueId
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects
 */
class OptionAndOptionValueId
{
    /**
     * OptionAndOptionValueId constructor.
     *
     * @param int $optionId
     * @param int $optionValueId
     */
    private function __construct(private int $optionId, private int $optionValueId) { }
    
    
    /**
     * @param int $optionId
     * @param int $optionValueId
     *
     * @return OptionAndOptionValueId
     */
    public static function create(int $optionId, int $optionValueId): OptionAndOptionValueId
    {
        Assert::greaterThan($optionId, 0, 'The option ID must be a positive integer. Got: %s');
        Assert::greaterThan($optionValueId, 0, 'The option value ID must be a positive integer. Got: %s');
        
        return new static($optionId, $optionValueId);
    }
    
    
    /**
     * @return int
     */
    public function optionId(): int
    {
        return $this->optionId;
    }
    
    
    /**
     * @return int
     */
    public function optionValueId(): int
    {
        return $this->optionValueId;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'optionId'      => $this->optionId(),
            'optionValueId' => $this->optionValueId(),
        ];
    }
    
    
    /**
     * @param OptionAndOptionValueId $optionAndOptionValueId
     *
     * @return bool
     */
    public function equals(OptionAndOptionValueId $optionAndOptionValueId): bool
    {
        return (string)$this === (string)$optionAndOptionValueId;
    }
    
    
    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->optionId() . '-' . $this->optionValueId();
    }
    
    
    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}