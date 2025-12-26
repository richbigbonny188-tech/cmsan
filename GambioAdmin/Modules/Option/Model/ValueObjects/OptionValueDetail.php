<?php
/* --------------------------------------------------------------
   OptionValueDetail.php 2021-03-31
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
 * Class OptionValueDetail
 *
 * @package Gambio\Admin\Modules\Option\Model\ValueObjects
 * @codeCoverageIgnore
 */
class OptionValueDetail
{
    /**
     * @var string
     */
    private $languageCode;
    
    /**
     * @var string
     */
    private $label;
    
    /**
     * @var string
     */
    private $description;
    
    
    /**
     * OptionDetail constructor.
     *
     * @param string $languageCode
     * @param string $label
     * @param string $description
     */
    private function __construct(string $languageCode, string $label, string $description)
    {
        $this->languageCode = $languageCode;
        $this->label        = $label;
        $this->description  = $description;
    }
    
    
    /**
     * @param string $languageCode
     * @param string $label
     * @param string $description
     *
     * @return OptionValueDetail
     */
    public static function create(string $languageCode, string $label, string $description): OptionValueDetail
    {
        Assert::regex($languageCode,
                      '/^[a-zA-Z]{2}$/',
                      'Given language code does not match two digit ISO format. Got: %s');
        Assert::notWhitespaceOnly($label, 'Label can not be whitespace only.');
        
        return new self($languageCode, $label, $description);
    }
    
    
    /**
     * @return string
     */
    public function languageCode(): string
    {
        return $this->languageCode;
    }
    
    
    /**
     * @return string
     */
    public function label(): string
    {
        return $this->label;
    }
    
    
    /**
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'languageCode' => $this->languageCode(),
            'label'        => $this->label(),
            'description'  => $this->description(),
        ];
    }
}