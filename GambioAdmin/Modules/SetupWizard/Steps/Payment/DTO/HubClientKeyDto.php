<?php
/* --------------------------------------------------------------
 HubClientKeyDto.php 2020-08-18
 Gambio GmbH
 http://www.gambio.de

 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\Payment\DTO;

/**
 * Class HubClientKeyDto
 * @package Gambio\Admin\Modules\SetupWizard\Steps\Payment\DTO
 */
class HubClientKeyDto
{
    /**
     * @var string
     */
    protected $clientKey;
    
    
    /**
     * HubClientKeyDto constructor.
     *
     * @param string $clientKey
     */
    public function __construct(string $clientKey)
    {
        $this->clientKey = $clientKey;
    }
    
    
    public function clientKey(): string
    {
        return $this->clientKey;
    }
}