<?php
/* --------------------------------------------------------------
   SpecialOfferDataAdapter.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SpecialOfferDataAdapter
 */
class SpecialOfferDataAdapter implements SpecialOfferDataAdapterInterface
{
    /**
     * @var SpecialOfferReaderInterface
     */
    protected $reader;
    
    /**
     * @var SpecialOfferWriterInterface
     */
    protected $writer;
    
    
    /**
     * SpecialOfferDataAdapter constructor.
     *
     * @param \SpecialOfferReaderInterface $reader
     * @param \SpecialOfferWriterInterface $writer
     */
    public function __construct(SpecialOfferReaderInterface $reader, SpecialOfferWriterInterface $writer)
    {
        $this->reader = $reader;
        $this->writer = $writer;
    }
    
    
    /**
     * Returns an instance that can fetch special offer data from an storage.
     *
     * @return \SpecialOfferReaderInterface
     */
    public function reader()
    {
        return $this->reader;
    }
    
    
    /**
     * Returns an instance that can write special offer data to an storage.
     *
     * @return \SpecialOfferWriterInterface
     */
    public function writer()
    {
        return $this->writer;
    }
}