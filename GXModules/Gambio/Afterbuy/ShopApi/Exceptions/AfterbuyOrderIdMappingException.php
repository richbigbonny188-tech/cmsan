<?php
/* --------------------------------------------------------------
   AfterbuyOrderIdMappingException.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\ShopApi\Exceptions;

use Exception;

/**
 * Class AfterbuyOrderIdMappingException
 *
 * @package GXModules\Gambio\Afterbuy\ShopApi\Exceptions
 */
class AfterbuyOrderIdMappingException extends Exception
{
    /**
     * @var string
     */
    private string $xmlResponse;
    
    
    /**
     * @var int|null
     */
    
    private ?int $orderId;
    /**
     * @var int|null
     */
    private ?int $afterbuyOrderId;
    
    
    /**
     * AfterbuyOrderIdMappingException constructor.
     *
     * @param string   $message
     * @param string   $xmlResponse
     * @param int|null $orderId
     * @param int|null $afterbuyOrderId
     */
    public function __construct(string $message, string $xmlResponse, int $orderId = null, int $afterbuyOrderId = null)
    {
        parent::__construct($message);
        $this->xmlResponse     = $xmlResponse;
        $this->orderId         = $orderId;
        $this->afterbuyOrderId = $afterbuyOrderId;
    }
    
    
    /**
     * @return string
     */
    public function xmlResponse(): string
    {
        return $this->xmlResponse;
    }
    
    
    /**
     * @return int|null
     */
    public function orderId(): ?int
    {
        return $this->orderId;
    }
    
    
    /**
     * @return int|null
     */
    public function afterbuyOrderId(): ?int
    {
        return $this->afterbuyOrderId;
    }
}