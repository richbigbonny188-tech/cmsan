<?php
/* --------------------------------------------------------------
   OptionDetails.php 2021-09-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionDetail;
use IteratorAggregate;
use Traversable;

/**
 * Class OptionDetails
 *
 * @package Gambio\Admin\Modules\Option\Model\Collections
 * @codeCoverageIgnore
 */
class OptionDetails implements IteratorAggregate
{
    /**
     * @var OptionDetail[]
     */
    private $optionDetails;
    
    
    /**
     * OptionDetails constructor.
     *
     * @param OptionDetail[] $optionDetails
     */
    private function __construct(array $optionDetails)
    {
        $this->optionDetails = [];
        foreach ($optionDetails as $optionDetail) {
            $this->optionDetails[$optionDetail->languageCode()] = $optionDetail;
        }
    }
    
    
    /**
     * @param OptionDetail ...$optionDetails
     *
     * @return OptionDetails
     */
    public static function create(OptionDetail ...$optionDetails): OptionDetails
    {
        return new self($optionDetails);
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function label(string $languageCode): string
    {
        return isset($this->optionDetails[$languageCode]) ? $this->optionDetails[$languageCode]->label() : '';
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function adminLabel(string $languageCode): string
    {
        return isset($this->optionDetails[$languageCode]) ? $this->optionDetails[$languageCode]->adminLabel() : '';
    }
    
    
    /**
     * @param string $languageCode
     *
     * @return string
     */
    public function description(string $languageCode): string
    {
        return isset($this->optionDetails[$languageCode]) ? $this->optionDetails[$languageCode]->description() : '';
    }
    
    
    /**
     * @return Traversable|OptionDetail[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->optionDetails);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (OptionDetail $optionDetail): array {
            return $optionDetail->toArray();
        },
            array_values($this->optionDetails));
    }
}