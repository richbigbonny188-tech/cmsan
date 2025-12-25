<?php
/* --------------------------------------------------------------
   DeleteHistoryDataAdapter.inc.php 2018-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DeleteHistoryDataAdapter
 */
class DeleteHistoryDataAdapter implements DeleteHistoryDataAdapterInterface
{
    /**
     * @var \DeleteHistoryReaderInterface
     */
    protected $reader;
    
    /**
     * @var \DeleteHistoryWriterInterface
     */
    protected $writer;
    
    
    /**
     * DeleteHistoryDataAdapter constructor.
     *
     * @param \DeleteHistoryReaderInterface $reader Reader instance.
     * @param \DeleteHistoryWriterInterface $writer Writer instance.
     */
    public function __construct(\DeleteHistoryReaderInterface $reader, \DeleteHistoryWriterInterface $writer)
    {
        $this->reader = $reader;
        $this->writer = $writer;
    }
    
    
    /**
     * Returns a reader that can fetch data from the delete history storage.
     *
     * @return \DeleteHistoryReaderInterface
     */
    public function reader()
    {
        return $this->reader;
    }
    
    
    /**
     * Returns a writer that can add new data to the delete history storage.
     *
     * @return \DeleteHistoryWriterInterface
     */
    public function writer()
    {
        return $this->writer;
    }
}