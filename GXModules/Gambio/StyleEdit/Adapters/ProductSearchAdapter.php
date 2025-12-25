<?php
/*--------------------------------------------------------------------------------------------------
    ProductSearchAdapter.php 2021-07-26
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace Gambio\StyleEdit\Adapters;


use Exception;
use Gambio\StyleEdit\Adapters\Interfaces\ProductSearchAdapterInterface;
use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\SingletonStrategyInterface;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use IdType;
use InvalidArgumentException;
use LanguageCode;
use MainFactory;
use ProductListItemJsonSerializer;
use ProductSearchCondition;
use StaticGXCoreLoader;
use StringType;

class ProductSearchAdapter implements ProductSearchAdapterInterface, SingletonStrategyInterface
{

    /**
     * @var Language
     */
    protected $language;
    protected   $productReadService;
    /**
     * @var ProductListItemJsonSerializer
     */
    protected $productListItemJsonSerializer;
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
        $this->productReadService = StaticGXCoreLoader::getService('ProductRead');
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
        $this->productListItemJsonSerializer = MainFactory::create(ProductListItemJsonSerializer::class);
    }

    /**
     * @param string $term
     *
     * @return array
     */
    public function searchByTerm(string $term): array
    {

        $result = [];

        $searchCondition = ProductSearchCondition::createByArray([
            'search' => [
                'should' => [
                    [
                        'match' => [
                            'products.products_id' => $term
                        ]
                    ],
                    [
                        'like' => [
                            'products_description.products_name' => "%{$term}%"
                        ]
                    ]
                ]
            ]
        ]);
    
        // hard limit of 100 products to avoid memory and performance problems (UI has a default limit of 15)
        $products = $this->productReadService->searchProducts($this->languageCode,
                                                              $searchCondition,
                                                              new \Pager(new \IntType(1), new \IntType(100)))
            ->getArray();

        foreach ($products as $product) {
            $data     = json_decode($this->productListItemJsonSerializer->serialize($product));
            $result[] = [
                'id'     => $data->id,
                'name'   => $data->name,
                'active' => $data->isActive,
                'image'  => $this->getCorrectImagePath($data->image)
            ];
        }
        return $result;
    }

    /**
     * @param $image
     *
     * @return string
     */
    protected function getCorrectImagePath($image): string
    {
        return HTTP_SERVER . DIR_WS_CATALOG . "images/product_images/thumbnail_images/{$image}";
    }

    public function searchByIds(array $idCollection)
    {
        $result = [];


        foreach ($idCollection as $id) {
            $parsedId = new IdType((int)$id);

            try {
                $product = $this->productReadService->getProductById($parsedId);

                $result[] = [
                    'id'     => $product->getProductId(),
                    'name'   => $product->getName($this->languageCode),
                    'active' => $product->isActive(),
                    'image'  => $this->getCorrectImagePath($product->getPrimaryImage()->getFilename())
                ];
            } catch (Exception $exception) {
                continue;
            }
        }

        return $result;
    }

}