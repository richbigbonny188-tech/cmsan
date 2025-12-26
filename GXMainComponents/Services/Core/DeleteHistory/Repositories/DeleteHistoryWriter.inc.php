<?php
/* --------------------------------------------------------------
   DeleteHistoryWriter.inc.php 2018-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DeleteHistoryWriter
 */
class DeleteHistoryWriter implements DeleteHistoryWriterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * DeleteHistoryWriter constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Adds a new delete history record in the storage.
     *
     * @param \NonEmptyStringType $scope     Delete history scope key.
     * @param \NonEmptyStringType $deletedId Deleted id.
     *
     * @return void
     */
    public function insert(NonEmptyStringType $scope, NonEmptyStringType $deletedId)
    {
        $this->db->insert('delete_history',
                          [
                              'scope'      => $scope->asString(),
                              'deleted_id' => $deletedId->asString()
                          ]);
    }
}