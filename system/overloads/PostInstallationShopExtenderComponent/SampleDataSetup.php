<?php
/*--------------------------------------------------------------------------------------------------
    SampleDataSetup.php 2021-04-09
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

class SampleDataSetup extends SampleDataSetup_parent
{
    public function proceed()
    {
        parent::proceed();
    
        if (file_exists(__DIR__ . '/../../../version_info/cloud.php') === true) {
            try {
                $this->sampleDataService()->install();
            } catch (Throwable $exception) {
                // do nothing
            }
        }
    }
    
    
    /**
     * @return \Gambio\Shop\SampleData\SampleDataService
     */
    protected function sampleDataService(): \Gambio\Shop\SampleData\SampleDataService
    {
        return \LegacyDependencyContainer::getInstance()->get(\Gambio\Shop\SampleData\SampleDataService::class);
    }
}
