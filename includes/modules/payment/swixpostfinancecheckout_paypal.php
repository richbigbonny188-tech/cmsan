<?php

$languageTextManager = MainFactory::create_object('LanguageTextManager', [], true);
$languageTextManager->init_from_lang_file('swixpostfinancecheckout_paypal', $_SESSION['languages_id']);

class swixpostfinancecheckout_paypal extends SwixPostfinanceCheckoutBase
{
    public $code = 'swixpostfinancecheckout_paypal';
    protected $alias = 'PP';
    protected $paymentMethodId = 1457546097613;
    public $sort_order = 50;
}