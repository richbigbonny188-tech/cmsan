<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerSaveCommand.php 2021-05-18
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\ContentManager\Command;

use ContentReadServiceInterface;
use ContentValueObjectFactory;
use ContentWriteServiceInterface;
use Gambio\StyleEdit\Core\Components\ContentManager\Entities\AbstractContentManagerOption;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\CurrentThemeInterface;
use Gambio\StyleEdit\Core\Language\Services\LanguageService;
use Gambio\StyleEdit\Core\Options\Commands\AbstractSaveCommand;

/**
 * Class WysiwygSaveCommand
 */
abstract class ContentManagerSaveCommand extends AbstractSaveCommand
{
    /**
     * @var ContentReadServiceInterface
     */
    protected $contentReadService;
    /**
     * @var bool|mixed
     */
    protected $contentValueObjectFactory;
    /**
     * @var ContentWriteServiceInterface
     */
    protected $contentWriteService;
    /**
     * @var CurrentThemeInterface
     */
    protected $currentTheme;
    /**
     * @var AbstractContentManagerOption
     */
    protected $option;
    /**
     * @var LanguageService
     */
    protected $languageService;
    
    
    /**
     * WysiwygSaveCommand constructor.
     *
     * @param ContentWriteServiceInterface   $contentWriteService
     * @param ContentReadServiceInterface    $contentReadService
     * @param ContentValueObjectFactory      $contentValueObjectFactory
     * @param LanguageService                $languageService
     * @param CurrentThemeInterface          $currentTheme
     */
    public function __construct(
        ContentWriteServiceInterface $contentWriteService,
        ContentReadServiceInterface $contentReadService,
        ContentValueObjectFactory $contentValueObjectFactory,
        LanguageService $languageService,
        CurrentThemeInterface $currentTheme
    ) {
        $this->contentWriteService       = $contentWriteService;
        $this->contentReadService        = $contentReadService;
        $this->contentValueObjectFactory = $contentValueObjectFactory;
        $this->currentTheme              = $currentTheme;
        $this->languageService           = $languageService;
    }
    
    /**
     * Execute the command
     */
    public function rollback(): void
    {
        // TODO: Implement rollback() method.
    }
}
