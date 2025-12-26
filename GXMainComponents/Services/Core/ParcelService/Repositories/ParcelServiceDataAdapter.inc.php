<?php
/* --------------------------------------------------------------
   ParcelServiceDataAdapter.inc.php 2018-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceDataAdapter
 */
class ParcelServiceDataAdapter implements ParcelServiceDataAdapterInterface
{
    /**
     * @var \ParcelServiceReaderInterface
     */
    protected $reader;
    
    /**
     * @var \ParcelServiceWriterInterface
     */
    protected $writer;
    
    
    public function __construct(\ParcelServiceReaderInterface $reader, \ParcelServiceWriterInterface $writer)
    {
        $this->reader = $reader;
        $this->writer = $writer;
    }
    
    
    /**
     * Returns the parcel service reader.
     *
     * @return \ParcelServiceReaderInterface
     */
    public function reader()
    {
        return $this->reader;
    }
    
    
    /**
     * Returns the parcel service writer.
     *
     * @return \ParcelServiceWriterInterface
     */
    public function writer()
    {
        return $this->writer;
    }
}