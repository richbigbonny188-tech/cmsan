<?php
/*--------------------------------------------------------------------------------------------------
    ContentIdentificationFactory.php 2019-12-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

class ContentIdentificationFactory implements ContentIdentificationFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createFromJson($identification): ?ContentIdentificationInterface
    {
        if (is_numeric($identification)) {
            return $this->forContentGroup((int)$identification);
        } elseif (isset($identification->contentAlias, $identification->contentGroup)) {
            return $this->forPreference($identification->contentAlias, $identification->contentGroup);
            
        } elseif (isset($identification->contentAlias)) {
            return $this->forContentAlias($identification->contentAlias);
        } elseif (isset($identification->contentGroup)) {
            return $this->forContentGroup($identification->contentGroup);
        }
        return null;
    }
    
    
    /**
     * @inheritDoc
     */
    public function forContentGroup(int $contentGroup): ContentIdentificationInterface
    {
        return new class($contentGroup, null) extends ContentIdentification {
        };
    }
    
    
    /**
     * @inheritDoc
     */
    public function forContentAlias(string $contentAlias): ContentIdentificationInterface
    {
        return new class(null, $contentAlias) extends ContentIdentification {
        };
    }
    
    
    /**
     * @inheritDoc
     */
    public function forPreference(string $contentAlias = null, int $contentGroup = null)
    {
        if (!empty($contentAlias) && !empty($contentGroup)) {
            return new class($contentGroup, $contentAlias) extends ContentIdentification {};
        } elseif (!empty($contentAlias)) {
            return $this->forContentAlias($contentAlias);
        } elseif (isset($contentGroup)) {
            return $this->forContentGroup($contentGroup);
        }
    }
}