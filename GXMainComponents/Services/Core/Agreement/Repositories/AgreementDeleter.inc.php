<?php
/* --------------------------------------------------------------
   AgreementDeleter.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AgreementDeleter
 *
 * @category   System
 * @package    Agreement
 * @subpackage Repositories
 */
class AgreementDeleter implements AgreementDeleterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * AgreementDeleter constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Deletes an agreement entity.
     *
     * @param \AgreementInterface $agreement Agreement entity to delete.
     *
     * @return $this|\AgreementDeleterInterface Same instance for chained method calls.
     */
    public function delete(AgreementInterface $agreement)
    {
        $this->queryBuilder->delete('agreements', ['agreements_id' => $agreement->getId()->asInt()]);
        
        return $this;
    }
}