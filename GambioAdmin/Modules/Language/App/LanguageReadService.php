<?php
/* --------------------------------------------------------------
   LanguageReadService.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\App;

use Gambio\Admin\Modules\Language\Model\Collections\Languages;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Admin\Modules\Language\Services\LanguageFactory;
use Gambio\Admin\Modules\Language\Services\LanguageReadService as LanguageReadServiceInterface;
use Gambio\Admin\Modules\Language\Services\LanguageRepository;

/**
 * Class LanguageReadService
 *
 * @package Gambio\Admin\Modules\Language\App
 */
class LanguageReadService implements LanguageReadServiceInterface
{
    /**
     * @var LanguageRepository
     */
    private $repository;
    
    /**
     * @var LanguageFactory
     */
    private $factory;
    
    
    /**
     * LanguageReadService constructor.
     *
     * @param LanguageRepository $repository
     * @param LanguageFactory    $factory
     */
    public function __construct(LanguageRepository $repository, LanguageFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLanguages(): Languages
    {
        return $this->repository->getAllLanguages();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLanguageById(int $id): Language
    {
        return $this->repository->getLanguageById($this->factory->createLanguageId($id));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLanguageByCode(string $code): Language
    {
        return $this->repository->getLanguageByCode($this->factory->createLanguageCode($code));
    }
}