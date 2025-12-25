<?php
/* --------------------------------------------------------------
   DeleteHistoryReader.inc.php 2018-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DeleteHistoryReader
 */
class DeleteHistoryReader implements DeleteHistoryReaderInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * DeleteHistoryReader constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Fetches delete history data from the storage.
     *
     * @param \NonEmptyStringType $scope Delete history scope key.
     * @param \DateTime           $begin Minimum date of entry.
     * @param \DateTime           $end   Maximum date of entry.
     *
     * @return array Delete history report data.
     */
    public function fetch(NonEmptyStringType $scope, DateTime $begin, DateTime $end)
    {
        $data = $this->db->select()
            ->from('delete_history')
            ->where('scope', $scope->asString())
            ->where('created_at >=',
                    $begin->format('Y-m-d') . ' 00:00:00')
            ->where('created_at <=', $end->format('Y-m-d') . ' 23:59:59')
            ->get()
            ->result_array();
        
        return $data;
    }
}