<?php
/*--------------------------------------------------------------
   CustomerReviewServiceProvider.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Reviews;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Customer\App\CustomerProductRepository;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\App\Actions\JSON\FetchAllCustomerReviewsActions;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\App\CustomerReviewReadService;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\App\CustomerReviewRepository;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\App\Data\ReviewMapper;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\App\Data\ReviewReader;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\CustomerReviewFactory;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\CustomerReviewReadService as CustomerReviewReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Reviews\Services\CustomerReviewRepository as CustomerReviewRepositoryInterface;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class CustomerReviewServiceProvider
 *
 * @package Gambio\Admin\Modules\CustomerMemo
 * @codeCoverageIgnore
 */
class CustomerReviewServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CustomerReviewReadServiceInterface::class,
            FetchAllCustomerReviewsActions::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(ReviewReader::class)->addArgument(Connection::class);
        $this->application->registerShared(CustomerReviewFactory::class);
        $this->application->registerShared(ReviewMapper::class);
        
        $this->application->registerShared(CustomerReviewRepositoryInterface::class, CustomerReviewRepository::class)
            ->addArgument(ReviewReader::class)
            ->addArgument(ReviewMapper::class);
        
        $this->application->registerShared(CustomerReviewReadServiceInterface::class, CustomerReviewReadService::class)
            ->addArgument(CustomerReviewFactory::class)
            ->addArgument(CustomerReviewRepositoryInterface::class);
        
        $this->application->registerShared(FetchAllCustomerReviewsActions::class)
            ->addArgument(CustomerReviewReadServiceInterface::class)
            ->addArgument(CustomerProductRepository::class)
            ->addArgument(UserPreferences::class);
    }
}