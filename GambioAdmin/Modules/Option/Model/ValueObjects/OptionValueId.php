<?php
/* --------------------------------------------------------------
   OptionValueId.php 2021-03-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class OptionValueId
 *
 * @package Gambio\Admin\Modules\Option\Model\ValueObjects
 * @codeCoverageIgnore
 */
class OptionValueId
{
    /**
     * @var int
     */
    private $value;
    
    
    /**
     * OptionValueId constructor.
     *
     * @param int $value
     */
    private function __construct(int $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @param int $value
     *
     * @return OptionValueId
     */
    public static function create(int $value): OptionValueId
    {
        Assert::greaterThan($value, 0, 'Given option value ID must be greater than 0. Got: %s');
        
        return new self($value);
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }
}