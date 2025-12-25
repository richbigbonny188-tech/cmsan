<?php
/*--------------------------------------------------------------------------------------------------
    ContentIdentificationFactoryInterface.php 2019-12-04
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Interface ContentIdentificationFactoryInterface
 */
interface ContentIdentificationFactoryInterface
{
    /**
     * @param $contentGroup
     *
     * @return ContentIdentificationInterface
     */
    public function forContentGroup(int $contentGroup): ContentIdentificationInterface;
    
    
    /**
     * @param string $contentAlias
     *
     * @return ContentIdentificationInterface
     */
    public function forContentAlias(string $contentAlias): ContentIdentificationInterface;
    
    
    /**
     * @param $identification
     *
     * @return mixed
     */
    public function createFromJson($identification);
    
    
    /**
     * @param string $contentAlias
     * @param int    $contentGroup
     *
     * @return mixed
     */
    public function forPreference(string $contentAlias = null, int $contentGroup = null);
    
}