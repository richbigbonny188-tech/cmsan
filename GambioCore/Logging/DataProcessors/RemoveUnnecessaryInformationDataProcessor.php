<?php
/* --------------------------------------------------------------
   RemoveUnnecessaryInformationDataProcessor.php 2022-02-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Logging\DataProcessors;

use Exception;
use Monolog\Processor\ProcessorInterface;

/**
 * Class RemoveUnnecessaryInformationDataProcessor
 *
 * @package Gambio\Core\Logging\DataProcessors
 */
class RemoveUnnecessaryInformationDataProcessor implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(array $recordData): array
    {
        // Remove Smarty object as debug information
        unset($recordData['context']['context']['_smarty_tpl']);
        
        return $recordData;
    }
}