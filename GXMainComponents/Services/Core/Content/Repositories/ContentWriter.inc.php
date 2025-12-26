<?php
/* --------------------------------------------------------------
   ContentWriter.inc.php 2021-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentWriter
 *
 * This class represents the content writer
 *
 * @category   System
 * @package    Content
 */
class ContentWriter implements ContentWriterInterface
{
    /**
     * @var ContentIdResolverInterface
     */
    protected $contentIdResolver;
    /**
     * Data
     *
     * @var array
     */
    protected $data = [];
    /**
     * Language map
     *
     * @var array
     */
    protected $languageMap;
    /**
     * CodeIgniter QueryBuilder
     *
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * ContentManagerWriter constructor
     *
     * @param CI_DB_query_builder        $queryBuilder CodeIgniter QueryBuilder
     * @param ContentIdResolverInterface $contentIdResolver
     */
    public function __construct(
        CI_DB_query_builder $queryBuilder,
        ContentIdResolverInterface $contentIdResolver
    ) {
        $this->queryBuilder      = $queryBuilder;
        $this->contentIdResolver = $contentIdResolver;
    }
    
    
    /**
     * Store an info element content into the database
     *
     * @param InfoElementContent $infoElementContent Info element content
     *
     * @return string New content group ID
     *
     * @throws Exception
     */
    public function storeInfoElementContent(InfoElementContent $infoElementContent): string
    {
        $this->mapLanguages();
        
        if ($infoElementContent->id() === null) {
            $contentGroupId = $this->nextId();
        } else {
            try {
                $contentGroupId = $this->contentIdResolver->getGroupByIdentifier($infoElementContent->id());
                if ($this->contentGroupIdExists($contentGroupId)) {
                    return $contentGroupId;
                }
            } catch (InvalidArgumentException $exception) {
                $contentGroupId = $this->nextId();
            }
        }
        
        
        
        foreach ($this->languageMap as $languageCode => $languageCodeId) {
            $languageCodeObject = new LanguageCode(new StringType($languageCode));
            $this->data         = [];
            
            $this->data['content_delete'] = '1';
            
            $this->data['content_heading']  = $this->getTranslation($infoElementContent->headings(),
                                                                    $languageCodeObject);
            $this->data['content_title']    = $this->getTranslation($infoElementContent->titles(), $languageCodeObject);
            $this->data['content_text']     = $this->getTranslation($infoElementContent->texts(), $languageCodeObject);
            $this->data['content_status']   = $this->getTranslation($infoElementContent->status(), $languageCodeObject);
            $this->data['content_position'] = ElementPositionMapper::getElementPositionForDatabase($infoElementContent->position());
            $this->data['content_group']    = $contentGroupId;
            $this->data['content_type']     = 'content';
            $this->data['languages_id']     = $this->languageMap[$languageCode];
            $this->data['group_ids']        = $this->getGroupIdsString();
            
            $this->queryBuilder->insert('content_manager', $this->data);
        }
        $this->persistAlias($contentGroupId, $infoElementContent->id());
        
        return $contentGroupId;
    }
    
    
    /**
     * Map the languages
     */
    protected function mapLanguages(): void
    {
        if ($this->languageMap === null) {
            $result = $this->queryBuilder->select('languages_id, code')->from('languages')->get()->result_array();
            
            foreach ($result as $language) {
                $this->languageMap[$language['code']] = (int)$language['languages_id'];
            }
        }
    }
    
    
    /**
     * Return the highest content group ID and add one
     *
     * @return string
     */
    protected function nextId(): string
    {
        $result         = $this->queryBuilder->select('MAX(content_group)')
            ->from('content_manager')
            ->get()
            ->result_array();
        $lastResult     = array_pop($result);
        $contentGroupId = (int)array_pop($lastResult);
        $nextId         = $contentGroupId + 1;
        
        return (string)$nextId;
    }
    
    
    /**
     * Checks if an content with given group id already exists.
     *
     * @param int $id
     *
     * @return bool
     */
    protected function contentGroupIdExists($id)
    {
        $result = $this->queryBuilder->select('count(*) AS cnt')
            ->from('content_manager')
            ->where('content_group', $id)
            ->get()
            ->result_array();
        
        return $result[0]['cnt'] > 0;
    }
    
    
    /**
     * @param LocalizedContentAttributeCollectionInterface $collection
     * @param LanguageCode                                 $languageCodeObject
     *
     * @return mixed|null
     */
    protected function getTranslation(
        LocalizedContentAttributeCollectionInterface $collection = null,
        LanguageCode $languageCodeObject = null
    ) {
        if ($collection !== null) {
            if ($collection->hasLanguageCode($languageCodeObject)) {
                return $collection->itemByLanguageCode($languageCodeObject)->content();
            } elseif ($collection->hasLanguageCode($english = new LanguageCode(new StringType('en')))) {
                return $collection->itemByLanguageCode($english)->content();
            } else {
                
                return $collection->count() !== 0 && $collection->getLastItem() ? $collection->getLastItem()->content() : '';
            }
        }
        
        return '';
    }
    
    
    /**
     * Returns a string used for customer group permissions of contents (i.e. c_0_group,c_1_group,c_2_group,c_3_group,)
     *
     * @return string
     */
    protected function getGroupIdsString()
    {
        static $groupIdsString;
        
        if ($groupIdsString !== null) {
            return $groupIdsString;
        }
        
        $groupIdsString = '';
        
        $result = $this->queryBuilder->select('customers_status_id')
            ->from('customers_status')
            ->order_by('customers_status_id')
            ->group_by('customers_status_id')
            ->get()
            ->result_array();
        
        foreach ($result as $row) {
            $groupIdsString .= 'c_' . $row['customers_status_id'] . '_group,';
        }
        
        return $groupIdsString;
    }
    
    
    /**
     * Store an info page content into the database
     *
     * @param InfoPageContent $infoPageContent Info page content
     *
     * @return string New content group ID
     *
     * @throws Exception
     */
    public function storeInfoPageContent(InfoPageContent $infoPageContent): string
    {
        $this->mapLanguages();
        
        if ($infoPageContent->id() === null) {
            $contentGroupId = $this->nextId();
        } else {
            try {
                $contentGroupId = $this->contentIdResolver->getGroupByIdentifier($infoPageContent->id());
            } catch (InvalidArgumentException $exception) {
                $contentGroupId = $this->nextId();
            }
        }
        
        if ($this->contentGroupIdExists($contentGroupId)) {
            return $contentGroupId;
        }
        
        foreach ($this->languageMap as $languageCode => $languageCodeId) {
            $languageCodeObject = new LanguageCode(new StringType($languageCode));
            $this->data         = [];
            
            //required
            $this->data['content_delete']   = '1';
            $this->data['content_type']     = 'content';
            $this->data['languages_id']     = $this->languageMap[$languageCode];
            $this->data['content_position'] = PagePositionMapper::getPagePositionForDatabase($infoPageContent->position())['position'];
            $this->data['file_flag']        = PagePositionMapper::getPagePositionForDatabase($infoPageContent->position())['fileFlag'];
            $this->data['content_status']   = $this->getTranslation($infoPageContent->status(), $languageCodeObject);
            $this->data['content_title']    = $this->getTranslation($infoPageContent->titles(), $languageCodeObject);
            $this->data['content_heading']  = $this->getTranslation($infoPageContent->headings(), $languageCodeObject);
            $this->data['content_text']     = $this->getTranslation($infoPageContent->texts(), $languageCodeObject);
            $this->data['content_name']     = $this->getTranslation($infoPageContent->names(), $languageCodeObject);
            
            //not required
            $this->data['content_group']             = $contentGroupId;
            $this->data['contents_meta_title']       = $this->getTranslation($infoPageContent->metaTitles(),
                                                                             $languageCodeObject);
            $this->data['contents_meta_description'] = $this->getTranslation($infoPageContent->metaDescriptions(),
                                                                             $languageCodeObject);
            $this->data['contents_meta_keywords']    = $this->getTranslation($infoPageContent->metaKeywords(),
                                                                             $languageCodeObject);
            $this->data['download_file']             = $this->getTranslation($infoPageContent->downloads(),
                                                                             $languageCodeObject);
            $this->data['opengraph_image']           = $this->getTranslation($infoPageContent->opengraphImages(),
                                                                             $languageCodeObject);
            $this->data['gm_url_keywords']           = $this->getTranslation($infoPageContent->urlKeywords(),
                                                                             $languageCodeObject);
            
            if ($infoPageContent->sitemaps() != null) {
                /* @var ContentSitemap $sitemap */
                foreach ($infoPageContent->sitemaps() as $sitemap) {
                    if(strtolower($sitemap->languageCode()) !== $languageCode) {
                        continue;
                    }
    
                    $this->data['gm_changefreq']    = $sitemap->frequencyOfChange();
                    $this->data['gm_priority']      = $sitemap->priority();
                    $this->data['gm_sitemap_entry'] = (int)$sitemap->isVisible();
                    break;
                }
                
                
            }
    
            $this->data['group_ids']       = $this->getGroupIdsString();
            $this->data['sort_order']      = $infoPageContent->order();
    
            if ($infoPageContent->allowRobotsStatuses() != null) {
                /* @var ContentAllowRobotsStatus $status */
                foreach ($infoPageContent->allowRobotsStatuses() as $status) {
                    if(strtolower($status->languageCode()) !== $languageCode) {
                        continue;
                    }
    
                    $this->data['gm_robots_entry'] = (int)$status->isAllowed();
                    break;
                }
            }
            
            $this->queryBuilder->insert('content_manager', $this->data);
            
            $urlRewrite = $this->getTranslation($infoPageContent->urlRewrites(), $languageCodeObject);
    
            if (!$urlRewrite) {
                continue;
            }
            
            $this->data = [];
            
            $this->data['content_id']   = $contentGroupId;
            $this->data['content_type'] = 'content';
            $this->data['language_id']  = $this->languageMap[$languageCode];
            $this->data['rewrite_url']  = $urlRewrite;
            $this->data['target_url']   = "shop_content.php?coID={$contentGroupId}&language=" . strtolower($languageCodeObject->asString());
            
            $this->queryBuilder->insert('url_rewrites', $this->data);
        }
        $this->persistAlias($contentGroupId, $infoPageContent->id());
        
        return $contentGroupId;
    }
    
    
    /**
     * Store a link page content into the database
     *
     * @param LinkPageContent $linkPageContent Link page content
     *
     * @return string New content group ID
     *
     * @throws Exception
     */
    public function storeLinkPageContent(LinkPageContent $linkPageContent): string
    {
        $this->mapLanguages();
        
        if ($linkPageContent->id() === null) {
            $contentGroupId = $this->nextId();
        } else {
            try {
                $contentGroupId = $this->contentIdResolver->getGroupByIdentifier($linkPageContent->id());
            } catch (InvalidArgumentException $exception) {
                $contentGroupId = $this->nextId();
            }
        }
        
        if ($this->contentGroupIdExists($contentGroupId)) {
            return $contentGroupId;
        }
        
        foreach ($this->languageMap as $languageCode => $languageCodeId) {
            $languageCodeObject = new LanguageCode(new StringType($languageCode));
            $this->data         = [];
            
            $this->data['content_delete']   = '1';
            $this->data['content_type']     = 'link';
            $this->data['content_status']   = $this->getTranslation($linkPageContent->status(), $languageCodeObject);
            $this->data['content_group']    = $contentGroupId;
            $this->data['languages_id']     = $this->languageMap[$languageCode];
            $this->data['content_position'] = PagePositionMapper::getPagePositionForDatabase($linkPageContent->position())['position'];
            $this->data['file_flag']        = PagePositionMapper::getPagePositionForDatabase($linkPageContent->position())['fileFlag'];
            $this->data['content_title']    = $this->getTranslation($linkPageContent->titles(), $languageCodeObject);
            $this->data['content_name']     = $this->getTranslation($linkPageContent->names(), $languageCodeObject);
            $this->data['gm_link']          = $this->getTranslation($linkPageContent->links(), $languageCodeObject);
            $this->data['gm_sitemap_entry'] = '0';
            $this->data['file_flag']        = '3';
            $this->data['group_ids']        = $this->getGroupIdsString();
    
            if ($linkPageContent->openInNewTab() != null) {
                /* @var ContentOpenInNewTabStatus $status */
                foreach ($linkPageContent->openInNewTab() as $status) {
                    if(strtolower($status->languageCode()) !== $languageCode) {
                        continue;
                    }
            
                    $this->data['gm_link_target'] = $status->opensInNewTab() ? '_blank' : '_top';
                    break;
                }
            }
            
            $this->queryBuilder->insert('content_manager', $this->data);
        }
        $this->persistAlias($contentGroupId, $linkPageContent->id());
        
        return $contentGroupId;
    }
    
    
    /**
     * Updates an info page content.
     *
     * @param InfoPageContent $infoPage
     *
     * @throws Exception
     * @throws Exception
     */
    public function updateInfoPageContent(InfoPageContent $infoPage): void
    {
        $this->mapLanguages();
        $contentGroupId = $this->contentIdResolver->getGroupByIdentifier($infoPage->id());
        
        foreach ($this->languageMap as $languageCode => $languageCodeId) {
            
            $languageCodeObject = new LanguageCode(new StringType($languageCode));
            $this->data         = [];
            
            $this->data['languages_id']     = $this->languageMap[$languageCode];
            $this->data['content_position'] = PagePositionMapper::getPagePositionForDatabase($infoPage->position())['position'];
            $this->data['file_flag']        = PagePositionMapper::getPagePositionForDatabase($infoPage->position())['fileFlag'];
            $this->data['content_status']   = $this->getTranslation($infoPage->status(), $languageCodeObject);
            $this->data['content_title']    = $this->getTranslation($infoPage->titles(), $languageCodeObject);
            $this->data['content_heading']  = $this->getTranslation($infoPage->headings(), $languageCodeObject);
            $this->data['content_text']     = $this->getTranslation($infoPage->texts(), $languageCodeObject);
            $this->data['content_name']     = $this->getTranslation($infoPage->names(), $languageCodeObject);
            
            $this->data['contents_meta_title']       = $this->getTranslation($infoPage->metaTitles(),
                                                                             $languageCodeObject);
            $this->data['contents_meta_description'] = $this->getTranslation($infoPage->metaDescriptions(),
                                                                             $languageCodeObject);
            $this->data['contents_meta_keywords']    = $this->getTranslation($infoPage->metaKeywords(),
                                                                             $languageCodeObject);
            $this->data['download_file']             = $this->getTranslation($infoPage->downloads(),
                                                                             $languageCodeObject);
            $this->data['opengraph_image']           = $this->getTranslation($infoPage->opengraphImages(),
                                                                             $languageCodeObject);
            $this->data['gm_url_keywords']           = $this->getTranslation($infoPage->urlKeywords(),
                                                                             $languageCodeObject);
            $this->data['sort_order']                = $infoPage->order();
    
            if ($infoPage->allowRobotsStatuses() != null) {
                /* @var ContentAllowRobotsStatus $status */
                foreach ($infoPage->allowRobotsStatuses() as $status) {
                    if(strtolower($status->languageCode()) !== $languageCode) {
                        continue;
                    }
            
                    $this->data['gm_robots_entry'] = (int)$status->isAllowed();
                    break;
                }
            }
            
            if ($infoPage->sitemaps() != null) {
                /* @var ContentSitemap $sitemap */
                foreach ($infoPage->sitemaps() as $sitemap) {
                    if (strtolower($sitemap->languageCode()) !== $languageCode) {
                        continue;
                    }
    
                    $this->data['gm_changefreq']    = $sitemap->frequencyOfChange();
                    $this->data['gm_priority']      = $sitemap->priority();
                    $this->data['gm_sitemap_entry'] = (int)$sitemap->isVisible();
                    break;
                }
            }
            
            $where = ['content_group' => $contentGroupId, 'languages_id' => $this->languageMap[$languageCode]];

            $content_id = $this->queryBuilder->select('content_id')
                ->from('content_manager')
                ->where($where)
                ->get()
                ->result_array();


            if(count($content_id)) {
                $this->queryBuilder->update('content_manager', $this->data, $where);
                $urlRewrite = $this->getTranslation($infoPage->urlRewrites(), $languageCodeObject);
    
                if (!$urlRewrite) {
                    continue;
                }
                
                $this->data = [];
                //$this->data['language_id'] = $this->languageMap[$languageCode];
                //$this->data['target_url']  = "shop_content.php?coID={$contentGroupId}&language=" . strtolower($languageCodeObject->asString());
                $this->data['rewrite_url'] = $urlRewrite;

                $urlRewriteEntry = $this->queryBuilder->select('*')
                    ->from('url_rewrites')
                    ->where(['content_id' => $contentGroupId, 'language_id' => $this->languageMap[$languageCode]])
                    ->get()
                    ->result_array();
                //$this->queryBuilder->update('url_rewrites', $this->data, ['content_id' => $content_id[0]['content_id']]);
                if ($urlRewriteEntry) {
                    $this->queryBuilder->update(
                        'url_rewrites',
                        $this->data,
                        ['content_id' => $contentGroupId, 'language_id' => $this->languageMap[$languageCode]]
                    );
                } else {
                    $this->queryBuilder->insert('url_rewrites', [
                        'content_id' => $contentGroupId,
                        'content_type' => 'content',
                        'language_id' => $this->languageMap[$languageCode],
                        'rewrite_url' => $urlRewrite,
                        'target_url' => "shop_content.php?coID={$contentGroupId}&language=" . strtolower($languageCodeObject->asString())
                    ]);
                }
            } else {
                $this->data['languages_id'] = $this->languageMap[$languageCode];
                $this->data['content_group'] = $contentGroupId;
                $this->data['content_type'] = 'content';
                $this->data['content_delete']   = '1';

                $this->queryBuilder->insert('content_manager', $this->data);

                $urlRewrite = $this->getTranslation($infoPage->urlRewrites(), $languageCodeObject);
    
                if (!$urlRewrite) {
                    continue;
                }
                
                $this->data                 = [];
                $this->data['content_id']   = $contentGroupId;
                $this->data['content_type'] = 'content';
                $this->data['language_id']  = $this->languageMap[$languageCode];
                $this->data['rewrite_url']  = $urlRewrite;
                $this->data['target_url']   = "shop_content.php?coID={$contentGroupId}&language=" . strtolower($languageCodeObject->asString());

                $this->queryBuilder->insert('url_rewrites', $this->data);
            }

        }
        $this->persistAlias($contentGroupId, $infoPage->id());
    }
    
    
    /**
     * Updates a link page content.
     *
     * @param LinkPageContent $linkPage
     *
     * @throws Exception
     * @throws Exception
     */
    public function updateLinkPageContent(LinkPageContent $linkPage): void
    {
        
        $contentGroupId = $this->contentIdResolver->getGroupByIdentifier($linkPage->id());
        
        $this->mapLanguages();
        
        foreach ($this->languageMap as $languageCode => $languageCodeId) {
            
            $languageCodeObject = new LanguageCode(new StringType($languageCode));
            $this->data         = [];
            
            $this->data['content_status']   = $this->getTranslation($linkPage->status(), $languageCodeObject);
            $this->data['languages_id']     = $this->languageMap[$languageCode];
            $this->data['content_position'] = PagePositionMapper::getPagePositionForDatabase($linkPage->position())['position'];
            $this->data['file_flag']        = PagePositionMapper::getPagePositionForDatabase($linkPage->position())['fileFlag'];
            $this->data['content_title']    = $this->getTranslation($linkPage->titles(), $languageCodeObject);
            $this->data['content_name']     = $this->getTranslation($linkPage->names(), $languageCodeObject);
            $this->data['gm_link']          = $this->getTranslation($linkPage->links(), $languageCodeObject);
            $this->data['gm_sitemap_entry'] = '0';
            $this->data['file_flag']        = '3';
            $this->data['content_type']     = 'link';
    
            if ($linkPage->openInNewTab() != null) {
                /* @var ContentOpenInNewTabStatus $status */
                foreach ($linkPage->openInNewTab() as $status) {
                    if(strtolower($status->languageCode()) !== $languageCode) {
                        continue;
                    }
            
                    $this->data['gm_link_target'] = $status->opensInNewTab() ? '_blank' : '_top';
                    break;
                }
            }
            
            $where = ['content_group' => $contentGroupId, 'languages_id' => $this->languageMap[$languageCode]];
            $this->queryBuilder->update('content_manager', $this->data, $where);
        }
        $this->persistAlias($contentGroupId, $linkPage->id());
    }
    
    
    /**
     * Updates an info element content
     *
     * @param InfoElementContent $infoElement
     *
     * @throws Exception
     * @throws Exception
     */
    public function updateInfoElementContent(InfoElementContent $infoElement): void
    {
        $this->mapLanguages();
        $contentGroupId = $this->contentIdResolver->getGroupByIdentifier($infoElement->id());
        
        foreach ($this->languageMap as $languageCode => $languageCodeId) {
            
            $languageCodeObject = new LanguageCode(new StringType($languageCode));
            $this->data         = [];
            
            $this->data['content_heading']  = $this->getTranslation($infoElement->headings(), $languageCodeObject);
            $this->data['content_title']    = $this->getTranslation($infoElement->titles(), $languageCodeObject);
            $this->data['content_text']     = $this->getTranslation($infoElement->texts(), $languageCodeObject);
            $this->data['content_status']   = $this->getTranslation($infoElement->status(), $languageCodeObject);
            $this->data['content_position'] = ElementPositionMapper::getElementPositionForDatabase($infoElement->position());
            $this->data['content_type']     = 'content';
            $this->data['languages_id']     = $this->languageMap[$languageCode];
            
            $where = ['content_group' => $contentGroupId, 'languages_id' => $this->languageMap[$languageCode]];
            $this->queryBuilder->update('content_manager', $this->data, $where);
        }
        $this->persistAlias($contentGroupId, $infoElement->id());
    }
    
    
    /**
     * @param int                   $contentGroup
     * @param ContentIdentification $id
     */
    public function persistAlias(int $contentGroup, ?ContentIdentification $id): void
    {
        if($id === null){
            $alias = $contentGroup;
        } else {
        try {
                $alias = $this->contentIdResolver->getAliasByIdentifier($id);
            } catch (InvalidArgumentException $exception) {
                $alias = $contentGroup;
            }
        }

        $data = [
            'content_group' => $contentGroup,
            'content_alias' => $alias
        ];
        $this->queryBuilder->replace('content_manager_aliases', $data);
    }
}
