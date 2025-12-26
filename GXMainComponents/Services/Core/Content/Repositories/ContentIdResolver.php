<?php
/*--------------------------------------------------------------------------------------------------
    ContentIdResolver.php 2023-03-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class ContentIdResolver
 */
class ContentIdResolver implements ContentIdResolverInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * ContentIdResolver constructor.
     *
     * @param CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * @param ContentIdentificationInterface $identification
     *
     * @return int
     */
    public function getGroupByIdentifier(ContentIdentificationInterface $identification): int
    {
        /**
         * While searching fot the contentGroup whe should always at first go to the database.
         * If it's not found at the database them we go to the object
         * */
        
        $records = $this->queryBuilder->select('content_group')
            ->where('content_alias', $identification->contentAlias())
            ->get('content_manager_aliases')
            ->result_array();
        if (count($records)) {
            return (int)$records[0]['content_group'];
        } elseif (!empty($identification->contentGroup())) {
            return $identification->contentGroup();
        } else {
            throw new InvalidArgumentException('Invalid alias ' . $identification->contentAlias());
        }
    }
    
    
    /**
     * @param ContentIdentificationInterface $identification
     *
     * @return string
     */
    public function getAliasByIdentifier(ContentIdentificationInterface $identification): string
    {
        if (!empty($identification->contentAlias())) {
            return $identification->contentAlias();
        } else {
            $records = $this->queryBuilder->select('content_alias')
                ->where('content_group', $identification->contentAlias())
                ->get('content_manager_aliases')
                ->result_array();
            if (count($records)) {
                return $records[0]['content_alias'];
            } else {
                throw new InvalidArgumentException('No alias found for group ' . $identification->contentGroup());
            }
        }
    }
    
}