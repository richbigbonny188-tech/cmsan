<?php
/*--------------------------------------------------------------
   AbstractImageText.php 2021-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\ValueObjects;

/**
 * Class AbstractImageText
 * @package Gambio\Admin\Modules\ImageList\Model\ValueObjects
 */
abstract class AbstractImageText
{
    /**
     * @var string
     */
    private $languageCode;
    
    /**
     * @var string
     */
    private $text;
    
    
    /**
     * AbstractImageText constructor.
     *
     * @param string $languageCode
     * @param string $text
     */
    protected function __construct(string $languageCode, string $text)
    {
        $this->languageCode = $languageCode;
        $this->text         = $text;
    }
    
    
    /**
     * @return string
     */
    abstract public function type(): string;
    
    
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
    public function text(): string
    {
        return $this->text;
    }
    
    
    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'languageCode' => $this->languageCode(),
            'text'         => $this->text()
        ];
    }
}