<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerPagesSaveCommandFactory.php 2021-05-18
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\ContentManager\Factories;

use Gambio\StyleEdit\Core\Components\ContentManager\Command\ContentManagerElementTypeSaveCommand;
use Gambio\StyleEdit\Core\Components\ContentManager\Command\ContentManagerSaveCommand;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\Components\ContentManager\Command\ContentManagerPageTypeSaveCommand;
use Gambio\StyleEdit\Core\Components\ContentManager\Exceptions\SaveCommandNotFoundException;
use Gambio\StyleEdit\Core\Components\ContentManager\Command\ContentManagerLinkTypeSaveCommand;

/**
 * Class ContentManagerPagesSaveCommandFactory
 * @package Gambio\StyleEdit\Core\Components\ContentManager\Factories
 */
class ContentManagerPagesSaveCommandFactory
{
    /**
     * @param string|null $pageType
     *
     * @return ContentManagerSaveCommand
     * @throws SaveCommandNotFoundException
     */
    public function createFromPageType(?string $pageType): ContentManagerSaveCommand
    {
        switch ($pageType) {
            case 'content':
            case 'page':
                return SingletonPrototype::instance()->get(ContentManagerPageTypeSaveCommand::class);
            case 'link':
                return SingletonPrototype::instance()->get(ContentManagerLinkTypeSaveCommand::class);
            case 'element':
                return SingletonPrototype::instance()->get(ContentManagerElementTypeSaveCommand::class);
            default:
                throw new SaveCommandNotFoundException("Command {$pageType} not found");
        }
    }
}
