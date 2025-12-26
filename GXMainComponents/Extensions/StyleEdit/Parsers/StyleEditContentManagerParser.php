<?php
/*--------------------------------------------------------------------------------------------------
    StyleEditContentManagerParser.php 2021-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);


/**
 * Class StyleEditContentManagerParser
 */
class StyleEditContentManagerParser
{
    /**
     * Parses the content manager data from the StyleEdit 4
     *
     * @param array $requestData
     *
     * @return array
     */
    public function parseRequestData(array $requestData): array
    {
        $parsedRequestData  = [];
        
        if ($requestData) {
            $currentLanguage = $_SESSION['language_code'];
            $languageContent = $requestData['content'][$currentLanguage] ?? [];
            
            $parsedRequestData = [
                'content_id'      => $requestData['id'],
                'content_type'    => $requestData['pageType'],
                'content_title'   => $languageContent['contentTitle'] ?? '',
                'content_name'    => $languageContent['contentName'] ?? '',
                'content_heading' => $languageContent['contentHeading'] ?? '',
                'content_text'    => $languageContent['contentText'] ?? '',
                'content_status'  => $languageContent['contentStatus'] ?? '',
                'content_file'    => $languageContent['contentFile'] ?? '',
            ];
        }
        
        return $parsedRequestData;
    }
}
