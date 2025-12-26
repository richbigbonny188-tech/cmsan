<?php
/* --------------------------------------------------------------
   MultiSelectTypeFactory.php 2020-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Services\TypeFactories;

use Gambio\Admin\Modules\Configuration\Model\Entities\Type;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class MultiSelectTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class MultiSelectTypeFactory implements TypeFactory
{
    /**
     * @var TextManager
     */
    private $textManager;
    
    
    /**
     * DropdownTypeFactory constructor.
     *
     * @param TextManager $textManager
     */
    public function __construct(TextManager $textManager)
    {
        $this->textManager = $textManager;
    }
    
    
    /**
     * @param array $params
     *
     * @return Type
     */
    public function createType(array $params): Type
    {
        $params['items'] = $params['items'] ?? [];
        foreach ($params['items'] as $key => $item) {
            if (is_array($item['text'])) {
                $params['items'][$key]['text'] = $this->textManager->getPhraseText($item['text']['phrase'],
                                                                                   $item['text']['section']);
            }
        }
        
        return Type::create('multi-select', $params);
    }
}