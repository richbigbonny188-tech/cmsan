<?php
/* --------------------------------------------------------------
   PackingSlipInterface.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface PackingSlipInterface
 */
interface PackingSlipInterface
{
    /**
     * PackingSlip constructor.
     *
     * @param \IdType             $id
     * @param \StringType         $number
     * @param \DateTime           $date
     * @param \FilenameStringType $filename
     * @param \IdType             $orderId
     */
    public function __construct(
        IdType $id,
        StringType $number,
        DateTime $date,
        FilenameStringType $filename,
        IdType $orderId
    );
    
    
    /**
     * @return int
     */
    public function getId();
    
    
    /**
     * @return string
     */
    public function getNumber();
    
    
    /**
     * @return \DateTime
     */
    public function getDate();
    
    
    /**
     * @return string
     */
    public function getFilename();
    
    
    /**
     * @return int
     */
    public function getOrderId();
}