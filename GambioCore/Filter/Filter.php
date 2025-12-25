<?php
/* --------------------------------------------------------------
   Filter.php 2021-11-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filter;

/**
 * Interface Filter
 *
 * @package Gambio\Core\Filter
 */
interface Filter
{
    public const ALLOWED_OPERATIONS = ['eq', 'neq', 'gt', 'gte', 'lt', 'ltq', 'like'];
    
    
    /**
     * @return string
     */
    public function attribute(): string;
    
    
    /**
     * @return string
     */
    public function operation(): string;
    
    
    /**
     * @return string
     */
    public function value(): string;
}