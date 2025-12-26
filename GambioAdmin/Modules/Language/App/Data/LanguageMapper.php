<?php
/* --------------------------------------------------------------
   LanguageMapper.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\App\Data;

use Gambio\Admin\Modules\Language\Model\Collections\LanguageIds;
use Gambio\Admin\Modules\Language\Model\Collections\Languages;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageId;
use Gambio\Admin\Modules\Language\Services\LanguageFactory;

/**
 * Class LanguageMapper
 *
 * @package Gambio\Admin\Modules\Language\App\Data
 */
class LanguageMapper
{
    /**
     * @var LanguageFactory
     */
    private $factory;
    
    
    /**
     * LanguageMapper constructor.
     *
     * @param LanguageFactory $factory
     */
    public function __construct(LanguageFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @param array $data
     *
     * @return Language
     */
    public function mapLanguage(array $data): Language
    {
        return $this->factory->createLanguage((int)$data['languages_id'],
                                              $data['code'],
                                              $data['name'],
                                              $data['language_charset'],
                                              $data['directory']);
    }
    
    
    /**
     * @param array $data
     *
     * @return Languages
     */
    public function mapLanguages(array $data): Languages
    {
        $parcelServices = array_map([$this, 'mapLanguage'], $data);
        
        return $this->factory->createLanguages(...$parcelServices);
    }
    
    
    /**
     * @param int $id
     *
     * @return LanguageId
     */
    public function mapLanguageId(int $id): LanguageId
    {
        return $this->factory->createLanguageId($id);
    }
    
    
    /**
     * @param int ...$ids
     *
     * @return LanguageIds
     */
    public function mapLanguageIds(int ...$ids): LanguageIds
    {
        $ids = array_map([$this, 'mapLanguageId'], $ids);
        
        return $this->factory->createLanguageIds(...$ids);
    }
}