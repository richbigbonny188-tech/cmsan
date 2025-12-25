<?php
/* --------------------------------------------------------------
   DataChange.inc.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DataChange
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class DataChange implements \JsonSerializable
{
	/**
	 * @var \CI_DB_query_builder
	 */
	protected $queryBuilder;
	
	/**
	 * @var string
	 */
	protected $action;
	
	/**
	 * @var string
	 */
	protected $table;
	
	/**
	 * @var string
	 */
	protected $idColumn;
	
	/**
	 * @var int
	 */
	protected $rowId;
	
	
	/**
	 * DataChange constructor.
	 *
	 * @param \CI_DB_query_builder $queryBuilder Performs database queries.
	 * @param string               $action       Action type ("update" or "delete").
	 * @param string               $table        Database table.
	 * @param string               $idColumn     Table ID column name.
	 * @param int                  $rowId        Row ID.
	 *
	 * @throws \Exception
	 */
	public function __construct(\CI_DB_query_builder $queryBuilder,
	                            $action,
	                            $table,
	                            $idColumn,
	                            $rowId)
	{
		$this->queryBuilder = $queryBuilder;
		$this->action       = $action;
		$this->table        = $table;
		$this->rowId        = $rowId;
		$this->idColumn     = $idColumn;
	}
	
	
	/**
	 * Returns the action type of the change ('update', 'delete').
	 *
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}
	
	
	/**
	 * Returns the database table of the change.
	 *
	 * @return string
	 */
	public function getTable()
	{
		return $this->table;
	}
	
	
	/**
	 * Returns the database table ID column.
	 *
	 * @return string
	 */
	public function getIdColumn()
	{
		return $this->idColumn;
	}
	
	
	/**
	 * Returns the database row ID of the change.
	 *
	 * @return string
	 */
	public function getRowId()
	{
		return $this->rowId;
	}
	
	
	/**
	 * Returns the payload of the change (lazy loading).
	 *
	 * @return array
	 */
	public function getPayload()
	{
		return $this->queryBuilder->get_where($this->table, [$this->idColumn => $this->rowId])->row_array();
	}
	
	
	/**
	 * Json encode the data change instance.
	 *
	 * @return mixed Data which can be serialized by json_encode, which is a value of any type other than a resource.
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		return [
			'action'   => $this->action,
			'table'    => $this->table,
			'idColumn' => $this->idColumn,
			'rowId'    => $this->rowId,
			'payload'  => $this->getPayload()
		];
	}
}
