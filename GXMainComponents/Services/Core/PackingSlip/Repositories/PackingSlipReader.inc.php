<?php
/* --------------------------------------------------------------
   PackingSlipReader.inc.php 2018-05-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PackingSlipReader
 */
class PackingSlipReader implements PackingSlipReaderInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var PackingSlipFactory
     */
    protected $factory;
    
    
    /**
     * PackingSlipReader constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     * @param \PackingSlipFactory  $factory
     */
    public function __construct(CI_DB_query_builder $queryBuilder, PackingSlipFactory $factory)
    {
        $this->queryBuilder = $queryBuilder;
        $this->factory      = $factory;
    }
    
    
    /**
     * Gets all packing slips for a given order ID.
     *
     * @param \IdType $orderId
     *
     * @return \PackingSlipCollection
     *
     * @throws \InvalidArgumentException
     */
    public function getPackingSlipsByOrderId(IdType $orderId)
    {
        $packingSlips = [];
        
        $result = $this->queryBuilder->select()
            ->from('packing_slips')
            ->where(['order_id' => $orderId->asInt()])
            ->get()
            ->result_array();
        foreach ($result as $row) {
            $packingSlips[] = $this->factory->createPackingSlip(new IdType($row['packing_slip_id']),
                                                                new StringType($row['number']),
                                                                new DateTime($row['date']),
                                                                new FilenameStringType($row['filename']),
                                                                new IdType($row['order_id']));
        }
        
        return MainFactory::create('PackingSlipCollection', $packingSlips);
    }
    
    
    /**
     * Gets all file names of packing slips for a given order ID.
     *
     * @param \IdType $orderId
     *
     * @return \StringCollection
     *
     * @throws \InvalidArgumentException
     */
    public function getPackingSlipFileNameCollectionByOrderId(IdType $orderId)
    {
        $fileNameArray = [];
        
        $result = $this->queryBuilder->select('filename')
            ->from('packing_slips')
            ->where(['order_id' => $orderId->asInt()])
            ->get()
            ->result_array();
        foreach ($result as $row) {
            $fileNameArray[] = new FilenameStringType($row['filename']);
        }
        
        return MainFactory::create('StringCollection', $fileNameArray);
    }
}