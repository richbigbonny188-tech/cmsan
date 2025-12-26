<?php
/*--------------------------------------------------------------------------------------------------
    AttachmentConfigurationServiceInterface.inc.php 2022-03-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);


interface AttachmentConfigurationServiceInterface
{
    /**
     * Returns the attachment configuration object value or null if no config was found
     *
     * @param string $key
     * @param string $languageCode
     *
     * @return AttachmentConfigurationInterface|null
     */
    public function getConfiguration(string $key, string $languageCode): ?AttachmentConfigurationInterface;
    
    
    /**
     * Saves the attachment configuration value to the database
     *
     * @param AttachmentConfigurationInterface $attachmentConfiguration
     *
     * @return void
     */
    public function saveConfiguration(AttachmentConfigurationInterface $attachmentConfiguration): void;
}