<?php
/* --------------------------------------------------------------
   OrderDataSubject.inc.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderDataSubject
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class OrderDataSubject implements \SplSubject
{
	/**
	 * @var \DataChange
	 */
	protected $dataChange;
	
	/**
	 * @var \SplObjectStorage
	 */
	protected $observers;
	
	/**
	 * @var \DataObserverFactory
	 */
	protected $factory;
	
	/**
	 * @var string
	 */
	protected $table = 'orders';
	
	/**
	 * @var string
	 */
	protected $idColumn = 'orders_id';
	
	
	/**
	 * OrderDataSubject constructor.
	 *
	 * @param array $observers
	 */
	public function __construct(\DataObserverFactory $factory, array $observers = [])
	{
		$this->factory   = $factory;
		$this->observers = new \SplObjectStorage();
		
		foreach($observers as $observer)
		{
			$this->attach($observer);
		}
	}
	
	
	/**
	 * Attach an SplObserver.
	 *
	 * @link  https://php.net/manual/en/splsubject.attach.php
	 *
	 * @param SplObserver $observer The SplObserver to attach.
	 */
	public function attach(SplObserver $observer): void
	{
		$this->observers->attach($observer);
	}
	
	
	/**
	 * Detach an observer.
	 *
	 * @link  https://php.net/manual/en/splsubject.detach.php
	 *
	 * @param SplObserver $observer The SplObserver to detach.
	 */
	public function detach(SplObserver $observer): void
	{
		$this->observers->detach($observer);
	}
	
	
	/**
	 * Change the current value of the subject.
	 *
	 * Calling this method will also notify all attached observers.
	 *
	 * @param string $action Executed function, provide one of 'insert', 'update' or 'delete'.
	 * @param int    $rowId  Changed row ID.
	 */
	public function change($action, $rowId): void
	{
		$this->dataChange = $this->factory->createDataChange($action, $this->table, $this->idColumn, $rowId);
		$this->notify();
	}
	
	
	/**
	 * Notify an observer.
	 *
	 * @link  https://php.net/manual/en/splsubject.notify.php
	 */
	public function notify(): void
	{
		/** @var \SplObserver $observer */
		foreach($this->observers as $observer)
		{
			$observer->update($this);
		}
	}
	
	
	/**
	 * Get the subject's DataChange object.
	 *
	 * @return \DataChange
	 */
	public function getDataChange()
	{
		return $this->dataChange;
	}
}
