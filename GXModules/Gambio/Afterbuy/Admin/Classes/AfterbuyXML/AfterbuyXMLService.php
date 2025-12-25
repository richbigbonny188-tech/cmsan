<?php
/* --------------------------------------------------------------
   AfterbuyXMLService.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\Exceptions\XMLException;
use GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\ValueObjects\GetShopProductsResult;
use GXModules\Gambio\Afterbuy\Admin\Classes\Catalogs\AfterbuyCatalogMapper;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\AfterbuyProductMapper;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SimpleXMLElement;
use function Gambio\Core\Logging\logger;

/**
 * Class AfterbuyXMLService
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML
 */
class AfterbuyXMLService implements LoggerAwareInterface
{
    
    private const BASE_URI = 'https://api.afterbuy.de/afterbuy/ABInterface.aspx';
    
    private const LOGGING_NAMESPACE = 'afterbuy';
    
    /**
     * @var string
     */
    private string $partnerToken;
    
    
    /**
     * @var string
     */
    private string $accountToken;
    
    
    /**
     * @var AfterbuyProductMapper
     */
    private AfterbuyProductMapper $productMapper;
    
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    
    
    /**
     * @var float
     */
    private float $apiTimeout = 10.0;
    
    
    /**
     * @param string $partnerToken
     * @param string $accountToken
     */
    public function __construct(string $partnerToken, string $accountToken)
    {
        $this->partnerToken  = $partnerToken;
        $this->accountToken  = $accountToken;
        $this->productMapper = new AfterbuyProductMapper();
        $this->logger        = new NullLogger();
    }
    
    
    /**
     * Performs a call to Afterbuy’s XML API.
     *
     * @param string $callName
     * @param array  $callData
     * @param array  $additionalGlobal
     *
     * @return SimpleXMLElement
     * @throws XMLException
     */
    public function performCall(string $callName, array $callData = [], array $additionalGlobal = []): SimpleXMLElement
    {
        //logger(static::LOGGING_NAMESPACE)->debug("Calling {$callName}");
        $this->logger->debug("Calling {$callName}");
        $client                                   = new Client([
                                                                   'base_uri' => static::BASE_URI,
                                                                   'timeout'  => $this->apiTimeout,
                                                               ]);
        $xmlBodyData                              = [
            'Request' => [
                'AfterbuyGlobal' => [
                    'PartnerToken' => $this->partnerToken,
                    'AccountToken' => $this->accountToken,
                    'CallName'     => $callName,
                ],
            ],
        ];
        $xmlBodyData['Request']['AfterbuyGlobal'] = array_merge($additionalGlobal,
                                                                $xmlBodyData['Request']['AfterbuyGlobal']);
        $xmlBodyData['Request']                   = array_merge($callData, $xmlBodyData['Request']);
        $xmlBody                                  = '<?xml version="1.0" encoding="utf-8"?>';
        $xmlBody                                  .= static::arrayToXML($xmlBodyData);
        $this->logger->debug("Request:\n" . $xmlBody);
        try {
            $response     = $client->post(static::BASE_URI, [
                'body'    => $xmlBody,
                'headers' => [
                    'Content-Type' => 'application/xml',
                ],
            ]);
            $responseBody = $response->getBody()->getContents();
            $this->logger->debug("Response {$response->getStatusCode()}:\n$responseBody");
            
            return static::parseXMLResponse($responseBody);
        } catch (TransferException $transferException) {
            //logger(static::LOGGING_NAMESPACE)->error("ERROR calling {$callName}: {$transferException->getMessage()} ({$transferException->getCode()})");
            $this->logger->error("ERROR calling {$callName}: {$transferException->getMessage()} ({$transferException->getCode()})");
            throw new XMLException($transferException->getMessage());
        }
    }
    
    
    /**
     * @param string $responseBody
     *
     * @return SimpleXMLElement
     * @throws XMLException
     */
    private static function parseXMLResponse(string $responseBody): SimpleXMLElement
    {
        $xmlErrorMode = libxml_use_internal_errors(true);
        libxml_clear_errors();
        $xmlResponse = simplexml_load_string($responseBody);
        $xmlErrors   = libxml_get_errors();
        libxml_use_internal_errors($xmlErrorMode);
        if (!empty($xmlErrors)) {
            foreach ($xmlErrors as $xmlError) {
                if ($xmlError->level === LIBXML_ERR_WARNING) {
                    logger(static::LOGGING_NAMESPACE)->warning("XML decoding: {$xmlError->message} in line {$xmlError->line}");
                } else {
                    $errorMessage = "API response cannot be processed: {$xmlError->message} (line {$xmlError->line})";
                    logger(static::LOGGING_NAMESPACE)->error($errorMessage . "\nXML:\n$responseBody");
                    throw new XMLException($errorMessage);
                }
            }
        }
        
        return $xmlResponse;
    }
    
    
    /**
     * Retrieves server time from Afterbuy and returns it as a DateTimeImmutable instance.
     *
     * @return DateTimeImmutable
     * @throws XMLException
     */
    public function getAfterbuyDatetime(): DateTimeImmutable
    {
        $xml       = $this->performCall('GetAfterbuyTime');
        $abUtcTime = (string)$xml->Result->AfterbuyUniversalTimeStamp;
        try {
            $dateTime = new DateTimeImmutable($abUtcTime, new \DateTimeZone('UTC'));
        } catch (Exception $e) {
            throw new XMLException($e->getMessage());
        }
        
        return $dateTime;
    }
    
    
    /**
     * @param DateTimeInterface $startDate
     *
     * @return array
     * @throws XMLException
     */
    public function getShopProductsModifiedSince(DateTimeInterface $startDate): array
    {
        $products      = [];
        $maxShopItems  = 100;
        $lastProductId = 0;
        do {
            $result          = $this->getShopProductsModifiedSinceRange($startDate, $maxShopItems, $lastProductId);
            $products        = array_merge($products, $result->getProducts());
            $hasMoreProducts = $result->isHasMoreProducts();
            $lastProductId   = $result->getLastProductId();
        } while ($hasMoreProducts);
        
        return $products;
    }
    
    
    /**
     * @param DateTimeInterface $startDate
     * @param int               $maxShopItems
     * @param int               $lastProductId
     * @param bool              $useLastSaleOrStockChange
     * @param int|null          $page
     *
     * @return GetShopProductsResult
     * @throws XMLException
     */
    public function getShopProductsModifiedSinceRange(
        DateTimeInterface $startDate,
        int               $maxShopItems = 100,
        int               $lastProductId = 0,
        bool              $useLastSaleOrStockChange = false,
        ?int              $page = null
    ): GetShopProductsResult {
        $startDateTime = new \DateTime('@' . $startDate->getTimestamp());
        $startDateTime->setTimezone(new \DateTimeZone('Europe/Berlin'));
        if ($useLastSaleOrStockChange) {
            //$dateFilterValue = 'LastSale';
            $dateFilterValue = 'LastStockChange';
            $this->logger->debug("XMLService getting up to $maxShopItems products sold/stockchanged after {$startDateTime->format('c')} starting with ProduktID $lastProductId or page "
                                 . (int)$page);
        } else {
            $dateFilterValue = 'ModDate';
            $this->logger->debug("XMLService getting up to $maxShopItems products modified after {$startDateTime->format('c')} starting with ProduktID $lastProductId or page "
                                 . (int)$page);
        }
        
        $callParams = [
            'MaxShopItems' => $maxShopItems,
            'DataFilter'   => [
                'Filter' => [
                    [
                        'FilterName'   => 'DateFilter',
                        'FilterValues' => [
                            'DateFrom'    => $startDateTime->format('Y-m-d H:i:s'),
                            'FilterValue' => $dateFilterValue,
                        ],
                    ],
                ],
            ],
        ];
        if ($page !== null) {
            $callParams['PaginationEnabled'] = 1;
            $callParams['PageNumber']        = $page;
        } else {
            $callParams['DataFilter']['Filter'][] = [
                'FilterName'   => 'RangeID',
                'FilterValues' => [
                    'ValueFrom' => $lastProductId + 1,
                ],
            ];
        }
        //print_r($callParams);
        $xml = $this->performCall('GetShopProducts', $callParams);
        if ((string)$xml->CallStatus === 'Error' && (string)$xml->Result->ErrorList->Error->ErrorCode === '15') {
            $this->logger->debug("XMLService: E15/Success, but got 0 products");
            
            // not really an error, just an empty result
            return new GetShopProductsResult([], false, 0);
        }
        if ((string)$xml->CallStatus !== 'Success' || count($xml->Result->Products->Product) === 0) {
            $this->logger->debug("ERROR response:\n" . $xml->asXML() . "\n\n");
            $message = "getShopProductsModifiedSince {$startDate->format('c')} failed";
            if (!empty($xml->Result->ErrorList)) {
                $message = "ERROR: ";
                foreach ($xml->Result->ErrorList->Error as $errorXml) {
                    $message .= (string)$errorXml->ErrorCode . " - " . (string)$errorXml->ErrorLongDescription;
                }
            }
            throw new XMLException($message);
        }
        
        $products         = [];
        $requiredProducts = [];
        $allProducts      = [];
        
        /** @var SimpleXMLElement $product */
        foreach ($xml->Result->Products->Product as $product) {
            $afterbuyProduct                               = $this->productMapper->createAfterbuyProductFromXml($product);
            $allProducts[$afterbuyProduct->getProductID()] = $afterbuyProduct;
            if (in_array($afterbuyProduct->getBaseProductFlag(), [0, 1, 2], true)) {
                // “real” product
                $products[$afterbuyProduct->getProductID()] = $afterbuyProduct;
            }
            if ($afterbuyProduct->getBaseProductFlag() === 1) {
                // base product of a variant set
                $baseProductIds = [];
                foreach ($afterbuyProduct->getBaseProducts() as $baseProduct) {
                    $baseProductIds[] = $baseProduct->getBaseProductID();
                }
                $getBaseProductsResult = $this->getProducts(...$baseProductIds);
                foreach ($getBaseProductsResult->getProducts() as $baseAfterbuyProduct) {
                    $afterbuyProduct->addVariantProduct($baseAfterbuyProduct);
                }
            }
            if ($afterbuyProduct->getBaseProductFlag() === 2 /* && $useLastSale === true */) {
                // main product of a product set
                $baseProductIds = [];
                foreach ($afterbuyProduct->getBaseProducts() as $baseProduct) {
                    $baseProductIds[] = $baseProduct->getBaseProductID();
                }
                $getBaseProductsResult = $this->getProducts(...$baseProductIds);
                foreach ($getBaseProductsResult->getProducts() as $baseAfterbuyProduct) {
                    $afterbuyProduct->addProductSetProduct($baseAfterbuyProduct);
                    $products[$baseAfterbuyProduct->getProductID()] = $baseAfterbuyProduct;
                }
            }
            if ($afterbuyProduct->getBaseProductFlag() === 3) {
                // pseudo-product representing a variation or part of a product set
                // -> make sure we get the parent as well
                foreach ($afterbuyProduct->getBaseProducts() as $baseProduct) {
                    $requiredProducts[] = $baseProduct->getBaseProductID();
                }
            }
        }
        
        $requiredProducts = array_unique($requiredProducts);
        $requiredProducts = array_diff($requiredProducts, array_keys($products));
        if (!empty($requiredProducts)) {
            $this->logger->debug("Required products: " . implode(", ", $requiredProducts));
            $additionalProducts = $this->getProducts(...$requiredProducts);
            foreach ($additionalProducts->getProducts() as $additionalProduct) {
                if ($additionalProduct->getBaseProductFlag() === 1) {
                    // variant set
                    $baseProductIds = [];
                    foreach ($additionalProduct->getBaseProducts() as $baseProduct) {
                        if (array_key_exists($baseProduct->getBaseProductID(), $allProducts)) {
                            $additionalProduct->addVariantProduct($allProducts[$baseProduct->getBaseProductID()]);
                            continue;
                        }
                        $baseProductIds[] = $baseProduct->getBaseProductID();
                    }
                    if (!empty($baseProductIds)) {
                        $this->logger->debug("Getting base products for {$additionalProduct->getProductID()}: "
                                             . implode(', ', $baseProductIds));
                        $getBaseProductsResult = $this->getProducts(...$baseProductIds);
                        foreach ($getBaseProductsResult->getProducts() as $baseAfterbuyProduct) {
                            $additionalProduct->addVariantProduct($baseAfterbuyProduct);
                        }
                    }
                }
                if ($additionalProduct->getBaseProductFlag() === 2) {
                    // product set
                    $baseProductIds = [];
                    foreach ($additionalProduct->getBaseProducts() as $baseProduct) {
                        if (array_key_exists($baseProduct->getBaseProductID(), $products)) {
                            $additionalProduct->addProductSetProduct($products[$baseProduct->getBaseProductID()]);
                        } else {
                            $baseProductIds[] = $baseProduct->getBaseProductID();
                        }
                    }
                    if (!empty($baseProductIds)) {
                        $this->logger->debug("Getting products from set {$additionalProduct->getProductID()}: "
                                             . implode(', ', $baseProductIds));
                        $getSetProductsResult = $this->getProducts(...$baseProductIds);
                        foreach ($getSetProductsResult->getProducts() as $setProduct) {
                            $additionalProduct->addProductSetProduct($setProduct);
                            $products[$setProduct->getProductID()] = $setProduct;
                        }
                    }
                }
                $products[$additionalProduct->getProductID()] = $additionalProduct;
            }
        }
        
        $this->logger->debug("Got " . count($products) . " products");
        
        $hasMoreProducts = (bool)(string)$xml->Result->HasMoreProducts;
        $lastProductId   = $hasMoreProducts ? (int)$xml->Result->LastProductID : 0;
        
        if (empty($xml->Result->PaginationResult)) {
            return new GetShopProductsResult($products, $hasMoreProducts, $lastProductId);
        }
        
        return new GetShopProductsResult($products,
                                         $hasMoreProducts,
                                         $lastProductId,
                                         (int)$xml->Result->PaginationResult->TotalNumberOfEntries,
                                         (int)$xml->Result->PaginationResult->TotalNumberOfPages,
                                         (int)$xml->Result->PaginationResult->ItemsPerPage,
                                         (int)$xml->Result->PaginationResult->PageNumber);
    }
    
    
    /**
     * Retrieves products from Afterbuy by their ProductIDs.
     *
     * @param int ...$productIDs
     *
     * @return GetShopProductsResult
     * @throws XMLException
     */
    public function getProducts(int ...$productIDs): GetShopProductsResult
    {
        $this->logger->debug("XMLService getting products " . implode(', ', $productIDs));
        $maxShopItems = 250;
        
        if (count($productIDs) > $maxShopItems) {
            throw new XMLException("Cannot retrieve more than {$maxShopItems} products.");
        }
        
        $additionalGlobal = [//'DetailLevel' => 0,
        ];
        
        $params       = [
            'MaxShopItems' => $maxShopItems,
            'DataFilter'   => [
                'Filter' => [
                    'FilterName'   => 'ProductID',
                    'FilterValues' => [
                        'FilterValue' => $productIDs,
                    ],
                ],
            ],
        ];
        $abProductXml = $this->performCall('GetShopProducts', $params, $additionalGlobal);
        if ((string)$abProductXml->CallStatus !== 'Success') {
            throw new XMLException("Product(s) could not be retrieved.");
        }
        
        $products = [];
        foreach ($abProductXml->Result->Products->Product as $xmlProduct) {
            //echo $xmlProduct->asXML(); die();
            $products[] = $this->productMapper->createAfterbuyProductFromXml($xmlProduct);
        }
        
        $hasMoreProducts = (bool)(string)$abProductXml->Result->HasMoreProducts;
        $lastProductId   = $hasMoreProducts ? (int)$abProductXml->Result->LastProductID : 0;
        
        return new GetShopProductsResult($products, $hasMoreProducts, $lastProductId);
    }
    
    
    /**
     * Returns Afterbuy catalogs
     *
     * @param bool $includeProducts
     *
     * @return array
     * @throws XMLException
     */
    public function getShopCatalogs(bool $includeProducts = false): array
    {
        $catalogs = [];
        
        $callParams       = [
            'MaxCatalogs' => 50, // 1..200
        ];
        $additionalGlobal = [
            'DetailLevel' => $includeProducts ? 2 : 0,
        ];
        
        do {
            $catalogsXml = $this->performCall('GetShopCatalogs', $callParams, $additionalGlobal);
            
            foreach ($catalogsXml->Result->Catalogs->Catalog as $catalogXml) {
                $catalog = AfterbuyCatalogMapper::createAfterbuyCatalogFromXml($catalogXml);
                
                if ($includeProducts && !empty($catalogXml->CatalogProducts)) {
                    foreach ($catalogXml->CatalogProducts->ProductID as $productID) {
                        $catalog->addProductID((int)$productID);
                    }
                }
                $catalogs[$catalog->getCatalogID()] = $catalog;
            }
            
            $hasMoreCatalogs = (int)$catalogsXml->Result->HasMoreCatalogs > 0;
            if ($hasMoreCatalogs) {
                $lastCatalogID = (int)$catalogsXml->Result->LastCatalogID;
                if ($lastCatalogID === 0) {
                    // this should never happen; prevent endless loop
                    break;
                }
                $callParams['DataFilter'] = [
                    'Filter' => [
                        'FilterName'   => 'RangeID',
                        'FilterValues' => [
                            'ValueFrom' => $lastCatalogID,
                        ],
                    ],
                ];
            }
        } while ($hasMoreCatalogs);
        
        return $catalogs;
    }
    
    
    /**
     * @param array  $input
     * @param int    $indent
     * @param string $indentChar
     *
     * @return string
     */
    public static function arrayToXML(array $input, int $indent = 0, string $indentChar = '    '): string
    {
        $output      = '';
        $indentation = str_repeat($indentChar, $indent);
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $output .= "{$indentation}<{$key}><![CDATA[{$value}]]></{$key}>\n";
            }
            if (is_numeric($value)) {
                $output .= "{$indentation}<{$key}>{$value}</{$key}>\n";
            }
            if (is_array($value)) {
                if (static::arrayIsList($value)) {
                    foreach ($value as $element) {
                        if (is_array($element)) {
                            $output .= "{$indentation}<{$key}>\n" . static::arrayToXML($element, $indent + 1)
                                       . "{$indentation}</{$key}>\n";
                        }
                        if (is_string($element) || is_numeric($element)) {
                            $output .= "{$indentation}<{$key}>{$element}</{$key}>\n";
                        }
                    }
                } else {
                    $output .= "{$indentation}<{$key}>\n" . static::arrayToXML($value, $indent + 1)
                               . "{$indentation}</{$key}>\n";
                }
            }
        }
        
        return $output;
    }
    
    
    /**
     * @param array $input
     *
     * @return bool
     */
    public static function arrayIsList(array $input): bool
    {
        if (function_exists('array_is_list')) {
            return array_is_list($input);
        }
        
        return array_keys($input)[0] === 0; // enough for this purpose, but not a general solution!
    }
    
    
    /**
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}