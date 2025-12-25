<?php
/* --------------------------------------------------------------
   CronjobCollection.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CronjobCollection
 */
class CronjobCollection implements \IteratorAggregate
{
    /**
     * @var \CronjobMetaInterface
     */
    protected $meta;
    
    /**
     * @var \CronjobInterface[]
     */
    protected $cronjobs;
    
    
    /**
     * CronjobCollection constructor.
     *
     * @param \CronjobMetaInterface $meta        Meta data for cronjobs.
     * @param \CronjobInterface     ...$cronjobs Cronjobs to be collected.
     */
    public function __construct(CronjobMetaInterface $meta, CronjobInterface ...$cronjobs)
    {
        $this->meta     = $meta;
        $this->cronjobs = $cronjobs;
    }
    
    
    /**
     * Named constructor of cronjob collection.
     *
     * @param \CronjobMetaInterface $meta        Meta data for cronjobs.
     * @param \CronjobInterface     ...$cronjobs Cronjobs to be collected.
     *
     * @return \CronjobCollection New instance.
     */
    public static function collect(CronjobMetaInterface $meta, CronjobInterface ...$cronjobs)
    {
        return MainFactory::create(static::class, $meta, ...$cronjobs);
    }
    
    
    /**
     * Returns meta data about cronjobs.
     *
     * @return \CronjobMetaInterface
     */
    public function getMeta()
    {
        return $this->meta;
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
    
    
    /**
     * Returns the array representation of collected cronjobs.
     *
     * @param \ExistingDirectory $cacheDir
     *
     * @return array
     */
    public function toArray(ExistingDirectory $cacheDir)
    {
        $cronjobs = [];
        
        foreach ($this->cronjobs as $cronjob) {
            $cronjobs[] = $cronjob->toArray($cacheDir);
        }
        
        return [
            'meta'     => [
                'active'     => $this->meta->isActive(),
                'executedAt' => $this->meta->executedAt()->format('d.m.Y - H:i')
            ],
            'cronjobs' => $cronjobs
        ];
    }
}
