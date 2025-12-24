<?php
/* --------------------------------------------------------------
   DsgvoLawsController.inc.php 2018-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2018 Gambio GmbH
   --------------------------------------------------------------
*/

class DsgvoLawsController extends DsgvoLawsController_parent
{
    /**
     * Generates a CSV file with legal text agreements of all users, saves it to the export directory and provides the
     * file for download.
     */
    public function actionExportAgreements()
    {
        $queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $agreementServiceFactory = MainFactory::create(AgreementServiceFactory::class, $queryBuilder);
        $agreementReadService = $agreementServiceFactory->createReadService();
        
        $agreementsCollection = $agreementReadService->getAll();
        
        $agreements = [];
        
        $agreements[] = [
            'Id',
            'Customer Name',
            'Customer E-Mail',
            'IP-Address',
            'Text',
            'Legal Text Version',
            'Date Added',
            'Last Modified'
        ];
        
        /**
         * @var \Agreement $agreement
         */
        foreach ($agreementsCollection->getArray() as $agreement)
        {
            $agreements[] = [
                $agreement->getId(),
                $agreement->getCustomer()->getCustomerName(),
                $agreement->getCustomer()->getCustomerEmail(),
                $agreement->getIpAddress()->asString(),
                $agreement->getText()->asString(),
                $agreement->getLegalTextVersion()->asString(),
                $agreement->getDateAdded()->format('Y-m-d H:i:s'),
                $agreement->getLastModifiedDateTime()->format('Y-m-d H:i:s'),
            ];
        }
        
        $exportPath = DIR_FS_CATALOG . 'export/agreements.csv';
        $exportFile = fopen($exportPath, 'wb');
        
        ftruncate($exportFile, 0);
        
        foreach ($agreements as $fields) {
            fputcsv($exportFile, $fields, '|');
        }
        
        fclose($exportFile);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($exportPath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($exportPath));
        readfile($exportPath);
        
        exit;
    }
}