<?php
/* --------------------------------------------------------------
   AttachmentConfiguration.inc.php 2022-03-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AttachmentConfigurationInterface');

/**
 * Class AttachmentConfiguration
 *
 * Important: This value object is not going to check if the attachment file exists in the
 * server because that would cause problems with the service usage (e.g. if an attachment
 * file is missing an exception would halt all the email service operations).
 *
 * @category   System
 * @package    Email
 * @subpackage ValueObjects
 */
class AttachmentConfiguration implements AttachmentConfigurationInterface
{
    public const ATTACHMENT_CONFIGURATION_NAMESPACE = 'email_attachment';
    
    public const WITHDRAWAL_INFORMATION_CONTENT_HASH = 'withdrawal_information_content_hash';
    public const WITHDRAWAL_FORM_CONTENT_HASH        = 'withdrawal_form_content_hash';
    public const TERMS_AND_CONDITIONS_CONTENT_HASH   = 'terms_and_conditions_content_hash';
    public const PRIVACY_NOTICE_CONTENT_HASH         = 'privacy_notice_content_hash';
    
    /**
     * Email attachment configuration language code.
     *
     * @var string
     */
    protected $languageCode;
    
    /**
     * Email attachment configuration key.
     *
     * @var string
     */
    protected $configurationKey;
    
    /**
     * Email attachment configuration hash.
     *
     * @var string
     */
    protected $configurationHash;
    
    
    /**
     * Constructor
     *
     * Executes the validation checks for the email attachment.
     *
     * @param string $p_configuration_key  E-Mail attachment configuration key.
     * @param string $p_configuration_hash E-Mail attachment configuration hash.
     * @param string $p_language_code      E-Mail attachment configuration language code.
     *
     * @throws InvalidArgumentException If the provided argument is not a string or empty.
     *
     */
    public function __construct($p_configuration_key, $p_configuration_hash, $p_language_code)
    {
        if (!is_string($p_configuration_hash)) {
            throw new InvalidArgumentException('Invalid argument provided (expected string configuration_hash) $p_emailAttachment: '
                                               . print_r($p_configuration_hash, true));
        }
        
        if (!is_string($p_language_code)) {
            throw new InvalidArgumentException('Invalid argument provided (expected string language_code) $p_emailAttachment: '
                                               . print_r($p_language_code, true));
        }
        
        if (!is_string($p_language_code)) {
            throw new InvalidArgumentException('Invalid argument provided (expected string language_code) $p_emailAttachment: '
                                               . print_r($p_language_code, true));
        }
        
        $this->languageCode      = (string)$p_language_code;
        $this->configurationKey  = (string)$p_configuration_key;
        $this->configurationHash = (string)$p_configuration_hash;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getNamespace(): string
    {
        return self::ATTACHMENT_CONFIGURATION_NAMESPACE;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getConfigurationKey(): string
    {
        return $this->configurationKey;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getConfigurationHash(): string
    {
        return $this->configurationHash;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setConfigurationHash(string $hash): AttachmentConfigurationInterface
    {
        $this->configurationHash = $hash;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function isEqual(string $hash): bool
    {
        return $this->configurationHash === $hash;
    }
    
}
