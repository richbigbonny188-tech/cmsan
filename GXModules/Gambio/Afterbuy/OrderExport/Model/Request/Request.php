<?php
/* --------------------------------------------------------------
   Request.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\Model\Request;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\AfterbuyGlobal;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\AfterbuyXmlRequest;

/**
 * Class Request
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\Model
 */
class Request implements AfterbuyXmlRequest
{
    /**
     * @var AfterbuyGlobal
     */
    private AfterbuyGlobal $afterbuyGlobal;
    
    
    /**
     * @var Orders
     */
    private Orders $orders;
    
    
    /**
     * Request constructor.
     *
     * @param AfterbuyGlobal $afterbuyGlobal
     * @param Orders         $orders
     */
    public function __construct(AfterbuyGlobal $afterbuyGlobal, Orders $orders)
    {
        $this->afterbuyGlobal = $afterbuyGlobal;
        $this->orders         = $orders;
    }
    
    
    /**
     * @inheritDoc
     */
    public function toXmlString(): string
    {
        $afterbuyGlobal = $this->afterbuyGlobal->toXmlString();
        $orders         = $this->orders->toXmlString();
        
        return <<<XML
<?xml version="1.0" encoding="utf-8"?>
<Request>
$afterbuyGlobal
$orders
</Request>
XML;
    }
}