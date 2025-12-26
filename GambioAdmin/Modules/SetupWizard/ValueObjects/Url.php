<?php
/* --------------------------------------------------------------
 Url.php 2022-08-05
 Gambio GmbH
 http://www.gambio.de

 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\ValueObjects;

/**
 * Class Url
 * @package Gambio\Admin\Modules\SetupWizard\ValueObjects
 */
class Url implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $url;
    
    
    /**
     * Url constructor.
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->url;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->url;
    }
}