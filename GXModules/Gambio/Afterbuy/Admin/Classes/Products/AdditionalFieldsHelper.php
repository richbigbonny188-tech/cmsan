<?php
/* --------------------------------------------------------------
   AdditionalFieldsHelper.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products;

use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldFactory;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldsReadService;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\AdditionalFieldsWriteService;
use Gambio\MainComponents\Services\Core\AdditionalFields\Services\Exceptions\CreationOfAdditionalFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldFactory;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldReadService;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldWriteService;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\CreationOfAdditionalProductFieldFailedException;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\Exceptions\DeletionOfAdditionalProductFieldsFailedException;
use LanguageCode;
use MainFactory;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use StaticGXCoreLoader;

/**
 * Class AdditionalFieldsHelper
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products
 */
class AdditionalFieldsHelper implements LoggerAwareInterface
{
    /**
     * @var AdditionalFieldsWriteService
     */
    private AdditionalFieldsWriteService $additionalFieldsWriteService;
    
    
    /**
     * @var AdditionalFieldsReadService
     */
    private AdditionalFieldsReadService $additionalFieldsReadService;
    
    
    /**
     * @var AdditionalFieldFactory
     */
    private AdditionalFieldFactory $additionalFieldFactory;
    
    
    /**
     * @var AdditionalProductFieldWriteService
     */
    private AdditionalProductFieldWriteService $additionalProductFieldWriteService;
    
    
    /**
     * @var AdditionalProductFieldReadService
     */
    private AdditionalProductFieldReadService $additionalProductFieldReadService;
    
    
    /**
     * @var AdditionalProductFieldFactory
     */
    private AdditionalProductFieldFactory $additionalProductFieldFactory;
    
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    
    
    /**
     *
     */
    public function __construct()
    {
        $this->additionalFieldsReadService        = StaticGXCoreLoader::getService('AdditionalFieldsRead');
        $this->additionalFieldsWriteService       = StaticGXCoreLoader::getService('AdditionalFieldsWrite');
        $this->additionalFieldFactory             = StaticGXCoreLoader::getService('AdditionalFieldsFactory');
        $this->additionalProductFieldReadService  = StaticGXCoreLoader::getService('AdditionalProductFieldRead');
        $this->additionalProductFieldWriteService = StaticGXCoreLoader::getService('AdditionalProductFieldWrite');
        $this->additionalProductFieldFactory      = StaticGXCoreLoader::getService('AdditionalProductFieldFactory');
        $this->logger                             = new NullLogger();
    }
    
    
    /**
     * Searches for an additional field by name.
     *
     * Returns additional_field_id of the first matching field or null for no match.
     *
     * @param string $name
     *
     * @return int|null
     */
    public function findAdditionalFieldIdByName(string $name): ?int
    {
        $candidates = $this->additionalFieldsReadService->getAllAdditionalFields(null, null, $name);
        foreach ($candidates as $candidate) {
            foreach ($candidate->fieldNames() as $fieldName) {
                if ($fieldName->name() === $name) {
                    return $candidate->id();
                }
            }
        }
        
        return null;
    }
    
    
    /**
     * Sets values (for all languages) of an additional product field identified by name to a given value.
     *
     * Product is assumed to have no additional fields when this method is called; use wipeAdditionalProductFields()
     * first.
     *
     * @param int    $productsId
     * @param string $name
     * @param string $value
     *
     * @return void
     * @throws CreationOfAdditionalFieldFailedException
     * @throws CreationOfAdditionalProductFieldFailedException
     */
    public function setAdditionalFieldValue(int $productsId, string $name, string $value): void
    {
        $fieldId = $this->findAdditionalFieldIdByName($name);
        if ($fieldId === null) {
            $this->logger->debug("Field $name not found, creating it");
            $localizations = [
                'names' => [],
            ];
            foreach (static::getAllLanguageCodes() as $languageCode) {
                $localizations['names'][$languageCode] = $name;
            }
            $names       = [$localizations];
            $newFieldIds = $this->additionalFieldsWriteService->createAdditionalField($names);
            $fieldId     = $newFieldIds->toArray()[0];
            $this->logger->debug("Created new additional field $fieldId ($name)");
        } else {
            $this->logger->debug("Additional field $name has ID $fieldId");
        }
        $values = [];
        foreach (static::getAllLanguageCodes() as $languageCode) {
            $values[$languageCode] = $value;
        }
        $this->additionalProductFieldWriteService->createAdditionalProductFields($productsId, $fieldId, $values);
        $this->logger->debug("Added new additional product field for product $productsId: $name -> $value");
    }
    
    
    /**
     * Deletes all additional product fields for a product.
     *
     * @param int $productId
     *
     * @return void
     * @throws DeletionOfAdditionalProductFieldsFailedException
     */
    public function wipeAdditionalProductFields(int $productId): void
    {
        $additionalProductFields   = $this->additionalProductFieldReadService->getAdditionalProductFields($productId);
        $additionalProductFieldIds = [];
        foreach ($additionalProductFields as $additionalProductField) {
            $additionalProductFieldIds[] = $additionalProductField->id();
        }
        
        $this->additionalProductFieldWriteService->deleteAdditionalProductField($productId,
            ...
                                                                                $additionalProductFieldIds);
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
    
    
    /**
     * Returns an array of all languages codes.
     *
     * @return array
     */
    private static function getAllLanguageCodes(): array
    {
        static $languageCodes = [];
        if (empty($languageCodes)) {
            /** @var \LanguageHelper $languageHelper */
            $languageHelper = MainFactory::create('LanguageHelper', StaticGXCoreLoader::getDatabaseQueryBuilder());
            /** @var LanguageCode $languageCode */
            foreach ($languageHelper->getLanguageCodes() as $languageCode) {
                $languageCodes[] = strtolower($languageCode->asString());
            }
        }
        
        return $languageCodes;
    }
}