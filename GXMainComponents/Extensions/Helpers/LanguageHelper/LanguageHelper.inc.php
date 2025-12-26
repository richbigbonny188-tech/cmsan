<?php
/* --------------------------------------------------------------
   LanguageHelper.inc.php 2016-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('LanguageHelperInterface');

/**
 * Class LanguageHelper
 *
 * @category   System
 * @package    Extensions
 * @subpackage Helpers
 */
class LanguageHelper implements LanguageHelperInterface, CrossCuttingObjectInterface
{
    /**
     * Database connector.
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * LanguageHelper constructor.
     *
     * @param CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @override
     *
     * @param IdType $id
     *
     * @return LanguageCode
     */
    public function getLanguageCodeById(IdType $id)
    {
        $result = $this->db->select('code')->where('languages_id', (string)$id->asInt())->get('languages');
        
        if ($result->num_rows() === 0) {
            throw new InvalidArgumentException('Unknown language ID: ' . (int)$id);
        }
        
        $languageCodeArray = $result->row_array();
        $languageCode      = new LanguageCode(new NonEmptyStringType($languageCodeArray['code']));
        
        return $languageCode;
    }
    
    
    /**
     * @param LanguageCode $code
     *
     * @return IdType
     */
    public function getLanguageIdByCode(LanguageCode $code)
    {
        $result = $this->db->select('languages_id')->where('code', $code->asString())->get('languages');
        
        if ($result->num_rows() === 0) {
            throw new InvalidArgumentException('Unknown language code: ' . $code->asString());
        }
        
        $languageCodeArray = $result->row_array();
        $languageId        = new IdType($languageCodeArray['languages_id']);
        
        return $languageId;
    }
    
    
    /**
     * @override
     *
     * @param BoolType|null $onlyActiveLanguages
     *
     * @return KeyValueCollection
     */
    public function getLanguageCodes(BoolType $onlyActiveLanguages = null)
    {
        if ($onlyActiveLanguages === null) {
            $onlyActiveLanguages = new BoolType(false);
        }
        $languageArray = [];
        
        $result = $this->db->select(['languages_id', 'code']);
        
        if ($onlyActiveLanguages->asBool()) {
            $result->where('status', '1');
        }
        $result = $result->get('languages');
        
        foreach ($result->result_array() as $row) {
            $languageArray[$row['languages_id']] = new LanguageCode(new NonEmptyStringType($row['code']));
        }
        
        $languageCollection = new KeyValueCollection($languageArray);
        
        return $languageCollection;
    }
    
    
    /**
     * Gets the language codes of all active languages in a KeyValueCollection
     *
     * @return KeyValueCollection
     */
    public function getActiveLanguageCodes()
    {
        return $this->getLanguageCodes(new BoolType(true));
    }
}