<?php
/* --------------------------------------------------------------
   CronjobTaskService.inc.php 2018-08-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CronjobTaskService
 */
class CronjobTaskService implements CronjobTaskServiceInterface
{
    /**
     * @var \CronjobTaskRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * CronjobService constructor.
     *
     * @param \CronjobTaskRepositoryInterface $repository Cronjob repository.
     */
    public function __construct(CronjobTaskRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Executes all valid cronjobs.
     *
     * @return void
     */
    public function run()
    {
        $microtime = microtime(true);
        $this->setLastRunFlag();
        
        /** @var \AbstractCronjobTask $job */
        foreach ($this->repository->getAll() as $job) {
            $cron = Cron\CronExpression::factory($job->getSchedule());
            
            if ($cron->isDue()) {
                $job->run($microtime);
            }
        }
        
        $this->setLastSuccessFlag();
    }
    
    
    /**
     * Returns the cronjob url.
     *
     * @return string
     */
    public static function getCronjobUrl()
    {
        return HTTP_SERVER . DIR_WS_CATALOG . 'shop.php?do=Cronjobs/Run&token=' . LogControl::get_secure_token();
    }
    
    
    /**
     * Sets a file flag for the last begin of cronjobs execution.
     */
    protected function setLastRunFlag()
    {
        $this->createCacheDirectory();
        $dateTime = new \DateTime();
        file_put_contents(DIR_FS_CATALOG . 'cache/cronjobs/last_run', $dateTime->format('Y-m-d H:i:s.u'));
    }
    
    
    /**
     * Sets a file flag for the last succceeded cronjobs execution.
     */
    protected function setLastSuccessFlag()
    {
        $this->createCacheDirectory();
        $dateTime = new \DateTime();
        file_put_contents(DIR_FS_CATALOG . 'cache/cronjobs/last_success', $dateTime->format('Y-m-d H:i:s.u'));
    }
    
    
    protected function createCacheDirectory()
    {
        if (!is_dir(DIR_FS_CATALOG . 'cache/cronjobs')) {
            mkdir(DIR_FS_CATALOG . 'cache/cronjobs');
        }
    }
}