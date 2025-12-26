<?php
/* --------------------------------------------------------------
   CronjobTaskCollection.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CronjobTaskCollection
 */
class CronjobTaskCollection implements \IteratorAggregate
{
    /**
     * @var \AbstractCronjobTask[]
     */
    protected $cronjobs;
    
    
    /**
     * CronjobCollection constructor.
     *
     * @param \AbstractCronjobTask[] $cronjobs
     */
    public function __construct(AbstractCronjobTask ...$cronjobs)
    {
        $this->cronjobs = $cronjobs;
        
        $sortByOrder = function (AbstractCronjobTask $jobA, AbstractCronjobTask $jobB) {
            if ($jobA->getSortOrder() === $jobB->getSortOrder()) {
                return 0;
            }
            
            return $jobA->getSortOrder() < $jobB->getSortOrder() ? -1 : 1;
        };
        
        usort($this->cronjobs, $sortByOrder);
    }
    
    
    /**
     * Named constructor of cronjob factory.
     *
     * @param AbstractCronjobTask ...$cronjobs Cronjobs.
     *
     * @return \CronjobTaskCollection
     */
    public static function collect(AbstractCronjobTask ...$cronjobs)
    {
        return new static(...$cronjobs);
    }
    
    
    /**
     * Returns an array iterator of the collected cronjobs, so instances are iterable.
     *
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->cronjobs);
    }
}

