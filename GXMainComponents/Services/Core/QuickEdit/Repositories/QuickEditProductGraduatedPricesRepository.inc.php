<?php

/* --------------------------------------------------------------
   QuickEditProductGraduatedPricesRepository.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductGraduatedPricesRepository
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Repositories
 */
class QuickEditProductGraduatedPricesRepository implements QuickEditProductGraduatedPricesRepositoryInterface
{
    /**
     * @var QuickEditProductGraduatedPricesReaderInterface
     */
    protected $graduatedPricesReader;
    
    
    /**
     * QuickEditProductGraduatedPricesRepository constructor.
     *
     * @param QuickEditProductGraduatedPricesReaderInterface $graduatedPricesReader QuickEdit product graduated prices
     *                                                                              reader.
     */
    public function __construct(QuickEditProductGraduatedPricesReaderInterface $graduatedPricesReader)
    {
        $this->graduatedPricesReader = $graduatedPricesReader;
    }
    
    
    /**
     * Returns the graduated prices of a product or an empty array nothing was found.
     *
     * @param array|null $productIds Array containing the selected product IDs to be processed.
     *
     * @return array Returns array that contains the graduated prices information.
     */
    public function getGraduatedPrices(array $productIds = null)
    {
        return $this->graduatedPricesReader->getGraduatedPrices($productIds);
    }
}