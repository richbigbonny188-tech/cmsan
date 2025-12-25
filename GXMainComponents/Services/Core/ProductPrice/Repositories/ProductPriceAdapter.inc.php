<?php
/* --------------------------------------------------------------
   ProductPriceAdapter.inc.php 2018-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductPriceAdapter
 */
class ProductPriceAdapter implements ProductPriceAdapterInterface
{
    /**
     * @var \ProductPriceReaderInterface
     */
    protected $reader;
    
    /**
     * @var \ProductPriceWriterInterface
     */
    protected $writer;
    
    
    /**
     * ProductPriceAdapter constructor.
     *
     * @param \ProductPriceReaderInterface $reader
     * @param \ProductPriceWriterInterface $writer
     */
    public function __construct(ProductPriceReaderInterface $reader, ProductPriceWriterInterface $writer)
    {
        $this->reader = $reader;
        $this->writer = $writer;
    }
    
    
    /**
     * Returns an instance that can fetch product price data from a storage.
     *
     * @return \ProductPriceReaderInterface
     */
    public function reader()
    {
        return $this->reader;
    }
    
    
    /**
     * Returns an instance that can write product price data to a storage.
     *
     * @return \ProductPriceWriterInterface
     */
    public function writer()
    {
        return $this->writer;
    }
}