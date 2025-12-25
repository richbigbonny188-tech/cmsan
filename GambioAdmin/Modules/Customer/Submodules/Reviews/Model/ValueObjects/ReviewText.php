<?php
/*--------------------------------------------------------------
   ReviewText.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ReviewText
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Reviews\Model\Entities
 */
class ReviewText
{
    private string $code;
    private string $text;
    
    
    /**
     * @param string $code
     * @param string $text
     */
    private function __construct(string $code, string $text)
    {
        $this->code = $code;
        $this->text = $text;
    }
    
    
    /**
     * @param string $code
     * @param string $text
     *
     * @return ReviewText
     */
    public static function create(string $code, string $text): ReviewText
    {
        Assert::minLength($code, 2);
        Assert::minLength($text, 50);
        
        return new self($code, $text);
    }
    
    
    /**
     * @return string
     */
    public function code(): string
    {
        return $this->code;
    }
    
    
    /**
     * @return string
     */
    public function text(): string
    {
        return $this->text;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code(),
            'text' => $this->text(),
        ];
    }
}