<?php
/* --------------------------------------------------------------
   DeleteHistoryReportItem.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DeleteHistoryReportItem
 */
class DeleteHistoryReportItem
{
    /**
     * @var string
     */
    protected $deletedId;
    
    /**
     * @var string
     */
    protected $scope;
    
    /**
     * @var \DateTime
     */
    protected $deletedAt;
    
    
    /**
     * DeleteHistoryReportItem constructor.
     *
     * @param \DeletedId          $deletedId Deleted id.
     * @param \DeleteHistoryScope $scope     Delete history scope.
     * @param \DateTime           $deletedAt Deletion date.
     */
    public function __construct(DeletedId $deletedId, DeleteHistoryScope $scope, \DateTime $deletedAt)
    {
        $this->deletedId = $deletedId->id();
        $this->scope     = $scope->scope();
        $this->deletedAt = $deletedAt;
    }
    
    
    /**
     * Named constructor of delete history report item.
     *
     * @param string|int          $deletedId Deleted id.
     * @param \DeleteHistoryScope $scope     Delete history scope.
     * @param \DateTime           $deletedAt Deletion date.
     *
     * @return \DeleteHistoryReportItem New instance.
     */
    public static function create($deletedId, DeleteHistoryScope $scope, \DateTime $deletedAt)
    {
        return MainFactory::create(static::class, DeletedId::create((string)$deletedId), $scope, $deletedAt);
    }
    
    
    /**
     * Returns the deleted id.
     *
     * @return string Deleted id.
     */
    public function deletedId()
    {
        return $this->deletedId;
    }
    
    
    /**
     * Returns the delete history scope.
     *
     * @return string Delete history scope.
     */
    public function scope()
    {
        return $this->scope;
    }
    
    
    /**
     * Returns the deletion date.
     *
     * @return \DateTime Deletion date.
     */
    public function deletedAt()
    {
        return $this->deletedAt;
    }
}
