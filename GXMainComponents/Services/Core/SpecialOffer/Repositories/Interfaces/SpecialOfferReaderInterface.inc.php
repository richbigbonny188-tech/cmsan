<?php
/* --------------------------------------------------------------
   SpecialOfferReaderInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SpecialOfferReaderInterface
 */
interface SpecialOfferReaderInterface
{
    /**
     * Fetches special offer data by the given id.
     *
     * @param \IdType $specialOfferId Id of expected special offer data.
     *
     * @return array|null Raw data of a special offer.
     */
    public function fetchById(IdType $specialOfferId);
    
    
    /**
     * Fetches special offer data by the given condition.
     *
     * @param \StringType $searchCondition Search condition for special offer data.
     * @param \Pager|null $pager           (Optional) Pager object with pagination information
     * @param array       $sorters         (Optional) array of Sorter objects with data sorting information
     *
     * @return array|null Raw data of special offers.
     */
    public function fetchBy(StringType $searchCondition, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns all special offers.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return array|null Raw data of special offers.
     */
    public function fetchAll(\Pager $pager = null, array $sorters = []);
}