<?php
/* --------------------------------------------------------------
  TextEditorOption.php 2023-04-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\TextEditor\Entities;

use ContentNotFoundException;
use ContentReadServiceInterface;
use ContentText;
use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;
use Gambio\StyleEdit\Core\Repositories\Entities\Configuration;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\Components\ContentManager\Entities\AbstractContentManagerOption;
use InfoElementContent;
use ReflectionException;

/**
 * Class TextEditorOption
 */
class TextEditorOption extends AbstractComponentOption
{
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function parseValue($value)
    {
        return $value;
    }
    
    
    protected function isValid($value): bool
    {
        return true;
    }
    
    
    /**
     * @return string
     */
    public function type(): ?string
    {
        return 'texteditor';
    }
}