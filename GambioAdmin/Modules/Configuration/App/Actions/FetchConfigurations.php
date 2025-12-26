<?php
/* --------------------------------------------------------------
 FetchConfigurations.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Actions;

use Gambio\Admin\Modules\Configuration\Services\Interfaces\CategoryRepositoryInterface;
use Gambio\Admin\Modules\Configuration\Services\Interfaces\ListingCategoryRepositoryInterface;
use Gambio\Admin\Modules\Configuration\Services\Interfaces\TagRepositoryInterface;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchConfigurations
 *
 * @package Gambio\Admin\Modules\Configuration\App\Actions
 * @codeCoverageIgnore
 */
class FetchConfigurations extends AbstractAction
{
    /**
     * @var ListingCategoryRepositoryInterface
     */
    private $listingCategoryRepository;
    
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;
    
    /**
     * @var TagRepositoryInterface
     */
    private $tagRepository;
    
    
    /**
     * FetchConfigurations constructor.
     *
     * @param ListingCategoryRepositoryInterface $listingCategoryRepository
     * @param CategoryRepositoryInterface        $categoryRepository
     * @param TagRepositoryInterface             $tagRepository
     */
    public function __construct(
        ListingCategoryRepositoryInterface $listingCategoryRepository,
        CategoryRepositoryInterface $categoryRepository,
        TagRepositoryInterface $tagRepository
    ) {
        $this->listingCategoryRepository = $listingCategoryRepository;
        $this->categoryRepository        = $categoryRepository;
        $this->tagRepository             = $tagRepository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $data = [
            'categories' => $this->categoryRepository->getAllCategories(),
            'tags'       => $this->tagRepository->getAllTags(),
            'data'       => $this->listingCategoryRepository->getAllListingCategories(),
        ];
        
        return $response->withJson($data);
    }
}