<?php
/* --------------------------------------------------------------
   CashFlowTechHubOrderExtender.inc.php 2019-08-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CashFlowTechHubOrderExtender extends CashFlowTechHubOrderExtender_parent
{
    const DOWNLOAD_INTERVAL = 1200;

    protected $text;
    protected $templatesBaseDir;
    protected $hubAssetHelper;
    
    public function proceed()
    {
        parent::proceed();
        $isInstalled = (bool)gm_get_conf('MODULE_CENTER_CASHFLOWTECH_INSTALLED');
        if (!$isInstalled) {
            return;
        }
    
        $installedVersion          = gm_get_conf('INSTALLED_VERSION');
        $this->hubAssetHelper      = MainFactory::create('HubAssetHelper', $installedVersion);
        $this->templatesBaseDir = DIR_FS_CATALOG . $this->hubAssetHelper->getTemplatesBasePath();

        $ordersId = (int)$this->v_data_array['GET']['oID'];
        /** @var \OrderReadServiceInterface $orderRead */
        $orderRead = StaticGXCoreLoader::getService('OrderRead');
        /** @var \GXEngineOrder $order */
        $order = $orderRead->getOrderById(new IdType($ordersId));

        // CashFlowTech supports EUR only
        if ($order->getCurrencyCode()->getCode() !== 'EUR') {
            return;
        }

        /** @var \CashFlowTechHubClient $cftHubClient */
        $cftHubClient = MainFactory::create('CashFlowTechHubClient');
        $mandatorStatus = $cftHubClient->getMandatorStatusFromHub();
        
        $this->text = MainFactory::create('LanguageTextManager', 'cashflowtech');
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && $mandatorStatus['mandatorStatus'] === 'active') {
            $this->handleActions($cftHubClient, $ordersId);
        }
    
        $position = 'below_order_info';
        $heading = $this->text->get_text('orderextender_title');
    
        /** @var \ContentView $contentView */
        $contentView = MainFactory::create('ContentView');
        $contentView->set_template_dir($this->templatesBaseDir);
        if (!empty($_SESSION['cft_messages'])) {
            $contentView->set_content_data('cft_messages', $_SESSION['cft_messages']);
            unset($_SESSION['cft_messages']);
        }

        if ($mandatorStatus['mandatorStatus'] === 'newMandator') {
            $contentView->set_content_template('cashflowtech_newmandatorinfo.html');
            $contentView->set_content_data(
                'boarding_url',
                xtc_href_link('admin.php', 'do=CashFlowTechModuleCenterModule')
            );
        } elseif ($mandatorStatus['mandatorStatus'] === 'boardingInitiated') {
            $contentView->set_content_template('cashflowtech_boardinginprogressinfo.html');
        } elseif ($mandatorStatus['mandatorStatus'] === 'active') {
            /** @var \CustomerReadService $customerRead */
            $customerRead = StaticGXCoreLoader::getService('CustomerRead');
            try {
                /** @var Customer $customer */
                $customer = $customerRead->getCustomerById(new IdType($order->getCustomerId()));
            } catch (InvalidArgumentException $e) {
                // deleted guest account, must reconstruct Customer
                $customer = MainFactory::create('Customer');
            }
            $collections = $cftHubClient->getCollectionStatus($ordersId);
            if (empty($collections)) {
                $this->prepareCreateCollectionForm($contentView, $order, $customer);
            } else {
                $this->prepareCollectionStatusForm($contentView, $collections);
            }
        } elseif ($mandatorStatus['mandatorStatus'] === 'disabled') {
            $contentView->set_content_template('cashflowtech_disabled.html');
        } else {
            // not implemented
            throw new RuntimeException('Unsupported CFT mandator status: ' . strip_tags($mandatorStatus['mandatorStatus']));
        }
        $content = $contentView->get_html();
        
        $this->addContentToCollection($position, $content, $heading);
    }
    
    
    protected function prepareCollectionStatusForm($contentView, $collections)
    {
        $contentView->set_content_template('cashflowtech_collectionstatus.html');
        $contentView->set_content_data('collections', $collections);
        
    }
    
    /**
     * Prepares output of create collection form
     *
     * @param ContentView    $contentView
     * @param \GXEngineOrder $order
     * @param Customer       $customer
     *
     * @throws \Exception
     */
    protected function prepareCreateCollectionForm($contentView, $order, $customer)
    {
        $contentView->set_content_template('cashflowtech_createcollection.html');
        $contentView->set_content_data('orderId', $order->getOrderId());
        $company = (string)$order->getBillingAddress()->getCompany();
        $gender = (string)$order->getBillingAddress()->getGender();
        $orderLanguage = strtoupper($order->getLanguageCode()->asString());
        $salutationId = 0; // none
        if (!empty($company)) {
            $salutationId = 3;
        } else {
            $salutationIdMap = [
                'DE_f' => 1,
                'DE_m' => 2,
                'EN_f' => 5,
                'EN_m' => 4,
            ];
            $salutationMapKey = "{$orderLanguage}_{$gender}";
            $salutationId = array_key_exists($salutationMapKey, $salutationIdMap) ? $salutationIdMap[$salutationMapKey] : $salutationId;
        }
        $collectionData = [
            'reference_1'              => (string)$order->getOrderId(),
            'reference_2'              => '',
            'reference_3'              => '',
            // person_data
            'salutation_id'            => $salutationId,
            'title_id'                 => 0,
            'company'                  => (string)$order->getBillingAddress()->getCompany(),
            'company_type_id'          => 0,
            'first_name'               => (string)$order->getBillingAddress()->getFirstname(),
            'last_name'                => (string)$order->getBillingAddress()->getLastname(),
            'maiden_name'              => '',
            'street'                   => (string)$order->getBillingAddress()->getStreet(),
            'housenumber'              => (string)$order->getBillingAddress()->getHouseNumber(),
            'postalcode'               => (string)$order->getBillingAddress()->getPostcode(),
            'city'                     => (string)$order->getBillingAddress()->getCity(),
            'country'                  => (string)$order->getBillingAddress()->getCountry()->getIso2(),
            'date_of_birth'            => $customer->getDateOfBirth()->format('Y-m-d'),
            'email'                    => (string)$order->getCustomerEmail(),
            'phone'                    => (string)$order->getCustomerTelephone(),
            'fax'                      => '',
            'account_number'           => '',
            'bank_number'              => '',
            'account_iban'             => '',
            'bank_bic'                 => '',
            'account_owner_first_name' => '',
            'account_owner_last_name'  => '',
            // booking_data
            'ext_id'                   => $order->getOrderId(),
            'invoice_nr'               => '',
            'booking_date'             => '',
            'due_date'                 => '',
            'order_date'               => '',
            'amount'                   => '',
            'description'              => '',
        ];
        $invoices = $this->getInvoicesForOrder($order->getOrderId());
        $contentView->set_content_data('invoices', $invoices);
        $invoiceData = base64_encode(json_encode($invoices));
        $contentView->set_content_data('invoice_data', $invoiceData);
        if (!empty($invoices)) {
            $collectionData['invoice_nr'] = $invoices[0]['invoiceNumber'];
            $collectionData['booking_date'] = (new \DateTime($invoices[0]['invoiceDate']))->format('Y-m-d');
            $collectionData['order_date'] = $order->getPurchaseDateTime()->format('Y-m-d');
            $collectionData['amount'] = $invoices[0]['invoiceAmount'];
            $collectionData['description'] = sprintf(
                'Bestellung %s vom %s',
                $order->getOrderId(),
                $order->getPurchaseDateTime()->format('Y-m-d')
            );
        }
        $contentView->set_content_data('collection_data', $collectionData);
    }
    
    protected function getInvoicesForOrder($orderId)
    {
        $now = time();
        $downloadExpires = $now + self::DOWNLOAD_INTERVAL;
        /** @var InvoiceArchiveReadService $invoiceReader */
        $invoiceReader   = StaticGXCoreLoader::getService('InvoiceArchiveRead');
        $invoiceCollection = $invoiceReader->getInvoiceListByConditions(['orders_id' => $orderId]);
        $invoices = [];
        /** @var \InvoiceListItem $invoiceListItem */
        foreach ($invoiceCollection as $invoiceListItem) {
            $invoiceFile = $invoiceListItem->getInvoiceFilename();
            $fileName = $invoiceFile . '_' .
                        $downloadExpires . '_' .
                        hash('sha256', $downloadExpires . $invoiceFile . LogControl::get_secure_token());
            $invoices[] = [
                'invoiceId'           => $invoiceListItem->getInvoiceId(),
                'invoiceNumber'       => $invoiceListItem->getInvoiceNumber(),
                'fileName'            => $fileName,
                'invoiceDate'         => $invoiceListItem->getInvoiceDate()->format('c'),
                'invoiceDateReadable' => $invoiceListItem->getInvoiceDate()->format('Y-m-d H:i'),
                'invoiceAmount'       => $invoiceListItem->getTotalSum(),
            ];
        }
        return $invoices;
    }
    

    protected function handleActions(CashFlowTechHubClient $cftHubClient, $ordersId)
    {
        if ($_POST['action'] === 'new-collection') {
            $newCollectionResponse = $this->handleNewCollection($cftHubClient, $ordersId);
            if ($newCollectionResponse['status'] === 'OK') {
                $_SESSION['cft_messages'] = [
                    [
                        'type'    => 'info',
                        'content' => $this->text->get_text('create_collection_ok'),
                    ],
                ];
            } elseif ($newCollectionResponse['status'] === 'error') {
                $_SESSION['cft_messages'] = [
                    [
                        'type'    => 'error',
                        'content' => $this->text->get_text('create_collection_error'),
                    ],
                ];
                $_SESSION['cft_messages'] = array_merge($_SESSION['cft_messages'], $newCollectionResponse['messages']);
            }
            xtc_redirect(xtc_href_link('orders.php', 'oID=' . (int)$_GET['oID']) . '&action=edit');
        }
        if ($_POST['action'] === 'new-payment') {
            $newPaymentResponse = $this->handleNewPayment($cftHubClient);
            if ($newPaymentResponse['status'] === 'OK') {
                $_SESSION['cft_messages'] = [
                    [
                        'type' => 'info',
                        'content' => $this->text->get_text('create_payment_ok'),
                    ]
                ];
            } elseif ($newPaymentResponse['status'] === 'error') {
                $_SESSION['cft_messages'] = [
                    [
                        'type' => 'error',
                        'content' => $this->text->get_text('create_payment_error'),
                    ]
                ];
                $_SESSION['cft_messages'] = array_merge($_SESSION['cft_messages'], $newPaymentResponse['messages']);
            }
            xtc_redirect(xtc_href_link('orders.php', 'oID=' . (int)$_GET['oID']) . '&action=edit');
        }
        if ($_POST['action'] === 'reverse-payment') {
            $paymentReversalResponse = $this->handlePaymentReversal($cftHubClient);
            if ($paymentReversalResponse['status'] === 'OK') {
                $_SESSION['cft_messages'] = [
                    [
                        'type'    => 'info',
                        'content' => $this->text->get_text('create_reversal_ok'),
                    ],
                ];
            } elseif ($paymentReversalResponse['status'] === 'error') {
                $_SESSION['cft_messages'] = [
                    [
                        'type'    => 'error',
                        'content' => $this->text->get_text('create_reversal_error'),
                    ],
                ];
                $_SESSION['cft_messages'] = array_merge($_SESSION['cft_messages'], $paymentReversalResponse['messages']);
            }
            xtc_redirect(xtc_href_link('orders.php', 'oID=' . (int)$_GET['oID']) . '&action=edit');
        }
    }

    protected function handleNewCollection(CashFlowTechHubClient $cftClient, $orderId)
    {
        $newCollection = $_POST['new-collection'];
        $newCollection['invoice_url_base'] = xtc_catalog_href_link(
            'shop.php',
            'do=CashFlowTechDocuments/DownloadInvoice&file=',
            'SSL'
        );
        $invoiceData = $_POST['invoiceData'];
        $invoices = $_POST['invoices'];
        $newCollectionResponse = $cftClient->createNewCollection(
            $orderId,
            $newCollection,
            $invoiceData,
            $invoices
        );
        return $newCollectionResponse;
    }
    
    
    protected function handleNewPayment(CashFlowTechHubClient $cftClient)
    {
        $collectionId = (int)$_POST['collection_id'];
        $amount       = (float)$_POST['new_payment']['amount'];
        $type         = (int)$_POST['new_payment']['type'];
        $newPaymentResponse = $cftClient->createNewPayment(
            $collectionId,
            $amount,
            $type
        );
        return $newPaymentResponse;
    }

    
    protected function handlePaymentReversal(CashFlowTechHubClient $cftClient)
    {
        $collectionId = (int)$_POST['collection_id'];
        $fokoId = (int)$_POST['foko_id'];
        $amount = (float)$_POST['reverse_payment_amount'];
        $paymentReversalResponse = $cftClient->reversePayment(
            $collectionId,
            $fokoId,
            $amount
        );
        return $paymentReversalResponse;
    }
}
