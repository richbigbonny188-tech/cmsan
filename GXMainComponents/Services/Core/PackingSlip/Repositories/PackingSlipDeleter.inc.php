<?php
/* --------------------------------------------------------------
   PackingSlipDeleter.inc.php 2018-05-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PackingSlipDeleter
 */
class PackingSlipDeleter implements PackingSlipDeleterInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * PackingSlipDeleter constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Deletes all packing slips by a given order ID.
     *
     * @param \IdType $orderId
     */
    public function deletePackingSlipsByOrderId(IdType $orderId)
    {
        $this->queryBuilder->delete('packing_slips', ['order_id' => $orderId->asInt()]);
    }
}