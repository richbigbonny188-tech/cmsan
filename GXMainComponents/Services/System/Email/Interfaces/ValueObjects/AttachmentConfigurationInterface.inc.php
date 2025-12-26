<?php
/* --------------------------------------------------------------
   AttachmentConfigurationInterface.inc.php 2022-04-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AttachmentConfigurationInterface
 *
 * @category   System
 * @package    Email
 * @subpackage ValueObjects
 */
interface AttachmentConfigurationInterface
{
    /**
     * Returns the namespace of the attachment configuration
     *
     * @return string
     */
    public function getNamespace(): string;
    
    
    /**
     * Returns the attachment configuration language code.
     *
     * @return string
     */
    public function getLanguageCode(): string;
    
    
    /**
     * Returns the attachment configuration key
     *
     * @return string
     */
    public function getConfigurationKey(): string;
    
    
    /**
     * Returns the attachment configuration value (hash)
     *
     * @return string
     */
    public function getConfigurationHash(): string;
    
    
    /**
     * Sets the value of the configuration hash
     *
     * @param string $hash
     *
     * @return AttachmentConfigurationInterface
     */
    public function setConfigurationHash(string $hash): AttachmentConfigurationInterface;
    
    
    /**
     * Checks if the given hash is identical as the property
     *
     * @param string $hash
     *
     * @return bool
     */
    public function isEqual(string $hash): bool;
}
