<?php
/* --------------------------------------------------------------
   LegalTextVersionHelper.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class LegalTextVersionHelper
 *
 * This class provides methods in order to get the legal text version of a legal text like eg. the privacy text.
 *
 * @category   System
 * @package    Agreement
 */
class LegalTextVersionHelper
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * LegalTextVersionHelper constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Returns the privacy text version by the provided language ID.
     *
     * @param \IdType $languageId Language ID.
     *
     * @return string Privacy text version
     */
    public function getPrivacyTextVersionByLanguageId(IdType $languageId)
    {
        return $this->queryBuilder->select('content_version')
                   ->from('content_manager')
                   ->where('content_group', 2)
                   ->where('languages_id', $languageId)
                   ->get()
                   ->result()[0]->content_version;
    }
    
    
    /**
     * Returns the AGB text version by the provided language ID.
     *
     * @param \IdType $languageId Language ID.
     *
     * @return string AGB text version
     */
    public function getAGBTextVersionByLanguageId(IdType $languageId)
    {
        return $this->queryBuilder->select('content_version')
                   ->from('content_manager')
                   ->where('content_group', 3)
                   ->where('languages_id', $languageId)
                   ->get()
                   ->result()[0]->content_version;
    }
    
    
    /**
     * Returns the withdrawal text version by the provided language ID.
     *
     * @param \IdType $languageId Language ID.
     *
     * @return string Withdrawal text version
     */
    public function getWithdrawalTextVersionByLanguageId(IdType $languageId)
    {
        return $this->queryBuilder->select('content_version')
                   ->from('content_manager')
                   ->where('content_group', 3889895)
                   ->where('languages_id', $languageId)
                   ->get()
                   ->result()[0]->content_version;
    }
    
    
    /**
     * Returns the transport text version by the provided language ID.
     *
     * @param \IdType $languageId Language ID.
     *
     * @return string Transport text version
     */
    public function getTransportTextVersionByLanguageId(IdType $languageId)
    {
        return $this->queryBuilder->select('content_version')
                   ->from('content_manager')
                   ->where('content_group', 3210123)
                   ->where('languages_id', $languageId)
                   ->get()
                   ->result()[0]->content_version;
    }
}