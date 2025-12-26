<?php
/* --------------------------------------------------------------
   UserFriendlyErrorPageService.php 2021-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\UserFriendlyErrorPage\App;

use Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\ErrorPageReader;
use Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\ErrorPageWriter;
use Gambio\Admin\Modules\UserFriendlyErrorPage\Services\UserFriendlyErrorPageService as UserFriendlyErrorPageServiceInterface;
use Webmozart\Assert\Assert;

/**
 * Class UserFriendlyErrorPageService
 *
 * @package Gambio\Admin\Modules\UserFriendlyErrorPage\App
 */
class UserFriendlyErrorPageService implements UserFriendlyErrorPageServiceInterface
{
    /**
     * @var array<string,ErrorPageReader>
     */
    private $readers;
    
    
    /**
     * @var array<string,ErrorPageWriter>
     */
    private $writers;
    
    
    /**
     * UserFriendlyErrorPageService constructor.
     *
     * @param ErrorPageReader[] $readers
     * @param ErrorPageWriter[] $writers
     */
    public function __construct(array $readers, array $writers)
    {
        Assert::allIsInstanceOf($readers, ErrorPageReader::class);
        Assert::allIsInstanceOf($writers, ErrorPageWriter::class);
        
        $this->readers = [];
        foreach ($readers as $reader) {
            $this->readers[strtolower($reader->getType())] = $reader;
        }
        
        $this->writers = [];
        foreach ($writers as $writer) {
            $this->writers[strtolower($writer->getType())] = $writer;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function getUserFriendlyErrorPageActiveState(string $type): bool
    {
        Assert::oneOf(strtolower($type),
                      array_keys($this->readers),
                      'Given type is not allowed. Type must be one of: ' . implode(',', array_keys($this->writers))
                      . '. Got: ' . $type);
        
        return $this->readers[strtolower($type)]->getUserFriendlyErrorPageActiveState();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getUserFriendlyErrorPageFilePath(string $type, string $languageCode): string
    {
        Assert::oneOf(strtolower($type),
                      array_keys($this->readers),
                      'Given type is not allowed. Type must be one of: ' . implode(',', array_keys($this->writers))
                      . '. Got: ' . $type);
        
        return $this->readers[strtolower($type)]->getUserFriendlyErrorPageFilePath($languageCode);
    }
    
    
    /**
     * @inheritDoc
     */
    public function setUserFriendlyErrorPageActiveState(string $type, bool $state): void
    {
        Assert::oneOf(strtolower($type),
                      array_keys($this->writers),
                      'Given type is not allowed. Type must be one of: ' . implode(',', array_keys($this->writers))
                      . '. Got: ' . $type);
        
        $this->writers[strtolower($type)]->setUserFriendlyErrorPageActiveState($state);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeUserFriendlyErrorPage(string $type, string $languageCode, string $html): void
    {
        Assert::oneOf(strtolower($type),
                      array_keys($this->writers),
                      'Given type is not allowed. Type must be one of: ' . implode(',', array_keys($this->writers))
                      . '. Got: ' . $type);
        
        $this->writers[strtolower($type)]->storeUserFriendlyErrorPage($languageCode, $html);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteUserFriendlyErrorPages(string $type): void
    {
        Assert::oneOf(strtolower($type),
                      array_keys($this->writers),
                      'Given type is not allowed. Type must be one of: ' . implode(',', array_keys($this->writers))
                      . '. Got: ' . $type);
        
        $this->writers[strtolower($type)]->deleteUserFriendlyErrorPages();
    }
}