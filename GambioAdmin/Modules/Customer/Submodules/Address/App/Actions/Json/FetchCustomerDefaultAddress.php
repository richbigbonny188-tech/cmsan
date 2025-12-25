<?php
/*--------------------------------------------------------------
   FetchCustomerDefaultAddress.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\App\Actions\Json;

use DateTimeInterface;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressReadService as CustomerDefaultAddressReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\CustomerAddressDoesNotExistException;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchCustomerDefaultAddress
 *
 * @package Gambio\Admin\Modules\CustomerAddress\App\Actions\Json
 */
class FetchCustomerDefaultAddress
{
    private CustomerDefaultAddressReadServiceInterface $service;
    private string                                     $datetimeFormat;
    
    
    /**
     * @param CustomerDefaultAddressReadServiceInterface $service
     */
    public function __construct(CustomerDefaultAddressReadServiceInterface $service)
    {
        $this->service        = $service;
        $this->datetimeFormat = DateTimeInterface::ATOM;
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $customerId = (int)$request->getAttribute('customerId');
            
            return $response->withJson([
                                           'shipping' => $this->service->getDefaultShippingAddress($customerId)
                                               ->toArray($this->datetimeFormat),
                                           'payment'  => $this->service->getDefaultPaymentAddress($customerId)
                                               ->toArray($this->datetimeFormat),
                                       ]);
        } catch (CustomerAddressDoesNotExistException $exception) {
            
            return $response->withStatus(404);
        }
    }
}