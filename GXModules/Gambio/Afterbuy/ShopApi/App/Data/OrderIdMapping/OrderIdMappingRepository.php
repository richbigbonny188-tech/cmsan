<?php
/* --------------------------------------------------------------
   OrderIdMappingRepository.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\ShopApi\App\Data\OrderIdMapping;

use GXModules\Gambio\Afterbuy\ShopApi\Model\AfterbuyOrderIdMapping;
use GXModules\Gambio\Afterbuy\ShopApi\Service\Data\AfterbuyOrderIdMappingRepository;

/**
 * Class OrderIdMappingRepository
 *
 * @package GXModules\Gambio\Afterbuy\ShopApi\App\Data
 */
class OrderIdMappingRepository implements AfterbuyOrderIdMappingRepository
{
    /**
     * @var OrderIdMappingResponseParser
     */
    private OrderIdMappingResponseParser $responseParser;
    
    
    /**
     * @var OrderIdMappingWriter
     */
    private OrderIdMappingWriter $writer;
    
    
    /**
     * OrderIdMappingRepository constructor.
     *
     * @param OrderIdMappingResponseParser $responseParser
     * @param OrderIdMappingWriter         $writer
     */
    public function __construct(OrderIdMappingResponseParser $responseParser, OrderIdMappingWriter $writer)
    {
        $this->responseParser = $responseParser;
        $this->writer         = $writer;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAfterbuyOrderIdMapping(string $xmlResponse): AfterbuyOrderIdMapping
    {
        return $this->responseParser->parse($xmlResponse);
    }
    
    
    /**
     * @inheritDoc
     */
    public function saveAfterbuyOrderIdMapping(AfterbuyOrderIdMapping $orderIdMapping, string $xmlResponse): void
    {
        $this->writer->save($orderIdMapping, $xmlResponse);
    }
}