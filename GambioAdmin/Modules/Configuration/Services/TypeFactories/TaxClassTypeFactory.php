<?php
/* --------------------------------------------------------------
   TaxClassTypeFactory.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Services\TypeFactories;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\Configuration\Model\Entities\Type;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class TaxClassTypeFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services\TypeFactories
 */
class TaxClassTypeFactory implements TypeFactory
{
    /**
     * @var Connection
     */
    private $db;
    
    /**
     * @var TextManager
     */
    private $textManager;
    
    
    /**
     * TaxClassTypeFactory constructor.
     *
     * @param Connection  $db
     * @param TextManager $textManager
     */
    public function __construct(Connection $db, TextManager $textManager)
    {
        $this->db          = $db;
        $this->textManager = $textManager;
    }
    
    
    /**
     * @param array $params
     *
     * @return Type
     * @throws Exception
     */
    public function createType(array $params): Type
    {
        $none            = $this->textManager->getPhraseText('TEXT_NONE', 'admin_general');
        $id              = (isset($params['multiSelect']) && $params['multiSelect']) ? 'multi-select' : 'dropdown';
        $params['items'] = array_merge([
                                           [
                                               'text'  => $none,
                                               'value' => '0',
                                           ],
                                       ],
                                       $this->getTaxClasses());
        unset($params['multiSelect']);
        
        return Type::create($id, $params);
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    private function getTaxClasses(): array
    {
        return $this->db->createQueryBuilder()
            ->select('`tax_class_id` as `value`, `tax_class_title` as `text`')
            ->from('`tax_class`')
            ->orderBy('`text`')
            ->executeQuery()
            ->fetchAllAssociative();
    }
}