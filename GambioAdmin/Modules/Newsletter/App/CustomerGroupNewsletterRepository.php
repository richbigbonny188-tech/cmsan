<?php
/* --------------------------------------------------------------
   CustomerGroupNewsletterRepository.php 2023-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\App;

use Gambio\Admin\Modules\Newsletter\App\Data\NewsletterWriter;
use Gambio\Admin\Modules\Newsletter\Model\ValueObjects\CustomerGroup;
use Gambio\Admin\Modules\Newsletter\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Newsletter\Services\CustomerGroupNewsletterRepository as CustomerGroupNewsletterRepositoryInterface;

/**
 * Class CustomerGroupNewsletterRepository
 *
 * @package Gambio\Admin\Modules\Newsletter\App
 */
class CustomerGroupNewsletterRepository implements CustomerGroupNewsletterRepositoryInterface
{
    
    /**
     * @param NewsletterWriter $writer
     */
    public function __construct(private NewsletterWriter $writer)
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function changeCustomerGroup(CustomerId $customerId, CustomerGroup $customerGroup): void
    {
        $this->writer->changeCustomerGroup($customerId, $customerGroup);
    }
}