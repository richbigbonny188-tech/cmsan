<?php
/*--------------------------------------------------------------
   CustomerNewsletterMapper.php 2022-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\App\Data;

use Gambio\Admin\Modules\Newsletter\Model\Collections\CustomerIds;
use Gambio\Admin\Modules\Newsletter\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterFactory;

/**
 * Class CustomerNewsletterMapper
 *
 * @package Gambio\Admin\Modules\Newsletter\App\Data
 */
class CustomerNewsletterMapper extends CustomerNewsletterFactory
{
    /**
     * @param array ...$data
     *
     * @return CustomerIds
     */
    public function mapCustomerIds(array ...$data): CustomerIds
    {
        return $this->createCustomerIds(...array_map([$this, 'mapCustomerId'], $data));
    }
    
    
    /**
     * @param array $data
     *
     * @return CustomerId
     */
    public function mapCustomerId(array $data): CustomerId
    {
        return $this->createCustomerId((int)$data['customers_id']);
    }
}