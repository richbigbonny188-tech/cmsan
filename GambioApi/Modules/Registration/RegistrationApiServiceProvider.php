<?php
/*--------------------------------------------------------------
   RegistrationApiServiceProvider.php 2022-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Registration;

use Gambio\Admin\Modules\Customer\Services\CustomerFactory;
use Gambio\Admin\Modules\Customer\Services\CustomerPasswordWriteService;
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressFactory;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressRepository;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressWriteService;
use Gambio\Api\Modules\Registration\App\Actions\RegistrationAction;
use Gambio\Api\Modules\Registration\App\RegistrationApiRequestParser;
use Gambio\Api\Modules\Registration\App\RegistrationApiRequestValidator;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;

/**
 * Class RegistrationApiServiceProvider
 *
 * @package Gambio\Api\Modules\Registration
 * @codeCoverageIgnore
 */
class RegistrationApiServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            RegistrationAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(RegistrationApiRequestParser::class)
            ->addArgument(CustomerFactory::class)
            ->addArgument(CustomerAddressFactory::class);
        
        $this->application->registerShared(RegistrationApiRequestValidator::class);
        
        $this->application->registerShared(RegistrationAction::class)
            ->addArgument(CustomerWriteService::class)
            ->addArgument(CustomerDefaultAddressWriteService::class)
            ->addArgument(CustomerPasswordWriteService::class)
            ->addArgument(RegistrationApiRequestValidator::class)
            ->addArgument(RegistrationApiRequestParser::class)
            ->addArgument(CustomerAddressRepository::class);
    }
}