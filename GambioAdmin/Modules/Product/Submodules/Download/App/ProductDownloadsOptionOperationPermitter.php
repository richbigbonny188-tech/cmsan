<?php
/* --------------------------------------------------------------
   ProductDownloadsOptionOperationPermitter.php 2023-06-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App;

use Gambio\Admin\Modules\Option\Model\Entities\OptionValue;
use Gambio\Admin\Modules\Option\Model\Option;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;
use Gambio\Admin\Modules\Option\Services\OptionOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\ProductDownloadReader;

/**
 * Class ProductDownloadsOptionOperationPermitter
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App
 */
class ProductDownloadsOptionOperationPermitter implements OptionOperationPermitter
{
    /**
     * ProductOptionsOptionOperationPermitter constructor.
     *
     * @param ProductDownloadReader $reader
     */
    public function __construct(private ProductDownloadReader $reader) { }
    
    
    /**
     * @inheritDoc
     */
    public function permitsCreations(array ...$creationArgs): bool
    {
        return true;
    }
    
    
    /**
     * Checks for each option if other option value IDs are used for product options.
     * If so, some option values have been removed from the option, that can not be deleted.
     *
     * @inheritDoc
     */
    public function permitsStorages(Option ...$options): bool
    {
        foreach ($options as $option) {
            $callback     = static fn(OptionValue $value): int => $value->id();
            $usedValueIds = array_map($callback, $option->values()->asArray());
            
            if ($this->reader->areDifferentOptionValuesInUse($option->id(), ...$usedValueIds)) {
                return false;
            }
        }
        
        return true;
    }
    
    
    /**
     * Checks if the given option IDs are used for product options.
     *
     * @inheritDoc
     */
    public function permitsDeletions(OptionId ...$ids): bool
    {
        $callback = static fn(OptionId $id): int => $id->value();
        $idValues = array_map($callback, $ids);
        
        return $this->reader->isOneOrMoreOptionsInUse(...$idValues) === false;
    }
}