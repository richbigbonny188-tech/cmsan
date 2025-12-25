<?php
/* --------------------------------------------------------------
   CountriesFacade.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);


namespace Gambio\Admin\Modules\DHLReturns\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use \Exception;

class CountriesFacade
{
    /**
     * @var Connection
     */
    private $dbConnection;
    
    
    public function __construct(Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }
    
    
    /**
     * @return array
     */
    public function getAllCountries(): array
    {
        $countries = [];
        try {
            $stmt = $this->dbConnection->executeQuery('SELECT * FROM `countries`');
            while ($countryRow = $stmt->fetchAssociative()) {
                $countries[] = [
                    'id' => $countryRow['countries_id'],
                    'iso2' => $countryRow['countries_iso_code_2'],
                    'iso3' => $countryRow['countries_iso_code_3'],
                    'status' => $countryRow['status'],
                ];
            }
        } catch (DBALException|Exception $e) {
        }
        
        return $countries;
    }
}