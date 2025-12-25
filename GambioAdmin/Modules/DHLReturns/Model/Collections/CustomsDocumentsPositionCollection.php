<?php
/* --------------------------------------------------------------
   CustomsDocumentsPositionCollection.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\Model\Collections;

use Gambio\Admin\Modules\DHLReturns\Model\ValueObjects\CustomsDocumentPosition;

class CustomsDocumentsPositionCollection implements \Iterator
{
    private $position = 0;
    private $customsDocumentPositions = [];
    
    private const MAX_POSITIONS = 5;
    
    /**
     * CustomsDocumentsPositionCollection constructor.
     */
    public function __construct()
    {
    }

    public function addCustomsDocumentPosition(CustomsDocumentPosition $position) {
        if (count($this->customsDocumentPositions) < static::MAX_POSITIONS) {
            $this->customsDocumentPositions[] = $position;
        }
        // else … throw Exception?
    }
    
    
    #[\ReturnTypeWillChange]
    public function current()
    {
        return array_key_exists($this->position,
                                $this->customsDocumentPositions) ? $this->customsDocumentPositions[$this->position] : null;
    }
    
    
    public function next(): void
    {
        ++$this->position;
    }
    
    
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }
    
    
    public function valid(): bool
    {
        return isset($this->customsDocumentPositions[$this->position]);
    }
    
    
    public function rewind(): void
    {
        $this->position = 0;
    }
}