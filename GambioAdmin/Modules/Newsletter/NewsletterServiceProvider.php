<?php
/* --------------------------------------------------------------
   NewsletterServiceProvider.php 2023-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Customer\Model\Events\CustomersCustomerGroupUpdated;
use Gambio\Admin\Modules\Newsletter\App\Actions\Json\UpdateNewsletterSubscriptionAction;
use Gambio\Admin\Modules\Newsletter\App\Data\CustomerNewsletterMapper;
use Gambio\Admin\Modules\Newsletter\App\Data\NewsletterReader;
use Gambio\Admin\Modules\Newsletter\App\Data\NewsletterWriter;
use Gambio\Admin\Modules\Newsletter\App\EventListeners\ChangeNewsletterCustomerGroupEventListener;
use Gambio\Admin\Modules\Newsletter\Services\CustomerGroupNewsletterWriteService;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterFactory;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class NewsletterServiceProvider
 *
 * @package Gambio\Admin\Modules\Newsletter
 */
class NewsletterServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CustomerNewsletterFactory::class,
            Services\CustomerNewsletterReadService::class,
            Services\CustomerNewsletterRepository::class,
            Services\CustomerNewsletterWriteService::class,
            UpdateNewsletterSubscriptionAction::class,
            ChangeNewsletterCustomerGroupEventListener::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(CustomerNewsletterMapper::class);
        
        $this->application->registerShared(CustomerNewsletterFactory::class);
        
        $this->application->registerShared(CustomerNewsletterMapper::class)
            ->addArgument(CustomerNewsletterFactory::class);
        
        $this->application->registerShared(NewsletterReader::class)->addArgument(Connection::class);
        
        $this->application->registerShared(NewsletterWriter::class)->addArgument(Connection::class);
        
        $this->application->registerShared(Services\CustomerNewsletterRepository::class,
                                           App\CustomerNewsletterRepository::class)
            ->addArgument(NewsletterReader::class)
            ->addArgument(NewsletterWriter::class)
            ->addArgument(CustomerNewsletterMapper::class);
        
        $this->application->registerShared(Services\CustomerNewsletterReadService::class,
                                           App\CustomerNewsletterReadService::class)
            ->addArgument(Services\CustomerNewsletterRepository::class)
            ->addArgument(CustomerNewsletterFactory::class);
        
        $this->application->registerShared(Services\CustomerNewsletterWriteService::class,
                                           App\CustomerNewsletterWriteService::class)
            ->addArgument(Services\CustomerNewsletterRepository::class)
            ->addArgument(CustomerNewsletterFactory::class);
        
        $this->application->registerShared(Services\CustomerGroupNewsletterWriteService::class,
                                           App\CustomerGroupNewsletterWriteService::class)
            ->addArgument(Services\CustomerGroupNewsletterRepository::class)
            ->addArgument(CustomerNewsletterFactory::class);
        
        $this->application->registerShared(UpdateNewsletterSubscriptionAction::class)
            ->addArgument(Services\CustomerNewsletterWriteService::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(Services\CustomerGroupNewsletterRepository::class,
                                           App\CustomerGroupNewsletterRepository::class)
            ->addArgument(NewsletterWriter::class)
            ->addArgument(CustomerNewsletterFactory::class);
        
        $this->application->registerShared(ChangeNewsletterCustomerGroupEventListener::class)
            ->addArgument(CustomerGroupNewsletterWriteService::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->attachEventListener(CustomersCustomerGroupUpdated::class,
                                                ChangeNewsletterCustomerGroupEventListener::class);
    }
}
