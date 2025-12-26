<?php
/*------------------------------------------------------------------------------
 OverviewAction.php 2024-01-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2024 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Vue;

use Gambio\Admin\Application\Http\VuePageAction;
use Gambio\Admin\Modules\Customer\App\CustomerUserConfigurationRepository;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response as HttpResponse;

/**
 * Class OverviewAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions
 * @codeCoverageIgnore
 */
class OverviewAction extends VuePageAction
{
    private const DOMAIN = 'customer';
    
    private const TRANSLATION_PHRASES = [
        'overview_add_customer',
        'overview_customer_group',
        'overview_search_placeholder',
        'overview_settings',
        'overview_country',
        'overview_city',
        'overview_is_favorite',
        'overview_has_subscribed',
        'overview_name',
        'overview_email',
        'overview_phone',
        'overview_company',
        'overview_vat_number',
        'overview_vat_id_verified',
        'overview_vat_id_not_verified',
        'overview_per_page',
        'overview_page_prev',
        'overview_page_next',
        'overview_page',
        'overview_from',
        'overview_to',
        'overview_customers',
        'overview_guest_account',
        'overview_no_filter_selected',
        'overview_no_results',
        'overview_no_results_hint',
        'overview_filter_favorite',
        'modal_save',
        'modal_cancel',
        'modal_close',
        'modal_delete',
        'modal_next',
        'modal_create_customer',
        'modal_create_customer_title',
        'modal_delete_customer',
        'modal_delete_customer_text',
        'modal_delete_customer_deleted_data_text',
        'modal_delete_customer_personal_data_gobd_warning_text',
        'create_customer_password_email_subject',
        'create_customer_password_email_body',
        'overview_delete_guest_accounts',
        'overview_delete_guest_accounts_modal_body',
        'overview_delete_guest_accounts_deleted',
        'overview_tooltip_customer_profile',
        'overview_tooltip_customer_orders',
        'overview_tooltip_customer_favorite_add',
        'overview_tooltip_customer_favorite_remove',
        'overview_tooltip_customer_email',
        'overview_registration_date',
        'overview_last_login',

        'profile_no_name',
        'profile_new_order',
        'profile_email',
        'profile_password',
        'profile_password_show',
        'profile_password_hide',
        'profile_password_generate',
        'profile_salutation',
        'profile_salutation_mr',
        'profile_salutation_ms',
        'profile_salutation_none',
        'profile_first_name',
        'profile_last_name',
        'profile_company_name',
        'profile_street_name',
        'profile_house_number',
        'profile_post_code',
        'profile_city',
        'profile_country',
        'profile_suburb',
        'profile_state',
        'profile_additional_information',
        'profile_vat_id',
        'profile_vat_id_verified',
        'profile_vat_id_not_verified',
        'profile_tradeperson',
        'profile_disallowed_payment_methods',
        'profile_disallowed_shipping_methods',
        'profile_logging',
        'profile_log_activity',
        'profile_phone_number',
        'profile_fax_number',
        'profile_payment_address',
        'profile_shipping_address',
        'profile_date_of_birth',
        'profile_customer_number',
        'profile_customer_group',
        'profile_business_information',
        'profile_configurations',
        'profile_contact_information',
        'profile_address',
        'profile_guest_account',
        'profile_gender',
        'profile_gender_m',
        'profile_gender_f',
        'profile_gender_d',
        'profile_balance',
        'profile_balance_hint',
        'profile_balance_increment',
        'profile_balance_decrement',
        
        'profile_new_password',
        'profile_error_password',
        'profile_error_password_empty',
        'profile_error_empty_first_name',
        'profile_error_empty_last_name',
        'profile_currency_format_locale',
        
        'profile_change_customer_group',
        'profile_customer_group_hint',
        'profile_create_customer_group',
        'profile_new_customer_group',
        'profile_current_customer_group',
        'profile_customer_group_desc',
        'profile_delete_customer',
        'profile_export_personal_data',
        'profile_delete_personal_data',
        'profile_select_personal_data_base_data',
        'profile_select_personal_data_orders',
        'profile_select_personal_data_withdrawals',
        'profile_select_personal_data_agreements',
        'profile_select_personal_data_emails',
        'profile_select_personal_data_carts',
        'profile_select_personal_data_reviews',
        'profile_select_personal_data_newsletter_subscriptions',
        'profile_personal_information',
        'profile_contact_information',
        'profile_business_information',
        'profile_address',
        'overview_send_new_customer_password_email',
        'overview_send_new_customer_password_email_hint',
        'profile_favorite_customer',
        'profile_favorite_customer_button',
        
        'profile_login_as_customer',
        'profile_login_as_customer_modal_title',
        'profile_login_as_customer_button',
        'profile_login_as_customer_warning',
        'profile_login_as_customer_warning_preference',

        'profile_error_first_name',
        'profile_error_last_name',
        'profile_error_gender',
        'profile_error_invalid_email',
        'profile_error_phone_number',
        'profile_error_company_name',
        'profile_error_street_name',
        'profile_error_house_number',
        'profile_error_street_name_house_number',
        'profile_error_postcode',
        'profile_error_city',
        'profile_error_state_empty',
        'profile_error_state',
        'profile_error_empty_field',
        
        'modal_delete_customer_action_label',
        'profile_select_all',
        'profile_select_personal_data_shopping_cart_wishlist',
        'profile_select_personal_data_balance',
        'modal_delete_customer_switch',
        'profile_delete_personal_data_action_label',
        'profile_delete_personal_data_account_text',
        'profile_select_personal_data_wishlist',
        'profile_delete_personal_data_switch',
        'profile_delete_personal_data_text',
        
        'profile_newsletter_subscription_badge',
        'profile_newsletter_subscription_label',
        'profile_newsletter_subscription_option',
        'profile_newsletter_subscription_warning',
    ];
    
    private CustomerUserConfigurationRepository $customerUserConfigurationRepository;
    
    public function __construct(
        CustomerUserConfigurationRepository $customerUserConfigurationRepository
    ) {
        $this->customerUserConfigurationRepository = $customerUserConfigurationRepository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, HttpResponse $response): HttpResponse
    {
        foreach (self::TRANSLATION_PHRASES as $phrase) {
            $this->addVuePageTranslation($phrase, self::DOMAIN);
        }
    
        $customerPerPage = $this->customerUserConfigurationRepository->getCustomersPerPageValue();
        
        return $response->write($this->render($this->translate('overview_title', self::DOMAIN),
                                              dirname(__DIR__, 3) . '/ui/overview.html', [ 'customers_per_page' => $customerPerPage ]));
    }
    
    
    /**
     * @inheritDoc
     */
    protected function jsEntrypoint(): string
    {
        return self::DOMAIN;
    }
}
