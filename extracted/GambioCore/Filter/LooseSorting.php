<?php
/* --------------------------------------------------------------
   LooseSorting.php 2020-10-19
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
 * Class LooseSorting
 *
 * @package Gambio\Core\Filter
 */
class LooseSorting implements Sorting
{
    /**
     * @var string|null
     */
    protected $sorting;
    
    
    /**
     * LooseSorting constructor.
     *
     * @param string|null $sorting
     */
    protected function __construct(?string $sorting = null)
    {
        $this->sorting = $sorting;
    }
    
    
    /**
     * @param string|null $sorting
     *
     * @return static
     */
    public static function create(?string $sorting = null)
    {
        if ($sorting !== null) {
            Assert::regex($sorting, self::SORTING_PATTERN, 'Sorting does not match expected pattern. Got: %s');
        }
        
        return new static($sorting);
    }
    
    
    /**
     * @inheritDoc
     */
    public function sorting(): ?string
    {
        return $this->sorting;
    }
}