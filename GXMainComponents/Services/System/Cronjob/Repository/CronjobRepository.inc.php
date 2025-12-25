<?php
/* --------------------------------------------------------------
   CronjobRepository.inc.php 2018-10-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CronjobRepository
 */
class CronjobRepository implements CronjobRepositoryInterface
{
    /**
     * @var \CronjobSettings
     */
    protected $settings;
    
    /**
     * @var \CronjobReaderInterface
     */
    protected $reader;
    
    /**
     * @var \CronjobWriterInterface
     */
    protected $writer;
    
    
    /**
     * CronjobRepository constructor.
     *
     * @param \CronjobReaderInterface $reader
     * @param \CronjobSettings        $settings
     * @param \CronjobWriterInterface $writer
     */
    public function __construct(
        CronjobReaderInterface $reader,
        CronjobSettings $settings,
        CronjobWriterInterface $writer
    ) {
        $this->reader   = $reader;
        $this->settings = $settings;
        $this->writer   = $writer;
    }
    
    
    /**
     * Saves cronjob configuration into the storage.
     *
     * @param \CronjobInterface|\StringType $cronjob
     * @param \KeyValueCollection           $data
     *
     * @return $this|\CronjobRepositoryInterface Same instance for chained method calls.
     */
    public function save(StringType $cronjob, KeyValueCollection $data)
    {
        $this->writer->save($cronjob, $data);
        
        return $this;
    }
    
    
    /**
     * Returns all cronjobs.
     *
     * @return \CronjobCollection Collected cronjobs with meta data about execution.
     */
    public function getAll()
    {
        $rawData = $this->reader->getAll();
        
        $lastRun     = $rawData['lastRun'];
        $rawCronjobs = $rawData['cronjobs'];
        
        $cronjobs = [];
        
        foreach ($rawCronjobs as $rawCronjob) {
            $rawConfigurations = $rawCronjob['configuration'];
            
            $configurations = [];
            
            foreach ($rawConfigurations as $rawConfiguration) {
                if (array_key_exists('values', $rawConfiguration)) {
                    $configurations[] = CronjobConfiguration::withValues($rawConfiguration['name'],
                                                                         $rawConfiguration['title'],
                                                                         $rawConfiguration['type'],
                                                                         $rawConfiguration['value'],
                                                                         $rawConfiguration['values']);
                    
                    continue;
                }
                
                $configurations[] = CronjobConfiguration::create($rawConfiguration['name'],
                                                                 $rawConfiguration['title'],
                                                                 $rawConfiguration['type'],
                                                                 $rawConfiguration['value']);
            }
            
            $cronjobs[] = Cronjob::create($rawCronjob['name'],
                                          $rawCronjob['title'],
                                          CronjobConfigurationCollection::collect(...$configurations));
        }
        
        return CronjobCollection::collect(CronjobMeta::create($lastRun), ...$cronjobs);
    }
    
    
    /**
     * Returns a cronjob by the given identifier.
     *
     * @param \StringType $name Cronjob identifier.
     *
     * @return \Cronjob Cronjob of given identifier.
     */
    public function getByName(StringType $name)
    {
        $cronjob = $this->reader->getByName($name);
        
        $configurations = [];
        foreach ($cronjob['configuration'] as $configuration) {
            $configurations[] = CronjobConfiguration::create($configuration['name'],
                                                             $configuration['title'],
                                                             $configuration['type'],
                                                             $configuration['value']);
        }
        
        $cronjob = Cronjob::create($cronjob['name'],
                                   $cronjob['title'],
                                   CronjobConfigurationCollection::collect(...$configurations));
        
        return $cronjob;
    }
}