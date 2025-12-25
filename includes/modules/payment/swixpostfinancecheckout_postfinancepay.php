<?php

$languageTextManager = MainFactory::create_object('LanguageTextManager', [], true);
$languageTextManager->init_from_lang_file('swixpostfinancecheckout_postfinancepay', $_SESSION['languages_id']);

class swixpostfinancecheckout_postfinancepay extends SwixPostfinanceCheckoutBase
{
    public $code = 'swixpostfinancecheckout_postfinancepay';
    protected $alias = 'PF';
    protected $allowedCountries = 'CH,LI';
    protected $paymentMethodId = 1689233132073;
    public $sort_order = 30;
}