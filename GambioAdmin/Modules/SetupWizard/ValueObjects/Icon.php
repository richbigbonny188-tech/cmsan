<?php
/* --------------------------------------------------------------
 Icon.php 2022-08-05
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
 * Class Icon
 * @package Gambio\Admin\Modules\SetupWizard\ValueObjects
 */
class Icon implements JsonSerializable
{
    /**
     * @var Url
     */
    protected $icon;
    
    
    /**
     * Icon constructor.
     *
     * @param Url $icon
     */
    public function __construct(Url $icon)
    {
        $this->icon = $icon;
    }
    
    
    /**
     * @return Url
     */
    public function value(): Url
    {
        return $this->icon;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->icon->jsonSerialize();
    }
}