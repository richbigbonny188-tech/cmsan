<?php
/* --------------------------------------------------------------
   RedirectServiceFactory.php 2020-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\RedirectRules\Factories;

use Gambio\Admin\Modules\RedirectRules\RedirectService;
use Gambio\Admin\Modules\RedirectRules\RedirectServiceInterface;
use Gambio\Admin\Modules\RedirectRules\Repository\RedirectRepositoryInterface;

/**
 * Class RedirectServiceFactory
 *
 * @package Gambio\Admin\Modules\RedirectRules\Factories
 */
class RedirectServiceFactory
{
    /**
     * @var RedirectRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * RedirectServiceFactory constructor.
     *
     * @param RedirectRepositoryInterface $repository
     */
    public function __construct(RedirectRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @return RedirectServiceInterface
     */
    public function service(): RedirectServiceInterface
    {
        return new RedirectService($this->repository);
    }
}
