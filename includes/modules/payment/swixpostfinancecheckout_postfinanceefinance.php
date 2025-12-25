<?php

$languageTextManager = MainFactory::create_object('LanguageTextManager', [], true);
$languageTextManager->init_from_lang_file('swixpostfinancecheckout_postfinanceefinance', $_SESSION['languages_id']);

class swixpostfinancecheckout_postfinanceefinance extends SwixPostfinanceCheckoutBase
{
    public $code = 'swixpostfinancecheckout_postfinanceefinance';
    protected $alias = 'PF';
    protected $allowedCountries = 'CH,LI';
    protected $paymentMethodId = 1460954915005;
    protected $paymentMethodBrandIds = [1461146715166];
    public $sort_order = 40;
}