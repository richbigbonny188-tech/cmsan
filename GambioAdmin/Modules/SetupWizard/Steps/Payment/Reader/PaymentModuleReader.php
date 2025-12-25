<?php
/* --------------------------------------------------------------
 PaymentModuleReader.php 2020-08-18
 Gambio GmbH
 http://www.gambio.de

 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\Payment\Reader;

use Gambio\Admin\Modules\SetupWizard\Steps\Payment\DTO\HubClientKeyDto;
use Gambio\Admin\Modules\SetupWizard\Steps\Payment\DTO\InstalledPaymentModulesDto;

/**
 * Interface PaymentModuleReader
 * @package Gambio\Admin\Modules\SetupWizard\Steps\Payment\Reader
 */
interface PaymentModuleReader
{
    /**
     * @return InstalledPaymentModulesDto
     */
    public function paymentMethods(): InstalledPaymentModulesDto;
    
    
    /**
     * @return HubClientKeyDto
     */
    public function hubClientKey(): HubClientKeyDto;
}