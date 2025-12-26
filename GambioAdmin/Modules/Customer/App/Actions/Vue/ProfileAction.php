<?php
/*------------------------------------------------------------------------------
 ProfileAction.php 2023-02-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Vue;

use Gambio\Admin\Application\Http\VuePageAction;
use Gambio\Admin\Layout\Menu\AdminMenuService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response as HttpResponse;

/**
 * Class ProfileAction
 *
 * @package Gambio\Admin\Modules\Option\App\Actions
 * @codeCoverageIgnore
 */
class ProfileAction extends VuePageAction
{
    private const DOMAIN = 'customer';
    
    private const TRANSLATION_PHRASES = [
        'profile_title',
        'profile_no_name',
        'profile_not_found',
        'profile_not_found_description',
        'profile_not_found_back_button',
        'profile_login_as_customer',
        'profile_login_as_customer_modal_title',
        'profile_login_as_customer_button',
        'profile_login_as_customer_warning',
        'profile_login_as_customer_warning_preference',
        'profile_email',
        'profile_new_order',
        'profile_memos',
        'profile_memos_loading',
        'profile_memos_empty',
        'profile_memos_creator',
        'profile_memos_placeholder',
        'profile_memos_add',
        'profile_memos_cancel',
        'profile_memos_delete',
        'profile_memos_delete_confirmation',
        'profile_change_customer_group',
        'profile_customer_group_hint',
        'profile_create_customer_group',
        'profile_new_customer_group',
        'profile_current_customer_group',
        'profile_customer_group_desc',
        'profile_customer_group',
        'profile_change_password',
        'profile_new_password',
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
        'profile_business_information',
        'profile_configurations',
        'profile_contact_information',
        'profile_address',
        'profile_guest_account',
        'profile_gender',
        'profile_gender_m',
        'profile_gender_f',
        'profile_gender_d',
        'profile_personal_information',
        'profile_error_empty_first_name',
        'profile_error_empty_last_name',
        'profile_error_password',
        'profile_error_password_empty',
        'profile_delete_customer',
        'profile_edit',
        'profile_total_spent_to_date',
        'profile_loading',
        'profile_total_orders',
        'profile_average_order_value',
        'profile_balance',
        'profile_balance_hint',
        'profile_balance_edit',
        'profile_balance_increment',
        'profile_balance_decrement',
        'profile_add',
        'profile_placed_order',
        'profile_reviewed_product',
        'profile_created_account',
        'profile_redeemed_voucher',
        'profile_added_product_to_cart',
        'profile_added_product_to_wishlist',
        'profile_subscribed_to_newsletter',
        'profile_subscribed_to_newsletter_by_you',
        'profile_subscribed_to_newsletter_by_admin',
        'profile_order',
        'profile_order_view',
        'profile_products',
        'profile_history',
        'profile_orders',
        'profile_wishlist',
        'profile_cart',
        'profile_reviews',
        'profile_currency_format_locale',
        'profile_error_empty_email',
        'profile_customer_deleted',
        'profile_product_number',
        'profile_empty_wishlist',
        'profile_empty_cart',
        'profile_empty_state_cart',
        'profile_empty_state_reviews',
        'profile_empty_state_wishlist',
        'profile_empty_state_orders',
        'modal_save',
        'modal_cancel',
        'modal_close',
        'modal_delete',
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
        'error_heading',
        'error_message',
        'profile_disallowed_payment_methods_hint',
        'profile_disallowed_shipping_methods_hint',
        'profile_newsletter_subscription_badge',
        'profile_newsletter_subscription_label',
        'profile_newsletter_subscription_option',
        'profile_newsletter_subscription_warning',
        'profile_favorite_customer',
        'profile_favorite_customer_button',
        'modal_delete_customer',
        'modal_delete_customer_text',
        'modal_delete_customer_deleted_data_text',
        'modal_delete_customer_personal_data_gobd_warning_text',
        'overview_tooltip_customer_favorite_add',
        'overview_tooltip_customer_favorite_remove',

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
        'profile_configurations_log_admin_activities_logging',
    ];
    
    public function __construct(AdminMenuService $adminMenuService)
    {
        // Keep Admin Menu Customers active
        $adminMenuService->changeSelectedAdminPage('customers');
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, HttpResponse $response): HttpResponse
    {
        foreach (self::TRANSLATION_PHRASES as $phrase) {
            $this->addVuePageTranslation($phrase, self::DOMAIN);
        }
        
        return $response->write($this->render($this->translate('profile_page_title', self::DOMAIN),
                                              dirname(__DIR__, 3) . '/ui/profile.html', [
                                                  'profile_back_to_overview' => $this->translate('profile_back_to_overview', self::DOMAIN),
                                                  'profile_title' => $this->translate('profile_title', self::DOMAIN),
                                              ]
        ));
    }
    
    
    /**
     * @inheritDoc
     */
    protected function jsEntrypoint(): string
    {
        return self::DOMAIN;
    }
}
