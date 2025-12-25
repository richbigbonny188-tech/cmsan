<?php
/*--------------------------------------------------------------------------------------------------
    AttachmentConfigurationFactory.inc.php 2022-03-30
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

/**
 *
 */
class AttachmentConfigurationFactory
{
    
    /**
     * Creates a AttachmentConfiguration value object
     *
     * @param string $key
     * @param string $hash
     * @param string $languageCode
     *
     * @return AttachmentConfigurationInterface
     *
     * @throws InvalidArgumentException
     */
    public function createValueObject(string $key, string $hash, string $languageCode): AttachmentConfigurationInterface
    {
        return new AttachmentConfiguration($key, $hash, $languageCode);
    }
    
    
    /**
     * Returns the Withdrawal Information Content configuration key
     *
     * @return string
     */
    public static function getWithdrawalInformationContentConfigurationKey(): string
    {
        return AttachmentConfiguration::ATTACHMENT_CONFIGURATION_NAMESPACE . '/' . AttachmentConfiguration::WITHDRAWAL_INFORMATION_CONTENT_HASH;
    }
    
    
    /**
     * Returns the Withdrawal Form Content configuration key
     *
     * @return string
     */
    public static function getWithdrawalFormContentConfigurationKey(): string
    {
        return AttachmentConfiguration::ATTACHMENT_CONFIGURATION_NAMESPACE . '/' . AttachmentConfiguration::WITHDRAWAL_FORM_CONTENT_HASH;
    }
    
    
    /**
     * Returns the Terms and Conditions configuration key
     *
     * @return string
     */
    public static function getTermsAndConditionsConfigurationKey(): string
    {
        return AttachmentConfiguration::ATTACHMENT_CONFIGURATION_NAMESPACE . '/' . AttachmentConfiguration::TERMS_AND_CONDITIONS_CONTENT_HASH;
    }
    
    
    /**
     * Returns the Privacy and Notice configuration key
     *
     * @return string
     */
    public static function getPrivacyNoticeConfigurationKey(): string
    {
        return AttachmentConfiguration::ATTACHMENT_CONFIGURATION_NAMESPACE . '/' . AttachmentConfiguration::PRIVACY_NOTICE_CONTENT_HASH;
    }
}