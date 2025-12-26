<?php
/* --------------------------------------------------------------
   LanguageId.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class LanguageId
 *
 * @package Gambio\Admin\Modules\Language\Model\ValueObjects
 */
class LanguageId
{
    /**
     * @var int
     */
    private $value;
    
    
    /**
     * LanguageId constructor.
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
     * @return LanguageId
     */
    public static function create(int $value): LanguageId
    {
        Assert::greaterThan($value, 0, 'Provided language ID is invalid. Got: %s');
        
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