<?php
/* --------------------------------------------------------------
   LanguageCode.php 2020-10-19
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
 * Class LanguageCode
 *
 * @package Gambio\Admin\Modules\Language\Model\ValueObjects
 */
class LanguageCode
{
    /**
     * @var string
     */
    private $value;
    
    
    /**
     * LanguageCode constructor.
     *
     * @param string $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @param string $value
     *
     * @return LanguageCode
     */
    public static function create(string $value): LanguageCode
    {
        Assert::regex($value, '/^[a-zA-Z]{2}$/', 'Language code must be a two character ISO code.');
        
        return new self($value);
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
}