<?php
/*--------------------------------------------------------------------------------------------------
    CustomThemeCopyResponseInterface.php 2019-06-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Interface CustomThemeCopyResponseInterface
 */
interface CustomThemeCopyResponseInterface
{
    /**
     * @return ExistingFileCollection
     */
    public function getWhiteList();
    
    
    /**
     * @return ExistingFileCollection
     */
    public function getOverloaders();
    
    
    /**
     * @param CustomThemeCopyResponseInterface $response
     *
     * @return void
     */
    public function append(CustomThemeCopyResponseInterface $response = null);
    
    
}