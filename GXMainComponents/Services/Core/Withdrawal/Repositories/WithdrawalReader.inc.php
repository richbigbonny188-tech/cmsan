<?php
/* --------------------------------------------------------------
   WithdrawalReader.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WithdrawalReader
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Repositories
 */
class WithdrawalReader implements WithdrawalReaderInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * WithdrawalReader constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Returns all withdrawal entities as array
     *
     * @return array
     */
    public function getAll()
    {
        $result = [];
        foreach ($this->queryBuilder->get('withdrawals')->result_array() as $withdrawalData) {
            $withdrawal = [
                'id'             => (int)$withdrawalData['withdrawal_id'],
                'date'           => $withdrawalData['withdrawal_date'],
                'content'        => $withdrawalData['withdrawal_content'],
                'dateCreated'    => $withdrawalData['date_created'],
                'createdByAdmin' => $withdrawalData['created_by_admin'],
                'order'          => $this->_getOrderData($withdrawalData)
            ];
            
            $result[] = $withdrawal;
        }
        
        return $result;
    }
    
    
    /**
     * Returns withdrawal entity data by the given id.
     *
     * @param \IdType $withdrawalId
     *
     * @return array
     * @throws \EntityNotFoundException
     *
     */
    public function getById(IdType $withdrawalId)
    {
        $rawData = $this->queryBuilder->select()
            ->from('withdrawals')
            ->where('withdrawal_id', $withdrawalId->asInt())
            ->get()
            ->result_array() ? : [];
        
        if (count($rawData) === 0) {
            throw new EntityNotFoundException('Withdrawal entity was not found with provided id "'
                                              . $withdrawalId->asInt() . '"');
        }
        
        $result = ['id' => $withdrawalId->asInt()];
        foreach ($rawData as $data) {
            $result['date']           = $data['withdrawal_date'];
            $result['content']        = $data['withdrawal_content'];
            $result['dateCreated']    = $data['date_created'];
            $result['createdByAdmin'] = $data['created_by_admin'];
            $result['order']          = $this->_getOrderData($data);
        }
        
        return $result;
    }
    
    
    /**
     * Returns WithdrawalOrder.
     *
     * @param array $OrderData
     *
     * @return array
     */
    protected function _getOrderData(array $OrderData)
    {
        return [
            'orderId'      => $OrderData['order_id'],
            'customerId'   => $OrderData['customer_id'],
            'gender'       => $OrderData['customer_gender'],
            'firstName'    => $OrderData['customer_firstname'],
            'lastName'     => $OrderData['customer_lastname'],
            'address'      => $OrderData['customer_street_address'],
            'postCode'     => $OrderData['customer_postcode'],
            'city'         => $OrderData['customer_city'],
            'country'      => $OrderData['customer_country'],
            'email'        => $OrderData['customer_email'],
            'orderDate'    => $OrderData['order_date'],
            'deliveryDate' => $OrderData['delivery_date']
        
        ];
    }
}
