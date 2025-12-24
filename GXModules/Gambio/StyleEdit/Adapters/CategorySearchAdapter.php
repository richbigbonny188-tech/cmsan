<?php
/*--------------------------------------------------------------------------------------------------
    CategorySearchAdapter.php 2021-07-25
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace GXModules\Gambio\StyleEdit\Adapters;


use CategoryListItemJsonSerializer;
use CategorySearchCondition;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use GXModules\Gambio\StyleEdit\Adapters\Interfaces\CategorySearchAdapterInterface;
use LanguageCode;
use MainFactory;
use StaticGXCoreLoader;
use StringType;

class CategorySearchAdapter implements CategorySearchAdapterInterface
{
    /**
     * @var bool
     */
    protected $categoryListItemJsonSerializer;
    /**
     * @var object
     */
    protected $categoryReadService;
    /**
     * @var LanguageCode
     */
    protected $languageCode;


    public function __construct( Language $language)
    {
        $this->createLanguageCode($language);
        $this->createService();
        $this->createSerializer();
    }

    /**
     *
     */
    protected function createService(): void
    {
        $this->categoryReadService = StaticGXCoreLoader::getService('CategoryRead');
    }

    protected function createLanguageCode(Language $language): void
    {
        $this->languageCode = MainFactory::create(LanguageCode::class, new StringType($language->code()));
    }

    /**
     *
     */
    protected function createSerializer(): void
    {
        $this->categoryListItemJsonSerializer = MainFactory::create(CategoryListItemJsonSerializer::class);
    }

    public function searchByTerm(string $term)
    {
        $result = [];

        $searchCondition = CategorySearchCondition::createByArray([
            'search' => [
                'should' => [
                    [
                        'match' => [
                            'categories.categories_id' => $term
                        ]
                    ],
                    [
                        'like' => [
                            'categories_description.categories_name' => "%{$term}%"
                        ]
                    ]
                ]
            ]
        ]);
    
        // hard limit of 100 categories to avoid memory and performance problems (UI has a default limit of 15)
        $categories = $this->categoryReadService->searchCategories($this->languageCode,
                                                                   $searchCondition,
                                                                   new \Pager(new \IntType(1), new \IntType(100)))
            ->getArray();

        foreach ($categories as $category) {
            $data     = json_decode($this->categoryListItemJsonSerializer->serialize($category));
            $result[] = [
                'id'     => $data->id,
                'name'   => $data->name,
                'active' => $data->isActive
            ];
        }

        return $result;

    }
}