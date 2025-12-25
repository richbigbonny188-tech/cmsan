<?php

/*--------------------------------------------------------------------------------------------------
    CookieConfigurationInterface.php 2019-12-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Interface CookieConfigurationInterface
 */
interface CookieConfigurationInterface extends JsonSerializable
{
    /**
     * @param int $feature
     *
     * @return void
     */
    public function deactivateFeature(int $feature):void;
    
    
    /**
     * @param int $purposeId
     *
     * @return void
     */
    public function deactivatePurpose(int $purposeId): void;
    
    
    
    public function deactivate() : void;
    
    
    public function activate() : void;
    
    
    /**
     * @return bool
     */
    public function isActive() : bool ;
    
    
}