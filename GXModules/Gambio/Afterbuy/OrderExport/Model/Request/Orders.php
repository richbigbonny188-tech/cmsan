<?php
/* --------------------------------------------------------------
   Orders.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\Model\Request;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\XmlSerializable;

/**
 * Class Orders
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\Model
 */
class Orders implements XmlSerializable
{
    /**
     * @var Order[]
     */
    private array $orders;
    
    
    /**
     * @param Order ...$orders
     */
    public function __construct(Order ...$orders)
    {
        $this->orders = $orders;
    }
    
    
    /**
     * @inheritDoc
     */
    public function toXmlString(): string
    {
        $orders = implode("\n", array_map(fn(Order $order): string => $order->toXmlString(), $this->orders));
        $indent = $this->indent();
        
        return <<<XML
$indent<Orders>
$orders
$indent</Orders>
XML;
    }
    
    
    /**
     * @inheritDoc
     */
    public function indent(): string
    {
        return str_repeat(' ', 4);
    }
}