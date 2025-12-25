<?php
/* --------------------------------------------------------------
   KlarnaOrderObserver.inc.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderDataObserver
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class OrderDataObserver implements \SplObserver
{
	/**
	 * @var \DataChange[]
	 */
	private $dataChanges = [];
	
	
	/**
	 * Receive update from subject.
	 *
	 * @link  https://php.net/manual/en/splobserver.update.php
	 *
	 * @param \SplSubject $subject The SplSubject notifying the observer of an update.
	 */
	public function update(SplSubject $subject): void
	{
		$this->dataChanges[] = $subject->getDataChange();
	}
	
	
	/**
	 * Get the recorded DataChange instances.
	 *
	 * @return \DataChange[]
	 */
	public function getDataChanges()
	{
		return $this->dataChanges;
	}
}
