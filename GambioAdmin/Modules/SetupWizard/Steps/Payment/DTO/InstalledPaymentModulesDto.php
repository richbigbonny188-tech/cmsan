<?php
/* --------------------------------------------------------------
 InstalledPaymentModulesDto.php 2020-08-18
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
 * Class InstalledPaymentModulesDto
 * @package Gambio\Admin\Modules\SetupWizard\Steps\Payment\DTO
 */
class InstalledPaymentModulesDto
{
    /**
     * @var string
     */
    protected $paymentMethods;
    
    
    /**
     * InstalledPaymentModulesDto constructor.
     *
     * @param string $paymentMethods
     */
    public function __construct(string $paymentMethods)
    {
        $this->paymentMethods = $paymentMethods;
    }
    
    
    /**
     * @return string
     */
    public function paymentMethods(): string
    {
        return $this->paymentMethods;
    }
}