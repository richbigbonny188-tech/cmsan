<?php
/*--------------------------------------------------------------------------------------------------
    ContentIdResolverInterface.php 2019-12-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Interface ContentIdResolverInterface
 */
interface ContentIdResolverInterface
{
    
    /**
     * @param ContentIdentificationInterface $identification
     *
     * @return int
     */
    public function getGroupByIdentifier(ContentIdentificationInterface $identification): int;
    
    
    /**
     * @param ContentIdentificationInterface $identification
     *
     * @return string
     */
    public function getAliasByIdentifier(ContentIdentificationInterface $identification): string;

    
}