<?php
/* --------------------------------------------------------------
   ContentDeleter.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentDeleter
 */
class ContentDeleter implements ContentDeleterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    /**
     * @var ContentIdResolverInterface
     */
    protected $idResolver;
    
    
    /**
     * ContentDeleter constructor.
     *
     * @param CI_DB_query_builder        $query_builder
     * @param ContentIdResolverInterface $idResolver
     */
    public function __construct(CI_DB_query_builder $query_builder, ContentIdResolverInterface $idResolver)
    {
        $this->queryBuilder = $query_builder;
        $this->idResolver   = $idResolver;
    }
    
    /**
     * Deletes the content data in database by id.
     *
     * @param ContentIdentificationInterface $Id
     *
     * @return $this|ContentDeleterInterface Same instance for chained method calls.
     */
    public function deleteById(ContentIdentificationInterface $Id)
    {
        $contentGroupId = $this->idResolver->getGroupByIdentifier($Id);
        
        $this->queryBuilder->query('DELETE FROM url_rewrites
                                        WHERE content_id IN (
                                            SELECT content_id
                                            FROM content_manager
                                            WHERE content_group ='. $contentGroupId . '
                                        )'
        );
        
        $this->queryBuilder->query('DELETE FROM content_manager_aliases WHERE content_group = ' . $contentGroupId);
        $this->queryBuilder->query('DELETE FROM content_manager WHERE content_group = ' . $contentGroupId);
        
        return $this;
    }
    
    
    
}