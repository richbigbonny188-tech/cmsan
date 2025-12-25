<?php
/*--------------------------------------------------------------
   CustomerNewsletterRepository.php 2022-11-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\App;

use Gambio\Admin\Modules\Newsletter\App\Data\CustomerNewsletterMapper;
use Gambio\Admin\Modules\Newsletter\App\Data\NewsletterReader;
use Gambio\Admin\Modules\Newsletter\App\Data\NewsletterWriter;
use Gambio\Admin\Modules\Newsletter\Model\Collections\CustomerIds;
use Gambio\Admin\Modules\Newsletter\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterRepository as CustomerNewsletterRepositoryInterface;

/**
 * Class CustomerNewsletterRepository
 *
 * @package Gambio\Admin\Modules\Newsletter\App
 */
class CustomerNewsletterRepository implements CustomerNewsletterRepositoryInterface
{
    private NewsletterReader $reader;
    private NewsletterWriter         $writer;
    private CustomerNewsletterMapper $mapper;
    
    
    /**
     * @param NewsletterReader         $reader
     * @param NewsletterWriter         $writer
     * @param CustomerNewsletterMapper $mapper
     */
    public function __construct(NewsletterReader $reader, NewsletterWriter $writer, CustomerNewsletterMapper $mapper)
    {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->mapper = $mapper;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSubscribedCustomers(): CustomerIds
    {
        return $this->mapper->mapCustomerIds(...$this->reader->getSubscribedCustomers());
    }
    
    
    /**
     * @inheritDoc
     */
    public function isCustomerSubscribed(CustomerId $customerId): bool
    {
        return $this->reader->isCustomerSubscribed($customerId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function subscribe(CustomerId $customerId, CustomerId $adminId): void
    {
        $this->writer->subscribe($customerId, $adminId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function unsubscribe(CustomerId $customerId): void
    {
        $this->writer->unsubscribe($customerId);
    }
}