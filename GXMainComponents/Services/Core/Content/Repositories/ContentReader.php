<?php
/* --------------------------------------------------------------
  ContentReader.php 2019-07-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class ContentReader
 */
class ContentReader implements ContentReaderInterface
{
    /**
     * CodeIgniter QueryBuilder
     *
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    /**
     * @var ContentIdResolverInterface
     */
    protected $contentIdResolver;
    /**
     * @var ContentIdentificationFactoryInterface
     */
    protected $contentIdentificationFactory;
    
    
    /**
     * ContentReader constructor.
     *
     * @param CI_DB_query_builder                   $queryBuilder
     * @param ContentIdentificationFactoryInterface $contentIdentificationFactory
     * @param ContentIdResolverInterface            $contentIdResolver
     */
    public function __construct(
        CI_DB_query_builder $queryBuilder,
        ContentIdentificationFactoryInterface $contentIdentificationFactory,
        ContentIdResolverInterface $contentIdResolver
    ) {
        $this->queryBuilder                 = $queryBuilder;
        $this->contentIdentificationFactory = $contentIdentificationFactory;
        $this->contentIdResolver            = $contentIdResolver;
    }
    
    
    /**
     * @param mixed $id id of the Content
     *
     * @return array
     * @throws ContentNotFoundException
     */
    public function findById(ContentIdentificationInterface $id): array
    {
        $contentGroup = $this->contentIdResolver->getGroupByIdentifier($id);

        $result       = $this->queryBuilder->select('content_manager.*, content_manager_aliases.content_alias')
            ->from('content_manager')
            ->join('content_manager_aliases',
                   'content_manager_aliases.content_group = content_manager.content_group ',
                   'LEFT')
            ->where('content_manager.content_group', $contentGroup)
            ->get()
            ->result_array();
        
        if (count($result) === 0) {
            throw new ContentNotFoundException($id);
        }
        
        return $result;
    }
    
    
    
    /**
     * @param $id
     *
     * @return array
     * @throws UrlRewriteNotFoundException
     */
    public function findUrlRewriteByContentId($id): array
    {
        $result = $this->queryBuilder->select('*')
            ->from('url_rewrites')
            ->where('content_id', $id)
            ->get()
            ->result_array();
        
        if (count($result) === 0) {
            
            throw new UrlRewriteNotFoundException($id);
        }
        
        return $result;
    }
    
    
    /**
     * @return ContentIdentificationInterface
     */
    public function nextContentGroupId(): ContentIdentificationInterface
    {
        $result = $this->queryBuilder->select('MAX(content_group)')->from('content_manager')->get()->result_array();
        $result = (int)$result[0]['MAX(content_group)'];
        
        return $this->contentIdentificationFactory->forContentGroup(++$result);
    }
    
    
    /**
     * @return array
     * @throws ContentNotFoundException
     */
    public function findAllInfoElements(): array
    {
        $result = $this->queryBuilder->select('content_manager.*, content_manager_aliases.content_alias')
            ->from('content_manager')
            ->join('content_manager_aliases',
                   'content_manager_aliases.content_group = content_manager.content_group ',
                   'LEFT')
            ->like('content_position', 'elements', 'after')
            ->order_by('content_group')
            ->get()
            ->result_array();
        
        if (count($result) === 0) {
            
            throw new ContentNotFoundException('*');
        }
        
        return $result;
    }
    
    
    public function findAllContentPages(): array
    {
        $result = $this->queryBuilder->select('content_manager.*, content_manager_aliases.content_alias')
            ->from('content_manager')
            ->join('content_manager_aliases',
                   'content_manager_aliases.content_group = content_manager.content_group ',
                   'LEFT')
            ->like('content_position', 'pages', 'after')
            ->order_by('sort_order')
            ->get()
            ->result_array();
    
        if (count($result) === 0) {
        
            throw new ContentNotFoundException('*');
        }
    
        return $result;
    }
}
