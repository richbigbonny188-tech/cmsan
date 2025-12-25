<?php
/*--------------------------------------------------------------
   CustomerDisallowedShippingMethodsRepository.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ShippingModule\App;

use Gambio\Admin\Modules\ShippingModule\App\Data\CustomerDisallowedShippingMethodsMapper;
use Gambio\Admin\Modules\ShippingModule\App\Data\CustomerDisallowedShippingMethodsReader;
use Gambio\Admin\Modules\ShippingModule\App\Data\CustomerDisallowedShippingMethodsWriter;
use Gambio\Admin\Modules\ShippingModule\Model\Collections\ShippingMethods;
use Gambio\Admin\Modules\ShippingModule\Model\ValueObjects\ShippingMethodId;
use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsRepository as CustomerDisallowedShippingMethodsRepositoryInterface;

/**
 * Class CustomerDisallowedShippingMethodsRepository
 *
 * @package Gambio\Admin\Modules\ShippingModule\App
 */
class CustomerDisallowedShippingMethodsRepository implements CustomerDisallowedShippingMethodsRepositoryInterface
{
    private CustomerDisallowedShippingMethodsReader $reader;
    private CustomerDisallowedShippingMethodsWriter $writer;
    private CustomerDisallowedShippingMethodsMapper $mapper;
    
    
    /**
     * @param CustomerDisallowedShippingMethodsReader $reader
     * @param CustomerDisallowedShippingMethodsWriter $writer
     * @param CustomerDisallowedShippingMethodsMapper $mapper
     */
    public function __construct(
        CustomerDisallowedShippingMethodsReader $reader,
        CustomerDisallowedShippingMethodsWriter $writer,
        CustomerDisallowedShippingMethodsMapper $mapper
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->mapper = $mapper;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomersDisallowedShippingMethods(int $customerId): ShippingMethods
    {
        return $this->mapper->mapShippingMethods($this->reader->getCustomersDisallowedShippingMethods($customerId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function setDisallowedShippingMethods(int $customerId, ShippingMethodId ...$shippingMethodIds): void
    {
        $this->writer->setDisallowedShippingMethods($customerId, ...$shippingMethodIds);
    }
}