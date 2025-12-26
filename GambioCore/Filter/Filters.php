<?php
/* --------------------------------------------------------------
   Filters.php 2022-05-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filter;

/**
 * Interface Filters
 *
 * @package Gambio\Core\Filter
 */
interface Filters
{
    public const OPERATION_AND_VALUE_STRING_PATTERN = '/^((eq|neq|gt|gte|lt|lte|like)\|)?(.*)$/';
    
    
    /**
     * @return Filter[]
     */
    public function filters(): array;
    
    
    /**
     * @return bool
     */
    public function useAndConcatenation(): bool;
}