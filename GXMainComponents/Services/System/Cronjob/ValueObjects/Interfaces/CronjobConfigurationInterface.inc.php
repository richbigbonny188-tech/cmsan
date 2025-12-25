<?php
/* --------------------------------------------------------------
   CronjobConfigurationInterface.inc.php 2018-09-03
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2018 Gambio GmbH
   --------------------------------------------------------------
*/

/**
 * Interface CronjobConfigurationInterface
 */
interface CronjobConfigurationInterface
{
    /**
     * Returns the cronjob configurations name.
     *
     * @return string
     */
    public function getName();
    
    
    /**
     * * Returns the cronjob configurations title.
     *
     * @return string
     */
    public function getTitle();
    
    
    /**
     * * Returns the cronjob configurations type.
     *
     * @return string
     */
    public function getType();
    
    
    /**
     * Returns the cronjob configuration field.
     *
     * @return mixed
     */
    public function getValue();
    
    
    /**
     * Returns the configuration values. Used by selects.
     *
     * @return array|null
     */
    public function getValues();
    
    
    /**
     * Returns the cronjob configuration data as array.
     *
     * @return array
     */
    public function toArray();
}