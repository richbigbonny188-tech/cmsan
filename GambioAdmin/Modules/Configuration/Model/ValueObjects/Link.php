<?php
/* --------------------------------------------------------------
   Link.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Model\ValueObjects;

use JsonSerializable;
use Webmozart\Assert\Assert;

/**
 * Class Link
 *
 * @package Gambio\Admin\Modules\Configuration\Model\ValueObjects
 */
class Link implements JsonSerializable
{
    /**
     * @var string
     */
    private $label;
    
    /**
     * @var string
     */
    private $link;
    
    /**
     * @var bool
     */
    private $newWindow;
    
    /**
     * @var string
     */
    private $buttonText;
    
    
    /**
     * Link constructor.
     *
     * @param string $label
     * @param string $link
     * @param string $buttonText
     * @param bool   $newWindow
     */
    private function __construct(string $label, string $link, string $buttonText, bool $newWindow)
    {
        $this->label      = $label;
        $this->link       = $link;
        $this->buttonText = $buttonText;
        $this->newWindow  = $newWindow;
    }
    
    
    /**
     * @param string $label
     * @param string $link
     * @param string $buttonText
     * @param bool   $newWindow
     *
     * @return Link
     */
    public static function create(string $label, string $link, string $buttonText, bool $newWindow): Link
    {
        Assert::notWhitespaceOnly($label, 'Label can not be whitespace only.');
        Assert::notWhitespaceOnly($link, 'Link can not be whitespace only.');
        
        return new self($label, $link, $buttonText, $newWindow);
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'label'      => $this->label,
            'buttonText' => $this->buttonText,
            'link'       => $this->link,
            'newWindow'  => $this->newWindow,
        ];
    }
}