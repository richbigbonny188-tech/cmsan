<?php
/* --------------------------------------------------------------
   DataChangeCollection.inc.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DataChangeCollection
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class DataChangeCollection implements IteratorAggregate, Countable, JsonSerializable
{
	/**
	 * @var \DataChange[]
	 */
	protected $dataChanges;
	
	
	/**
	 * DataChangeCollection constructor.
	 *
	 * @param \DataChange[] $dataChanges
	 */
	public function __construct(array $dataChanges)
	{
		$this->dataChanges = $dataChanges;
	}
	
	
	/**
	 * Returns iterator object.
	 *
	 * @return \Traversable An instance of an object implementing Iterator or Traversable
	 */
	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->dataChanges);
	}
	
	
	/**
	 * Count elements of an object.
	 *
	 * @return int The custom count as an integer.
	 */
	public function count(): int
	{
		return count($this->dataChanges);
	}
	
	
	/**
	 * Specify data which should be serialized to JSON
	 *
	 * @return mixed Data which can be serialized by json_encode, which is a value of any type other than a resource.
	 */
    #[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		return $this->dataChanges;
	}
}