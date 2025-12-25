<?php
/* --------------------------------------------------------------
   CronjobMeta.inc.php 2018-08-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CronjobMeta
 */
class CronjobMeta implements CronjobMetaInterface
{
    /**
     * @var \DateTime
     */
    protected $executedAt;
    
    
    /**
     * CronjobMeta constructor.
     *
     * @param \DateTime $executedAt Last execution of cronjobs.
     */
    public function __construct(\DateTime $executedAt)
    {
        $this->executedAt = $executedAt;
    }
    
    
    /**
     * Named constructor of cronjob meta.
     *
     * @param \DateTime $executedAt Last execution of cronjobs.
     *
     * @return CronjobMeta New instance.
     */
    public static function create(\DateTime $executedAt)
    {
        return MainFactory::create(static::class, $executedAt);
    }
    
    
    /**
     * Returns true if the last cronjobs execution is less then one minute.
     *
     * @return bool True if cronjob execution works properly.
     */
    public function isActive()
    {
        return $this->executedAt > new DateTime('-1 minutes');
    }
    
    
    /**
     * Returns the last execution date of the cronjobs.
     *
     * @return \DateTime Last execution date of cronjobs.
     */
    public function executedAt()
    {
        return $this->executedAt;
    }
}
