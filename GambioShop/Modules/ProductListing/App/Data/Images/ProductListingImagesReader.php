<?php
/* --------------------------------------------------------------
  ProductListingImagesReader.php 2023-06-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Images;

use Doctrine\DBAL\Connection;

/**
 * Class ProductListingImagesReader
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Images
 */
class ProductListingImagesReader
{
    private Connection $connection;
    
    
    /**
     * ProductListingImagesReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Fetches main image for a product by product ID and language ID.
     * Returns null if non-existing product ID or existing product ID without main image.
     *
     * @param int $productId
     * @param int $languageId
     *
     * @return array|null
     */
    public function fetchMainImage(int $productId, int $languageId): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        
        // prepare query params
        $tablesJoinConstraint = $qb->expr()->eq('p.products_id', 'pd.products_id');
        $pIdEq                = $qb->expr()->eq('p.products_id', $qb->createNamedParameter($productId));
        $languageIdEq         = $qb->expr()->eq('pd.language_id', $qb->createNamedParameter($languageId));
        $joinCondition        = $qb->expr()->and($tablesJoinConstraint, $pIdEq);
        $selects              = [
            'p.products_image AS image',
            'pd.gm_alt_text AS alt',
        ];
        
        $statement = $qb->from('products', 'p')
            ->leftJoin('p', 'products_description', 'pd', $joinCondition)
            ->where($languageIdEq)
            ->select($selects)
            ->executeQuery();
        
        $data = $statement->fetchAssociative();
        if (!$data || empty($data['image'])) {
            return null;
        }
        
        return $data;
    }
    
    
    /**
     * Fetches available additional images for a product by product ID and language ID.
     * Image availability is defined by 'gm_show_image'
     *
     * @param int $productId
     * @param int $languageId
     *
     * @return array
     */
    public function fetchAdditionalImages(int $productId, int $languageId): array
    {
        $qb = $this->connection->createQueryBuilder();
        
        // prepare query params
        $piaTableJoinConstraints[] = $qb->expr()->eq('pi.image_id', 'pia.image_id');
        $piaTableJoinConstraints[] = $qb->expr()->eq('pi.products_id', 'pia.products_id');
        $piaTableJoinConstraints[] = $qb->expr()->eq('pia.language_id', $qb->createNamedParameter($languageId));
        $pdJoinConstraints[]       = $qb->expr()->eq('pd.products_id', 'pi.products_id');
        $pdJoinConstraints[]       = $qb->expr()->eq('pd.language_id', $qb->createNamedParameter($languageId));
        $tablesWhereConstraints[]  = $qb->expr()->eq('pi.products_id', $qb->createNamedParameter($productId));
        $tablesWhereConstraints[]  = $qb->expr()->eq('pi.gm_show_image', 1);
        $tablesWhereConstraints[]  = $qb->expr()->eq('p.gm_show_image', 1);
        $piaJoinCondition          = $qb->expr()->and(...$piaTableJoinConstraints);
        $pdJoinCondition           = $qb->expr()->and(...$pdJoinConstraints);
        $pJoinCondition            = $qb->expr()->eq('pi.products_id', 'p.products_id');
        $tablesWhere               = $qb->expr()->and(...$tablesWhereConstraints);
        $selects                   = [
            'pi.image_name image',
            'pia.gm_alt_text as alt',
        ];
        
        $statement = $qb->from('products_images', 'pi')
            ->leftJoin('pi', 'gm_prd_img_alt', 'pia', $piaJoinCondition)
            ->leftJoin('pi', 'products_description', 'pd', $pdJoinCondition)
            ->rightJoin('pi', 'products', 'p', $pJoinCondition)
            ->where($tablesWhere)
            ->select($selects)
            ->orderBy('pi.image_nr')
            ->executeQuery();
        
        return $statement->fetchAllAssociative();
    }
}