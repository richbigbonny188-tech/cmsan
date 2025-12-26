<?php
/* --------------------------------------------------------------
  BackgroundSaveCommand.php 2019-09-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\BackgroundGroup\Commands;

use Gambio\StyleEdit\Core\Components\BackgroundGroup\Entities\BackgroundGroupOption;
use Gambio\StyleEdit\Core\Components\Option\Commands\GroupOptionSaveCommand;

/**
 * Class BackgroundSaveCommand
 */
class BackgroundSaveCommand extends GroupOptionSaveCommand
{
    /**
     * @var BackgroundGroupOption
     */
    protected $option;
    /**
     * Execute the command
     */
    public function execute(): void
    {
        $imageGroup = $this->option->image();
        
        if ($imageGroup->enabled()->value() === true && $imageGroup->url()->value() === '') {
            
            $imageGroup->enabled()->setValue(false);
        }
        
        parent::execute();
    }
    
    
    /**
     * Execute the command
     */
    public function rollback(): void
    {
        // TODO: Implement rollback() method.
    }
}