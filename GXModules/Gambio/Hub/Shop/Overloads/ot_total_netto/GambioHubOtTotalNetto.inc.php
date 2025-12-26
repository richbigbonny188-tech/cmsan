<?php
/* --------------------------------------------------------------
   GambioHubOtTotalNetto.inc.php 2023-04-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubOtTotalNetto extends GambioHubOtTotalNetto_parent
{
    public function process()
    {
        $order        = $GLOBALS['order'];
        $xtPrice      = $GLOBALS['xtPrice'];
        $showPriceTax = (bool)$_SESSION['customers_status']['customers_status_show_price_tax'];
        
        if($showPriceTax === true && is_array($order->info['tax_groups'] ?? null))
        {
            $taxGroupsSum = 0;
            foreach($order->info['tax_groups'] as $taxGroupName => $taxGroupValue)
            {
                $taxGroupsSum += $taxGroupValue;
            }
            
            $netto     = $xtPrice->xtcFormat($order->info['total'], false) - $xtPrice->xtcFormat($taxGroupsSum, false);
            $netto     = max(0, $netto);
            
            $this->output[] = array(
                'title' => $this->title . ':',
                'text'  => $xtPrice->xtcFormat($netto, true),
                'value' => $netto,
            );
        }
    }
}
