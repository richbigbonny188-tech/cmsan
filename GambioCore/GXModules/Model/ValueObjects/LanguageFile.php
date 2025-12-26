<?php
/* --------------------------------------------------------------
   LanguageFile.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\Model\ValueObjects;

/**
 * Class LanguageFile
 *
 * @package Gambio\Core\GXModules\Model\ValueObjects
 */
class LanguageFile implements GXModuleComponent
{
    /**
     * @var string
     */
    private $filePath;
    
    /**
     * @var string
     */
    private $language;
    
    
    /**
     * LanguageFile constructor.
     *
     * @param string $filePath
     * @param string $language
     */
    private function __construct(string $filePath, string $language)
    {
        $this->filePath = $filePath;
        $this->language = $language;
    }
    
    
    /**
     * @param string $filePath
     * @param string $language
     *
     * @return LanguageFile
     */
    public static function create(string $filePath, string $language): LanguageFile
    {
        return new self($filePath, $language);
    }
    
    
    /**
     * @return string
     */
    public function filePath(): string
    {
        return $this->filePath;
    }
    
    
    /**
     * @return string
     */
    public function language(): string
    {
        return $this->language;
    }
    
    
    /**
     * @return string
     */
    public static function type(): string
    {
        return 'language_file';
    }
}