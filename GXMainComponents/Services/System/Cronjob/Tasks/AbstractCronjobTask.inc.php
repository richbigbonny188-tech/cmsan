<?php
/* --------------------------------------------------------------
   AbstractCronjobTask.inc.php 2018-08-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractCronjobTask
 */
abstract class AbstractCronjobTask
{
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $schedule;
    
    /**
     * @var int
     */
    protected $sortOrder;
    
    /**
     * @var \AbstractCronjobLogger
     */
    protected $logger;
    
    /**
     * @var \AbstractCronjobDependencies
     */
    protected $dependencies;
    
    
    /**
     * AbstractCronjob constructor.
     *
     * @param \StringType                  $schedule     Cronjob schedule, can be a cron expression string.
     * @param \IntType                     $sortOrder    Sort order of cronjob execution.
     * @param \AbstractCronjobLogger       $logger       Logger instance for the cronjob callback.
     * @param \AbstractCronjobDependencies $dependencies Dependencies of cronjob's service.
     */
    public function __construct(
        StringType $schedule,
        IntType $sortOrder,
        AbstractCronjobLogger $logger,
        AbstractCronjobDependencies $dependencies
    ) {
        $this->schedule     = $schedule->asString();
        $this->sortOrder    = $sortOrder->asInt();
        $this->logger       = $logger;
        $this->dependencies = $dependencies;
    }
    
    
    /**
     * Named constructor of AbstractCronjob
     *
     * @param string $schedule  Cronjob schedule, can be a cron expression string.
     * @param int    $sortOrder Sort order of cronjob execution.
     *
     * @return \AbstractCronjobTask New instance.
     */
    public static function create($schedule, $sortOrder)
    {
        return MainFactory::create(static::class, new StringType($schedule), new IntType($sortOrder));
    }
    
    
    /**
     * Returns the cronjob name.
     *
     * @return string
     */
    public function getName()
    {
        return str_replace('CronjobTask', '', get_class($this));
    }
    
    
    /**
     * Returns the cronjob schedule.
     *
     * @return string
     */
    public function getSchedule()
    {
        return $this->schedule;
    }
    
    
    /**
     * Returns the cronjob sort order.
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
    
    
    /**
     * Runs the cronjob callback.
     *
     * @param $cronjobStartAsMicrotime
     *
     * @return mixed
     */
    public function run($cronjobStartAsMicrotime)
    {
        $closure = $this->getCallback($cronjobStartAsMicrotime);
        
        return $closure();
    }
    
    
    /**
     * Returns the cronjob callback.
     *
     * @param float $cronjobStartAsMicrotime
     *
     * @return \Closure
     */
    abstract public function getCallback($cronjobStartAsMicrotime);
}
