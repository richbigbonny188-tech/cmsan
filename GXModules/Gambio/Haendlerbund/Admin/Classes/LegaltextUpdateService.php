<?php
/* --------------------------------------------------------------
   LegaltextUpdateService.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Haendlerbund\Admin\Classes;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\ParameterType;
use Gambio\Admin\Modules\Language\Services\LanguageReadService;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\Gambio\Haendlerbund\Admin\Classes\Exceptions\LegaltextUpdateException;

class LegaltextUpdateService
{
    private const CONTENT_GROUP_PRIVACY              = 2;
    private const CONTENT_GROUP_TOS                  = 3;
    private const CONTENT_GROUP_IMPRINT              = 4;
    private const CONTENT_GROUP_PAYMENT_AND_SHIPPING = 3889891;
    private const CONTENT_GROUP_WITHDRAWAL_1         = 3889896;
    private const CONTENT_GROUP_WITHDRAWAL_2         = 3889897;
    private const CONTENT_GROUP_WITHDRAWAL_3         = 3889898;
    private const CONTENT_GROUP_WITHDRAWAL_4         = 3889899;
    
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    /**
     * @var LanguageReadService
     */
    private $languageReadService;
    
    
    /**
     * @param Connection           $connection
     * @param ConfigurationService $configurationService
     * @param LanguageReadService  $languageReadService
     */
    public function __construct(
        Connection           $connection,
        ConfigurationService $configurationService,
        LanguageReadService  $languageReadService
    ) {
        $this->connection           = $connection;
        $this->configurationService = $configurationService;
        $this->languageReadService  = $languageReadService;
    }
    
    
    /**
     * @param string      $tosText
     * @param string      $languageCode
     * @param string|null $heading
     *
     * @throws LegaltextUpdateException
     */
    public function updateTermsOfService(string $tosText, string $languageCode, ?string $heading = null): void
    {
        if (!in_array($languageCode, $this->getValidLanguageCodes(), true)) {
            return;
        }
        try {
            $this->updateContentText(static::CONTENT_GROUP_TOS, $tosText, $languageCode);
            if ($heading !== null) {
                $this->updateContentHeading(static::CONTENT_GROUP_TOS, $heading, $languageCode);
            }
        } catch (DBALException $e) {
            throw new LegaltextUpdateException($e->getMessage());
        }
    }
    
    
    /**
     * @param int    $contentGroupId
     * @param string $text
     * @param string $languageCode
     *
     * @throws DBALException
     */
    private function updateContentText(int $contentGroupId, string $text, string $languageCode): void
    {
        if (!in_array($languageCode, $this->getValidLanguageCodes(), true)) {
            return;
        }
        $sql  = "update content_manager cm " . "join languages l on l.languages_id = cm.languages_id "
                . "set cm.content_text = :newtext ," . "cm.content_version = :newversion "
                . "where cm.content_group = :content_group and l.code = :language_code";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('newtext', $text);
        $stmt->bindValue('newversion', 'HB-' . (new \DateTimeImmutable())->format('c'));
        $stmt->bindValue('content_group', $contentGroupId, ParameterType::INTEGER);
        $stmt->bindValue('language_code', $languageCode);
        $stmt->executeQuery();
    }
    
    
    /**
     * @param int    $contentGroupId
     * @param string $heading
     * @param string $languageCode
     *
     * @throws DBALException
     */
    private function updateContentHeading(int $contentGroupId, string $heading, string $languageCode): void
    {
        if (!in_array($languageCode, $this->getValidLanguageCodes(), true)) {
            return;
        }
        $sql  = "update content_manager cm " . "join languages l on l.languages_id = cm.languages_id "
                . "set cm.content_heading = :newheading "
                . "where cm.content_group = :content_group and l.code = :language_code";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('newheading', $heading);
        $stmt->bindValue('content_group', $contentGroupId, ParameterType::INTEGER);
        $stmt->bindValue('language_code', $languageCode);
        $stmt->executeQuery();
    }
    
    
    /**
     * Updates ToS text for inclusion in PDF invoices.
     *
     * @param string      $tosText
     * @param string      $languageCode
     * @param string|null $heading
     */
    public function updateTermsOfServicePdf(string $tosText, string $languageCode, ?string $heading = null): void
    {
        if (!in_array($languageCode, $this->getValidLanguageCodes(), true)) {
            return;
        }
        $this->configurationService->saveLanguageDependent('gm_configuration/GM_PDF_CONDITIONS',
                                                           $languageCode,
                                                           $tosText);
        if ($heading !== null) {
            $this->configurationService->saveLanguageDependent('gm_configuration/GM_PDF_HEADING_CONDITIONS',
                                                               $languageCode,
                                                               $heading);
        }
    }
    
    
    /**
     * @param string      $withdrawalText
     * @param string      $languageCode
     * @param string|null $heading
     */
    public function updateWithdrawalPdf(string $withdrawalText, string $languageCode, ?string $heading = null): void
    {
        if (!in_array($languageCode, $this->getValidLanguageCodes(), true)) {
            return;
        }
        $this->configurationService->saveLanguageDependent('gm_configuration/GM_PDF_WITHDRAWAL',
                                                           $languageCode,
                                                           $withdrawalText);
        if ($heading !== null) {
            $this->configurationService->saveLanguageDependent('gm_configuration/GM_PDF_HEADING_WITHDRAWAL',
                                                               $languageCode,
                                                               $heading);
        }
    }
    
    
    /**
     * @param string      $privacyText
     * @param string      $languageCode
     * @param string|null $heading
     *
     * @throws LegaltextUpdateException
     */
    public function updatePrivacyNotice(string $privacyText, string $languageCode, ?string $heading = null): void
    {
        if (!in_array($languageCode, $this->getValidLanguageCodes(), true)) {
            return;
        }
        try {
            $this->updateContentText(static::CONTENT_GROUP_PRIVACY, $privacyText, $languageCode);
            if ($heading !== null) {
                $this->updateContentHeading(static::CONTENT_GROUP_PRIVACY, $heading, $languageCode);
            }
        } catch (DBALException $e) {
            throw new LegaltextUpdateException($e->getMessage());
        }
    }
    
    
    /**
     * @param string $paymentAndShippingText
     * @param string $languageCode
     *
     * @throws LegaltextUpdateException
     */
    public function updatePaymentAndShipping(
        string  $paymentAndShippingText,
        string  $languageCode,
        ?string $heading = null
    ): void {
        if (!in_array($languageCode, $this->getValidLanguageCodes(), true)) {
            return;
        }
        try {
            $this->updateContentText(static::CONTENT_GROUP_PAYMENT_AND_SHIPPING,
                                     $paymentAndShippingText,
                                     $languageCode);
            if ($heading !== null) {
                $this->updateContentHeading(static::CONTENT_GROUP_PAYMENT_AND_SHIPPING, $heading, $languageCode);
            }
        } catch (DBALException $e) {
            throw new LegaltextUpdateException($e->getMessage());
        }
    }
    
    
    /**
     * @param string      $withdrawalText
     * @param string      $languageCode
     * @param string|null $heading
     *
     * @throws LegaltextUpdateException
     */
    public function updateWithdrawal1(string $withdrawalText, string $languageCode, ?string $heading = null): void
    {
        if (!in_array($languageCode, $this->getValidLanguageCodes(), true)) {
            return;
        }
        try {
            $this->updateContentText(static::CONTENT_GROUP_WITHDRAWAL_1, $withdrawalText, $languageCode);
            if ($heading !== null) {
                $this->updateContentHeading(static::CONTENT_GROUP_WITHDRAWAL_1, $heading, $languageCode);
            }
        } catch (DBALException $e) {
            throw new LegaltextUpdateException($e->getMessage());
        }
    }
    
    
    /**
     * @param string      $withdrawalText
     * @param string      $languageCode
     * @param string|null $heading
     *
     * @throws LegaltextUpdateException
     */
    public function updateWithdrawal2(string $withdrawalText, string $languageCode, ?string $heading = null): void
    {
        if (!in_array($languageCode, $this->getValidLanguageCodes(), true)) {
            return;
        }
        try {
            $this->updateContentText(static::CONTENT_GROUP_WITHDRAWAL_2, $withdrawalText, $languageCode);
            if ($heading !== null) {
                $this->updateContentHeading(static::CONTENT_GROUP_WITHDRAWAL_2, $heading, $languageCode);
            }
        } catch (DBALException $e) {
            throw new LegaltextUpdateException($e->getMessage());
        }
    }
    
    
    /**
     * @param string      $withdrawalText
     * @param string      $languageCode
     * @param string|null $heading
     *
     * @throws LegaltextUpdateException
     */
    public function updateWithdrawal3(string $withdrawalText, string $languageCode, ?string $heading = null): void
    {
        if (!in_array($languageCode, $this->getValidLanguageCodes(), true)) {
            return;
        }
        try {
            $this->updateContentText(static::CONTENT_GROUP_WITHDRAWAL_3, $withdrawalText, $languageCode);
            if ($heading !== null) {
                $this->updateContentHeading(static::CONTENT_GROUP_WITHDRAWAL_3, $heading, $languageCode);
            }
        } catch (DBALException $e) {
            throw new LegaltextUpdateException($e->getMessage());
        }
    }
    
    
    /**
     * @param string      $withdrawalText
     * @param string      $languageCode
     * @param string|null $heading
     *
     * @throws LegaltextUpdateException
     */
    public function updateWithdrawal4(string $withdrawalText, string $languageCode, ?string $heading = null): void
    {
        if (!in_array($languageCode, $this->getValidLanguageCodes(), true)) {
            return;
        }
        try {
            $this->updateContentText(static::CONTENT_GROUP_WITHDRAWAL_4, $withdrawalText, $languageCode);
            if ($heading !== null) {
                $this->updateContentHeading(static::CONTENT_GROUP_WITHDRAWAL_4, $heading, $languageCode);
            }
        } catch (DBALException $e) {
            throw new LegaltextUpdateException($e->getMessage());
        }
    }
    
    
    /**
     * @param string      $imprintText
     * @param string      $languageCode
     * @param string|null $heading
     *
     * @throws LegaltextUpdateException
     */
    public function updateImprint(string $imprintText, string $languageCode, ?string $heading = null): void
    {
        if (!in_array($languageCode, $this->getValidLanguageCodes(), true)) {
            return;
        }
        try {
            $this->updateContentText(static::CONTENT_GROUP_IMPRINT, $imprintText, $languageCode);
            if ($heading !== null) {
                $this->updateContentHeading(static::CONTENT_GROUP_IMPRINT, $heading, $languageCode);
            }
        } catch (DBALException $e) {
            throw new LegaltextUpdateException($e->getMessage());
        }
    }
    
    
    /**
     * Returns an array of valid, configured language codes.
     *
     * @return array
     */
    private function getValidLanguageCodes(): array
    {
        static $languageCodes = [];
        if (!empty($languageCodes)) {
            return $languageCodes;
        }
        $allLanguages = $this->languageReadService->getLanguages();
        foreach ($allLanguages as $language) {
            $languageCodes[] = $language->code();
        }
        
        return $languageCodes;
    }
}
