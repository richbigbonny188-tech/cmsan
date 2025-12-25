<?php
/* --------------------------------------------------------------
 Text.php 2022-08-05
 Gambio GmbH
 http://www.gambio.de

 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\ValueObjects;

use JsonSerializable;

/**
 * Class Text
 * @package Gambio\Admin\Modules\SetupWizard\ValueObjects
 */
class Text implements JsonSerializable
{
    /**
     * @var string
     */
    protected $text;
    
    
    /**
     * Text constructor.
     *
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->text;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->text;
    }
}