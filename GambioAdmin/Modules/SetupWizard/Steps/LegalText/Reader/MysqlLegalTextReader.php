<?php
/*--------------------------------------------------------------
   MysqlLegalTextReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Reader;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Collections\LegalTextDTOCollection;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\DTO\LegalTextDTO;

/**
 * Class MysqlLegalTextReader
 *
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Reader
 */
class MysqlLegalTextReader implements LegalTextReader
{
    /**
     * @var Connection
     */
    protected $connection;
    
    
    /**
     * MysqlLegalTextReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function legalTexts(): LegalTextDTOCollection
    {
        $results                = new LegalTextDTOCollection;
        $queryBuilder           = $this->connection->createQueryBuilder();
        $withdrawalTexts        = $queryBuilder->select('content_heading, content_text, content_type, languages_id')
            ->from('content_manager')
            ->where('content_group=3889896')
            ->executeQuery()
            ->fetchAllAssociative();
        $queryBuilder           = $this->connection->createQueryBuilder();
        $termsAndConditionTexts = $queryBuilder->select('content_heading, content_text, content_type, languages_id')
            ->from('content_manager')
            ->where('content_group=3')
            ->executeQuery()
            ->fetchAllAssociative();
        
        foreach ($withdrawalTexts as $withdrawalText) {
            foreach ($termsAndConditionTexts as $termsAndConditionText) {
                if ($withdrawalText['languages_id'] === $termsAndConditionText['languages_id']) {
                    $results[] = new LegalTextDTO(sha1($termsAndConditionText['content_heading']),
                                                  sha1($termsAndConditionText['content_text']),
                                                  $termsAndConditionText['content_type'],
                                                  sha1($withdrawalText['content_heading']),
                                                  sha1($withdrawalText['content_text']),
                                                  $withdrawalText['content_type'],
                                                  (int)$withdrawalText['languages_id']);
                }
            }
        }
        
        return $results;
    }
}