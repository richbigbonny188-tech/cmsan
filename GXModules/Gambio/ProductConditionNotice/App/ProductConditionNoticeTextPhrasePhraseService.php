<?php
/* --------------------------------------------------------------
   ProductConditionNoticeTextPhraseService.php 2021-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\ProductConditionNotice\App;

use GXModules\Gambio\ProductConditionNotice\Services\ProductConditionNoticeTextPhraseService as ProductConditionNoticeTextPhraseServiceInterface;
use LanguageTextManager;

/**
 * Class ProductConditionNoticeTextPhraseService
 *
 * @package GXModules\Gambio\ProductConditionNotice\App
 */
class ProductConditionNoticeTextPhrasePhraseService implements ProductConditionNoticeTextPhraseServiceInterface
{
    /**
     * @var LanguageTextManager
     */
    private $textManager;
    
    
    /**
     * @param LanguageTextManager $textManager
     */
    public function __construct(LanguageTextManager $textManager)
    {
        $this->textManager = $textManager;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getTextPhrase(string $phraseName): string
    {
        return $this->textManager->get_text($phraseName, 'product_condition_notice');
    }
}