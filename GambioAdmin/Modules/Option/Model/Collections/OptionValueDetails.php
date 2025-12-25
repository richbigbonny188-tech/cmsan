<?php
/* --------------------------------------------------------------
   OptionValueDetails.php 2022-02-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueDetail;
use IteratorAggregate;
use Traversable;

/**
 * Class OptionValueDetails
 *
 * @package Gambio\Admin\Modules\Option\Model\Collections
 * @codeCoverageIgnore
 */
class OptionValueDetails implements IteratorAggregate
{
    /**
     * @var OptionValueDetail[]
     */
    private $optionValueDetails;
    
    
    /**
     * OptionValueDetails constructor.
     *
     * @param OptionValueDetail[] $optionValueDetails
     */
    private function __construct(array $optionValueDetails)
    {
        $this->optionValueDetails = [];
        foreach ($optionValueDetails as $optionValueDetail) {
            $this->optionValueDetails[$optionValueDetail->languageCode()] = $optionValueDetail;
        }
    }
    
    
    /**
     * @param OptionValueDetail ...$optionValueDetails
     *
     * @return OptionValueDetails
     */
    public static function create(OptionValueDetail ...$optionValueDetails): OptionValueDetails
    {
        return new self($optionValueDetails);
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function label(string $languageCode): string
    {
        return isset($this->optionValueDetails[$languageCode]) ? $this->optionValueDetails[$languageCode]->label() : '';
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function description(string $languageCode): string
    {
        return isset($this->optionValueDetails[$languageCode]) ? $this->optionValueDetails[$languageCode]->description() : '';
    }
    
    
    /**
     * @return Traversable|OptionValueDetail[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->optionValueDetails);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (OptionValueDetail $optionValueDetail): array {
            return $optionValueDetail->toArray();
        },
            array_values($this->optionValueDetails));
    }
}