<?php
/* --------------------------------------------------------------
   TextPhraseReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\TextManager\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/**
 * Class TextPhraseReader
 *
 * @package Gambio\Core\TextManager\App\Data
 */
class TextPhraseReader
{
    /**
     * @var Connection
     */
    private $db;
    
    
    /**
     * TextPhraseReader constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @param string $section
     * @param int    $languageId
     *
     * @return string[]
     * @throws Exception
     */
    public function getSectionPhrases(string $section, int $languageId): array
    {
        $result = $this->db->createQueryBuilder()
            ->select('phrase_name, phrase_text')
            ->from('language_phrases_cache')
            ->where('section_name = :section')
            ->andWhere('language_id = :languageId')
            ->setParameter('section', $section)
            ->setParameter('languageId', $languageId)
            ->executeQuery()
            ->fetchAllAssociative();
        
        $phrases = [];
        foreach ($result as $row) {
            $phrases[$row['phrase_name']] = $row['phrase_text'];
        }
        
        return $phrases;
    }
}