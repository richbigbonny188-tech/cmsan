<?php
/* --------------------------------------------------------------
   CronjobConfigurationCollection.inc.php 2018-09-03
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2018 Gambio GmbH
   --------------------------------------------------------------
*/

/**
 * Class CronjobConfigurationCollection
 */
class CronjobConfigurationCollection implements \IteratorAggregate
{
    /**
     * @var \CronjobConfigurationInterface[]
     */
    protected $configurations;
    
    /**
     * @var \CronjobConfigurationInterface
     */
    protected $active;
    
    /**
     * @var \CronjobConfigurationInterface
     */
    protected $interval;
    
    ///**
    // * @var \CronjobConfigurationInterface
    // */
    //protected $sortOrder;
    
    /**
     * CronjobConfigurationCollection constructor.
     *
     * @param \CronjobConfigurationInterface ...$configurations
     */
    public function __construct(CronjobConfigurationInterface ...$configurations)
    {
        $this->configurations = $configurations;
    }
    
    
    /**
     * Named constructor of cronjob configuration collection.
     *
     * @param \CronjobConfigurationInterface ...$configurations
     *
     * @return array|bool|\CronjobConfigurationCollection
     */
    public static function collect(CronjobConfigurationInterface ...$configurations)
    {
        return MainFactory::create(static::class, ...$configurations);
    }
    
    
    /**
     * Returns an array iterator of the collected cronjob configuration, so instances are iterable.
     *
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->configurations);
    }
    
    
    public function getActive()
    {
        if (null === $this->active) {
            foreach ($this->configurations as $configuration) {
                if ($configuration->getName() === 'active') {
                    $this->active = $configuration;
                    
                    return $this->active;
                }
            }
        }
        
        return $this->active;
    }
    
    
    public function getInterval()
    {
        if (null === $this->interval) {
            foreach ($this->configurations as $configuration) {
                if ($configuration->getName() === 'interval') {
                    $this->interval = $configuration;
                    
                    return $this->interval;
                }
            }
        }
        
        return $this->interval;
    }
    
    
    public function toArray()
    {
        $data = [];
        
        foreach ($this->configurations as $configuration) {
            $data[] = $configuration->toArray();
        }
        
        return $data;
    }
    //public function getSortOrder()
    //{
    //	if(null === $this->sortOrder)
    //	{
    //		foreach($this->configurations as $configuration)
    //		{
    //			if($configuration->getName() === 'sortOrder')
    //			{
    //				$this->sortOrder = $configuration;
    //
    //				return $this->sortOrder;
    //			}
    //		}
    //	}
    //
    //	return $this->sortOrder;
    //}
}