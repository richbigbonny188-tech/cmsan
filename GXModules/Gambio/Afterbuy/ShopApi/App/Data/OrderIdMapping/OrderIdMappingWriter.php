<?php
/* --------------------------------------------------------------
   OrderIdMappingWriter.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\ShopApi\App\Data\OrderIdMapping;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use GXModules\Gambio\Afterbuy\ShopApi\Exceptions\AfterbuyOrderIdAlreadyMappedException;
use GXModules\Gambio\Afterbuy\ShopApi\Exceptions\AfterbuyOrderIdMappingException;
use GXModules\Gambio\Afterbuy\ShopApi\Model\AfterbuyOrderIdMapping;
use Throwable;

/**
 * Class OrderIdMappingWriter
 *
 * @package GXModules\Gambio\Afterbuy\ShopApi\App\Data
 */
class OrderIdMappingWriter
{
    private const ERROR_INTEGRITY_VIOLATION = 'Integrity constraint violation';
    
    
    /**
     * @var Connection
     */
    private Connection $connection;
    
    
    /**
     * OrderIdMappingWriter constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Saves the afterbuy order id mapping in the 'afterbuy_orders' table.
     *
     * @param AfterbuyOrderIdMapping $orderIdMapping
     * @param string                 $xmlResponse
     *
     *
     * @throws AfterbuyOrderIdMappingException|AfterbuyOrderIdAlreadyMappedException
     */
    public function save(AfterbuyOrderIdMapping $orderIdMapping, string $xmlResponse): void
    {
        $state = $orderIdMapping->transmitted() === true ? 'transmitted' : 'unprocessed';
        $data  = [
            'order_id'          => $orderIdMapping->orderId(),
            'afterbuy_order_id' => $orderIdMapping->afterbuyOrderId(),
            'state'             => $state,
        ];
        try {
            $this->connection->insert('afterbuy_orders', $data);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            if ($this->isIntegrityConstraintViolation($errorMessage)) {
                $this->throwAlreadyMappedException($errorMessage, $xmlResponse, $orderIdMapping);
            }
            $message = "Failed to save the Afterbuy order id mapping due to an database error.\n";
            $message = $this->enhanceErrorMessage($message, $xmlResponse, $errorMessage, $orderIdMapping);
            
            throw new AfterbuyOrderIdMappingException($message,
                                                      $xmlResponse,
                                                      $orderIdMapping->orderId(),
                                                      $orderIdMapping->afterbuyOrderId());
        } catch (Throwable $t) {
            $errorMessage = $t->getMessage();
            if ($this->isIntegrityConstraintViolation($errorMessage)) {
                $this->throwAlreadyMappedException($errorMessage, $xmlResponse, $orderIdMapping);
            }
            $message = "Failed to save the Afterbuy order id mapping due to an database error.\n";
            $message = $this->enhanceErrorMessage($message, $xmlResponse, $errorMessage, $orderIdMapping);
            
            throw new AfterbuyOrderIdMappingException($message,
                                                      $xmlResponse,
                                                      $orderIdMapping->orderId(),
                                                      $orderIdMapping->afterbuyOrderId());
        }
    }
    
    
    /**
     * Throws an `AfterbuyOrderIdConstraintViolationException` containing useful information about what went wrong.
     *
     * @param string                 $errorMessage
     * @param string                 $xmlResponse
     * @param AfterbuyOrderIdMapping $orderIdMapping
     *
     * @throws AfterbuyOrderIdAlreadyMappedException
     */
    private function throwAlreadyMappedException(
        string                 $errorMessage,
        string                 $xmlResponse,
        AfterbuyOrderIdMapping $orderIdMapping
    ): void {
        $message = "Afterbuy- and Shop order id already mapped.\n";
        $message = $this->enhanceErrorMessage($message, $xmlResponse, $errorMessage, $orderIdMapping);
        
        throw new AfterbuyOrderIdAlreadyMappedException($message);
    }
    
    
    /**
     * Enhances error messages by appending useful information from the current context to the given message.
     * Returns the enhanced error message, containing the Afterbuy- and Shop order id and additionally the XML-Response
     * of the Afterbuy Shop-API call.
     *
     * @param string                 $message
     * @param string                 $xmlResponse
     * @param string                 $errorMessage
     * @param AfterbuyOrderIdMapping $orderIdMapping
     *
     * @return string
     */
    private function enhanceErrorMessage(
        string                 $message,
        string                 $xmlResponse,
        string                 $errorMessage,
        AfterbuyOrderIdMapping $orderIdMapping
    ): string {
        $message .= "Afterbuy order id: {$orderIdMapping->afterbuyOrderId()}\n";
        $message .= "Shop order id: {$orderIdMapping->orderId()}\n\n";
        $message .= "XML-Response:\n===\n$xmlResponse\n===\n\n";
        $message .= "Error message: $errorMessage";
        
        return $message;
    }
    
    
    /**
     * Checks if error message contains integrity constraint violation in the error message.
     *
     * @param string $errorMessage
     *
     * @return bool
     */
    private function isIntegrityConstraintViolation(string $errorMessage): bool
    {
        return stripos($errorMessage, self::ERROR_INTEGRITY_VIOLATION) !== false;
    }
}