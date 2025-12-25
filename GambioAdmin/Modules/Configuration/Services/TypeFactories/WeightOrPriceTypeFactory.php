<?php
/* --------------------------------------------------------------
   WeightOrPriceTypeFactory.php 2021-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Services\TypeFactories;

use Gambio\Admin\Modules\Configuration\Model\Entities\Type;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class WeightOrPriceTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class WeightOrPriceTypeFactory implements TypeFactory
{
    /**
     * @var TextManager
     */
    private $textManager;
    
    
    /**
     * WeightOrPriceTypeFactory constructor.
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
        $params['items'] = [
            [
                'value' => 'weight',
                'text'  => $this->textManager->getPhraseText('GM_CFG_WEIGHT', 'gm_general'),
            ],
            [
                'value' => 'price',
                'text'  => $this->textManager->getPhraseText('GM_CFG_PRICE', 'gm_general'),
            ],
        ];
        
        return Type::create('dropdown', $params);
    }
}