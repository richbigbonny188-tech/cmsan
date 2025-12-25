<?php
/*--------------------------------------------------------------------------------------------------
    AttachmentConfigurationService.inc.php 2023-03-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

use Gambio\Core\Configuration\Services\ConfigurationService;

class AttachmentConfigurationService implements AttachmentConfigurationServiceInterface
{
    /**
     * @var ConfigurationService
     */
    protected $configurationService;
    
    /**
     * @var AttachmentConfigurationFactory
     */
    protected $factory;
    
    
    /**
     * @param AttachmentConfigurationFactory $factory
     * @param ConfigurationService           $configurationService
     */
    public function __construct(AttachmentConfigurationFactory $factory, ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
        $this->factory              = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getConfiguration(string $key, string $languageCode): ?AttachmentConfigurationInterface
    {
        $config = $this->configurationService->findLanguageDependent($key, $languageCode);
        
        if (!$config) {
            return null;
        }
        
        return $this->factory->createValueObject($key, $config->value(), $languageCode);
    }
    
    
    /**
     * @inheritDoc
     */
    public function saveConfiguration(AttachmentConfigurationInterface $attachmentConfiguration): void
    {
        $this->configurationService->saveLanguageDependent($attachmentConfiguration->getConfigurationKey(),
                                                           $attachmentConfiguration->getLanguageCode(),
                                                           $attachmentConfiguration->getConfigurationHash());
    }
}