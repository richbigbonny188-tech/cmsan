<?php
/*--------------------------------------------------------------
   LanguageReader.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class LanguageReader
 */
class LanguageReader
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * LanguageReader constructor.
     *
     * @param CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * @return string[]
     */
    public function getLanguageData(): array
    {
        $columns = [
            'l.`languages_id`',
            'l.`name`',
            'l.`code`',
            'l.`image`',
            'l.`directory`',
            'l.`sort_order`',
            'l.`status`',
            'l.`status_admin`',
            'l.`language_currency`',
            'c.`currencies_id`',
        ];
        
        return $this->queryBuilder->reset_query()
            ->select(implode(', ', $columns))
            ->from('languages as l')
            ->join('currencies as c', 'UPPER(l.language_currency) = UPPER(c.code)', 'INNER')
            ->order_by('l.languages_id')
            ->get()
            ->result_array();
    }
}