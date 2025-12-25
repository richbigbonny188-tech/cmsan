<?php
/* --------------------------------------------------------------
   PersonalDataXmlSerializer.inc.php 2018-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PersonalDataXmlSerializer
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class PersonalDataXmlSerializer
{
    public function serialize(array $personalDataArray)
    {
        $personalDataXml = new ExtendedSimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><personal_data/>');
        
        if (array_key_exists('base_data', $personalDataArray)) {
            $baseDataXml = $personalDataXml->addChild('base_data');
            $this->serializeBaseData($baseDataXml, $personalDataArray['base_data']);
        }
        
        if (array_key_exists('addresses', $personalDataArray)) {
            $addressesXml = $personalDataXml->addChild('addresses');
            $this->serializeCustomerAddresses($addressesXml, $personalDataArray['addresses']);
        }
        
        if (array_key_exists('orders', $personalDataArray)) {
            $orderXml = $personalDataXml->addChild('orders');
            $this->serializeOrders($orderXml, $personalDataArray['orders']);
        }
        
        if (array_key_exists('invoices', $personalDataArray)) {
            $invoiceXml = $personalDataXml->addChild('invoices');
            $this->serializeInvoices($invoiceXml, $personalDataArray['invoices']);
        }
        
        if (array_key_exists('packing_slips', $personalDataArray)) {
            $packingSlipXml = $personalDataXml->addChild('packing_slips');
            $this->serializePackingSlips($packingSlipXml, $personalDataArray['packing_slips']);
        }
        
        if (array_key_exists('withdrawals', $personalDataArray)) {
            $withdrawalXml = $personalDataXml->addChild('withdrawals');
            $this->serializeWithdrawals($withdrawalXml, $personalDataArray['withdrawals']);
        }
        
        if (array_key_exists('agreements', $personalDataArray)) {
            $agreementXml = $personalDataXml->addChild('agreements');
            $this->serializeAgreements($agreementXml, $personalDataArray['agreements']);
        }
        
        if (array_key_exists('emails', $personalDataArray)) {
            $emailXml = $personalDataXml->addChild('emails');
            $this->serializeEmails($emailXml, $personalDataArray['emails']);
        }
        
        if (array_key_exists('shared_shopping_carts', $personalDataArray)) {
            $sharedShoppingCartXml = $personalDataXml->addChild('shared_shopping_carts');
            $this->serializeSharedShoppingCarts($sharedShoppingCartXml, $personalDataArray['shared_shopping_carts']);
        }
        
        if (array_key_exists('shopping_cart', $personalDataArray)) {
            $shoppingCartXml = $personalDataXml->addChild('shopping_cart');
            $this->serializeShoppingCarts($shoppingCartXml, $personalDataArray['shopping_cart']);
        }
        
        if (array_key_exists('reviews', $personalDataArray)) {
            $reviewXml = $personalDataXml->addChild('reviews');
            $this->serializeReviews($reviewXml, $personalDataArray['reviews']);
        }
        
        if (array_key_exists('newsletter_subscription', $personalDataArray)) {
            $newsletterSubscriptionXml = $personalDataXml->addChild('newsletter_subscription');
            $this->serializeNewsletterSubscription($newsletterSubscriptionXml,
                                                   $personalDataArray['newsletter_subscription']);
        }
        
        return $personalDataXml;
    }
    
    
    protected function serializeBaseData(SimpleXMLElement $xml, CustomerInterface $customer)
    {
        $xml->addChild('customer_id', $customer->getId());
        $xml->addChild('customer_number', $customer->getCustomerNumber());
        $xml->addChild('gender', $customer->getGender());
        $xml->addChild('first_name', $customer->getFirstname());
        $xml->addChild('last_name', $customer->getLastname());
        $xml->addChild('date_of_birth', $customer->getDateOfBirth()->format('Y-m-d'));
        $xml->addChild('vat_id_number', $customer->getVatNumber());
        $xml->addChild('phone_number', $customer->getTelephoneNumber());
        $xml->addChild('fax_number', $customer->getFaxNumber());
        $xml->addChild('email_address', $customer->getEmail());
        $addOnValueXml = $xml->addChild('additional_information');
        $this->serializeAddOnValues($addOnValueXml, $customer->getAddonValues());
    }
    
    
    protected function serializeAddOnValues(SimpleXMLElement $xml, KeyValueCollection $addOnValues)
    {
        foreach ($addOnValues as $key => $value) {
            $xml->addChild($key, $value);
        }
    }
    
    
    protected function serializeCustomerAddresses(SimpleXMLElement $xml, array $addresses)
    {
        /**
         * @var CustomerAddress $address
         */
        foreach ($addresses as $address) {
            $addressXml = $xml->addChild('address');
            $this->serializeAddress($addressXml, $address);
        }
    }
    
    
    protected function serializeOrderAddresses(SimpleXMLElement $xml, OrderInterface $order)
    {
        $customerAddressXml = $xml->addChild('customer_address');
        $this->serializeAddress($customerAddressXml, $order->getCustomerAddress());
        
        $shippingAddressXml = $xml->addChild('shipping_address');
        $this->serializeAddress($shippingAddressXml, $order->getDeliveryAddress());
        
        $billingAddressXml = $xml->addChild('billing_address');
        $this->serializeAddress($billingAddressXml, $order->getBillingAddress());
    }
    
    
    /**
     * @param \SimpleXMLElement                                $xml
     * @param \CustomerAddressInterface|\AddressBlockInterface $address
     */
    protected function serializeAddress(SimpleXMLElement $xml, $address)
    {
        $xml->addChild('gender', $address->getGender());
        $xml->addChild('first_name', $address->getFirstname());
        $xml->addChild('last_name', $address->getLastname());
        $xml->addChild('company', $address->getCompany());
        $xml->addChild('street', $address->getStreet());
        $xml->addChild('house_number', $address->getHouseNumber());
        $xml->addChild('additional_info', $address->getAdditionalAddressInfo());
        $xml->addChild('suburb', $address->getSuburb());
        $xml->addChild('postcode', $address->getPostcode());
        $xml->addChild('city', $address->getCity());
        $xml->addChild('country', $address->getCountry()->getName());
        $xml->addChild('country_zone', $address->getCountryZone()->getName());
        $xml->addChild('b2b_status', $address->getB2BStatus());
    }
    
    
    protected function serializeOrderAddressBlock(SimpleXMLElement $xml, OrderAddressBlock $address)
    {
        $xml->addChild('first_name', $address->getFirstName());
        $xml->addChild('last_name', $address->getLastName());
        $xml->addChild('company', $address->getCompany());
        $xml->addChild('street', $address->getStreet());
        $xml->addChild('house_number', $address->getHouseNumber());
        $xml->addChild('additional_info', $address->getAdditionalAddressInfo());
        $xml->addChild('postcode', $address->getPostcode());
        $xml->addChild('city', $address->getCity());
        $xml->addChild('state', $address->getState());
        $xml->addChild('country', $address->getCountry());
        $xml->addChild('country_iso_code', $address->getCountryIsoCode());
    }
    
    
    protected function serializeOrders(SimpleXMLElement $xml, array $orders)
    {
        /**
         * @var GXEngineOrder $order
         */
        foreach ($orders as $order) {
            $orderXml = $xml->addChild('order');
            $orderXml->addChild('id', $order->getOrderId());
            $orderXml->addChild('shipping_type', $order->getShippingType()->getTitle());
            $orderXml->addChild('payment_type', $order->getPaymentType()->getTitle());
            $orderXml->addChild('currency', $order->getCurrencyCode()->getCode());
            $orderXml->addChild('language', $order->getLanguageCode()->asString());
            $orderXml->addChild('order_date', $order->getPurchaseDateTime()->format('Y-m-d H:i:s'));
            $orderXml->addChild('date_of_last_modification', $order->getLastModifiedDateTime()->format('Y-m-d H:i:s'));
            $orderXml->addChild('comment', $order->getComment());
            $orderXml->addChild('totalWeight', $order->getTotalWeight());
            
            $customerXml = $orderXml->addChild('customer');
            $customerXml->addChild('customer_number', $order->getCustomerNumber());
            $customerXml->addChild('email_address', $order->getCustomerEmail());
            $customerXml->addChild('phone_number', $order->getCustomerTelephone());
            $customerXml->addChild('vat_id_number', $order->getVatIdNumber());
            $customerXml->addChild('status', $order->getCustomerStatusInformation()->getStatusName());
            $this->serializeOrderAddresses($customerXml, $order);
            
            $productsXml = $orderXml->addChild('products');
            $this->serializeOrderItems($productsXml, $order->getOrderItems());
            
            $orderTotalsXml = $orderXml->addChild('order_totals');
            $this->serializeOrderTotalCollection($orderTotalsXml, $order->getOrderTotals());
            
            $addOnValueXml = $xml->addChild('additional_information');
            $this->serializeAddOnValues($addOnValueXml, $order->getAddonValues());
        }
    }
    
    
    protected function serializeOrderItems(SimpleXMLElement $xml, OrderItemCollection $products)
    {
        /**
         * @var OrderItem $product
         */
        foreach ($products as $product) {
            $productXml = $xml->addChild('product');
            $this->serializeOrderItem($productXml, $product);
        }
    }
    
    
    protected function serializeOrderItem(SimpleXMLElement $xml, OrderItemInterface $product)
    {
        $xml->addChild('model', $product->getProductModel());
        $xml->addChild('name', $product->getName());
        $xml->addChild('price', $product->getPrice());
        $xml->addChild('quantity', $product->getQuantity());
        $xml->addChild('quantity_unit', $product->getQuantityUnitName());
        $xml->addChild('tax', $product->getTax());
        $xml->addChild('discount', $product->getDiscountMade());
        $xml->addChild('shipping_time_information', $product->getShippingTimeInfo());
        $xml->addChild('checkout_information', $product->getCheckoutInformation());
        
        $attributesXml = $xml->addChild('attributes');
        $this->serializeOrderItemAttributes($attributesXml, $product->getAttributes());
        $addOnValueXml = $xml->addChild('additional_information');
        $this->serializeAddOnValues($addOnValueXml, $product->getAddonValues());
    }
    
    
    protected function serializeOrderTotalCollection(SimpleXMLElement $xml, OrderTotalCollection $orderTotals)
    {
        /**
         * @var OrderTotal $orderTotal
         */
        foreach ($orderTotals as $orderTotal) {
            $xml->addChild('order_total', $orderTotal->getTitle() . ' ' . $orderTotal->getValueText());
        }
    }
    
    
    protected function serializeOrderItemAttributes(SimpleXMLElement $xml, OrderItemAttributeCollection $attributes)
    {
        /**
         * @var OrderItemAttribute $attribute
         */
        foreach ($attributes as $attribute) {
            $attributeXml = $xml->addChild('attribute');
            $attributeXml->addChild('name', $attribute->getName());
            $attributeXml->addChild('value', $attribute->getValue());
            $attributeXml->addChild('priceType', $attribute->getPriceType());
            $attributeXml->addChild('price', $attribute->getPrice());
        }
    }
    
    
    protected function serializeInvoices(SimpleXMLElement $xml, InvoiceListItemCollection $invoices)
    {
        /**
         * @var InvoiceListItem $invoice
         */
        foreach ($invoices as $invoice) {
            $invoiceXml = $xml->addChild('invoice');
            $invoiceXml->addChild('id', $invoice->getInvoiceId());
            $invoiceXml->addChild('invoice_number', $invoice->getInvoiceNumber());
            $invoiceXml->addChild('order_id', $invoice->getOrderId());
            $invoiceXml->addChild('filename', $invoice->getInvoiceFilename());
            $invoiceXml->addChild('date', $invoice->getInvoiceDate()->format('Y-m-d H:i:s'));
            $invoiceXml->addChild('order_date', $invoice->getOrderDatePurchased()->format('Y-m-d H:i:s'));
            $invoiceXml->addChild('total', $invoice->getTotalSum());
            $invoiceXml->addChild('currency', $invoice->getCurrency()->getCode());
            $invoiceXml->addChild('payment_type', $invoice->getPaymentType()->getTitle());
            $invoiceXml->addChild('order_status', $invoice->getOrderStatusName());
            
            $customerXml = $invoiceXml->addChild('customer');
            $customerXml->addChild('name', $invoice->getCustomerName());
            $customerXml->addChild('status', $invoice->getCustomerStatusName());
            $this->serializeInvoiceAddresses($customerXml, $invoice);
        }
    }
    
    
    protected function serializeInvoiceAddresses(SimpleXMLElement $xml, InvoiceListItem $invoice)
    {
        $shippingAddressXml = $xml->addChild('shipping_address');
        $this->serializeOrderAddressBlock($shippingAddressXml, $invoice->getShippingAddress());
        
        $billingAddressXml = $xml->addChild('billing_address');
        $this->serializeOrderAddressBlock($billingAddressXml, $invoice->getPaymentAddress());
    }
    
    
    protected function serializePackingSlips(SimpleXMLElement $xml, PackingSlipCollection $packingSlips)
    {
        /**
         * @var PackingSlip $packingSlip
         */
        foreach ($packingSlips as $packingSlip) {
            $packingSlipXml = $xml->addChild('packing_slip');
            $this->serializePackingSlip($packingSlipXml, $packingSlip);
        }
    }
    
    
    protected function serializePackingSlip(SimpleXMLElement $xml, PackingSlipInterface $packingSlip)
    {
        $xml->addChild('id', $packingSlip->getId());
        $xml->addChild('number', $packingSlip->getNumber());
        $xml->addChild('date', $packingSlip->getDate()->format('Y-m-d H:i:s'));
        $xml->addChild('filename', $packingSlip->getFilename());
        $xml->addChild('order_id', $packingSlip->getOrderId());
    }
    
    
    protected function serializeWithdrawals(SimpleXMLElement $xml, WithdrawalCollection $withdrawals)
    {
        /**
         * @var Withdrawal $withdrawal
         */
        foreach ($withdrawals as $withdrawal) {
            $withdrawalXml = $xml->addChild('withdrawal');
            $this->serializeWithdrawal($withdrawalXml, $withdrawal);
        }
    }
    
    
    protected function serializeWithdrawal(SimpleXMLElement $xml, WithdrawalInterface $withdrawal)
    {
        $xml->addChild('id', $withdrawal->getWithdrawalId());
        $xml->addChild('withdrawal_date', $withdrawal->getWithdrawalDate()->format('Y-m-d H:i:s'));
        $xml->addChild('creation_date', $withdrawal->getDateCreated()->format('Y-m-d H:i:s'));
        $xml->addChild('content', $withdrawal->getWithdrawalContent());
        $orderXml = $xml->addChild('order');
        $this->serializeWithdrawalOrder($orderXml, $withdrawal);
    }
    
    
    protected function serializeWithdrawalOrder(SimpleXMLElement $xml, WithdrawalInterface $withdrawal)
    {
        $xml->addChild('order_id', $withdrawal->getWithdrawalOrder()->getOrderId());
        $xml->addChild('customer_id', $withdrawal->getWithdrawalOrder()->getCustomerId());
        $xml->addChild('gender', $withdrawal->getWithdrawalOrder()->getCustomerGender());
        $xml->addChild('first_name', $withdrawal->getWithdrawalOrder()->getCustomerFirstName());
        $xml->addChild('last_name', $withdrawal->getWithdrawalOrder()->getCustomerLastName());
        $xml->addChild('street_address', $withdrawal->getWithdrawalOrder()->getCustomerStreetAddress());
        $xml->addChild('postcode', $withdrawal->getWithdrawalOrder()->getCustomerPostCode());
        $xml->addChild('city', $withdrawal->getWithdrawalOrder()->getCustomerCity());
        $xml->addChild('country', $withdrawal->getWithdrawalOrder()->getCustomerCountry());
        $xml->addChild('email_address', $withdrawal->getWithdrawalOrder()->getCustomerEmail());
        $xml->addChild('order_date', $withdrawal->getWithdrawalOrder()->getOrderDate()->format('Y-m-d H:i:s'));
        $xml->addChild('delivery_date', $withdrawal->getWithdrawalOrder()->getDeliveryDate()->format('Y-m-d H:i:s'));
    }
    
    
    protected function serializeAgreements(SimpleXMLElement $xml, AgreementCollection $agreements)
    {
        /**
         * @var Agreement $agreement
         */
        foreach ($agreements as $agreement) {
            $agreementXml = $xml->addChild('agreement');
            $this->serializeAgreement($agreementXml, $agreement);
        }
    }
    
    
    protected function serializeAgreement(SimpleXMLElement $xml, AgreementInterface $agreement)
    {
        $xml->addChild('id', $agreement->getId());
        $xml->addChild('date', $agreement->getDateAdded()->format('Y-m-d H:i:s'));
        $xml->addChild('ip_address', $agreement->getIpAddress()->asString());
        $xml->addChild('text', $agreement->getText()->asString());
        $xml->addChild('legal_text_version', $agreement->getLegalTextVersion()->asString());
        $xml->addChild('legal_text_content_id', $agreement->getContentGroup()->asInt());
        $xml->addChild('language_id', $agreement->getLanguageId()->asInt());
        $customerXml = $xml->addChild('customer');
        $customerXml->addChild('name', $agreement->getCustomer()->getCustomerName());
        $customerXml->addChild('email_address', $agreement->getCustomer()->getCustomerEmail());
    }
    
    
    protected function serializeEmails(SimpleXMLElement $xml, EmailCollection $emails)
    {
        /**
         * @var EmailInterface $email
         */
        foreach ($emails as $email) {
            $emailXml = $xml->addChild('email');
            $this->serializeEmail($emailXml, $email);
        }
    }
    
    
    protected function serializeEmail(SimpleXMLElement $xml, EmailInterface $email)
    {
        $xml->addChild('id', $email->getId());
        $xml->addChild('subject', $email->getSubject());
        $xml->addChild('pending', $email->isPending() ? 1 : 0);
        $xml->addChild('content_html', (string)$email->getContentHtml());
        $xml->addChild('content_plain', (string)$email->getContentPlain());
        $xml->addChild('creation_date', $email->getCreationDate()->format('Y-m-d H:i:s'));
        
        if ($email->getSentDate() !== null) {
            $xml->addChild('send_date', $email->getSentDate()->format('Y-m-d H:i:s'));
        }
        
        $contacts = $xml->addChild('contacts');
        $this->serializeEmailContacts($contacts, $email);
    }
    
    
    protected function serializeEmailContacts(SimpleXMLElement $xml, EmailInterface $email)
    {
        
        $sender = $email->getSender();
        if ($sender !== null) {
            $senderXml = $xml->addChild('sender');
            $this->serializeEmailContact($senderXml, $sender);
        }
        
        $replyTo = $email->getReplyTo();
        if ($replyTo !== null) {
            $replyToXml = $xml->addChild('reply_to');
            $this->serializeEmailContact($replyToXml, $replyTo);
        }
        
        $recipient = $email->getRecipient();
        if ($recipient !== null) {
            $recipientXml = $xml->addChild('recipient');
            $this->serializeEmailContact($recipientXml, $recipient);
        }
        
        $cc = $email->getCc();
        if ($cc !== null) {
            $ccCollectionXml = $xml->addChild('cc');
            $this->serializeEmailContactCollection($ccCollectionXml, $cc);
        }
        
        $bcc = $email->getBcc();
        if ($bcc !== null) {
            $bccCollectionXml = $xml->addChild('bcc');
            $this->serializeEmailContactCollection($bccCollectionXml, $bcc);
        }
    }
    
    
    protected function serializeEmailContactCollection(SimpleXMLElement $xml, ContactCollection $contacts)
    {
        foreach ($contacts as $contact) {
            $contactXml = $xml->addChild('contact');
            $this->serializeEmailContact($contactXml, $contact);
        }
    }
    
    
    protected function serializeEmailContact(SimpleXMLElement $xml, EmailContactInterface $contact)
    {
        $xml->addChild('name', $contact->getContactName());
        $xml->addChild('email_address', $contact->getEmailAddress());
    }
    
    
    protected function serializeSharedShoppingCarts(SimpleXMLElement $xml, SharedShoppingCartCollection $shoppingCarts)
    {
        /**
         * @var SharedShoppingCartInterface $shoppingCart
         */
        foreach ($shoppingCarts as $shoppingCart) {
            $sharedShoppingCartXml = $xml->addChild('shared_shopping_cart');
            $this->serializeSharedShoppingCart($sharedShoppingCartXml, $shoppingCart);
        }
    }
    
    
    protected function serializeSharedShoppingCart(SimpleXMLElement $xml, SharedShoppingCartInterface $shoppingCart)
    {
        $xml->addChild('hash', $shoppingCart->getHash());
        $xml->addChild('content_json', $shoppingCart->getShoppingCartJson());
        $xml->addChild('creation_date', $shoppingCart->getCreationDate()->format('Y-m-d H:i:s'));
    }
    
    
    protected function serializeShoppingCarts(SimpleXMLElement $xml, ShoppingCartCollection $shoppingCarts)
    {
        /**
         * @var ShoppingCartInterface $shoppingCart
         */
        foreach ($shoppingCarts as $shoppingCart) {
            $shoppingCartXml = $xml->addChild('shopping_cart_item');
            $this->serializeShoppingCart($shoppingCartXml, $shoppingCart);
        }
    }
    
    
    protected function serializeShoppingCart(SimpleXMLElement $xml, ShoppingCartInterface $shoppingCart)
    {
        $xml->addChild('id', $shoppingCart->getId());
        $xml->addChild('customer_id', $shoppingCart->getCustomerId());
        $xml->addChild('product_id', $shoppingCart->getProductId());
        $xml->addChild('quantity', $shoppingCart->getQuantity());
        $xml->addChild('final_price', $shoppingCart->getFinalPrice());
        $xml->addChild('creation_date', $shoppingCart->getCreationDate());
    }
    
    
    protected function serializeReviews(SimpleXMLElement $xml, ReviewCollection $reviews)
    {
        /**
         * @var ReviewInterface $review
         */
        foreach ($reviews as $review) {
            $reviewXml = $xml->addChild('review');
            $this->serializeReview($reviewXml, $review);
        }
    }
    
    
    protected function serializeReview(SimpleXMLElement $xml, ReviewInterface $review)
    {
        $xml->addChild('id', $review->getId());
        $xml->addChild('customer_name', $review->getCustomer()->getCustomerName());
        $xml->addChild('product_id', $review->getProductId());
        $xml->addChild('language_id', $review->getLanguageId());
        $xml->addChild('rating', $review->getRating());
        $xml->addChild('text', $review->getText());
        $xml->addChild('number_of_readings', $review->getRead());
        $xml->addChild('creation_date', $review->getDateAdded()->format('Y-m-d H:i:s'));
        $xml->addChild('last_modification_date', $review->getLastModifiedDate()->format('Y-m-d H:i:s'));
    }
    
    
    protected function serializeNewsletterSubscription(
        SimpleXMLElement $xml,
        NewsletterSubscriptionInterface $newsletterSubscription
    ) {
        $xml->addChild('id', $newsletterSubscription->getId());
        $xml->addChild('status', $newsletterSubscription->getMailStatus());
        $xml->addChild('key', $newsletterSubscription->getMailKey());
        $xml->addChild('subscription_date', $newsletterSubscription->getSubscriptionDate()->format('Y-m-d H:i:s'));
    
        if ($newsletterSubscription->getIpAddress()) {
            $xml->addChild('ip_address', $newsletterSubscription->getIpAddress());
        }
        
        $customerXml = $xml->addChild('customer');
        $this->serializeNewsletterSubscriptionCustomer($customerXml, $newsletterSubscription);
    }
    
    
    protected function serializeNewsletterSubscriptionCustomer(
        SimpleXMLElement $xml,
        NewsletterSubscriptionInterface $newsletterSubscription
    ) {
        $xml->addChild('id', $newsletterSubscription->getCustomerId());
        $xml->addChild('email_address', $newsletterSubscription->getEmail());
        $xml->addChild('status', $newsletterSubscription->getCustomerStatus());
        $xml->addChild('first_name', $newsletterSubscription->getFirstName());
        $xml->addChild('last_name', $newsletterSubscription->getLastName());
    }
}