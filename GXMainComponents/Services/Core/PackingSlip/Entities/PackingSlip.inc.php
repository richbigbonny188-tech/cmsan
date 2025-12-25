<?php
/* --------------------------------------------------------------
   PackingSlip.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PackingSlip
 */
class PackingSlip implements PackingSlipInterface
{
    /**
     * @var IdType
     */
    protected $id;
    
    /**
     * @var StringType
     */
    protected $number;
    
    /**
     * @var DateTime
     */
    protected $date;
    
    /**
     * @var FilenameStringType
     */
    protected $filename;
    
    /**
     * @var IdType
     */
    protected $orderId;
    
    
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
    ) {
        $this->id       = $id;
        $this->number   = $number;
        $this->date     = $date;
        $this->filename = $filename;
        $this->orderId  = $orderId;
    }
    
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id->asInt();
    }
    
    
    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number->asString();
    }
    
    
    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
    
    
    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename->asString();
    }
    
    
    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId->asInt();
    }
}