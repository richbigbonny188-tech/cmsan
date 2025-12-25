<?php
/* --------------------------------------------------------------
   TypeRepository.php 2020-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Data\Repositories;

use Gambio\Admin\Modules\Configuration\App\Data\ConfigurationMapper;
use Gambio\Admin\Modules\Configuration\App\Data\Readers\TypeJsonReader;
use Gambio\Admin\Modules\Configuration\App\Exceptions\TypeDoesNotExist;
use Gambio\Admin\Modules\Configuration\Model\Entities\Type;

/**
 * Class TypeRepository
 *
 * @package Gambio\Admin\Modules\Configuration\App\Data\Repositories
 */
class TypeRepository
{
    /**
     * @var TypeJsonReader
     */
    private $reader;
    
    /**
     * @var ConfigurationMapper
     */
    private $mapper;
    
    
    /**
     * TypeRepository constructor.
     *
     * @param TypeJsonReader      $reader
     * @param ConfigurationMapper $mapper
     */
    public function __construct(TypeJsonReader $reader, ConfigurationMapper $mapper)
    {
        $this->reader = $reader;
        $this->mapper = $mapper;
    }
    
    
    /**
     * @param string $id
     * @param array  $params
     *
     * @return Type
     *
     * @throws TypeDoesNotExist
     */
    public function getTypeByIdAndParams(string $id, array $params): Type
    {
        $typeData = $this->reader->getTypeDataById($id);
        
        return $this->mapper->mapType([
                                          'id'     => $id,
                                          'params' => $params + $typeData['defaultParams'],
                                      ]);
    }
}