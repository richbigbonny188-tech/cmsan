<?php
/*--------------------------------------------------------------
   CustomerDisallowedPaymentMethodsRepository.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\PaymentModule\App;

use Gambio\Admin\Modules\PaymentModule\App\Data\CustomerDisallowedPaymentMethodsMapper;
use Gambio\Admin\Modules\PaymentModule\App\Data\CustomerDisallowedPaymentMethodsReader;
use Gambio\Admin\Modules\PaymentModule\App\Data\CustomerDisallowedPaymentMethodsWriter;
use Gambio\Admin\Modules\PaymentModule\Model\Collections\PaymentMethods;
use Gambio\Admin\Modules\PaymentModule\Model\ValueObjects\PaymentMethodId;
use Gambio\Admin\Modules\PaymentModule\Services\CustomerDisallowedPaymentMethodsRepository as CustomerDisallowedPaymentMethodsRepositoryInterface;

/**
 * Class CustomerDisallowedPaymentMethodsRepository
 *
 * @package Gambio\Admin\Modules\PaymentModule\App
 */
class CustomerDisallowedPaymentMethodsRepository implements CustomerDisallowedPaymentMethodsRepositoryInterface
{
    private CustomerDisallowedPaymentMethodsReader $reader;
    private CustomerDisallowedPaymentMethodsWriter $writer;
    private CustomerDisallowedPaymentMethodsMapper $mapper;
    
    
    /**
     * @param CustomerDisallowedPaymentMethodsReader $reader
     * @param CustomerDisallowedPaymentMethodsWriter $writer
     * @param CustomerDisallowedPaymentMethodsMapper $mapper
     */
    public function __construct(
        CustomerDisallowedPaymentMethodsReader $reader,
        CustomerDisallowedPaymentMethodsWriter $writer,
        CustomerDisallowedPaymentMethodsMapper $mapper
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->mapper = $mapper;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomersDisallowedPaymentMethods(int $customerId): PaymentMethods
    {
        return $this->mapper->mapPaymentMethods($this->reader->getCustomersDisallowedPaymentMethods($customerId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function setDisallowedPaymentMethods(int $customerId, PaymentMethodId ...$methodsIds): void
    {
        $this->writer->setDisallowedPaymentMethods($customerId, ...$methodsIds);
    }
}