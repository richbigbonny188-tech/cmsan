<?php
/*--------------------------------------------------------------------
 gm_get_meta.inc.php 2023-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

use Doctrine\DBAL\Connection;

/**
 * @param $languages_id
 *
 * @return array
 * @throws \Doctrine\DBAL\Exception
 * @deprecated
 */
function gm_get_meta($languages_id)
{
    /** @var Connection $connection */
    $container  = LegacyDependencyContainer::getInstance();
    $connection = $container->get(Connection::class);
    $mapSelect  = static function (array $data) use ($connection) {
        $quotedData = [];
        foreach ($data as $key => $value) {
            $quotedData[] = "{$connection->quoteIdentifier($key)} as {$connection->quoteIdentifier($value)}";
        }
        
        return implode(', ', $quotedData);
    };
    $metaKeys   = "'gm_configuration/robots', 'gm_configuration/keywords'";
    $selects    = [
        'id'    => 'gm_contents_id',
        'value' => 'gm_value',
        'key'   => 'gm_key',
    ];
    $qb         = $connection->createQueryBuilder();
    $qb->select($mapSelect($selects))->from('gx_lang_configurations')->where("`key` IN ($metaKeys)")->orWhere(
        "`key` LIKE 'gm_configuration/meta/%'"
    )->andWhere("language_id = {$qb->createNamedParameter($languages_id)}");
    
    return $qb->executeQuery()->fetchAllAssociative();
}

?>