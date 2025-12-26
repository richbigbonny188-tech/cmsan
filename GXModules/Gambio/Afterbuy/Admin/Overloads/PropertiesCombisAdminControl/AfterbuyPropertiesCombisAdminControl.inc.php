<?php
/* --------------------------------------------------------------
   AfterbuyPropertiesCombisAdminControl.inc.php 2023-11-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

/**
 * Class AfterbuyPropertiesCombisAdminControl
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Overloads\PropertiesCombisAdminControl
 */
class AfterbuyPropertiesCombisAdminControl extends AfterbuyPropertiesCombisAdminControl_parent
{
    function delete_combis($p_properties_combis_id_array)
    {
        parent::delete_combis($p_properties_combis_id_array);
        
        // Delete afterbuy mapping
        $t_sql = '
            DELETE
            FROM
                afterbuy_products
            WHERE
                combi_id IN (' . implode(',', $p_properties_combis_id_array) . ')
        ';
        xtc_db_query($t_sql);
    }
}