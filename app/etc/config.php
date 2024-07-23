<?php
return [
    'scopes' => [
        'websites' => [
            'admin' => [
                'website_id' => '0',
                'code' => 'admin',
                'name' => 'Admin',
                'sort_order' => '0',
                'default_group_id' => '0',
                'is_default' => '0',
            ],
            'digi_website_au' => [
                'website_id' => '1',
                'code' => 'digi_website_au',
                'name' => 'digiDirect Au',
                'sort_order' => '0',
                'default_group_id' => '1',
                'is_default' => '1',
            ],
            'marketplaces' => [
                'website_id' => '5',
                'code' => 'marketplaces',
                'name' => 'Marketplaces',
                'sort_order' => '2',
                'default_group_id' => '5',
                'is_default' => '0',
            ],
            'retail_stores' => [
                'website_id' => '7',
                'code' => 'retail_stores',
                'name' => 'Retail Stores',
                'sort_order' => '0',
                'default_group_id' => '10',
                'is_default' => '0',
            ],
        ],
        'groups' => [
            0 => [
                'group_id' => '0',
                'website_id' => '0',
                'name' => 'Default',
                'root_category_id' => '0',
                'default_store_id' => '0',
                'code' => 'default',
            ],
            1 => [
                'group_id' => '1',
                'website_id' => '1',
                'name' => 'digiDirect AU',
                'root_category_id' => '2',
                'default_store_id' => '1',
                'code' => 'digi_store_au',
            ],
            5 => [
                'group_id' => '5',
                'website_id' => '5',
                'name' => 'Marketplaces Store',
                'root_category_id' => '2',
                'default_store_id' => '5',
                'code' => 'mpstore',
            ],
            7 => [
                'group_id' => '7',
                'website_id' => '1',
                'name' => 'Retail Store',
                'root_category_id' => '2',
                'default_store_id' => '13',
                'code' => 'retail_store',
            ],
            10 => [
                'group_id' => '10',
                'website_id' => '7',
                'name' => 'Retail Stores Store',
                'root_category_id' => '2',
                'default_store_id' => '19',
                'code' => 'retail_stores_store',
            ],
        ],
        'stores' => [
            'admin' => [
                'store_id' => '0',
                'code' => 'admin',
                'website_id' => '0',
                'group_id' => '0',
                'name' => 'Admin',
                'sort_order' => '0',
                'is_active' => '1',
            ],
            'digi_store_view_au' => [
                'store_id' => '1',
                'code' => 'digi_store_view_au',
                'website_id' => '1',
                'group_id' => '1',
                'name' => 'digiDirect AU',
                'sort_order' => '0',
                'is_active' => '1',
            ],
            'digidirectmarketplaces' => [
                'store_id' => '5',
                'code' => 'digidirectmarketplaces',
                'website_id' => '5',
                'group_id' => '5',
                'name' => 'Marketplaces',
                'sort_order' => '2',
                'is_active' => '1',
            ],
            'retail_store_view' => [
                'store_id' => '13',
                'code' => 'retail_store_view',
                'website_id' => '1',
                'group_id' => '7',
                'name' => 'Retail Store View',
                'sort_order' => '0',
                'is_active' => '1',
            ],
            'BOND' => [
                'store_id' => '19',
                'code' => 'BOND',
                'website_id' => '7',
                'group_id' => '10',
                'name' => 'BOND',
                'sort_order' => '0',
                'is_active' => '1',
            ],
            'BRISB' => [
                'store_id' => '25',
                'code' => 'BRISB',
                'website_id' => '7',
                'group_id' => '10',
                'name' => 'BRISB',
                'sort_order' => '0',
                'is_active' => '1',
            ],
            'CANN' => [
                'store_id' => '28',
                'code' => 'CANN',
                'website_id' => '7',
                'group_id' => '10',
                'name' => 'CANN',
                'sort_order' => '0',
                'is_active' => '1',
            ],
            'MELB' => [
                'store_id' => '31',
                'code' => 'MELB',
                'website_id' => '7',
                'group_id' => '10',
                'name' => 'MELB',
                'sort_order' => '0',
                'is_active' => '1',
            ],
            'MIRA' => [
                'store_id' => '34',
                'code' => 'MIRA',
                'website_id' => '7',
                'group_id' => '10',
                'name' => 'MIRA',
                'sort_order' => '0',
                'is_active' => '1',
            ],
            'PARR' => [
                'store_id' => '37',
                'code' => 'PARR',
                'website_id' => '7',
                'group_id' => '10',
                'name' => 'PARR',
                'sort_order' => '0',
                'is_active' => '1',
            ],
            'SYDN' => [
                'store_id' => '40',
                'code' => 'SYDN',
                'website_id' => '7',
                'group_id' => '10',
                'name' => 'SYDN',
                'sort_order' => '0',
                'is_active' => '1',
            ],
        ],
    ],
    /**
     * For the section: system
     * Shared configuration was written to config.php and system-specific configuration to env.php.
     * Shared configuration file (config.php) doesn't contain sensitive data for security reasons.
     * Sensitive data can be stored in the following environment variables:
     * CONFIG__DEFAULT__PAYMENT__PAYFLOWPRO__PARTNER for payment/payflowpro/partner
     * CONFIG__DEFAULT__PAYMENT__PAYFLOWPRO__USER for payment/payflowpro/user
     * CONFIG__DEFAULT__PAYMENT__PAYFLOWPRO__PWD for payment/payflowpro/pwd
     * CONFIG__DEFAULT__PAYMENT__PAYFLOWPRO__PROXY_HOST for payment/payflowpro/proxy_host
     * CONFIG__DEFAULT__PAYMENT__PAYFLOW_LINK__PWD for payment/payflow_link/pwd
     * CONFIG__DEFAULT__PAYMENT__PAYFLOW_LINK__PROXY_HOST for payment/payflow_link/proxy_host
     * CONFIG__DEFAULT__PAYMENT__PAYPAL_EXPRESS_BML__PUBLISHER_ID for payment/paypal_express_bml/publisher_id
     * CONFIG__DEFAULT__PAYMENT__PAYPAL_EXPRESS__MERCHANT_ID for payment/paypal_express/merchant_id
     * CONFIG__DEFAULT__PAYMENT__BRAINTREE__MERCHANT_ID for payment/braintree/merchant_id
     * CONFIG__DEFAULT__PAYMENT__BRAINTREE__PUBLIC_KEY for payment/braintree/public_key
     * CONFIG__DEFAULT__PAYMENT__BRAINTREE__PRIVATE_KEY for payment/braintree/private_key
     * CONFIG__DEFAULT__PAYMENT__BRAINTREE__MERCHANT_ACCOUNT_ID for payment/braintree/merchant_account_id
     * CONFIG__DEFAULT__PAYMENT__BRAINTREE__KOUNT_ID for payment/braintree/kount_id
     * CONFIG__DEFAULT__PAYMENT__BRAINTREE_PAYPAL__MERCHANT_NAME_OVERRIDE for payment/braintree_paypal/merchant_name_override
     * CONFIG__DEFAULT__PAYMENT__CHECKMO__MAILING_ADDRESS for payment/checkmo/mailing_address
     * CONFIG__DEFAULT__PAYMENT__PAYFLOW_ADVANCED__USER for payment/payflow_advanced/user
     * CONFIG__DEFAULT__PAYMENT__PAYFLOW_ADVANCED__PWD for payment/payflow_advanced/pwd
     * CONFIG__DEFAULT__PAYMENT__PAYFLOW_ADVANCED__PROXY_HOST for payment/payflow_advanced/proxy_host
     * CONFIG__DEFAULT__PAYMENT_ALL_PAYPAL__PAYPAL_PAYFLOWPRO__SETTINGS_PAYPAL_PAYFLOW__HEADING_CC for payment_all_paypal/paypal_payflowpro/settings_paypal_payflow/heading_cc
     * CONFIG__DEFAULT__PAYMENT_ALL_PAYPAL__PAYPAL_PAYFLOWPRO__SETTINGS_PAYPAL_PAYFLOW__SETTINGS_PAYPAL_PAYFLOW_ADVANCED__PAYPAL_PAYFLOW_SETTLEMENT_REPORT__HEADING_SFTP for payment_all_paypal/paypal_payflowpro/settings_paypal_payflow/settings_paypal_payflow_advanced/paypal_payflow_settlement_report/heading_sftp
     * CONFIG__DEFAULT__PAYMENT_ALL_PAYPAL__PAYFLOW_LINK__SETTINGS_PAYFLOW_LINK__SETTINGS_PAYFLOW_LINK_ADVANCED__PAYFLOW_LINK_SETTLEMENT_REPORT__HEADING_SFTP for payment_all_paypal/payflow_link/settings_payflow_link/settings_payflow_link_advanced/payflow_link_settlement_report/heading_sftp
     * CONFIG__DEFAULT__PAYMENT_ALL_PAYPAL__PAYMENTS_PRO_HOSTED_SOLUTION__PPHS_SETTINGS__PPHS_SETTINGS_ADVANCED__PPHS_SETTLEMENT_REPORT__HEADING_SFTP for payment_all_paypal/payments_pro_hosted_solution/pphs_settings/pphs_settings_advanced/pphs_settlement_report/heading_sftp
     * CONFIG__DEFAULT__PAYMENT_ALL_PAYPAL__EXPRESS_CHECKOUT__SETTINGS_EC__SETTINGS_EC_ADVANCED__EXPRESS_CHECKOUT_SETTLEMENT_REPORT__HEADING_SFTP for payment_all_paypal/express_checkout/settings_ec/settings_ec_advanced/express_checkout_settlement_report/heading_sftp
     * CONFIG__DEFAULT__PAYPAL__FETCH_REPORTS__FTP_LOGIN for paypal/fetch_reports/ftp_login
     * CONFIG__DEFAULT__PAYPAL__FETCH_REPORTS__FTP_PASSWORD for paypal/fetch_reports/ftp_password
     * CONFIG__DEFAULT__PAYPAL__FETCH_REPORTS__FTP_IP for paypal/fetch_reports/ftp_ip
     * CONFIG__DEFAULT__PAYPAL__FETCH_REPORTS__FTP_PATH for paypal/fetch_reports/ftp_path
     * CONFIG__DEFAULT__PAYPAL__GENERAL__BUSINESS_ACCOUNT for paypal/general/business_account
     * CONFIG__DEFAULT__PAYPAL__GENERAL__MERCHANT_COUNTRY for paypal/general/merchant_country
     * CONFIG__DEFAULT__PAYPAL__WPP__API_USERNAME for paypal/wpp/api_username
     * CONFIG__DEFAULT__PAYPAL__WPP__API_PASSWORD for paypal/wpp/api_password
     * CONFIG__DEFAULT__PAYPAL__WPP__API_SIGNATURE for paypal/wpp/api_signature
     * CONFIG__DEFAULT__PAYPAL__WPP__API_CERT for paypal/wpp/api_cert
     * CONFIG__DEFAULT__PAYPAL__WPP__PROXY_HOST for paypal/wpp/proxy_host
     * CONFIG__DEFAULT__GIFTCARD_SERVICE__VII__ENDPOINT_URL for giftcard_service/vii/endpoint_url
     * CONFIG__DEFAULT__GIFTCARD_SERVICE__VII__USER_NAME for giftcard_service/vii/user_name
     * CONFIG__DEFAULT__GIFTCARD_SERVICE__VII__PASSWORD for giftcard_service/vii/password
     * CONFIG__DEFAULT__MARKETPLACER_BASE__BASE__API_KEY for marketplacer_base/base/api_key
     * CONFIG__DEFAULT__DEV__RESTRICT__ALLOW_IPS for dev/restrict/allow_ips
     * CONFIG__DEFAULT__ADMIN__URL__CUSTOM for admin/url/custom
     * CONFIG__DEFAULT__ADMIN__URL__CUSTOM_PATH for admin/url/custom_path
     * CONFIG__DEFAULT__CATALOG__PRODUCTALERT_CRON__ERROR_EMAIL for catalog/productalert_cron/error_email
     * CONFIG__DEFAULT__CATALOG__PRODUCT_VIDEO__YOUTUBE_API_KEY for catalog/product_video/youtube_api_key
     * CONFIG__DEFAULT__CATALOG__SEARCH__ELASTICSEARCH5_SERVER_HOSTNAME for catalog/search/elasticsearch5_server_hostname
     * CONFIG__DEFAULT__CATALOG__SEARCH__ELASTICSEARCH7_SERVER_HOSTNAME for catalog/search/elasticsearch7_server_hostname
     * CONFIG__DEFAULT__CATALOG__SEARCH__ELASTICSEARCH5_USERNAME for catalog/search/elasticsearch5_username
     * CONFIG__DEFAULT__CATALOG__SEARCH__ELASTICSEARCH7_USERNAME for catalog/search/elasticsearch7_username
     * CONFIG__DEFAULT__CATALOG__SEARCH__ELASTICSEARCH5_PASSWORD for catalog/search/elasticsearch5_password
     * CONFIG__DEFAULT__CATALOG__SEARCH__ELASTICSEARCH7_PASSWORD for catalog/search/elasticsearch7_password
     * CONFIG__DEFAULT__CATALOG__SEARCH__ELASTICSEARCH6_SERVER_HOSTNAME for catalog/search/elasticsearch6_server_hostname
     * CONFIG__DEFAULT__CATALOG__SEARCH__ELASTICSEARCH6_USERNAME for catalog/search/elasticsearch6_username
     * CONFIG__DEFAULT__CATALOG__SEARCH__ELASTICSEARCH6_PASSWORD for catalog/search/elasticsearch6_password
     * CONFIG__DEFAULT__CATALOGINVENTORY__SOURCE_SELECTION_DISTANCE_BASED_GOOGLE__API_KEY for cataloginventory/source_selection_distance_based_google/api_key
     * CONFIG__DEFAULT__CURRENCY__IMPORT__ERROR_EMAIL for currency/import/error_email
     * CONFIG__DEFAULT__SITEMAP__GENERATE__ERROR_EMAIL for sitemap/generate/error_email
     * CONFIG__DEFAULT__TRANS_EMAIL__IDENT_GENERAL__NAME for trans_email/ident_general/name
     * CONFIG__DEFAULT__TRANS_EMAIL__IDENT_GENERAL__EMAIL for trans_email/ident_general/email
     * CONFIG__DEFAULT__TRANS_EMAIL__IDENT_SALES__NAME for trans_email/ident_sales/name
     * CONFIG__DEFAULT__TRANS_EMAIL__IDENT_SALES__EMAIL for trans_email/ident_sales/email
     * CONFIG__DEFAULT__TRANS_EMAIL__IDENT_SUPPORT__NAME for trans_email/ident_support/name
     * CONFIG__DEFAULT__TRANS_EMAIL__IDENT_SUPPORT__EMAIL for trans_email/ident_support/email
     * CONFIG__DEFAULT__TRANS_EMAIL__IDENT_CUSTOM1__NAME for trans_email/ident_custom1/name
     * CONFIG__DEFAULT__TRANS_EMAIL__IDENT_CUSTOM1__EMAIL for trans_email/ident_custom1/email
     * CONFIG__DEFAULT__TRANS_EMAIL__IDENT_CUSTOM2__NAME for trans_email/ident_custom2/name
     * CONFIG__DEFAULT__TRANS_EMAIL__IDENT_CUSTOM2__EMAIL for trans_email/ident_custom2/email
     * CONFIG__DEFAULT__CONTACT__EMAIL__RECIPIENT_EMAIL for contact/email/recipient_email
     * CONFIG__DEFAULT__SALES_EMAIL__ORDER__COPY_TO for sales_email/order/copy_to
     * CONFIG__DEFAULT__SALES_EMAIL__ORDER_COMMENT__COPY_TO for sales_email/order_comment/copy_to
     * CONFIG__DEFAULT__SALES_EMAIL__INVOICE__COPY_TO for sales_email/invoice/copy_to
     * CONFIG__DEFAULT__SALES_EMAIL__INVOICE_COMMENT__COPY_TO for sales_email/invoice_comment/copy_to
     * CONFIG__DEFAULT__SALES_EMAIL__SHIPMENT__COPY_TO for sales_email/shipment/copy_to
     * CONFIG__DEFAULT__SALES_EMAIL__SHIPMENT_COMMENT__COPY_TO for sales_email/shipment_comment/copy_to
     * CONFIG__DEFAULT__SALES_EMAIL__CREDITMEMO__COPY_TO for sales_email/creditmemo/copy_to
     * CONFIG__DEFAULT__SALES_EMAIL__CREDITMEMO_COMMENT__COPY_TO for sales_email/creditmemo_comment/copy_to
     * CONFIG__DEFAULT__SALES_EMAIL__MAGENTO_RMA__COPY_TO for sales_email/magento_rma/copy_to
     * CONFIG__DEFAULT__SALES_EMAIL__MAGENTO_RMA_AUTH__COPY_TO for sales_email/magento_rma_auth/copy_to
     * CONFIG__DEFAULT__SALES_EMAIL__MAGENTO_RMA_COMMENT__COPY_TO for sales_email/magento_rma_comment/copy_to
     * CONFIG__DEFAULT__SALES_EMAIL__MAGENTO_RMA_CUSTOMER_COMMENT__COPY_TO for sales_email/magento_rma_customer_comment/copy_to
     * CONFIG__DEFAULT__CHECKOUT__PAYMENT_FAILED__COPY_TO for checkout/payment_failed/copy_to
     * CONFIG__DEFAULT__CARRIERS__UPS__ACCESS_LICENSE_NUMBER for carriers/ups/access_license_number
     * CONFIG__DEFAULT__CARRIERS__UPS__GATEWAY_XML_URL for carriers/ups/gateway_xml_url
     * CONFIG__DEFAULT__CARRIERS__UPS__PASSWORD for carriers/ups/password
     * CONFIG__DEFAULT__CARRIERS__UPS__USERNAME for carriers/ups/username
     * CONFIG__DEFAULT__CARRIERS__UPS__GATEWAY_URL for carriers/ups/gateway_url
     * CONFIG__DEFAULT__CARRIERS__UPS__SHIPPER_NUMBER for carriers/ups/shipper_number
     * CONFIG__DEFAULT__CARRIERS__UPS__TRACKING_XML_URL for carriers/ups/tracking_xml_url
     * CONFIG__DEFAULT__CARRIERS__USPS__GATEWAY_URL for carriers/usps/gateway_url
     * CONFIG__DEFAULT__CARRIERS__USPS__GATEWAY_SECURE_URL for carriers/usps/gateway_secure_url
     * CONFIG__DEFAULT__CARRIERS__USPS__USERID for carriers/usps/userid
     * CONFIG__DEFAULT__CARRIERS__USPS__PASSWORD for carriers/usps/password
     * CONFIG__DEFAULT__CARRIERS__FEDEX__ACCOUNT for carriers/fedex/account
     * CONFIG__DEFAULT__CARRIERS__FEDEX__METER_NUMBER for carriers/fedex/meter_number
     * CONFIG__DEFAULT__CARRIERS__FEDEX__KEY for carriers/fedex/key
     * CONFIG__DEFAULT__CARRIERS__FEDEX__PASSWORD for carriers/fedex/password
     * CONFIG__DEFAULT__CARRIERS__FEDEX__PRODUCTION_WEBSERVICES_URL for carriers/fedex/production_webservices_url
     * CONFIG__DEFAULT__CARRIERS__FEDEX__SANDBOX_WEBSERVICES_URL for carriers/fedex/sandbox_webservices_url
     * CONFIG__DEFAULT__CARRIERS__FEDEX__SMARTPOST_HUBID for carriers/fedex/smartpost_hubid
     * CONFIG__DEFAULT__CARRIERS__DHL__ID for carriers/dhl/id
     * CONFIG__DEFAULT__CARRIERS__DHL__PASSWORD for carriers/dhl/password
     * CONFIG__DEFAULT__CARRIERS__DHL__ACCOUNT for carriers/dhl/account
     * CONFIG__DEFAULT__CARRIERS__DHL__GATEWAY_URL for carriers/dhl/gateway_url
     * CONFIG__DEFAULT__GOOGLE__ANALYTICS__ACCOUNT for google/analytics/account
     * CONFIG__DEFAULT__GOOGLE__ANALYTICS__CONTAINER_ID for google/analytics/container_id
     * CONFIG__DEFAULT__PROMO__MAGENTO_REMINDER__IDENTITY for promo/magento_reminder/identity
     * CONFIG__DEFAULT__RECAPTCHA_BACKEND__TYPE_RECAPTCHA__PUBLIC_KEY for recaptcha_backend/type_recaptcha/public_key
     * CONFIG__DEFAULT__RECAPTCHA_BACKEND__TYPE_RECAPTCHA__PRIVATE_KEY for recaptcha_backend/type_recaptcha/private_key
     * CONFIG__DEFAULT__RECAPTCHA_BACKEND__TYPE_INVISIBLE__PUBLIC_KEY for recaptcha_backend/type_invisible/public_key
     * CONFIG__DEFAULT__RECAPTCHA_BACKEND__TYPE_INVISIBLE__PRIVATE_KEY for recaptcha_backend/type_invisible/private_key
     * CONFIG__DEFAULT__RECAPTCHA_BACKEND__TYPE_RECAPTCHA_V3__PUBLIC_KEY for recaptcha_backend/type_recaptcha_v3/public_key
     * CONFIG__DEFAULT__RECAPTCHA_BACKEND__TYPE_RECAPTCHA_V3__PRIVATE_KEY for recaptcha_backend/type_recaptcha_v3/private_key
     * CONFIG__DEFAULT__RECAPTCHA_FRONTEND__TYPE_RECAPTCHA__PUBLIC_KEY for recaptcha_frontend/type_recaptcha/public_key
     * CONFIG__DEFAULT__RECAPTCHA_FRONTEND__TYPE_RECAPTCHA__PRIVATE_KEY for recaptcha_frontend/type_recaptcha/private_key
     * CONFIG__DEFAULT__RECAPTCHA_FRONTEND__TYPE_INVISIBLE__PUBLIC_KEY for recaptcha_frontend/type_invisible/public_key
     * CONFIG__DEFAULT__RECAPTCHA_FRONTEND__TYPE_INVISIBLE__PRIVATE_KEY for recaptcha_frontend/type_invisible/private_key
     * CONFIG__DEFAULT__RECAPTCHA_FRONTEND__TYPE_RECAPTCHA_V3__PUBLIC_KEY for recaptcha_frontend/type_recaptcha_v3/public_key
     * CONFIG__DEFAULT__RECAPTCHA_FRONTEND__TYPE_RECAPTCHA_V3__PRIVATE_KEY for recaptcha_frontend/type_recaptcha_v3/private_key
     * CONFIG__DEFAULT__SYSTEM__SMTP__HOST for system/smtp/host
     * CONFIG__DEFAULT__SYSTEM__FULL_PAGE_CACHE__VARNISH__ACCESS_LIST for system/full_page_cache/varnish/access_list
     * CONFIG__DEFAULT__SYSTEM__FULL_PAGE_CACHE__VARNISH__BACKEND_HOST for system/full_page_cache/varnish/backend_host
     * CONFIG__DEFAULT__SYSTEM__MAGENTO_SCHEDULED_IMPORT_EXPORT_LOG__ERROR_EMAIL for system/magento_scheduled_import_export_log/error_email
     * CONFIG__DEFAULT__SYSTEM__RELEASE_NOTIFICATION__CONTENT_URL for system/release_notification/content_url
     * CONFIG__DEFAULT__SYSTEM__RELEASE_NOTIFICATION__USE_HTTPS for system/release_notification/use_https
     * CONFIG__DEFAULT__ADOBE_IMS__INTEGRATION__API_KEY for adobe_ims/integration/api_key
     * CONFIG__DEFAULT__ADOBE_IMS__INTEGRATION__PRIVATE_KEY for adobe_ims/integration/private_key
     * CONFIG__DEFAULT__NEWRELICREPORTING__GENERAL__API_URL for newrelicreporting/general/api_url
     * CONFIG__DEFAULT__NEWRELICREPORTING__GENERAL__INSIGHTS_API_URL for newrelicreporting/general/insights_api_url
     * CONFIG__DEFAULT__NEWRELICREPORTING__GENERAL__ACCOUNT_ID for newrelicreporting/general/account_id
     * CONFIG__DEFAULT__NEWRELICREPORTING__GENERAL__APP_ID for newrelicreporting/general/app_id
     * CONFIG__DEFAULT__NEWRELICREPORTING__GENERAL__API for newrelicreporting/general/api
     * CONFIG__DEFAULT__NEWRELICREPORTING__GENERAL__INSIGHTS_INSERT_KEY for newrelicreporting/general/insights_insert_key
     * CONFIG__DEFAULT__ANALYTICS__GENERAL__TOKEN for analytics/general/token
     * CONFIG__DEFAULT__ANALYTICS__URL__SIGNUP for analytics/url/signup
     * CONFIG__DEFAULT__ANALYTICS__URL__UPDATE for analytics/url/update
     * CONFIG__DEFAULT__ANALYTICS__URL__BI_ESSENTIALS for analytics/url/bi_essentials
     * CONFIG__DEFAULT__ANALYTICS__URL__OTP for analytics/url/otp
     * CONFIG__DEFAULT__ANALYTICS__URL__REPORT for analytics/url/report
     * CONFIG__DEFAULT__ANALYTICS__URL__NOTIFY_DATA_CHANGED for analytics/url/notify_data_changed
     */
    'system' => [
        'default' => [
            'amasty_base' => [
                'notifications' => [
                    'frequency' => '5',
                    'type' => 'INFO,PROMO,INSTALLED_UPDATE,TIPS_TRICKS',
                    'ads_enable' => '1',
                ],
                'menu' => [
                    'enable' => '1',
                ],
                'licence_service' => [
                    'api_url' => 'https://irs.amasty.com',
                ],
                'system_value' => [
                    'first_module_run' => '1631835834',
                    'last_update' => '1721515555',
                    'remove_date' => '1721685151',
                ],
            ],
            'payment' => [
                'payflowpro' => [
                    'vendor' => null,
                    'buyer_country' => null,
                    'use_proxy' => '0',
                    'active' => '0',
                    'title' => 'Credit Card',
                    'sort_order' => null,
                    'payment_action' => 'Authorization',
                    'cctypes' => 'AE,VI',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'verify_peer' => '1',
                    'useccv' => '1',
                    'fmf' => '0',
                    'avs_street' => '0',
                    'avs_zip' => '0',
                    'avs_international' => '0',
                    'avs_security_code' => '1',
                    'model' => 'Magento\\Paypal\\Model\\Payflow\\Transparent',
                    'tender' => 'C',
                    'verbosity' => 'HIGH',
                    'group' => 'paypal',
                    'date_delim' => null,
                    'ccfields' => 'csc,expdate,acct',
                    'place_order_url' => 'paypal/transparent/requestSecureToken',
                    'cgi_url_test_mode' => 'https://pilot-payflowlink.paypal.com',
                    'cgi_url' => 'https://payflowlink.paypal.com',
                    'transaction_url_test_mode' => 'https://pilot-payflowpro.paypal.com',
                    'transaction_url' => 'https://payflowpro.paypal.com',
                    'cc_year_length' => '2',
                    'can_authorize_vault' => '1',
                    'can_capture_vault' => '1',
                    'avs_ems_adapter' => 'Magento\\Paypal\\Model\\Payflow\\AvsEmsCodeMapper',
                    'cvv_ems_adapter' => 'Magento\\Paypal\\Model\\Payflow\\CvvEmsCodeMapper',
                ],
                'payflowpro_cc_vault' => [
                    'active' => '0',
                    'title' => 'Stored Cards (Payflow Pro)',
                    'model' => 'PayflowProCreditCardVaultFacade',
                    'instant_purchase' => [
                        'tokenFormat' => '\\Magento\\Paypal\\Model\\InstantPurchase\\Payflow\\Pro\\TokenFormatter',
                    ],
                    'group' => 'paypal',
                ],
                'paypal_paylater' => [
                    'experience_active' => '1',
                    'enabled' => '0',
                    'homepage_display' => '0',
                    'homepage_position' => 'header',
                    'homepage_stylelayout' => 'flex',
                    'homepage_logotype' => 'primary',
                    'homepage_logoposition' => 'left',
                    'homepage_textcolor' => 'black',
                    'homepage_textsize' => '12',
                    'homepage_ratio' => '1x1',
                    'homepage_color' => 'blue',
                    'productpage_display' => '1',
                    'productpage_position' => 'header',
                    'productpage_stylelayout' => 'text',
                    'productpage_logotype' => 'primary',
                    'productpage_logoposition' => 'left',
                    'productpage_textcolor' => 'black',
                    'productpage_textsize' => '12',
                    'productpage_ratio' => '1x1',
                    'productpage_color' => 'blue',
                    'cartpage_display' => '0',
                    'cartpage_position' => 'header',
                    'cartpage_stylelayout' => 'text',
                    'cartpage_logotype' => 'primary',
                    'cartpage_logoposition' => 'left',
                    'cartpage_textcolor' => 'black',
                    'cartpage_textsize' => '12',
                    'cartpage_ratio' => '1x1',
                    'cartpage_color' => 'blue',
                    'checkout_payment_display' => '0',
                    'checkout_payment_position' => 'near_pp_button',
                    'checkout_payment_stylelayout' => 'text',
                    'checkout_payment_logotype' => 'primary',
                    'checkout_payment_logoposition' => 'left',
                    'checkout_payment_textcolor' => 'black',
                    'checkout_payment_textsize' => '12',
                    'checkout_payment_ratio' => '1x1',
                    'checkout_payment_color' => 'blue',
                    'categorypage_display' => '0',
                    'categorypage_position' => 'header',
                    'categorypage_stylelayout' => 'flex',
                    'categorypage_logotype' => 'primary',
                    'categorypage_logoposition' => 'left',
                    'categorypage_textcolor' => 'black',
                    'categorypage_textsize' => '12',
                    'categorypage_ratio' => '20x1',
                    'categorypage_color' => 'blue',
                ],
                'payflow_link' => [
                    'partner' => 'PayPal',
                    'vendor' => null,
                    'user' => null,
                    'buyer_country' => null,
                    'active' => '0',
                    'title' => 'Credit Card',
                    'sort_order' => null,
                    'payment_action' => 'Authorization',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'verify_peer' => '1',
                    'csc_editable' => '1',
                    'csc_required' => '1',
                    'email_confirmation' => '0',
                    'model' => 'Magento\\Paypal\\Model\\Payflowlink',
                    'verbosity' => 'HIGH',
                    'group' => 'paypal',
                    'transaction_url_test_mode' => 'https://pilot-payflowpro.paypal.com',
                    'transaction_url' => 'https://payflowpro.paypal.com',
                    'cgi_url_test_mode' => 'https://pilot-payflowlink.paypal.com',
                    'cgi_url' => 'https://payflowlink.paypal.com',
                ],
                'payflow_express' => [
                    'active' => '0',
                    'title' => 'PayPal Express Checkout Payflow Edition',
                    'sort_order' => null,
                    'payment_action' => 'Authorization',
                    'visible_on_product' => '1',
                    'visible_on_cart' => '1',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'verify_peer' => '1',
                    'line_items_enabled' => '1',
                    'group' => 'paypal',
                    'model' => 'Magento\\Paypal\\Model\\PayflowExpress',
                ],
                'payflow_express_bml' => [
                    'active' => '0',
                    'sort_order' => null,
                    'model' => 'Magento\\Paypal\\Model\\Payflow\\Bml',
                    'title' => 'PayPal Credit (Payflow Express Bml)',
                    'group' => 'paypal',
                ],
                'paypal_express_bml' => [
                    'homepage_display' => '0',
                    'homepage_position' => '0',
                    'homepage_size' => '190x100',
                    'categorypage_display' => '0',
                    'categorypage_position' => '0',
                    'categorypage_size' => '190x100',
                    'productpage_display' => '0',
                    'productpage_position' => '0',
                    'productpage_size' => '190x100',
                    'checkout_display' => '0',
                    'checkout_position' => '0',
                    'checkout_size' => '234x60',
                    'active' => '0',
                    'sort_order' => null,
                    'model' => 'Magento\\Paypal\\Model\\Bml',
                    'title' => 'PayPal Credit (Paypal Express Bml)',
                    'group' => 'paypal',
                ],
                'paypal_express' => [
                    'skip_order_review_step' => '1',
                    'buyer_country' => null,
                    'title' => 'PayPal Express Checkout',
                    'sort_order' => null,
                    'payment_action' => 'Authorization',
                    'visible_on_product' => '1',
                    'authorization_honor_period' => '3',
                    'order_valid_period' => '29',
                    'child_authorization_number' => '1',
                    'visible_on_cart' => '0',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'verify_peer' => '1',
                    'line_items_enabled' => '1',
                    'transfer_shipping_options' => '1',
                    'solution_type' => 'Sole',
                    'require_billing_address' => '1',
                    'allow_ba_signup' => 'never',
                    'active' => '1',
                    'in_context' => '1',
                    'model' => 'Magento\\Paypal\\Model\\Express',
                    'group' => 'paypal',
                    'supported_locales' => 'ar_EG,cs_CZ,da_DK,de_DE,el_GR,en_AU,en_GB,en_IN,en_US,es_ES,es_XC,fi_FI,fr_CA,fr_FR,fr_XC,he_IL,hu_HU,id_ID,it_IT,ja_JP,ko_KR,nl_NL,no_NO,pl_PL,pt_BR,pt_PT,ru_RU,sk_SK,sv_SE,th_TH,zh_CN,zh_HK,zh_TW,zh_XC',
                    'smart_buttons_supported_locales' => 'en_AD,fr_AD,es_AD,zh_AD,en_AE,fr_AE,es_AE,zh_AE,ar_AE,en_AG,fr_AG,es_AG,zh_AG,en_AI,fr_AI,es_AI,zh_AI,en_AL,en_AM,fr_AM,es_AM,zh_AM,en_AN,fr_AN,es_AN,zh_AN,en_AO,fr_AO,es_AO,zh_AO,es_AR,en_AR, de_AT,en_AT,en_AU,en_AW,fr_AW,es_AW,zh_AW,en_AZ,fr_AZ,es_AZ,zh_AZ,en_BA,en_BB,fr_BB,es_BB,zh_BB,en_BE,nl_BE,fr_BE,fr_BF,en_BF,es_BF,zh_BF,en_BG,ar_BH,en_BH,fr_BH,es_BH,zh_BH,fr_BI,en_BI,es_BI,zh_BI,fr_BJ,en_BJ,es_BJ,zh_BJ,en_BM,fr_BM,es_BM,zh_BM,en_BN,es_BO,en_BO,fr_BO,zh_BO,pt_BR,en_BR,en_BS,fr_BS,es_BS,zh_BS,en_BT,en_BW,fr_BW,es_BW,zh_BW,en_BY,en_BZ,es_BZ,fr_BZ,zh_BZ,en_CA,fr_CA,fr_CD,en_CD,es_CD,zh_CD,en_CG,fr_CG,es_CG,zh_CG,de_CH,fr_CH,en_CH,fr_CI,en_CI,en_CK,fr_CK,es_CK,zh_CK,es_CL,en_CL,fr_CL,zh_CL,fr_CM,en_CM,zh_CN,es_CO,en_CO,fr_CO,zh_CO,es_CR,en_CR,fr_CR,zh_CR,en_CV,fr_CV,es_CV,zh_CV,en_CY,cs_CZ,en_CZ,fr_CZ,es_CZ,zh_CZ,de_DE,en_DE,fr_DJ,en_DJ,es_DJ,zh_DJ,da_DK,en_DK,en_DM,fr_DM,es_DM,zh_DM,es_DO,en_DO,fr_DO,zh_DO,ar_DZ,en_DZ,fr_DZ,es_DZ,zh_DZ,es_EC,en_EC,fr_EC,zh_EC,en_EE,ru_EE,fr_EE,es_EE,zh_EE,ar_EG,en_EG,fr_EG,es_EG,zh_EG,en_ER,fr_ER,es_ER,zh_ER,es_ES,en_ES,en_ET,fr_ET,es_ET,zh_ET,fi_FI,en_FI,fr_FI,es_FI,zh_FI,en_FJ,fr_FJ,es_FJ,zh_FJ,en_FK,fr_FK,es_FK,zh_FK,en_FM,da_FO,en_FO,fr_FO,es_FO,zh_FO,fr_FR,en_FR,fr_GA,en_GA,es_GA,zh_GA,en_GB,en_GD,fr_GD,es_GD,zh_GD,en_GE,fr_GE,es_GE,zh_GE,en_GF,fr_GF,es_GF,zh_GF,en_GI,fr_GI,es_GI,zh_GI,da_GL,en_GL,fr_GL,es_GL,zh_GL,en_GM,fr_GM,es_GM,zh_GM,fr_GN,en_GN,es_GN,zh_GN,en_GP,fr_GP,es_GP,zh_GP,el_GR,en_GR,fr_GR,es_GR,zh_GR,es_GT,en_GT,fr_GT,zh_GT,en_GW,fr_GW,es_GW,zh_GW,en_GY,fr_GY,es_GY,zh_GY,en_HK,zh_HK,es_HN,en_HN,fr_HN,zh_HN,en_HR,hu_HU,en_HU,fr_HU,es_HU,zh_HU,id_ID,en_ID,en_IE,fr_IE,es_IE,zh_IE,he_IL,en_IL,en_IN,en_IS,it_IT,en_IT,en_JM,es_JM,fr_JM,zh_JM,ar_JO,en_JO,fr_JO,es_JO,zh_JO,ja_JP,en_JP,en_KE,fr_KE,es_KE,zh_KE,en_KG,fr_KG,es_KG,zh_KG,en_KH,en_KI,fr_KI,es_KI,zh_KI,fr_KM,en_KM,es_KM,zh_KM,en_KN,fr_KN,es_KN,zh_KN,ko_KR,en_KR,ar_KW,en_KW,fr_KW,es_KW,zh_KW,en_KY,fr_KY,es_KY,zh_KY,en_KZ,fr_KZ,es_KZ,zh_KZ,en_LA,en_LC,fr_LC,es_LC,zh_LC,en_LI,fr_LI,es_LI,zh_LI,en_LK,en_LS,fr_LS,es_LS,zh_LS,en_LT,ru_LT,fr_LT,es_LT,zh_LT,en_LU,de_LU,fr_LU,es_LU,zh_LU,en_LV,ru_LV,fr_LV,es_LV,zh_LV,ar_MA,en_MA,fr_MA,es_MA,zh_MA,fr_MC,en_MC,en_MD,en_ME,en_MG,fr_MG,es_MG,zh_MG,en_MH,fr_MH,es_MH,zh_MH,en_MK,fr_ML,en_ML,es_ML,zh_ML,en_MN,en_MQ,fr_MQ,es_MQ,zh_MQ,en_MR,fr_MR,es_MR,zh_MR,en_MS,fr_MS,es_MS,zh_MS,en_MT,en_MU,fr_MU,es_MU,zh_MU,en_MV,en_MW,fr_MW,es_MW,zh_MW,es_MX,en_MX,en_MY,en_MZ,fr_MZ,es_MZ,zh_MZ,en_NA,fr_NA,es_NA,zh_NA,en_NC,fr_NC,es_NC,zh_NC,fr_NE,en_NE,es_NE,zh_NE,en_NF,fr_NF,es_NF,zh_NF,en_NG,es_NI,en_NI,fr_NI,zh_NI,nl_NL,en_NL,no_NO,en_NO,en_NP,en_NR,fr_NR,es_NR,zh_NR,en_NU,fr_NU,es_NU,zh_NU,en_NZ,fr_NZ,es_NZ,zh_NZ,ar_OM,en_OM,fr_OM,es_OM,zh_OM,es_PA,en_PA,fr_PA,zh_PA,es_PE,en_PE,fr_PE,zh_PE,en_PF,fr_PF,es_PF,zh_PF,en_PG,fr_PG,es_PG,zh_PG,en_PH,pl_PL,en_PL,en_PM,fr_PM,es_PM,zh_PM,en_PN,fr_PN,es_PN,zh_PN,pt_PT,en_PT,en_PW,fr_PW,es_PW,zh_PW,es_PY,en_PY,en_QA,fr_QA,es_QA,zh_QA,ar_QA,en_RE,fr_RE,es_RE,zh_RE,en_RO,fr_RO,es_RO,zh_RO,en_RS,fr_RS,es_RS,zh_RS,ru_RU,en_RU,fr_RW,en_RW,es_RW,zh_RW,ar_SA,en_SA,fr_SA,es_SA,zh_SA,en_SB,fr_SB,es_SB,zh_SB,fr_SC,en_SC,es_SC,zh_SC,sv_SE,en_SE,en_SG,en_SH,fr_SH,es_SH,zh_SH,en_SI,fr_SI,es_SI,zh_SI,en_SJ,fr_SJ,es_SJ,zh_SJ,sk_SK,en_SK,fr_SK,es_SK,zh_SK,en_SL,fr_SL,es_SL,zh_SL,en_SM,fr_SM,es_SM,zh_SM,fr_SN,en_SN,es_SN,zh_SN,en_SO,fr_SO,es_SO,zh_SO,en_SR,fr_SR,es_SR,zh_SR,en_ST,fr_ST,es_ST,zh_ST,es_SV,en_SV,fr_SV,zh_SV,en_SZ,fr_SZ,es_SZ,zh_SZ,en_TC,fr_TC,es_TC,zh_TC,fr_TD,en_TD,es_TD,zh_TD,fr_TG,en_TG,es_TG,zh_TG,th_TH,en_TH,en_TJ,fr_TJ,es_TJ,zh_TJ,en_TM,fr_TM,es_TM,zh_TM,ar_TN,en_TN,fr_TN,es_TN,zh_TN,en_TO,tr_TR,en_TR,en_TT,fr_TT,es_TT,zh_TT,en_TV,fr_TV,es_TV,zh_TV,zh_TW,en_TW,en_TZ,fr_TZ,es_TZ,zh_TZ,en_UA,ru_UA,fr_UA,es_UA,zh_UA,en_UG,fr_UG,es_UG,zh_UG,en_US,fr_US,es_US,zh_US,es_UY,en_UY,fr_UY,zh_UY,en_VA,fr_VA,es_VA,zh_VA,en_VC,fr_VC,es_VC,zh_VC,es_VE,en_VE,fr_VE,zh_VE,en_VG,fr_VG,es_VG,zh_VG,en_VN,en_VU,fr_VU,es_VU,zh_VU,en_WF,fr_WF,es_WF,zh_WF,en_WS,ar_YE,en_YE,fr_YE,es_YE,zh_YE,en_YT,fr_YT,es_YT,zh_YT,en_ZA,fr_ZA,es_ZA,zh_ZA,en_ZM,fr_ZM,es_ZM,zh_ZM,en_ZW',
                    'client_id' => 'ATDZ9_ECFh-fudesZo4kz3fGTSO1pzuWCS4IjZMq4JKdRK7hQR3Rxyafx39H2fP363WtmlQNYXjUiAae',
                    'sandbox_client_id' => 'AUZfbDQ_4m8ibp82qV9pi9wxGkGrdGILVYWbWaTWreW9mmTm6LjQorLZxpP7kjymXc7flRnepHBFSQWp',
                ],
                'hosted_pro' => [
                    'active' => '0',
                    'title' => 'Payment by cards or by PayPal account',
                    'sort_order' => null,
                    'payment_action' => 'Authorization',
                    'display_ec' => '0',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'verify_peer' => '1',
                    'model' => 'Magento\\Paypal\\Model\\Hostedpro',
                    'group' => 'paypal',
                ],
                'paypal_billing_agreement' => [
                    'active' => '0',
                    'title' => 'PayPal Billing Agreement',
                    'sort_order' => null,
                    'payment_action' => 'Authorization',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'verify_peer' => '1',
                    'line_items_enabled' => '0',
                    'allow_billing_agreement_wizard' => '1',
                    'model' => 'Magento\\Paypal\\Model\\Method\\Agreement',
                    'group' => 'paypal',
                ],
                'braintree' => [
                    'title' => 'Credit Card',
                    'environment' => 'production',
                    'payment_action' => 'authorize_capture',
                    'sandbox_merchant_id' => null,
                    'sandbox_public_key' => null,
                    'sandbox_private_key' => null,
                    'active' => '1',
                    'fraudprotection' => '1',
                    'kount_skip_admin' => '0',
                    'kount_allowed_ips' => '208.75.112.0/22,209.81.12.0/24',
                    'fraudprotection_threshold' => null,
                    'debug' => '1',
                    'useccv' => '1',
                    'cctypes' => 'AE,VI,MC',
                    'sort_order' => '-2',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'countrycreditcard' => [],
                    'verify_3dsecure' => '1',
                    'threshold_amount' => '10',
                    'verify_all_countries' => '0',
                    'verify_specific_countries' => null,
                    'descriptor_name' => null,
                    'descriptor_phone' => null,
                    'descriptor_url' => null,
                    'model' => 'BraintreeFacade',
                    'is_gateway' => '1',
                    'can_use_checkout' => '1',
                    'can_authorize' => '1',
                    'can_capture' => '1',
                    'can_capture_partial' => '1',
                    'can_authorize_vault' => '1',
                    'can_capture_vault' => '1',
                    'can_use_internal' => '1',
                    'can_refund_partial_per_invoice' => '1',
                    'can_refund' => '1',
                    'can_void' => '1',
                    'can_cancel' => '1',
                    'can_edit' => '1',
                    'can_review_payment' => '1',
                    'can_deny_payment' => '1',
                    'cctypes_braintree_mapper' => '{"american-express":"AE","discover":"DI","jcb":"JCB","mastercard":"MC","master-card":"MC","visa":"VI","maestro":"MI","uk-maestro":"MI","diners-club":"DN"}',
                    'order_status' => 'processing',
                    'masked_fields' => 'cvv,number',
                    'privateInfoKeys' => 'avsPostalCodeResponseCode,avsStreetAddressResponseCode,cvvResponseCode,processorAuthorizationCode,processorResponseCode,processorResponseText,liabilityShifted,liabilityShiftPossible,eciFlag,riskDataId,riskDataDecision,transactionSource',
                    'paymentInfoKeys' => 'cc_type,cc_number,avsPostalCodeResponseCode,avsStreetAddressResponseCode,cvvResponseCode,processorAuthorizationCode,processorResponseCode,processorResponseText,liabilityShifted,liabilityShiftPossible,eciFlag,riskDataId,riskDataDecision,transactionSource',
                    'group' => 'braintree_group',
                ],
                'braintree_section' => [
                    'braintree' => [
                        'braintree_required' => [
                            'key_validation' => null,
                        ],
                        'braintree_advanced' => [
                            'kount' => [
                                'kount_ens_url' => null,
                            ],
                        ],
                    ],
                ],
                'braintree_paypal' => [
                    'active' => '1',
                    'title' => 'Express Checkout',
                    'sort_order' => '-1',
                    'payment_action' => 'authorize_capture',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'require_billing_address' => '0',
                    'debug' => '0',
                    'display_on_shopping_cart' => '0',
                    'button_paylater_cart_enable' => '0',
                    'message_cart_enable' => '0',
                    'disabled_funding_cart' => 'card',
                    'button_customise_cart' => '0',
                    'button_shape_cart' => '0',
                    'button_size_cart' => '2',
                    'button_color_cart' => '2',
                    'button_paylater_checkout_enable' => '1',
                    'message_checkout_enable' => '1',
                    'disabled_funding_checkout' => 'card',
                    'button_customise_checkout' => '1',
                    'button_shape_checkout' => '1',
                    'button_size_checkout' => '0',
                    'button_color_checkout' => '3',
                    'button_productpage_enabled' => '0',
                    'button_paylater_productpage_enable' => '0',
                    'message_productpage_enable' => '0',
                    'disabled_funding_productpage' => 'card',
                    'button_customise_productpage' => '1',
                    'button_shape_productpage' => '0',
                    'button_size_productpage' => '2',
                    'button_color_productpage' => '2',
                    'model' => 'BraintreePayPalFacade',
                    'allow_shipping_address_override' => '1',
                    'order_status' => 'processing',
                    'is_gateway' => '1',
                    'can_use_checkout' => '1',
                    'can_authorize' => '1',
                    'can_capture' => '1',
                    'can_capture_partial' => '1',
                    'can_refund' => '1',
                    'can_refund_partial_per_invoice' => '1',
                    'can_void' => '1',
                    'can_cancel' => '1',
                    'can_authorize_vault' => '1',
                    'can_capture_vault' => '1',
                    'privateInfoKeys' => 'processorResponseCode,processorResponseText,paymentId',
                    'paymentInfoKeys' => 'processorResponseCode,processorResponseText,paymentId,payerEmail',
                    'button_productpage_enable' => '1',
                    'button_cart_enabled' => '1',
                    'message_cart_enabled' => '1',
                    'group' => 'braintree_group',
                    'skip_order_review' => '0',
                ],
                'braintree_paypal_credit' => [
                    'active' => '0',
                    'client_id' => null,
                    'secret' => null,
                    'sort_order' => null,
                    'uk_activation_code' => null,
                    'uk_merchant_name' => null,
                ],
                'braintree_paypal_paylater' => [
                    'active' => '1',
                ],
                'braintree_cc_vault' => [
                    'active' => '1',
                    'cvv' => '1',
                    'title' => 'Stored Cards (Braintree)',
                    'model' => 'BraintreeCreditCardVaultFacade',
                    'instant_purchase' => [
                        'available' => 'PayPal\\Braintree\\Model\\InstantPurchase\\CreditCard\\AvailabilityChecker',
                        'tokenFormat' => 'PayPal\\Braintree\\Model\\InstantPurchase\\CreditCard\\TokenFormatter',
                        'additionalInformation' => 'PayPal\\Braintree\\Model\\InstantPurchase\\PaymentAdditionalInformationProvider',
                    ],
                    'group' => 'braintree_group',
                ],
                'braintree_ach_direct_debit' => [
                    'active' => '0',
                    'sort_order' => null,
                    'can_authorize' => '1',
                    'can_cancel' => '1',
                    'can_capture' => '1',
                    'can_capture_partial' => '0',
                    'can_refund' => '1',
                    'can_refund_partial_per_invoice' => '0',
                    'can_use_checkout' => '1',
                    'can_void' => '1',
                    'is_gateway' => '1',
                    'model' => 'BraintreeAch',
                    'order_status' => 'processing',
                    'payment_action' => 'authorize_capture',
                    'title' => 'ACH Direct Debit',
                    'privateInfoKeys' => 'processorAuthorizationCode,processorResponseCode,processorResponseText',
                    'paymentInfoKeys' => 'processorAuthorizationCode,processorResponseCode,processorResponseText',
                    'group' => 'braintree_group',
                ],
                'braintree_applepay' => [
                    'active' => '1',
                    'payment_action' => 'authorize_capture',
                    'merchant_name' => 'Store',
                    'sort_order' => null,
                    'model' => 'BraintreeApplePay',
                    'title' => 'Apple Pay',
                    'allowspecific' => '0',
                    'require_billing_address' => '0',
                    'allow_shipping_address_override' => '0',
                    'display_on_shopping_cart' => '0',
                    'order_status' => 'processing',
                    'is_gateway' => '1',
                    'can_use_checkout' => '1',
                    'can_authorize' => '1',
                    'can_capture' => '1',
                    'can_capture_partial' => '1',
                    'can_refund' => '1',
                    'can_refund_partial_per_invoice' => '1',
                    'can_void' => '1',
                    'can_cancel' => '1',
                    'privateInfoKeys' => 'processorAuthorizationCode,processorResponseCode,processorResponseText',
                    'paymentInfoKeys' => 'processorAuthorizationCode,processorResponseCode,processorResponseText',
                    'group' => 'braintree_group',
                ],
                'braintree_local_payment' => [
                    'active' => '0',
                    'title' => 'Local Payments',
                    'allowed_methods' => 'bancontact,eps,giropay,ideal,sofort,mybank,p24,sepa',
                    'sort_order' => null,
                    'can_authorize' => '1',
                    'can_cancel' => '1',
                    'can_capture' => '1',
                    'can_capture_partial' => '0',
                    'can_refund' => '1',
                    'can_refund_partial_per_invoice' => '0',
                    'can_use_checkout' => '1',
                    'can_void' => '1',
                    'is_gateway' => '1',
                    'model' => 'BraintreeLpm',
                    'order_status' => 'processing',
                    'payment_action' => 'authorize_capture',
                    'paymentInfoKeys' => 'processorAuthorizationCode,processorResponseCode,processorResponseText',
                    'privateInfoKeys' => 'processorAuthorizationCode,processorResponseCode,processorResponseText',
                    'group' => 'braintree_group',
                ],
                'braintree_googlepay' => [
                    'active' => '0',
                    'payment_action' => 'authorize_capture',
                    'btn_color' => '1',
                    'merchant_id' => 'BCR2DN6TTPSKLFQD',
                    'cctypes' => 'VISA,MASTERCARD,AMEX',
                    'sort_order' => null,
                    'model' => 'BraintreeGooglePay',
                    'title' => 'Google Pay',
                    'allowspecific' => '0',
                    'require_billing_address' => '0',
                    'allow_shipping_address_override' => '0',
                    'display_on_shopping_cart' => '0',
                    'order_status' => 'processing',
                    'is_gateway' => '1',
                    'can_use_checkout' => '1',
                    'can_authorize' => '1',
                    'can_capture' => '1',
                    'can_capture_partial' => '1',
                    'can_refund' => '1',
                    'can_refund_partial_per_invoice' => '1',
                    'can_void' => '1',
                    'can_cancel' => '1',
                    'privateInfoKeys' => 'processorAuthorizationCode,processorResponseCode,processorResponseText',
                    'paymentInfoKeys' => 'processorAuthorizationCode,processorResponseCode,processorResponseText',
                    'group' => 'braintree_group',
                ],
                'braintree_venmo' => [
                    'active' => '0',
                    'payment_action' => 'authorize',
                    'sort_order' => null,
                    'can_authorize' => '1',
                    'can_cancel' => '1',
                    'can_capture' => '1',
                    'can_capture_partial' => '0',
                    'can_refund' => '1',
                    'can_refund_partial_per_invoice' => '0',
                    'can_use_checkout' => '1',
                    'can_void' => '1',
                    'is_gateway' => '1',
                    'model' => 'BraintreeVenmo',
                    'order_status' => 'processing',
                    'title' => 'Venmo',
                    'privateInfoKeys' => 'processorAuthorizationCode,processorResponseCode,processorResponseText',
                    'paymentInfoKeys' => 'processorAuthorizationCode,processorResponseCode,processorResponseText',
                    'group' => 'braintree_group',
                ],
                'braintree_paypal_vault' => [
                    'active' => '0',
                    'model' => 'BraintreePayPalVaultFacade',
                    'title' => 'Stored Accounts (PayPal)',
                    'can_use_internal' => '1',
                    'instant_purchase' => [
                        'tokenFormat' => 'PayPal\\Braintree\\Model\\InstantPurchase\\PayPal\\TokenFormatter',
                        'additionalInformation' => 'PayPal\\Braintree\\Model\\InstantPurchase\\PaymentAdditionalInformationProvider',
                    ],
                    'group' => 'braintree_group',
                ],
                'zippayment' => [
                    'active' => '1',
                    'title' => 'Zip - Own it now, pay later',
                    'environment' => 'production',
                    'merchant_public_key' => '1:3:qHCfeoJxgtF45DJX5MtmECqlv5It5uXZBRyhiPrhrL4brCp+TKVUzC3NjMkSa+ZoUOZ+gaB+V8IIHxmgqpZbrQ==',
                    'merchant_private_key' => '1:3:hxNeUChzZbfhvoxG8vPC9VrgAojwVeGE2/do84nhxwynxjw71ZfV/0p14coA/oldw1M4hjq9j/WKRvv1jpraSHeHuX17ZM7M',
                    'check_validity' => null,
                    'widget_region' => 'au',
                    'enable_tokenisation' => '0',
                    'payment_action' => 'capture',
                    'log_settings' => '100',
                    'display_widget_mode' => 'iframe',
                    'incontext_checkout' => '0',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'min_order_total' => '1',
                    'max_order_total' => '7000',
                    'sort_order' => '0',
                    'zip_advert' => [
                        'homepage' => [
                            'banner' => '0',
                            'banner_selector' => null,
                        ],
                        'productpage' => [
                            'banner' => '0',
                            'banner_selector' => null,
                            'widget' => '1',
                            'widget_selector' => null,
                            'tagline' => '0',
                            'tagline_selector' => null,
                        ],
                        'cartpage' => [
                            'banner' => '0',
                            'banner_selector' => null,
                            'widget' => '1',
                            'widget_selector' => null,
                            'tagline' => '0',
                            'tagline_selector' => null,
                        ],
                        'categorypage' => [
                            'banner' => '0',
                            'banner_selector' => null,
                        ],
                    ],
                    'debug' => '1',
                    'model' => 'ZipMoneyGatewayFacade',
                    'order_status' => 'processing',
                    'can_initialize' => '1',
                    'currency' => 'AUD',
                    'can_authorize' => '1',
                    'can_capture' => '1',
                    'can_refund' => '1',
                    'can_capture_partial' => '1',
                    'can_refund_partial_per_invoice' => '1',
                    'can_void' => '1',
                    'can_cancel' => '1',
                    'can_use_checkout' => '1',
                    'is_gateway' => '1',
                    'can_use_for_multishipping' => '0',
                    'paymentInfoKeys' => 'receipt_number',
                    'privateInfoKeys' => 'receipt_number',
                    'zip_messages' => [
                        'error_header' => 'An error has occurred!',
                        'error_body' => 'An error occurred while processing your request.',
                        'referred_header' => 'Your application has been referred!',
                        'referred_body' => 'Your application is currently under review by zipMoney and will be processed very
                        shortly. You can contact the customer care at customercare@zipmoney.com.au for any enquiries.
                    ',
                    ],
                ],
                'free' => [
                    'model' => 'Magento\\Payment\\Model\\Method\\Free',
                    'active' => '1',
                    'title' => 'Gift Card',
                    'order_status' => 'pending',
                    'payment_action' => 'authorize_capture',
                    'allowspecific' => '1',
                    'specificcountry' => 'AU,NZ',
                    'sort_order' => '0',
                    'group' => 'offline',
                ],
                'checkmo' => [
                    'model' => 'Magento\\OfflinePayments\\Model\\Checkmo',
                    'active' => '1',
                    'title' => 'Check / Money order',
                    'order_status' => 'pending',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'payable_to' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'group' => 'offline',
                ],
                'banktransfer' => [
                    'active' => '1',
                    'title' => 'Bank Transfer',
                    'order_status' => 'pending',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'instructions' => 'Our Bank details:
Account Name: DigiDirect
Bank Name: Westpac
BSB: 032-051
Account Number: 602008',
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => '7',
                    'model' => 'Magento\\OfflinePayments\\Model\\Banktransfer',
                    'group' => 'offline',
                ],
                'cashondelivery' => [
                    'active' => '0',
                    'title' => 'Cash On Delivery',
                    'order_status' => 'pending',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'model' => 'Magento\\OfflinePayments\\Model\\Cashondelivery',
                    'group' => 'offline',
                ],
                'latitude_section' => [
                    'version' => null,
                ],
                'latitude' => [
                    'active' => '1',
                    'merchant_id' => '000420664',
                    'store_merchant_id' => null,
                    'merchant_secret' => '0:3:5+95QlBEBV+hPNXRva3cm5UaUWC9t7JTnM6fzlzIUB799uaVyfRVbjCVdzpLZaEqpN8TNBgsNR+SJJ+v1KyvnyUnmQ==',
                    'test_mode' => '0',
                    'debug_mode' => '0',
                    'show_on_pdp' => '0',
                    'show_on_cart' => '0',
                    'minimum_amount' => '1000',
                    'layout' => 'standard',
                    'plan_type' => 'equal',
                    'plan_period' => '36',
                    'model' => 'LatitudeNew\\Payment\\Model\\Latitude',
                    'order_status' => 'pending_latitude_approval',
                    'payment_action' => 'sale',
                    'title' => 'Latitude Interest Free',
                    'allowspecific' => '1',
                    'specificcountry' => 'AU,NZ',
                    'api_url_production' => 'https://api.latitudefinancial.com/v1/applybuy-checkout-service',
                    'api_url_sandbox' => 'https://api.test.latitudefinancial.com/v1/applybuy-checkout-service',
                    'callback_url' => 'latitudepay/lcorder/callback',
                    'cancel_url' => 'latitudepay/lcorder/callback',
                    'instructions' => 'Latitude Checkout',
                    'allowspecificcurrency' => '1',
                    'currency' => 'AUD,NZD',
                    'can_authorize' => '1',
                    'can_capture' => '1',
                    'can_void' => '1',
                    'can_use_checkout' => '1',
                    'is_gateway' => '1',
                ],
                'latitudepay' => [
                    'active' => '0',
                    'payment_services' => 'LPAY',
                    'payment_terms' => null,
                    'environment' => 'production',
                    'client_key' => null,
                    'client_secret' => null,
                    'show_on_pdp' => '0',
                    'show_on_cart' => '0',
                    'sort_order' => '3',
                    'allowspecific' => '0',
                    'specificcountry' => 'AU,NZ',
                    'logging' => '1',
                    'order_status' => 'pending_latitude_approval',
                    'title' => 'LatitudePay',
                    'version' => '3.0.9',
                    'can_use_checkout' => '1',
                    'payment_action' => 'true',
                    'line_items_enabled' => '1',
                    'group' => 'installment',
                    'currency' => 'AUD',
                    'installment_no' => '10',
                    'image_api_url' => 'https://images.latitudepayapps.com/v2',
                    'can_capture' => '1',
                    'can_refund' => '1',
                    'can_refund_partial_per_invoice' => '1',
                    'success_url' => 'latitudepay/order/callback',
                    'callback_url' => 'latitudepay/order/callback',
                    'fail_url' => 'latitudepay/order/callback',
                    'model' => 'LatitudeNew\\Payment\\Model\\Latitudepay',
                    'api_url_production' => 'https://api.latitudepay.com/v3',
                    'api_url_sandbox' => 'https://api.uat.latitudepay.com/v3',
                    'content_type' => 'application/com.latitudepay.ecom-v3.1+json',
                    'instructions' => 'LatitudePay Checkout',
                ],
                'genoapay' => [
                    'active' => '0',
                    'environment' => 'production',
                    'client_key' => null,
                    'client_secret' => null,
                    'show_on_pdp' => '1',
                    'show_on_cart' => '1',
                    'sort_order' => '3',
                    'allowspecific' => '0',
                    'specificcountry' => 'AU,NZ',
                    'logging' => '1',
                    'order_status' => 'pending_latitude_approval',
                    'title' => 'Genoapay',
                    'can_use_checkout' => '1',
                    'payment_action' => 'true',
                    'line_items_enabled' => '1',
                    'group' => 'installment',
                    'currency' => 'NZD',
                    'installment_no' => '10',
                    'image_api_url' => 'https://images.latitudepayapps.com/v2',
                    'can_capture' => '1',
                    'can_refund' => '1',
                    'can_refund_partial_per_invoice' => '1',
                    'success_url' => 'latitudepay/order/callback',
                    'callback_url' => 'latitudepay/order/callback',
                    'fail_url' => 'latitudepay/order/callback',
                    'model' => 'LatitudeNew\\Payment\\Model\\Genoapay',
                    'api_url_production' => 'https://api.genoapay.com/v3',
                    'api_url_sandbox' => 'https://api.uat.genoapay.com/v3',
                    'content_type' => 'application/com.genoapay.ecom-v3.1+json',
                    'instructions' => 'Genoapay Checkout',
                ],
                'purchaseorder' => [
                    'model' => 'Magento\\OfflinePayments\\Model\\Purchaseorder',
                    'active' => '0',
                    'title' => 'Purchase Order',
                    'order_status' => 'pending',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'group' => 'offline',
                ],
                'latipay' => [
                    'active' => '1',
                    'title' => 'Latipay',
                    'environment' => 'production',
                    'nzd_wallet_id' => null,
                    'aud_wallet_id' => 'W000046777',
                    'cny_wallet_id' => null,
                    'user_id' => '1:3:76w83/hlkZg58tJ7aHn5wrlrEUcVqQ76kgoCLN0EPslkHlpy3E0=',
                    'api_key' => '1:3:5Wq2yqj4sIEtEDhQeVp7dSynIiDLYUkh558KyymWaiE3TdW0awISXjQ7kvzqTQ==',
                    'tooltip' => null,
                    'instructions' => null,
                    'sort_order' => null,
                    'is_spotpay' => '0',
                    'is_debug' => '0',
                    'model' => 'Magento5\\Latipay\\Model\\Latipay',
                    'service_provider' => 'latipay_paisa',
                    'production_url' => 'https://api.latipay.net/v2/transaction',
                    'sandbox_url' => 'https://api-staging.latipay.net/v2/transaction',
                    'redirect_url' => 'latipay/standard/redirect',
                    'return_url' => 'latipay/standard/response',
                    'callback_url' => 'latipay/standard/callback',
                    'about_url' => 'https://www.latipay.net',
                ],
                'payflow_advanced' => [
                    'partner' => 'PayPal',
                    'vendor' => 'PayPal',
                    'buyer_country' => null,
                    'use_proxy' => '0',
                    'active' => '0',
                    'title' => 'Credit Card',
                    'sort_order' => null,
                    'payment_action' => 'Authorization',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'verify_peer' => '1',
                    'csc_editable' => '1',
                    'csc_required' => '1',
                    'email_confirmation' => '0',
                    'model' => 'Magento\\Paypal\\Model\\Payflowadvanced',
                    'verbosity' => 'HIGH',
                    'group' => 'paypal',
                    'transaction_url_test_mode' => 'https://pilot-payflowpro.paypal.com',
                    'transaction_url' => 'https://payflowpro.paypal.com',
                    'cgi_url_test_mode' => 'https://pilot-payflowlink.paypal.com',
                    'cgi_url' => 'https://payflowlink.paypal.com',
                ],
                'paypal_payment_pro' => [
                    'active' => '0',
                ],
                'wps_express' => [
                    'active' => '0',
                ],
                'wps_express_bml' => [
                    'active' => '0',
                ],
                'substitution' => [
                    'active' => '0',
                    'model' => 'Magento\\Payment\\Model\\Method\\Substitution',
                    'allowspecific' => '0',
                ],
                'vault' => [
                    'debug' => '1',
                    'model' => 'Magento\\Vault\\Model\\VaultPaymentInterface',
                ],
                'amazon_payments' => [
                    'simplepath' => [
                        'publickey' => '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAno8LwO9bfPD6XFPZmYdK
y10kiCXQogomqnzby1Kb7akBbxNRBY/QVvo0/A7bfnDydMb1OkWMEB1Lsfy3lDVA
fK+MyK0ppZbh8qUiFjCvKJBfQ37WrShg9WVlpifuci3TldoHcaDoWxGls/VOgbay
aoCHgc2bZS8D3tcGFgZ4HZ7RlwGx4Y2iCO54dU857de75XRb4gwvEjaungwkZKdp
sZhdZcDi49nU7GbxTUdDv0ViMAtfDGxUQwoBaGnCAvX9YV8b3yHDoliKaPb6CXo9
V389w35vFSPB3OlIl0OB/JHu6KrfYktgd94ZlZOqqFWD2OynLvJIa5wjeY+bG+Sq
BQIDAQAB
-----END PUBLIC KEY-----',
                        'privatekey' => '0:3:y8GE+8Z/wDMi20DpXpgAxLuGE1oNTKsVs3iIKkfPMQmeY/CPMCVYGU+RpJqKgq367mE97x/WA65vgT6ZNKF5lKdwDChlr+/bncIE1ombPFog/Q/l0GJr6LSTtYDaEpEn1KSg9LIJj/I9byYBAtX0MYjQb2ee7zwG4Af44spkoDMi+Akk5zHlrEIDfjdT5EJEmDF2ybKpL5CM2g7vXYHuDM300sh4yyENxxS2A402n+XXalHmWNqNPEGZxzRmt5LsvqWdxWUmRtPfo89voFpTEOoxgf9TEzMBxm1/JtxLgrCasXVnVmrt7sBKCIBWo2W1OORO9Tw51tMiJ8u2RztH+rS4TUvmeXJqux/x/t7imnJSBppwdDMBl3xuaun5o9szg0jou0+suhzyGL6ZbQvMZwK7MUdxoxEdSIR6NtRiKocE/bCOwqg5GnhwIqYEbzEfnaKgEXD+Kk1+sd8CPI/Tj0jx+xK5PEXxSBX+HVbit/OwOtr4b562WSm4MDwsfCrnFTldJFx9ZO9jgNNBWbZGj/1TQPIFyPc/v2lST8+mEVoiC1YZ2eOJvYndZKSdsFR18OHty1tcbYegk0B6SYnXDRL1VjEsG0fOoar/AQGbW9ykvuX7p8/so+I1ZzeE2AgO3Gn6XseMtCJ2nOum6gK8eRlMFGigp1qVYScaGYkDmrFE1SDSA2kaYhX0E7Fwokt+VTnUK+OFPBaSyIFRpp8O5R32Ygq/E7nQlTfo3fxJDxaQZXGhVnu1dUYui4hYJnMyCk2dRAp5T2STVyBb5PwVhlLXk1T2R1aZWeeaN/BZyY33fp1ydpQQ4IWxPyfTFayJEyiHuEZIeLQI0AYOWjW2hG0Im7AynCz9ZMrA3z0qRSN95voBC5rJ4P0qTSY8DrfLQDsLJ0Iwzo/7hR4UV7ewz+WZCsWiiFFI9BEvyYe7WH+U7dxpPK/3re3wEXkbCdVG8I7hJxW7JREWujuTMXg07XMCvfbWL2y3Qsn0nq89dfdeOouVzRVSHbSshY7qjcY0cAcLL3YYQMyTxik03YrBKUbSijTpzFkK8T2TsrxDaGSj7g2S3yELw6r1xwIMV5KGlmxhJYJX9621qXwJcMCJG8uwuVoMZfz35nyKn/vQBCNUXu6W2g2t2B5ZGqlq/zHy9Kv0L9x67A+5kMmHWxGonsVwSTqk4DpjBERYxow7ZDDyP7QrB2OC3rDT61VV4DKUwf800WPSDxZfWLUx0ztC0Z5YJRiWQRJa7b+EX0AVGIAG1f+SYpFNu0zLiRnN1HdE+zyyKbd6haGc8ahRoGdT7iuMoMlOZdfps29LIE/B5dpko5VsS/k80f9sGsM0Hpcl7g6O80iSkHW8HxnsVnoVfG27DV7plWkF7oG39opFOPj1b4sDND8WT+MQG8k4r3YeIb84w75an3gH9ki9ZWVkxFYT1Aryn9a++jcTfdAUNZZDNwmduUYb6j9JLfvj+55d85xrrNRyUA+rgjHMNCDO4YyP8KEj/oHZa1n+p3r6io07pTKqwoapTMzNoQZmHuTnKFq5Zuee/IhqW57UrQmWCJA16gFvujWMEaM+m3HHdKO6bP6R1VveGpfYgxo7l13T9V40xCq7rwb8FW7KRbHiFTwafd/DlEbcPQqE3X/oeHKAzvhGBjrCZtv2tI6jR3BvhtxoR8COF4ksRO8eJuB/JcPmKXaVtPZaNTjFFz9fWu4s4rpBDO80aLPo+ftexF5dMl7af05fgfTew/WoX7EdWrLbn6+wQ1SAcBc70F0ubI4iFaO/DwzFc8E1XQzEWfN+YbHwnlTevTiI59Dbc0EryntIifbpeaiSNImDCEFF0f/c1pHZj451PuqciOO3kruo4W+XStB/IexS5Esgb6rYW+j1gaLl8PpWB+Ioyy3snR8qgl4tijdatagAZgYmioxMFokxnesx6z8ESAAWJN9m7vlmPrTq9IxRvnpqFm84aiOFSvYSPfVDP+CeVVr/jWUs6J4tpklr/5xQRz7tJMVIxPy9LWuNf/LlqqML37krR6MyFHTS8q6EzDOeGYxTCpkGulXvXvvq9sMpDm9b1CUtacLIYTM6P/9GgCv6cWrRa228Yn6f4VugYMpqfl3q4V9R5S/Jf39j9cVbMV7GUXMsnevRKpSzg3ZgjBT2NObpK5moCifpwa/yXanHYONo5b32gWVQUqO32Zk6rvsUSGRSnPqaqjZbiwCTQQM3DZX8/Jx5LfXtHTdtDMlSasLvU8RJdieRtHkzMd9Dx3tBidsjFgWWLD0eyZDRZV52+2m02FKlkB7HBUptmSpCZRm6JSta2mpa7A==',
                    ],
                ],
                'klarna_kp' => [
                    'active' => '0',
                    'allowspecific' => '0',
                    'data_sharing' => '0',
                    'sort_order' => '4',
                    'enable_b2b' => '1',
                    'data_sharing_onload' => '1',
                ],
                'amazon_payment' => [
                    'merchant_id' => null,
                    'access_key' => null,
                    'secret_key' => null,
                    'client_id' => null,
                    'client_secret' => null,
                    'credentials_json' => null,
                    'payment_region' => null,
                    'sandbox' => '0',
                    'active' => '0',
                    'lwa_enabled' => '0',
                    'payment_action' => 'authorize',
                    'authorization_mode' => 'synchronous',
                    'update_mechanism' => 'polling',
                    'button_display_language' => null,
                    'button_color' => 'Gold',
                    'button_size' => 'medium',
                    'amazon_login_in_popup' => '1',
                    'pwa_pp_button_is_visible' => '1',
                    'minicart_button_is_visible' => '1',
                    'storename' => null,
                    'logging' => '1',
                    'allowed_ips' => null,
                    'multicurrency' => '0',
                ],
                'amazonlogin' => [
                    'active' => '0',
                ],
                'cybersource' => [
                    'active' => '0',
                    'payment_action' => 'authorize',
                    'title' => 'Credit Card (Cybersource)',
                    'merchant_id' => null,
                    'transaction_key' => null,
                    'profile_id' => null,
                    'access_key' => null,
                    'secret_key' => null,
                    'order_status' => 'processing',
                    'sandbox_flag' => '1',
                    'debug' => '0',
                    'cctypes' => 'AE,VI,MC,DI,JCB,DN,MI,MD',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'is_multidomain' => '0',
                ],
                'authorizenet_directpost' => [
                    'login' => null,
                    'trans_key' => null,
                    'trans_md5' => null,
                    'merchant_email' => null,
                    'useccv' => '0',
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'active' => '0',
                    'payment_action' => 'authorize',
                    'title' => 'Credit Card Direct Post (Authorize.Net)',
                    'signature_key' => null,
                    'order_status' => 'processing',
                    'test' => '1',
                    'cgi_url' => 'https://secure.authorize.net/gateway/transact.dll',
                    'cgi_url_td' => 'https://api2.authorize.net/xml/v1/request.api',
                    'currency' => 'USD',
                    'debug' => '0',
                    'email_customer' => '0',
                    'cctypes' => 'AE,VI,MC,DI,JCB,DN',
                    'allowspecific' => '0',
                ],
                'worldpay' => [
                    'active' => '0',
                    'title' => 'Payment method (Worldpay)',
                    'installation_id' => null,
                    'response_password' => null,
                    'admin_installation_id' => null,
                    'auth_password' => null,
                    'md5_secret' => null,
                    'fix_contact' => '1',
                    'hide_contact' => '0',
                    'signature_fields' => 'instId:cartId:amount:currency',
                    'debug' => '1',
                    'sandbox_flag' => '1',
                    'test_action' => 'AUTHORISED',
                    'payment_action' => 'authorize',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'cvv_fraud_case' => null,
                    'avs_fraud_case' => null,
                    'sort_order' => '99',
                ],
                'eway' => [
                    'active' => '0',
                    'connection_type' => 'direct',
                    'title' => 'Credit Card (eWAY)',
                    'sandbox_flag' => '1',
                    'live_api_key' => null,
                    'live_api_password' => null,
                    'live_encryption_key' => null,
                    'sandbox_api_key' => null,
                    'sandbox_api_password' => null,
                    'sandbox_encryption_key' => null,
                    'payment_action' => 'authorize',
                    'debug' => '1',
                    'cctypes' => 'AE,VI,MC,JCB,DN',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'sort_order' => null,
                ],
                'authorizenet_acceptjs' => [
                    'cctypes' => 'AE,VI,MC,DI,JCB,DN',
                    'order_status' => 'processing',
                    'payment_action' => 'authorize',
                    'currency' => 'USD',
                    'active' => '0',
                    'title' => 'Credit Card (Authorize.Net)',
                    'environment' => 'production',
                    'login' => null,
                    'trans_key' => null,
                    'public_client_key' => null,
                    'trans_signature_key' => null,
                    'trans_md5' => null,
                    'debug' => '0',
                    'email_customer' => '0',
                    'cvv_enabled' => '1',
                    'allowspecific' => '0',
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'zipmoneypayment' => [
                    'title' => 'Zip - Own it now, pay later',
                    'active' => '1',
                    'merchant_private_key' => '0:3:dUR78llLY749JdTcTbqCUgf+mJhz0l3vOxWKq97eoeQ18f5SK9ZBdJBFad7teW7UD0IJusykCnBvCrXrDk/Tk+d25BXu8MFD',
                    'merchant_public_key' => '0:3:xD0HYtVNFe8sUPrenEvRA1EDVTLjlDW51lKGJkn6CKkgihYjmm1fRLFdn0HMmDNzz9WjmanutxbrL3YPCc0KSw==',
                    'payment_action' => 'capture',
                    'log_settings' => '100',
                    'environment' => 'production',
                    'incontext_checkout' => '0',
                    'min_order_total' => '1',
                    'sort_order' => '5',
                    'zipmoney_advert' => [
                        'homepage' => [
                            'banner' => '0',
                        ],
                        'productpage' => [
                            'banner' => '0',
                            'widget' => '0',
                            'tagline' => '0',
                        ],
                        'cartpage' => [
                            'banner' => '0',
                            'widget' => '0',
                            'tagline' => '0',
                        ],
                        'categorypage' => [
                            'banner' => '0',
                        ],
                    ],
                    'allowspecific' => '0',
                    'specificcountry' => null,
                ],
                'm2epropayment' => [
                    'active' => '1',
                    'title' => 'M2E Pro Payment',
                ],
                'paybympcatch' => [
                    'active' => '1',
                    'title' => 'Catch Payment Method(Default)',
                    'order_status' => 'processing',
                ],
                'klarna_kco' => [
                    'active' => '0',
                    'order_status' => 'processing',
                    'disable_customer_group' => null,
                    'external_payment_methods' => null,
                    'shipping_and_billing_countries_same' => '1',
                ],
                'klarna_kss' => [
                    'enabled' => '0',
                ],
            ],
            'payment_all_paypal' => [
                'paypal_payflowpro' => [
                    'settings_paypal_payflow' => [
                        'settings_paypal_payflow_advanced' => [
                            'paypal_payflow_avs_check' => [
                                'heading_avs_settings' => null,
                            ],
                            'paypal_payflow_settlement_report' => [
                                'heading_schedule' => null,
                            ],
                        ],
                    ],
                ],
                'payflow_link' => [
                    'payflow_link_required' => [
                        'payflow_link_payflow_link' => [
                            'payflowlink_info' => null,
                        ],
                        'payflow_link_advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'settings_payflow_link' => [
                        'settings_payflow_link_advanced' => [
                            'payflow_link_settlement_report' => [
                                'heading_schedule' => null,
                            ],
                            'payflow_link_frontend' => [
                                'paypal_pages' => null,
                            ],
                        ],
                    ],
                ],
                'payments_pro_hosted_solution' => [
                    'pphs_required_settings' => [
                        'pphs_required_settings_pphs' => [
                            'api_wizard' => null,
                        ],
                        'pphs_advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'pphs_settings' => [
                        'pphs_settings_advanced' => [
                            'pphs_settlement_report' => [
                                'heading_schedule' => null,
                            ],
                        ],
                    ],
                ],
                'payments_pro_hosted_solution_without_bml' => [
                    'pphs_required_settings' => [
                        'express_checkout_bml_sort_order' => null,
                        'pphs_required_settings_pphs' => [
                            'api_wizard' => null,
                        ],
                        'pphs_advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'pphs_settings' => [
                        'pphs_settings_advanced' => [
                            'pphs_settlement_report' => [
                                'heading_sftp' => null,
                                'heading_schedule' => null,
                            ],
                        ],
                    ],
                ],
                'payments_pro_hosted_solution_without_bml_and_paylater' => [
                    'pphs_required_settings' => [
                        'express_checkout_bml_sort_order' => null,
                        'pphs_required_settings_pphs' => [
                            'api_wizard' => null,
                        ],
                        'pphs_advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'pphs_settings' => [
                        'pphs_settings_advanced' => [
                            'pphs_settlement_report' => [
                                'heading_sftp' => null,
                                'heading_schedule' => null,
                            ],
                        ],
                    ],
                ],
                'payments_pro_hosted_solution_with_express_checkout' => [
                    'pphs_required_settings' => [
                        'express_checkout_bml_sort_order' => null,
                        'pphs_required_settings_pphs' => [
                            'api_wizard' => null,
                        ],
                        'pphs_advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'pphs_settings' => [
                        'pphs_settings_advanced' => [
                            'pphs_settlement_report' => [
                                'heading_sftp' => null,
                                'heading_schedule' => null,
                            ],
                            'pphs_frontend' => [
                                'paypal_pages' => null,
                            ],
                        ],
                    ],
                ],
                'express_checkout' => [
                    'express_checkout_required' => [
                        'onboarding_wizard' => null,
                        'express_checkout_required_express_checkout' => [
                            'api_wizard' => null,
                        ],
                        'advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'settings_ec' => [
                        'settings_ec_advanced' => [
                            'express_checkout_settlement_report' => [
                                'heading_schedule' => null,
                            ],
                            'express_checkout_frontend' => [
                                'paypal_pages' => null,
                                'checkout_display' => null,
                            ],
                        ],
                    ],
                ],
            ],
            'paypal' => [
                'fetch_reports' => [
                    'active' => '0',
                    'schedule' => '1',
                    'time' => '00,00,00',
                ],
                'style' => [
                    'logo' => 'shopNowUsing_150x40',
                    'page_style' => null,
                    'paypal_hdrimg' => null,
                    'paypal_hdrbackcolor' => null,
                    'paypal_hdrbordercolor' => null,
                    'paypal_payflowcolor' => null,
                    'checkout_page_button_customize' => '0',
                    'checkout_page_button_label' => 'paypal',
                    'checkout_page_button_mx_installment_period' => null,
                    'checkout_page_button_br_installment_period' => null,
                    'checkout_page_button_layout' => 'horizontal',
                    'checkout_page_button_shape' => 'rect',
                    'checkout_page_button_color' => 'silver',
                    'product_page_button_customize' => '1',
                    'product_page_button_label' => 'paypal',
                    'product_page_button_mx_installment_period' => null,
                    'product_page_button_br_installment_period' => null,
                    'product_page_button_layout' => 'horizontal',
                    'product_page_button_shape' => 'pill',
                    'product_page_button_color' => 'gold',
                    'cart_page_button_customize' => '0',
                    'cart_page_button_label' => 'paypal',
                    'cart_page_button_mx_installment_period' => null,
                    'cart_page_button_br_installment_period' => null,
                    'cart_page_button_layout' => 'vertical',
                    'cart_page_button_shape' => 'rect',
                    'cart_page_button_color' => 'silver',
                    'mini_cart_page_button_customize' => '0',
                    'mini_cart_page_button_label' => 'paypal',
                    'mini_cart_page_button_mx_installment_period' => null,
                    'mini_cart_page_button_br_installment_period' => null,
                    'mini_cart_page_button_layout' => 'vertical',
                    'mini_cart_page_button_shape' => 'rect',
                    'mini_cart_page_button_color' => 'gold',
                    'disable_funding_options' => 'ELV',
                    'checkout_page_button_size' => 'responsive',
                    'product_page_button_size' => 'responsive',
                    'cart_page_button_size' => 'responsive',
                    'mini_cart_page_button_size' => 'responsive',
                ],
                'wpp' => [
                    'api_authentication' => '0',
                    'use_proxy' => '0',
                    'button_flavor' => 'dynamic',
                ],
                'wpuk' => [
                    'user' => null,
                    'pwd' => null,
                ],
            ],
            'digidirect_abstract_gift_card' => [
                'settings' => [
                    'active' => '1',
                    'allow_native_gift_cards' => '1',
                    'accept_gift_cards_for_paid_orders' => '0',
                ],
            ],
            'giftcard_service' => [
                'vii' => [
                    'active' => '1',
                    'title' => 'Vii',
                    'account_id' => 'DDOnline',
                    'curl_ssl_version' => '-1',
                    'mapping_not_responding_message' => '{"785746c84c0d0922a3117bb3c1eab294":{"request_type_column":"Undo","message_column":"Vii does not respond"},"291342860dbc0318f1762ff78787398a":{"request_type_column":"PreAuthRequest","message_column":"Pre Auth is not responding"},"b03c317920123619883b82a3541b3c39":{"request_type_column":"CheckBalance","message_column":"Check balance is not responding"},"26ac0651c0a78ac86feac2de5741cc53":{"request_type_column":"Redemption","message_column":"Redemption is not responding"}}',
                    'mapping_card_status' => '{"68f5e4bdf9655210e67b9bb9b73697c0":{"card_status_id_column":"1","card_status_column":"Active"},"df4d8e6efcfcfd70b60f58361a2cf7f5":{"card_status_id_column":"2","card_status_column":"Expired"},"b6debf225612024054dd292acbdf67cf":{"card_status_id_column":"3","card_status_column":"Suspended"},"c0c3fb0a8ebb6f940a2218dae5e9d557":{"card_status_id_column":"4","card_status_column":"Cancelled"},"5184c8bf9c722c4c75fa78872044cf7f":{"card_status_id_column":"0","card_status_column":"Not Issued"},"de44b0787aafa0984b432270b1635403":{"card_status_id_column":"5","card_status_column":"Expired"}}',
                    'cancellation_delay' => '0',
                    'order_cron_expr' => '*/10 * * * *',
                    'quote_cron_expr' => '*/7 * * * *',
                    'quote_visitor_cron_expr' => '*/5 * * * *',
                    'send_undo_cron_expr' => '*/5 * * * *',
                    'order_cancel_email_sender' => 'general',
                    'order_cancel_email_template' => 'giftcard_service_vii_order_cancel_email_template',
                    'code' => 'vii',
                    'allowspecific' => '0',
                    'model' => 'ViiServiceAdapter',
                    'sort_order' => '1',
                    'can_check_status' => '1',
                    'can_hold' => '1',
                    'can_accept' => '1',
                    'can_cancel' => '1',
                    'can_use_on_front' => '1',
                    'can_undo' => '1',
                ],
            ],
            'mgzbuilder' => [
                'general' => [
                    'row_inner_width' => '1260px',
                    'google_api_key' => null,
                ],
                'customization' => [
                    'css' => null,
                ],
            ],
            'ninjamenus' => [
                'general' => [
                    'version' => null,
                    'enabled' => '1',
                ],
            ],
            'marketplacer_base' => [
                'base' => [
                    'api_endpoint' => null,
                ],
            ],
            'login_as_customer' => [
                'general' => [
                    'enabled' => '1',
                    'disable_page_cache' => '1',
                    'store_view_manual_choice_enabled' => '0',
                    'shopping_assistance_checkbox_title' => 'Allow remote shopping assistance',
                    'shopping_assistance_checkbox_tooltip' => 'This allows merchants to "see what you see" and take actions on your behalf in order to provide better assistance.',
                ],
            ],
            'preorder' => [
                'general' => [
                    'enable' => '1',
                    'mix' => '1',
                    'display_oos_with_pre_status_only' => '1',
                    'button' => 'Pre-Order',
                    'note' => 'Pre-Ordered Product',
                    'mess' => 'Pre-Ordered Product',
                ],
            ],
            'general' => [
                'country' => [
                    'default' => 'AU',
                    'allow' => 'AU,CA,CN,FR,DE,IT,JP,NZ,RU,AE,GB,US',
                    'optional_zip_countries' => 'CA,FR,HK,IE,JP,MO,PA,GB,US',
                    'eu_countries' => 'AT,BE,BG,HR,CY,CZ,DK,EE,FI,FR,DE,GR,HU,IE,IT,LV,LT,LU,MT,NL,PL,PT,RO,SK,SI,ES,SE,GB',
                    'destinations' => 'AU',
                ],
                'region' => [
                    'state_required' => 'AU,CA,CN,IT,US',
                    'display_all' => '1',
                ],
                'locale' => [
                    'timezone' => 'Australia/Sydney',
                    'code' => 'en_AU',
                    'weight_unit' => 'kgs',
                    'firstday' => '0',
                    'weekend' => '0,6',
                    'datetime_format_long' => '%A, %B %e %Y [%I:%M %p]',
                    'datetime_format_medium' => '%a, %b %e %Y [%I:%M %p]',
                    'datetime_format_short' => '%m/%d/%y [%I:%M %p]',
                    'date_format_long' => '%A, %B %e %Y',
                    'date_format_medium' => '%a, %b %e %Y',
                    'date_format_short' => '%m/%d/%y',
                    'language' => 'en',
                ],
                'restriction' => [
                    'is_active' => '0',
                    'mode' => '0',
                    'http_redirect' => '0',
                    'cms_page' => 'no-route',
                    'http_status' => '0',
                ],
                'store_information' => [
                    'name' => 'digiDirect',
                    'phone' => '1300 889 148',
                    'hours' => null,
                    'country_id' => 'AU',
                    'region_id' => '581',
                    'postcode' => '2000',
                    'city' => 'Sydney',
                    'street_line1' => 'King Street',
                    'street_line2' => null,
                    'merchant_vat_number' => null,
                    'validate_vat_number' => null,
                ],
                'single_store_mode' => [
                    'enabled' => '0',
                ],
                'file' => [
                    'protected_extensions' => [
                        'php' => 'php',
                        'php3' => 'php3',
                        'php4' => 'php4',
                        'php5' => 'php5',
                        'php7' => 'php7',
                        'htaccess' => 'htaccess',
                        'jsp' => 'jsp',
                        'pl' => 'pl',
                        'py' => 'py',
                        'asp' => 'asp',
                        'aspx' => 'aspx',
                        'sh' => 'sh',
                        'cgi' => 'cgi',
                        'htm' => 'htm',
                        'html' => 'html',
                        'phtml' => 'phtml',
                        'shtml' => 'shtml',
                        'phpt' => 'phpt',
                        'pht' => 'pht',
                        'phar' => 'phar',
                        'svg' => 'svg',
                        'svgz' => 'svgz',
                        'xml' => 'xml',
                        'xhtml' => 'xhtml',
                    ],
                    'public_files_valid_paths' => [
                        'protected' => [
                            'app' => '*/app/*/*',
                            'bin' => '*/bin/*/*',
                            'dev' => '*/dev/*/*',
                            'generated' => '*/generated/*/*',
                            'lib' => '*/lib/*/*',
                            'setup' => '*/setup/*/*',
                            'update' => '*/update/*/*',
                            'vendor' => '*/vendor/*/*',
                        ],
                    ],
                    'importexport_local_valid_paths' => [
                        'available' => [
                            'export_xml' => 'var/export/*/*.xml',
                            'export_csv' => 'var/export/*/*.csv',
                            'import_xml' => 'var/import/*/*.xml',
                            'import_csv' => 'var/import/*/*.csv',
                        ],
                    ],
                    'bunch_size' => '100',
                    'import_images_base_dir' => 'var/import/images',
                ],
                'validator_data' => [
                    'input_types' => [
                        'text' => 'text',
                        'textarea' => 'textarea',
                        'texteditor' => 'texteditor',
                        'date' => 'date',
                        'boolean' => 'boolean',
                        'multiselect' => 'multiselect',
                        'select' => 'select',
                        'price' => 'price',
                        'media_image' => 'media_image',
                        'gallery' => 'gallery',
                        'datetime' => 'datetime',
                        'weee' => 'weee',
                        'child_entity' => 'child_entity',
                        'entity_list' => 'entity_list',
                        'pagebuilder' => 'pagebuilder',
                        'swatch_visual' => 'swatch_visual',
                        'swatch_text' => 'swatch_text',
                    ],
                ],
            ],
            'dev' => [
                'front_end_development_workflow' => [
                    'type' => 'server_side_compilation',
                ],
                'debug' => [
                    'template_hints_storefront' => '0',
                    'template_hints_storefront_show_with_parameter' => '0',
                    'template_hints_parameter_value' => 'magento',
                    'template_hints_admin' => '0',
                    'template_hints_blocks' => '0',
                    'profiler' => '0',
                    'debug_logging' => '0',
                ],
                'template' => [
                    'allow_symlink' => '0',
                    'minify_html' => '1',
                ],
                'translate_inline' => [
                    'active' => '0',
                    'active_admin' => '0',
                    'invalid_caches' => [
                        'block_html' => null,
                    ],
                ],
                'js' => [
                    'merge_files' => '0',
                    'enable_js_bundling' => '0',
                    'minify_files' => '0',
                    'move_script_to_bottom' => '0',
                    'translate_strategy' => 'dictionary',
                    'session_storage_logging' => '0',
                    'minify_exclude' => [
                        'tiny_mce' => '/tiny_mce/',
                        'cardinal_commerce' => '/v1/songbird',
                        'authorizenet_acceptjs' => '\\.authorize\\.net/v1/Accept',
                    ],
                ],
                'css' => [
                    'merge_css_files' => '1',
                    'minify_files' => '1',
                    'use_css_critical_path' => '0',
                    'minify_exclude' => [
                        'tiny_mce' => '/tiny_mce/',
                    ],
                ],
                'caching' => [
                    'cache_user_defined_attributes' => '0',
                ],
                'image' => [
                    'default_adapter' => 'GD2',
                    'adapters' => [
                        'GD2' => [
                            'title' => 'PHP GD2',
                            'class' => 'Magento\\Framework\\Image\\Adapter\\Gd2',
                        ],
                        'IMAGEMAGICK' => [
                            'title' => 'ImageMagick',
                            'class' => 'Magento\\Framework\\Image\\Adapter\\ImageMagick',
                        ],
                    ],
                ],
                'static' => [
                    'sign' => '1',
                ],
                'grid' => [
                    'async_indexing' => '0',
                ],
                'message_castomization' => [
                    'message_settings' => null,
                ],
                'cms_block_processing' => [
                    'use_cms_cache_block' => '1',
                    'cms_block_cache_lifetime' => '3600',
                ],
                'blog' => [
                    'localize_publish_date' => '0',
                ],
                'syslog' => [
                    'syslog_logging' => '0',
                ],
                'styleguide' => [
                    'show' => 'disable',
                ],
            ],
            'digidirect_aa_config' => [
                'general' => [
                    'url_suffix' => '.html',
                ],
            ],
            'digidirect_ai' => [
                'backups' => [
                    'backups_path' => null,
                ],
                'logs' => [
                    'remove_db_logs_older_than_days' => null,
                    'remove_logs_cron_expr' => '0 1 * * *',
                    'logs_path' => null,
                    'backup_logs_path' => null,
                    'cron_expr' => '0 2 * * *',
                    'logs_files_backup_days' => '7',
                    'logs_email_template' => 'digidirect_ai_logs_logs_email_template',
                ],
                'exceptions' => [
                    'exceptions_emails' => null,
                    'fail_run_email_template' => 'digidirect_ai_exceptions_fail_run_email_template',
                ],
                'queue' => [
                    'cron_expr' => '*/5 * * * *',
                    'number_of_attempts' => '5',
                    'number_of_attempts_per_process' => null,
                    'interval_of_attempts' => '300',
                    'interval_of_attempts_for_pending_depends' => '900',
                    'fail_emails' => null,
                    'fail_email_template' => 'digidirect_ai_queue_fail_email_template',
                    'batch_size' => '10',
                ],
            ],
            'digidirect_googleapi_config' => [
                'general' => [
                    'google_api_key' => 'AIzaSyAQMJ4R2QcT0ohrTK3fvAq0AAAzffi8Xno',
                ],
            ],
            'digidirect_blog' => [
                'general' => [
                    'active' => '1',
                    'breadcrumb' => '0',
                    'postonlist' => '12',
                    'post_sorting' => 'desc',
                    'date_format' => 'd-m-Y',
                    'post_layout' => '2columns-left',
                    'post_list_layout' => '1column',
                    'cat_layout' => '1column',
                    'list_url' => 'blog',
                    'url_prefix' => 'blogpost',
                    'url_suffix' => null,
                    'cat_prefix' => 'blog',
                    'top_menu_link' => '1',
                    'footer_link' => '1',
                    'top_menu_title' => 'Blog',
                ],
                'comments' => [
                    'type_of_comment' => 'default',
                    'login_require' => '0',
                    'autoapprove' => '0',
                    'loginapprove' => '0',
                    'emailoncomment' => '0',
                    'email_template' => 'digidirect_blog_comments_email_template',
                    'admin_email' => null,
                    'commentcount' => '1',
                    'no_of_comments' => '5',
                    'fb_app_id' => null,
                    'disqus_shortname' => null,
                ],
                'opengraph_settings' => [
                    'enable_opengraph_on_category_page' => '1',
                    'enable_display_alternative_locales_category_page' => '1',
                    'enable_opengraph_on_post_page' => '1',
                    'enable_display_alternative_locales_post_page' => '1',
                ],
                'related_posts' => [
                    'enabled' => '1',
                    'number_of_posts' => '5',
                ],
                'related_products' => [
                    'enabled' => '1',
                    'number_of_products' => '5',
                    'show_addtocart' => '1',
                    'show_whishlist_icon' => '1',
                    'show_compare_icon' => '1',
                ],
                'infinite_scroll' => [
                    'enable_category_listing' => '0',
                    'enable_post_listing' => '0',
                ],
                'display_settings' => [
                    'display_views' => '0',
                    'display_tags' => '1',
                    'display_share' => '1',
                    'share_above' => '1',
                    'share_below' => '1',
                ],
                'list_page' => [
                    'show_type' => 'grid',
                    'display_tags_listing' => '0',
                    'category_onlist_left' => '1',
                    'category_onlist_right' => '0',
                    'popular_onlist_left' => '0',
                    'popular_onlist_right' => '0',
                    'latest_onlist_left' => '0',
                    'latest_onlist_right' => '0',
                    'archive_onlist_left' => '0',
                    'archive_onlist_right' => '0',
                    'show_category_name' => '1',
                ],
                'view_page' => [
                    'category_onpost_left' => '1',
                    'category_onpost_right' => '0',
                    'popular_onpost_left' => '0',
                    'popular_onpost_right' => '0',
                    'latest_onpost_left' => '1',
                    'latest_onpost_right' => '0',
                    'archive_onpost_left' => '0',
                    'archive_onpost_right' => '0',
                    'show_category_name_onpost' => '0',
                ],
                'cat_page' => [
                    'show_type' => 'grid',
                    'categorylist_oncat_left' => '1',
                    'categorylist_oncat_right' => '1',
                    'popular_oncat_left' => '0',
                    'popular_oncat_right' => '0',
                    'latest_oncat_left' => '0',
                    'latest_oncat_right' => '0',
                    'archive_oncat_left' => '0',
                    'archive_oncat_right' => '0',
                    'show_category_name_oncat' => '1',
                ],
                'rss_feed' => [
                    'title' => null,
                    'description' => null,
                ],
                'design' => [
                    'theme' => '0',
                    'update_xml' => null,
                ],
            ],
            'digidirect_extendedcatalogpricerules' => [
                'general' => [
                    'add_dynamic_price' => '0',
                ],
            ],
            'digidirect_extendedshippingrates' => [
                'main' => [
                    'multiple_rates_price' => '0',
                ],
                'hide_methods' => [
                    'hided_methods_relations' => [],
                ],
            ],
            'custom_canonicals' => [
                'general' => [
                    'mapper' => '{"_1564990570942_942":{"request_path":"https:\\/\\/www.digidirect.com.au\\/blog\\/tag\\/buyers+guide\\/","canonical_link":"https:\\/\\/www.digidirect.com.au\\/blog\\/buying-guides\\/"},"_1564990657527_527":{"request_path":"https:\\/\\/www.digidirect.com.au\\/blog\\/buying-guides\\/","canonical_link":"https:\\/\\/www.digidirect.com.au\\/blog\\/buying-guides\\/"}}',
                ],
            ],
            'digidirect_quickview' => [
                'general' => [
                    'quickview_enabled' => '0',
                    'close_after_add_to_cart' => null,
                ],
            ],
            'digidirect_related_product' => [
                'shown_parameters' => [
                    'filter_tab_enable' => '1',
                    'category_attributes' => 'description',
                    'attributes' => 'sku',
                    'visibility_products' => '2,4',
                    'images_enable' => '1',
                    'attribute_sets' => '4',
                    'display_one_category' => '0',
                ],
            ],
            'digidirect_shippingavailabilitycheck' => [
                'general' => [
                    'enable' => '1',
                    'all_methods' => null,
                    'display_for_out_of_stock' => null,
                ],
            ],
            'digidirect_social_sharing' => [
                'general' => [
                    'enable' => '1',
                ],
                'sharing_facebook' => [
                    'enable' => '1',
                    'id' => null,
                    'like' => '1',
                    'count' => '1',
                ],
                'sharing_google' => [
                    'enable_plus_one' => '1',
                    'enable_share' => '1',
                    'count' => '1',
                ],
                'sharing_pin' => [
                    'enable' => '1',
                    'count' => '1',
                ],
                'sharing_twitter' => [
                    'enable' => '1',
                ],
            ],
            'digidirect_storelocator_config' => [
                'general' => [
                    'enabled' => '1',
                    'api' => 'google',
                    'ask_to_use_geolocation_on_first_visit' => '1',
                    'search_results_display_mode' => 'DEFAULT',
                ],
                'list_settings' => [
                    'detail_click_action' => 'redirect',
                    'page_url' => 'store-locator',
                    'available_countries' => 'AU',
                    'page_meta_description' => null,
                    'default_radius' => '50',
                    'radius_options' => '10,15,20,25,30,50,75,100',
                    'default_country' => 'AU',
                    'default_image' => null,
                    'stores_on_locator_page' => '10',
                    'max_stores_to_show' => '100',
                    'group_search_results_by_parent_entity' => '1',
                    'countries_xml_path' => 'default_country',
                    'metric' => '1',
                ],
                'search_settings' => [
                    'extend_radius' => '1',
                    'sort_order' => 'DISTANCE',
                    'show_featured_stores_at_the_top' => '1',
                    'search_attributes' => 'name,street,city,country,state,postcode,longitude,latitude',
                    'entities' => '79',
                    'search_min_length' => '3',
                    'ignore_search_term' => '0',
                ],
                'exclusive_management' => [
                    'exclusive_icon' => null,
                ],
                'store_details' => [
                    'enable_directions' => '1',
                ],
                'seo' => [
                    'remove_trailing_slash' => '0',
                ],
                'dev' => [
                    'main_entity' => '79',
                    'entity_layout_mapping' => '{"b0fb32e720c4a183e8403615e65c030b":{"entity_column":"79","additional_layout_column":""}}',
                    'load_all_children_for_parent' => '1',
                    'guess_state' => '0',
                    'guess_country' => '0',
                    'guess_postcode' => '0',
                ],
                'search_settigns' => [
                    'ignore_search_term' => '0',
                ],
            ],
            'marketplacer_brand' => [
                'general' => [
                    'enabled' => '0',
                    'allow_admin_edit' => '0',
                ],
                'seo' => [
                    'base_url_key' => 'marketplacerbrands',
                    'url_suffix' => '.html',
                ],
                'listing' => [
                    'page_title' => 'Brands',
                    'meta_title' => 'Brands',
                    'meta_description' => 'Brands',
                ],
            ],
            'marketplacer_seller' => [
                'general' => [
                    'enabled' => '1',
                    'general_seller_id' => '20329',
                    'allow_admin_edit' => '1',
                ],
                'seo' => [
                    'base_url_key' => 'sellers',
                    'url_suffix' => null,
                ],
                'listing' => [
                    'page_title' => 'Sellers',
                    'meta_title' => 'Sellers',
                    'meta_description' => 'Sellers',
                ],
            ],
            'structureddata' => [
                'product' => [
                    'enable' => '1',
                    'enable_category' => '0',
                    'use_short_description' => '1',
                    'include_children' => '0',
                    'include_weight' => '0',
                    'include_reviews' => '1',
                    'product_brand_field' => 'brand',
                    'product_gtin_field' => null,
                ],
                'cms' => [
                    'enable' => '0',
                    'enable_about' => null,
                    'about_page' => null,
                ],
                'contact' => [
                    'enable' => '0',
                    'type' => 'Organization',
                    'latitude' => null,
                    'longitude' => null,
                ],
            ],
            'zendesk' => [
                'setup' => [
                    'setup' => null,
                ],
                'general' => [
                    'domain' => 'digidirect.zendesk.com',
                    'email' => 'clint@kayweb.com.au',
                    'password' => 'Ys4zLCvg21YxkmXQOGZJcSuTIsdJxvwVhNIFlNc7',
                    'test' => null,
                ],
                'zendesk_integration' => [
                    'integration_status' => null,
                    'zendesk_app_status' => null,
                    'install_uninstall_zendesk_app' => null,
                    'remove_zendesk_integration' => null,
                    'auto_install' => '1',
                ],
                'zendesk_app' => [
                    'display_name' => '1',
                    'display_order_status' => '1',
                    'display_order_store' => '1',
                    'display_item_quantity' => '1',
                    'display_item_price' => '1',
                    'display_total_price' => '1',
                    'display_shipping_address' => '1',
                    'display_shipping_method' => '1',
                    'display_tracking_number' => '1',
                    'display_order_comments' => '1',
                    'app_id' => '190391',
                    'cors_origin_pattern' => '#https://.*zdusercontent\\.com#',
                ],
                'frontend_features' => [
                    'web_widget_code_active' => '1',
                    'web_widget_customize' => null,
                ],
                'debug' => [
                    'enable_debug_logging' => '0',
                ],
                'web_widget' => [
                    'web_widget_code_active' => '0',
                    'dynamic_snippet_url_pattern' => 'https://ekr.zdassets.com/snippets/web_widget/{domain}?dynamic_snippet=true',
                    'web_widget_customize_url' => 'https://{domain}/agent/admin/widget',
                    'saved_widget_snippet_digidirect' => '<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=88672bb4-20de-4954-9aae-cec47a62429f"> </script>',
                ],
                'brand_mapping' => [
                    'brand-mapping-360002225416' => '1,5',
                ],
            ],
            'sunshine' => [
                'events' => [
                    'cart_add_items' => '0',
                    'cart_remove_items' => '0',
                    'refund_status' => '0',
                    'checkout_begin' => '0',
                    'customer_create_update' => '0',
                    'customer_delete' => '0',
                    'order_placed_updated' => '0',
                    'order_cancel' => '0',
                    'order_paid' => '0',
                    'order_shipped' => '0',
                ],
                'debug' => [
                    'enable_debug_logging' => null,
                ],
                'general' => [
                    'cors_origin_pattern' => '#https://.*zdusercontent\\.com#',
                ],
            ],
            'admin' => [
                'emails' => [
                    'forgot_email_template' => 'admin_emails_forgot_email_template',
                    'forgot_email_identity' => 'general',
                    'user_notification_template' => 'admin_emails_user_notification_template',
                    'new_user_notification_template' => 'admin_emails_new_user_notification_template',
                ],
                'startup' => [
                    'menu_item_id' => 'Magento_Backend::dashboard',
                ],
                'url' => [
                    'use_custom' => '0',
                    'use_custom_path' => '0',
                ],
                'security' => [
                    'admin_account_sharing' => '1',
                    'password_reset_protection_type' => '1',
                    'password_reset_link_expiration_period' => '2',
                    'max_number_password_reset_requests' => '5',
                    'min_time_between_password_reset_requests' => '10',
                    'use_form_key' => '1',
                    'use_case_sensitive_login' => '0',
                    'session_lifetime' => '31536000',
                    'lockout_failures' => '6',
                    'lockout_threshold' => '30',
                    'password_lifetime' => '0',
                    'password_is_forced' => '1',
                ],
                'dashboard' => [
                    'enable_charts' => '1',
                ],
                'captcha' => [
                    'enable' => '0',
                    'font' => 'linlibertine',
                    'forms' => 'backend_login,backend_forgotpassword',
                    'mode' => 'after_fail',
                    'failed_attempts_login' => '3',
                    'timeout' => '7',
                    'length' => '4-5',
                    'symbols' => 'ABCDEFGHJKMnpqrstuvwxyz23456789',
                    'case_sensitive' => '0',
                    'type' => 'default',
                    'failed_attempts_ip' => '1000',
                    'shown_to_logged_in_user' => null,
                    'always_for' => [
                        'backend_forgotpassword' => '1',
                    ],
                ],
                'magento_logging' => [
                    'actions' => '{"adminhtml_system_account":"1","adminhtml_permission_roles":"1","adminhtml_permission_users":"1","admin_login":"1","cms_blocks":"1","magento_versionscms_hierarchy":"1","version_cms_pages":"1","cache_management":"1","salesrule":"1","catalog_attributes":"1","catalog_categories":"1","magento_catalogevent":"1","catalogrule":"1","tax_product_tax_classes":"1","catalog_attributesets":"1","catalog_products":"1","rating":"1","review":"1","catalogsearch":"1","sales_agreement":"1","adminhtml_system_variable":"1","customer_groups":"1","magento_invitation":"1","tax_customer_tax_classes":"1","customer":"1","theme_design_config":"1","digidirect_abstractentity":"1","digidirect_abstractentity_option":"1","digidirect_feed_dynamic_categories":"1","digidirect_feed_dynamic_attributes":"1","digidirect_feed_rules":"1","digidirect_feed_templates":"1","digidirect_feed_feeds":"1","digidirect_productoverlay_overlays":"1","magento_giftcardaccount":"1","magento_giftregistry_entity":"1","magento_giftregistry_type":"1","index_managment":"1","eaa_options":"1","blog_category_items":"1","blog_comment_items":"1","blog_post_items":"1","adminhtml_system_currency":"1","adminhtml_customer_address_attribute":"1","adminhtml_customer_attribute":"1","adminhtml_system_design":"1","magento_banner":"1","digidirect_extendedshippingrates_carrier":"1","digidirect_extendedshippingrates_method":"1","digidirect_extendedshippingrates_rate":"1","digidirect_extendedshippingrates_rule":"1","digidirect_extendedshippingrates_zone":"1","faq_category_items":"1","faq_items":"1","magento_customersegment":"1","adminhtml_system_stores":"1","adminhtml_system_store_groups":"1","adminhtml_system_websites":"1","newsletter_queue":"1","newsletter_subscribers":"1","newsletter_templates":"1","paypal_settlement_reports":"1","reports":"1","magento_reward_rate":"1","magento_targetrule":"1","magento_salesarchive":"1","sales_creditmemos":"1","sales_invoices":"1","sales_order_status":"1","sales_orders":"1","sales_shipments":"1","magento_advancedcheckout":"1","magento_customerbalance":"1","backups":"1","adminhtml_system_config":"1","tax_rates":"1","tax_rules":"1","adminhtml_email_template":"1","urlrewrites":"1","widget_instance":"1","google_sitemap":"1"}',
                ],
                'usage' => [
                    'enabled' => '0',
                ],
            ],
            'web' => [
                'url' => [
                    'use_store' => '0',
                    'redirect_to_base' => '1',
                    'catalog_media_url_format' => 'hash',
                ],
                'seo' => [
                    'use_rewrites' => '1',
                ],
                'unsecure' => [
                    'base_web_url' => '{{unsecure_base_url}}',
                ],
                'secure' => [
                    'use_in_frontend' => '1',
                    'use_in_adminhtml' => '1',
                    'enable_hsts' => '0',
                    'enable_upgrade_insecure' => '0',
                    'offloader_header' => 'X-Forwarded-Proto',
                    'base_web_url' => '{{secure_base_url}}',
                ],
                'default' => [
                    'cms_home_page' => 'home',
                    'no_route' => 'cms/noroute/index',
                    'cms_no_route' => 'no-route',
                    'cms_no_cookies' => 'enable-cookies',
                    'show_cms_breadcrumbs' => '1',
                ],
                'default_layouts' => [
                    'default_product_layout' => null,
                    'default_category_layout' => null,
                    'default_cms_layout' => '1column',
                ],
                'cookie' => [
                    'cookie_lifetime' => '3600',
                    'cookie_httponly' => '1',
                    'cookie_restriction' => '1',
                    'cookie_restriction_lifetime' => '31536000',
                ],
                'session' => [
                    'use_remote_addr' => '0',
                    'use_http_via' => '0',
                    'use_http_x_forwarded_for' => '0',
                    'use_http_user_agent' => '0',
                    'use_frontend_sid' => '0',
                ],
                'browser_capabilities' => [
                    'cookies' => '1',
                    'javascript' => '1',
                    'local_storage' => '0',
                ],
            ],
            'digidirect_abstractentity' => [
                'general' => [
                    'enable' => '1',
                    'entities' => '79',
                    'fulltext_search_pattern' => '**',
                ],
            ],
            'digidirect_mystorewidget' => [
                'general' => [
                    'enable' => '1',
                    'entities' => '79',
                    'search_type' => '0',
                    'search_attributes' => 'name,url_key,street,city,country,state,postcode,longitude,latitude',
                    'response_attributes' => 'name,status,image,street,city,country,state,postcode,longitude,latitude',
                    'search_min_length' => '3',
                    'remember_store_cookie_lifetime' => '60',
                    'show_widget_on_checkout' => '1',
                    'enable_my_account' => '1',
                    'enable_header' => '1',
                ],
                'click_and_collect' => [
                    'disable_not_full_c_c' => '1',
                    'c_c_relation_enable' => '1',
                ],
                'shipping_address' => [
                    'enable' => '1',
                    'fields_info' => '{"city_city":{"shipping_field":"city","entity_attribute":"city"},"country_id_country":{"shipping_field":"country_id","entity_attribute":"country"},"postcode_postcode":{"shipping_field":"postcode","entity_attribute":"postcode"},"region_state":{"shipping_field":"region","entity_attribute":"state"},"street_street":{"shipping_field":"street","entity_attribute":"street"},"telephone_phone_number":{"shipping_field":"telephone","entity_attribute":"phone_number"}}',
                    'verify_on_checkout' => '0',
                ],
                'google_auto_suggest' => [
                    'enable' => '0',
                    'api_key' => null,
                ],
                'geo_location' => [
                    'enable' => '0',
                    'behavior' => 'keep',
                    'disable_autocomplite_mobile_divice' => '0',
                ],
            ],
            'design' => [
                'loading_animation' => [
                    'enable' => '1',
                    'image' => 'default/813.gif',
                    'text' => 'loading....',
                ],
                'pagination' => [
                    'list_allow_all' => '1',
                    'pagination_frame' => '5',
                    'pagination_frame_skip' => null,
                    'anchor_text_for_previous' => null,
                    'anchor_text_for_next' => null,
                ],
                'invalid_caches' => [
                    'block_html' => null,
                    'layout' => null,
                    'translate' => null,
                    'full_page' => null,
                ],
                'head' => [
                    'default_title' => 'Magento Enterprise Edition',
                    'default_media_type' => 'text/html',
                    'default_charset' => 'utf-8',
                    'demonotice' => '0',
                    'default_keywords' => 'Magento, Varien, E-commerce',
                    'title_prefix' => null,
                    'title_suffix' => null,
                    'default_description' => null,
                    'includes' => '<link  rel="stylesheet" type="text/css"  media="all" href="{{MEDIA_URL}}styles.css" />',
                    'shortcut_icon' => 'default/dD-Icon.png',
                ],
                'search_engine_robots' => [
                    'default_robots' => 'INDEX,FOLLOW',
                    'default_custom_instructions' => '
User-agent: *
Disallow: /index.php/
Disallow: /*?
Disallow: /checkout/
Disallow: /app/
Disallow: /lib/
Disallow: /*.php$
Disallow: /pkginfo/
Disallow: /report/
Disallow: /var/
Disallow: /catalog/
Disallow: /customer/
Disallow: /sendfriend/
Disallow: /review/
Disallow: /*SID=
                    ',
                    'custom_instructions' => 'User-agent: *
Crawl-delay: 10

Disallow: /index.php/
Disallow: /*?
Disallow: /checkout/
Disallow: /app/
Disallow: /lib/
Disallow: /*.php$
Disallow: /pkginfo/
Disallow: /report/
Disallow: /var/
Disallow: /catalog/
Disallow: /customer/
Disallow: /sendfriend/
Disallow: /review/
Disallow: /*SID=

Disallow: /page_cache/
Disallow: /*?*product_list_mode=
Disallow: /*?*product_list_order=
Disallow: /*?*product_list_limit=
Disallow: /*?*product_list_dir=
Disallow: /*?*clearFilters=1
Disallow: /*?*q=
Disallow: /*?*cat=
Disallow: /*?*sort=
Disallow: /*?*filter=
Disallow: /*?*brand=
Disallow: /*?*color=
Disallow: /*?*price=
Disallow: /*?*find=


#Block bot section

# go away DotBot
User-agent: Dotbot
Disallow: /

# go away Exabot
User-agent: Exabot
Disallow: /

# go away Gigabot
User-agent: Gigabot
Disallow: /

# go away ICCrawler
User-agent: ICCrawler
Disallow: /

# go away Snappy
User-agent: Snappy
Disallow: /

# go away Yandex
User-agent: Yandex
Disallow: /

# go away yandexbot
User-agent: yandexbot
Disallow: /

# go away Yeti
User-agent: Yeti
Disallow: /

# go away Mb2345Browser
User-agent: Mb2345Browser
Disallow: /

# go away QQBrowser
User-agent: QQBrowser
Disallow: /

# go away LieBaoFast
User-agent: LieBaoFast
Disallow: /

# go away MicroMessenger
User-agent: MicroMessenger
Disallow: /

# go away Kinza
User-agent: Kinza
Disallow: /

# go away slurp
User-agent: slurp
Disallow: /

# go away TheWorld
User-agent: TheWorld
Disallow: /

# go away YoudaoBot
User-agent: YoudaoBot
Disallow: /

User-agent: Googlebot
Disallow: /*?*page_id
Disallow: /catalogsearch/
Disallow: /*SID=
Disallow: /*product_list_dir
Disallow: /*price=
Disallow: /*option=

User-agent: Googlebot-image
Disallow:

',
                ],
                'header' => [
                    'welcome' => 'Default welcome msg!',
                    'translate_title' => '1',
                    'logo_width' => null,
                    'logo_height' => null,
                    'logo_alt' => null,
                ],
                'footer' => [
                    'copyright' => '© DigiDIRECT 2024. All rights reserved.',
                    'report_bugs' => '1',
                    'absolute_footer' => null,
                ],
                'watermark' => [
                    'image_position' => 'stretch',
                    'small_image_position' => 'stretch',
                    'thumbnail_position' => 'stretch',
                    'image_size' => null,
                    'image_imageOpacity' => null,
                    'small_image_size' => null,
                    'small_image_imageOpacity' => null,
                    'thumbnail_size' => null,
                    'thumbnail_imageOpacity' => null,
                    'swatch_image_size' => null,
                    'swatch_image_imageOpacity' => null,
                    'swatch_image_position' => 'stretch',
                ],
                'email' => [
                    'header_template' => 'design_email_header_template',
                    'footer_template' => 'design_email_footer_template',
                    'logo_alt' => null,
                    'logo_width' => null,
                    'logo_height' => null,
                ],
                'theme' => [
                    'theme_id' => 'frontend/Digidirect/digi',
                ],
            ],
            'apptrian_imageoptimizer' => [
                'general' => [
                    'enabled' => '1',
                    'batch_size' => '50',
                    'paths' => 'pub',
                    'heading_scan' => null,
                    'scan' => null,
                    'heading_optimize' => null,
                    'optimize' => null,
                    'heading_stats' => null,
                    'stats' => null,
                    'heading_clear' => null,
                    'clear' => null,
                ],
                'cron' => [
                    'heading_warning' => null,
                    'enabled_scan' => '1',
                    'expression_scan' => '15 4 * * *',
                    'enabled_optimize' => '1',
                    'expression_optimize' => '0 * * * *',
                ],
                'utility' => [
                    'heading_warning' => null,
                    'use64bit' => '0',
                    'log_output' => '0',
                    'permissions' => null,
                    'path' => 'bin',
                    'gif' => 'gifsicle',
                    'gif_path' => null,
                    'gif_options' => '-b -O3 %filepath%',
                    'jpg' => 'jpegtran',
                    'jpg_path' => null,
                    'jpg_options' => '-copy none -optimize -progressive -outfile %filepath% %filepath%',
                    'png' => 'optipng',
                    'png_path' => null,
                    'png_options' => '-o7 -quiet -strip all -fix %filepath%',
                ],
            ],
            'catalog' => [
                'fields_masks' => [
                    'sku' => null,
                    'meta_title' => null,
                    'meta_keyword' => null,
                    'meta_description' => null,
                ],
                'frontend' => [
                    'list_mode' => 'grid-list',
                    'grid_per_page_values' => '10,15,25,30',
                    'grid_per_page' => '30',
                    'list_per_page_values' => '5,10,15,20,25,30',
                    'list_per_page' => '10',
                    'default_sort_by' => 'position',
                    'list_allow_all' => '1',
                    'remember_pagination' => '1',
                    'flat_catalog_category' => '0',
                    'flat_catalog_product' => '0',
                    'swatches_per_product' => '16',
                    'show_swatches_in_product_list' => '1',
                    'show_swatch_tooltip' => '1',
                    'parse_url_directives' => '1',
                ],
                'review' => [
                    'active' => '1',
                    'allow_guest' => '1',
                ],
                'productalert' => [
                    'allow_price' => '0',
                    'email_price_template' => 'catalog_productalert_email_price_template',
                    'allow_stock' => '0',
                    'email_stock_template' => 'catalog_productalert_email_stock_template',
                    'email_identity' => 'general',
                ],
                'productalert_cron' => [
                    'frequency' => 'D',
                    'time' => '00,00,00',
                    'error_email_identity' => 'general',
                    'error_email_template' => 'catalog_productalert_cron_error_email_template',
                ],
                'placeholder' => [
                    'placeholder' => null,
                    'alternative_image_placeholder' => null,
                    'image_placeholder' => 'default/ICS_placeHolder.fw.png',
                    'small_image_placeholder' => 'default/ICS_placeHolder.fw_1.png',
                    'swatch_image_placeholder' => null,
                    'thumbnail_placeholder' => 'default/ICS_placeHolder.fw_2.png',
                    'key_features22_placeholder' => null,
                    'product_image_background_desktop_placeholder' => null,
                    'product_image_background_mobile_placeholder' => null,
                ],
                'background_placeholders' => [
                    'enabled' => '1',
                    'category_desktop' => 'default/D0A08BD7-E716-4D02-9128-1CB9BC4A67F0_2.png',
                    'category_mobile' => 'default/1E3A9B11-66ED-4B30-8340-61BDF2F76BE8_2.png',
                    'product_desktop' => 'default/image_1920x1380.jpg',
                    'product_mobile' => 'default/767x180-image_dimmed.jpg',
                ],
                'recently_products' => [
                    'synchronize_with_backend' => '0',
                    'scope' => 'website',
                    'viewed_count' => '6',
                    'compared_count' => '6',
                    'recently_viewed_lifetime' => '1000',
                    'recently_compared_lifetime' => '1000',
                ],
                'product_video' => [
                    'play_if_base' => '0',
                    'show_related' => '0',
                    'video_auto_restart' => '0',
                ],
                'price' => [
                    'scope' => '0',
                    'default_product_price' => '0',
                ],
                'layered_navigation' => [
                    'display_product_count' => '0',
                    'price_range_calculation' => 'manual',
                    'price_range_step' => '100',
                    'one_price_interval' => '0',
                    'price_range_max_intervals' => '10',
                    'interval_division_limit' => '9',
                    'display_category' => '1',
                ],
                'seo' => [
                    'search_terms' => '0',
                    'product_url_suffix' => null,
                    'category_url_suffix' => null,
                    'product_use_categories' => '0',
                    'save_rewrites_history' => '1',
                    'generate_category_product_rewrites' => '1',
                    'title_separator' => '-',
                    'category_canonical_tag' => '0',
                    'product_canonical_tag' => '0',
                    'enable_category_metadata_generation' => '1',
                    'cms_canonical_tag' => '1',
                ],
                'navigation' => [
                    'max_depth' => '0',
                ],
                'search' => [
                    'min_query_length' => '1',
                    'max_query_length' => '128',
                    'max_count_cacheable_search_terms' => '0',
                    'enable_eav_indexer' => '1',
                    'autocomplete_limit' => '5',
                    'engine' => 'elasticsearch7',
                    'elasticsearch5_test_connect_wizard' => null,
                    'elasticsearch7_test_connect_wizard' => null,
                    'elasticsearch6_test_connect_wizard' => null,
                    'search_recommendations_enabled' => '0',
                    'search_recommendations_count' => '5',
                    'search_recommendations_count_results_enabled' => '0',
                    'search_suggestion_enabled' => '0',
                    'search_suggestion_count' => '5',
                    'search_suggestion_count_results_enabled' => '0',
                    'elasticsearch5_minimum_should_match' => null,
                    'elasticsearch6_minimum_should_match' => null,
                    'elasticsearch7_minimum_should_match' => null,
                ],
                'magento_catalogpermissions' => [
                    'enabled' => '0',
                    'grant_catalog_category_view' => '0',
                    'grant_catalog_category_view_groups' => null,
                    'restricted_landing_page' => 'no-route',
                    'grant_catalog_product_price' => '0',
                    'grant_catalog_product_price_groups' => null,
                    'grant_checkout_items' => '0',
                    'grant_checkout_items_groups' => null,
                    'deny_catalog_search' => null,
                ],
                'downloadable' => [
                    'order_item_status' => '9',
                    'downloads_number' => '0',
                    'shareable' => '0',
                    'samples_title' => 'Samples',
                    'links_title' => 'Links',
                    'links_target_new_window' => '1',
                    'content_disposition' => 'inline',
                    'disable_guest_checkout' => '1',
                ],
                'custom_options' => [
                    'use_calendar' => '0',
                    'date_fields_order' => 'm,d,y',
                    'time_format' => '12h',
                    'year_range' => ',',
                    'forbidden_extensions' => 'php,exe',
                ],
                'magento_catalogevent' => [
                    'enabled' => '1',
                    'lister_output' => '1',
                    'lister_widget_limit' => '5',
                    'lister_widget_scroll' => '2',
                ],
                'magento_targetrule' => [
                    'related_position_limit' => '40',
                    'related_position_behavior' => '0',
                    'related_rotation_mode' => '0',
                    'crosssell_position_limit' => '10',
                    'crosssell_position_behavior' => '0',
                    'crosssell_rotation_mode' => '1',
                    'upsell_position_limit' => '8',
                    'upsell_position_behavior' => '0',
                    'upsell_rotation_mode' => '1',
                ],
                'product' => [
                    'flat' => [
                        'max_index_count' => '64',
                    ],
                    'default_tax_group' => '2',
                ],
                'category' => [
                    'root_id' => '2',
                ],
            ],
            'cataloginventory' => [
                'options' => [
                    'can_subtract' => '1',
                    'can_back_in_stock' => '1',
                    'show_out_of_stock' => '1',
                    'stock_threshold_qty' => '0',
                    'display_product_stock_status' => '0',
                    'enable_inventory_check' => '0',
                    'synchronize_with_catalog' => '0',
                ],
                'item_options' => [
                    'manage_stock' => '1',
                    'backorders' => '1',
                    'use_deferred_stock_update' => '0',
                    'max_sale_qty' => '1000',
                    'min_qty' => '-1000',
                    'min_sale_qty' => '1',
                    'notify_stock_qty' => '0',
                    'enable_qty_increments' => '0',
                    'qty_increments' => '1',
                    'auto_return' => '1',
                ],
                'bulk_operations' => [
                    'async' => '0',
                    'batch_size' => '100',
                ],
                'indexer' => [
                    'strategy' => 'sync',
                ],
                'source_selection_distance_based' => [
                    'provider' => 'google',
                ],
                'source_selection_distance_based_google' => [
                    'mode' => 'driving',
                    'value' => 'distance',
                ],
                'inventory_source_carriers' => [
                    'use_enabled_carriers' => '1',
                ],
            ],
            'visualmerchandiser' => [
                'options' => [
                    'smart_attributes' => 'color,hot_deals,name,price,quantity_and_stock_status,sku',
                    'minimum_stock_threshold' => '1',
                    'color_attribute_code' => 'color',
                    'color_order' => null,
                    'product_attributes' => 'name,sku,price,stock',
                    'insert_mode' => '0',
                ],
            ],
            'marketplacer_sales_displaying' => [
                'seller' => [
                    'display_business_number' => '1',
                    'title_for_business_number' => 'Business Number',
                ],
                'brand' => [
                    'display_brand_name' => '1',
                ],
            ],
            'currency' => [
                'options' => [
                    'base' => 'AUD',
                    'default' => 'AUD',
                    'allow' => 'AUD',
                ],
                'fixerio' => [
                    'api_key' => null,
                    'timeout' => '100',
                ],
                'currencyconverterapi' => [
                    'api_key' => null,
                    'timeout' => '100',
                ],
                'import' => [
                    'enabled' => '0',
                    'service' => null,
                    'time' => null,
                    'frequency' => null,
                    'error_email_identity' => 'general',
                    'error_email_template' => 'currency_import_error_email_template',
                ],
            ],
            'sitemap' => [
                'category' => [
                    'changefreq' => 'daily',
                    'priority' => '0.5',
                ],
                'product' => [
                    'changefreq' => 'daily',
                    'priority' => '1',
                    'image_include' => 'base',
                ],
                'page' => [
                    'changefreq' => 'daily',
                    'priority' => '0.25',
                ],
                'store' => [
                    'changefreq' => 'daily',
                    'priority' => '1',
                ],
                'generate' => [
                    'enabled' => '1',
                    'time' => '02,13,00',
                    'frequency' => 'D',
                    'error_email_identity' => 'general',
                    'error_email_template' => 'sitemap_generate_error_email_template',
                ],
                'limit' => [
                    'max_lines' => '50000',
                    'max_file_size' => '10485760',
                ],
                'search_engines' => [
                    'submission_robots' => '1',
                ],
                'blog' => [
                    'changefreq' => 'daily',
                    'show_categories' => '0',
                    'category_priority' => '0.5',
                    'show_posts' => '0',
                    'post_priority' => '0.5',
                ],
                'file' => [
                    'valid_paths' => [
                        'available' => [
                            'any_path' => '/*/*.xml',
                        ],
                    ],
                ],
            ],
            'rss' => [
                'config' => [
                    'active' => null,
                ],
                'wishlist' => [
                    'active' => null,
                ],
                'catalog' => [
                    'new' => null,
                    'special' => null,
                    'discounts' => null,
                    'category' => null,
                ],
                'order' => [
                    'status' => null,
                ],
            ],
            'trans_email' => [
                'ident_storepickup' => [
                    'email' => 'sales@example.com',
                    'name' => 'Store pickup',
                ],
            ],
            'contact' => [
                'contact' => [
                    'enabled' => '1',
                ],
                'email' => [
                    'sender_email_identity' => 'sales',
                    'email_template' => '51',
                ],
            ],
            'bss_facebook_pixel' => [
                'general' => [
                    'active' => '1',
                    'pixel_id' => '1693151124302598',
                ],
                'event_tracking' => [
                    'disable_code' => 'cms_page,account_page',
                    'product_view' => '1',
                    'category_view' => '1',
                    'add_to_cart' => '0',
                    'initiate_checkout' => '0',
                    'purchase' => '1',
                    'add_to_wishlist' => '0',
                    'registration' => '0',
                    'subscribe' => '0',
                    'search' => '1',
                ],
            ],
            'digidirect_utilities_config' => [
                'wysiwyg' => [
                    'wrap_in_parent_tag' => '0',
                    'allowed_childs' => null,
                    'allowed_tags' => null,
                    'allowed_filetypes' => null,
                ],
                'sales_prefix' => [
                    'active' => null,
                    'use_store_id_as_prefix' => null,
                    'order' => null,
                    'invoice' => null,
                    'shipment' => null,
                    'creditmemo' => null,
                    'rma_item' => null,
                ],
                'frontend_settings' => [
                    'show_theme_switcher' => null,
                ],
            ],
            'checkout_fields' => [
                'checkout_fields' => [
                    'enable_checkout_fields' => '1',
                    'checkout_fields' => 'a:3:{s:15:"delivery_number";a:1:{s:6:"active";s:2:"on";}s:14:"delivery_notes";a:1:{s:6:"active";s:2:"on";}s:13:"order_comment";a:1:{s:6:"active";s:2:"on";}}',
                ],
                'cron' => [
                    'frequency' => 'D',
                    'time' => '00,00,00',
                ],
            ],
            'digidirect_address_suggestion' => [
                'general' => [
                    'enabled' => '2',
                    'country_code' => null,
                    'address_data_file' => null,
                    'import_address_data_file' => null,
                    'api_key' => 'AIzaSyAQMJ4R2QcT0ohrTK3fvAq0AAAzffi8Xno',
                    'postcode_length' => [],
                ],
            ],
            'itoris_core' => [
                'notifications' => [
                    'updates' => '1',
                    'newext' => '1',
                    'news' => '0',
                ],
                'installed' => [
                    'Itoris_PriceMatch' => '368|a873c99d75963423b1fcb2b63b45a4e4',
                ],
            ],
            'mgzpagebuilder' => [
                'general' => [
                    'version' => null,
                    'enable' => '1',
                    'enable_pages' => '1',
                    'enable_blocks' => '1',
                    'enable_products' => '0',
                    'enable_categories' => '0',
                    'exclude_namespaces' => 'blog_post_form
digidirect_abstractentity_form
faq_faq_form
faq_category_form
catalog_rule_form',
                ],
                'instagram' => [
                    'user_token' => null,
                    'password' => null,
                ],
            ],
            'webapi' => [
                'soap' => [
                    'charset' => null,
                ],
                'validation' => [
                    'input_limit_enabled' => null,
                    'complex_array_limit' => null,
                    'maximum_page_size' => null,
                    'default_page_size' => null,
                ],
                'webapisecurity' => [
                    'allow_insecure' => '0',
                ],
                'jwtauth' => [
                    'jwt_alg' => 'HS256',
                    'jwe_alg' => 'A128GCM',
                    'customer_expiration' => '60',
                    'admin_expiration' => '60',
                ],
            ],
            'graphql' => [
                'validation' => [
                    'input_limit_enabled' => null,
                    'maximum_page_size' => null,
                ],
            ],
            'newsletter' => [
                'general' => [
                    'active' => '1',
                ],
                'subscription' => [
                    'allow_guest_subscribe' => '1',
                    'confirm' => '1',
                    'confirm_email_identity' => 'support',
                    'confirm_email_template' => '59',
                    'success_email_identity' => 'general',
                    'success_email_template' => '62',
                    'un_email_identity' => 'support',
                    'un_email_template' => '65',
                    'enable_subscribe_on_checkout' => '1',
                    'subscribe_on_checkout_text' => 'Join our email list to get $10 off your next purchase',
                    'use_billing_info_for_guest' => '0',
                    'enable_success_email' => '1',
                    'enable_unsubscribtion_email' => '1',
                    'enable_subscribe_on_paypal_review_page' => '0',
                ],
                'sending' => [
                    'set_return_path' => '0',
                ],
            ],
            'sendfriend' => [
                'email' => [
                    'enabled' => '0',
                    'template' => 'sendfriend_email_template',
                    'allow_guest' => '0',
                    'max_recipients' => '5',
                    'max_per_hour' => '5',
                    'check_by' => '0',
                ],
            ],
            'customer' => [
                'account_share' => [
                    'scope' => '1',
                ],
                'online_customers' => [
                    'online_minutes_interval' => null,
                    'section_data_lifetime' => '60',
                ],
                'create_account' => [
                    'auto_group_assign' => '0',
                    'tax_calculation_address_type' => 'billing',
                    'default_group' => '1',
                    'viv_domestic_group' => null,
                    'viv_intra_union_group' => null,
                    'viv_invalid_group' => null,
                    'viv_error_group' => null,
                    'viv_on_each_transaction' => null,
                    'viv_disable_auto_group_assign_default' => '0',
                    'vat_frontend_visibility' => '0',
                    'email_required_create_order' => '1',
                    'email_domain' => 'example.com',
                    'email_template' => '9',
                    'email_no_password_template' => 'customer_create_account_email_no_password_template',
                    'email_identity' => 'general',
                    'confirm' => '0',
                    'email_confirmation_template' => 'customer_create_account_email_confirmation_template',
                    'email_confirmed_template' => 'customer_create_account_email_confirmed_template',
                    'generate_human_friendly_id' => '0',
                ],
                'password' => [
                    'password_reset_protection_type' => '1',
                    'max_number_password_reset_requests' => '5',
                    'min_time_between_password_reset_requests' => '10',
                    'forgot_email_template' => '23',
                    'remind_email_template' => '17',
                    'reset_password_template' => 'customer_password_reset_password_template',
                    'forgot_email_identity' => 'support',
                    'reset_link_expiration_period' => '2',
                    'autocomplete_on_storefront' => '0',
                    'required_character_classes_number' => '3',
                    'lockout_failures' => '10',
                    'minimum_password_length' => '8',
                    'lockout_threshold' => '10',
                ],
                'account_information' => [
                    'change_email_template' => 'customer_account_information_change_email_template',
                    'change_email_and_password_template' => 'customer_account_information_change_email_and_password_template',
                    'confirm' => '0',
                ],
                'address' => [
                    'street_lines' => '3',
                    'prefix_show' => null,
                    'prefix_options' => null,
                    'middlename_show' => null,
                    'suffix_show' => null,
                    'suffix_options' => null,
                    'dob_show' => null,
                    'taxvat_show' => null,
                    'gender_show' => null,
                    'telephone_show' => 'req',
                    'company_show' => 'opt',
                    'fax_show' => null,
                ],
                'magento_customerbalance' => [
                    'is_enabled' => '1',
                    'show_history' => '1',
                    'refund_automatically' => '0',
                    'email_identity' => 'general',
                    'email_template' => 'customer_magento_customerbalance_email_template',
                ],
                'startup' => [
                    'redirect_dashboard' => '0',
                ],
                'address_templates' => [
                    'text' => '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}
{{depend company}}{{var company}}{{/depend}}
{{if street1}}{{var street1}}
{{/if}}
{{depend street2}}{{var street2}}{{/depend}}
{{depend street3}}{{var street3}}{{/depend}}
{{depend street4}}{{var street4}}{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}
{{var country}}
{{depend telephone}}T: {{var telephone}}{{/depend}}
{{depend fax}}F: {{var fax}}{{/depend}}
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}',
                    'oneline' => '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}, {{var street}}, {{var city}}, {{var region}} {{var postcode}}, {{var country}}',
                    'html' => '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}{{depend firstname}}<br />{{/depend}}
{{depend company}}{{var company}}<br />{{/depend}}
{{depend unit_number}}{{var unit_number}}<br />{{/depend}}
{{if street1}}{{var street1}}<br />{{/if}}
{{depend street2}}{{var street2}}<br />{{/depend}}
{{depend street3}}{{var street3}}<br />{{/depend}}
{{depend street4}}{{var street4}}<br />{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}<br />
{{var country}}<br />
{{depend telephone}}T: <a href="tel:{{var telephone}}">{{var telephone}}</a>{{/depend}}
{{depend fax}}<br />F: {{var fax}}{{/depend}}
{{depend vat_id}}<br />VAT: {{var vat_id}}{{/depend}}',
                    'pdf' => '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}|
{{depend company}}{{var company}}|{{/depend}}
{{if street1}}{{var street1}}|{{/if}}
{{depend street2}}{{var street2}}|{{/depend}}
{{depend street3}}{{var street3}}|{{/depend}}
{{depend street4}}{{var street4}}|{{/depend}}
{{if city}}{{var city}}, {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}|
{{var country}}|
{{depend telephone}}T: {{var telephone}}|{{/depend}}
{{depend fax}}F: {{var fax}}|{{/depend}}|
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}|',
                ],
                'magento_customersegment' => [
                    'is_enabled' => '1',
                ],
                'captcha' => [
                    'enable' => '0',
                    'font' => 'linlibertine',
                    'forms' => 'user_create',
                    'mode' => 'after_fail',
                    'failed_attempts_login' => '3',
                    'timeout' => '7',
                    'length' => '4-5',
                    'symbols' => 'ABCDEFGHJKMnpqrstuvwxyz23456789',
                    'case_sensitive' => '0',
                    'shown_to_logged_in_user' => [
                        'sales_rule_coupon_request' => '1',
                        'payment_processing_request' => '1',
                        'contact_us' => '1',
                        'user_edit' => '1',
                        'share_wishlist_form' => '1',
                        'gift_code_request' => '1',
                        'product_sendtofriend_form' => '1',
                        'co-payment-form' => '1',
                    ],
                    'type' => 'default',
                    'failed_attempts_ip' => '1000',
                    'always_for' => [
                        'user_create' => '1',
                        'user_forgotpassword' => '1',
                        'contact_us' => '1',
                        'co-payment-form' => '1',
                    ],
                ],
                'default' => [
                    'group' => '1',
                ],
            ],
            'wishlist' => [
                'general' => [
                    'active' => '1',
                    'multiple_enabled' => null,
                    'multiple_wishlist_number' => '5',
                    'show_in_sidebar' => '1',
                ],
                'email' => [
                    'email_identity' => 'general',
                    'email_template' => 'wishlist_email_email_template',
                    'number_limit' => '10',
                    'text_limit' => '255',
                ],
                'wishlist_link' => [
                    'use_qty' => '0',
                ],
            ],
            'magento_invitation' => [
                'general' => [
                    'enabled' => '1',
                    'enabled_on_front' => '1',
                    'registration_use_inviter_group' => '1',
                    'registration_required_invitation' => '0',
                    'allow_customer_message' => '1',
                    'max_invitation_amount_per_send' => '5',
                ],
                'email' => [
                    'identity' => 'general',
                    'template' => 'magento_invitation_email_template',
                ],
            ],
            'weltpixel_ga4' => [
                'module_information' => [
                    'important_note' => null,
                ],
                'general' => [
                    'enable' => '1',
                    'gtm_code' => '<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':
new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,\'script\',\'dataLayer\',\'GTM-M7HJ7PQ\');</script>
<!-- End Google Tag Manager -->',
                    'gtm_nonjs_code' => '<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M7HJ7PQ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->',
                    'enable_datalayer_preview' => '0',
                    'datalayer_preview_ip_addresses' => null,
                    'impression_chunk_size' => '15',
                    'id_selection' => 'sku',
                    'enable_brand' => '1',
                    'brand_attribute' => 'brand',
                    'enable_variant' => '0',
                    'order_total_calculation' => 'grandtotal',
                    'exclude_tax_from_transaction' => '0',
                    'exclude_shipping_from_transaction' => '0',
                    'exclude_shipping_from_transaction_including_tax' => '0',
                    'exclude_free_purchase' => '0',
                    'exclude_order_by_status_flag' => '0',
                    'exclude_order_by_statuses' => null,
                    'checkout_page_paths' => null,
                    'success_page_paths' => null,
                    'product_click_tracking' => '0',
                    'promotion_tracking' => '0',
                    'persistentlayer_expiry' => '30',
                    'parent_vs_child' => 'parent',
                    'send_all_child_configurable' => '0',
                    'secure_cookies' => '0',
                    'loadlistingblock' => '0',
                    'separator_user_dimensions' => null,
                    'custom_dimension_customerid' => '1',
                    'custom_dimension_customergroup' => '1',
                    'separator_hitscoped_dimensions' => null,
                    'custom_dimension_pagename' => '1',
                    'custom_dimension_pagetype' => '1',
                    'separator_product_dimensions' => null,
                    'separator_stock_status' => null,
                    'track_stockstatus' => '1',
                    'separator_reviews_count' => null,
                    'track_reviewscount' => '1',
                    'separator_reviews_score' => null,
                    'track_reviewsscore' => '1',
                    'separator_sale_product' => null,
                    'track_saleproduct' => '1',
                    'separator_product_attribute_custom_dimensions' => null,
                    'separator_custom_attribute_1' => null,
                    'track_custom_attribute_1' => '0',
                    'track_custom_attribute_1_name' => null,
                    'track_custom_attribute_1_code' => null,
                    'separator_custom_attribute_2' => null,
                    'track_custom_attribute_2' => '0',
                    'track_custom_attribute_2_name' => null,
                    'track_custom_attribute_2_code' => null,
                    'separator_custom_attribute_3' => null,
                    'track_custom_attribute_3' => '0',
                    'track_custom_attribute_3_name' => null,
                    'track_custom_attribute_3_code' => null,
                    'separator_custom_attribute_4' => null,
                    'track_custom_attribute_4' => '0',
                    'track_custom_attribute_4_name' => null,
                    'track_custom_attribute_4_code' => null,
                    'separator_custom_attribute_5' => null,
                    'track_custom_attribute_5' => '0',
                    'track_custom_attribute_5_name' => null,
                    'track_custom_attribute_5_code' => null,
                ],
                'api' => [
                    'account_id' => '1818904765',
                    'container_id' => '7573549',
                    'measurement_id' => 'G-BWZYKR56TT',
                ],
                'adwords_conversion_tracking' => [
                    'enable' => '0',
                    'google_conversion_id' => '0000000',
                    'google_conversion_label' => '00000',
                    'google_conversion_currency_code' => 'usd',
                    'exclude_free_purchase' => '0',
                    'enable_enhanced_conversion' => '0',
                    'enable_new_customer_acquisition' => '0',
                    'purchase_day_lapse' => '540',
                    'enable_cart_data' => '0',
                    'merchant_center_id' => null,
                    'feed_country' => null,
                    'feed_language' => null,
                ],
                'adwords_remarketing' => [
                    'enable' => '0',
                    'conversion_code' => null,
                    'conversion_label' => null,
                    'exclude_free_purchase' => '0',
                ],
                'json_export' => [
                    'public_id' => 'GTM-M7HJ7PQ',
                    'api_container' => null,
                ],
                'serverside_measurement' => [
                    'enable' => '0',
                    'measurement_id' => null,
                    'api_secret' => null,
                    'events' => null,
                    'send_user_id' => '1',
                    'disable_datalayer_events' => '1',
                    'enable_file_log' => '0',
                    'enable_debug_collect' => '0',
                ],
                'meta_pixel_tracking' => [
                    'enable' => '0',
                    'code_snippet' => null,
                    'events' => null,
                    'id_selection' => 'id',
                ],
            ],
            'itoris_pricematch' => [
                'general' => [
                    'module_enabled' => '1',
                    'send_email_admin' => '1',
                    'sender_admin_email' => 'sales@digidirect.com.au',
                    'template_admin_email' => 'itoris_pricematch_general_template_admin_email',
                    'sender_customer_email' => '0',
                    'template_customer_email' => 'itoris_pricematch_general_template_customer_email',
                    'group_list' => '-1',
                    'link_text' => 'Found a better price? Click here and watch us try to beat it.',
                    'comment_popup' => 'We don\'t like being beaten on price. Please fill in details below and we will respond shortly.',
                ],
            ],
            'sales' => [
                'general' => [
                    'hide_customer_ip' => '0',
                ],
                'totals_sort' => [
                    'subtotal' => '10',
                    'discount' => '20',
                    'shipping' => '30',
                    'tax' => '40',
                    'weee' => '35',
                    'grand_total' => '100',
                    'giftcardaccount' => '90',
                    'customerbalance' => '95',
                    'weee_tax' => '35',
                    'lof_paymentfee' => '35',
                ],
                'reorder' => [
                    'allow' => '1',
                ],
                'zerograndtotal_creditmemo' => [
                    'allow_zero_grandtotal' => '1',
                ],
                'identity' => [
                    'logo' => null,
                    'logo_html' => null,
                    'address' => null,
                ],
                'minimum_order' => [
                    'active' => '0',
                    'amount' => null,
                    'include_discount_amount' => '1',
                    'tax_including' => '1',
                    'description' => null,
                    'error_message' => null,
                    'multi_address' => '0',
                    'multi_address_description' => null,
                    'multi_address_error_message' => null,
                ],
                'dashboard' => [
                    'use_aggregated_data' => '0',
                ],
                'orders' => [
                    'delete_pending_after' => '480',
                    'items_per_page' => '20',
                ],
                'gift_options' => [
                    'allow_order' => '0',
                    'allow_items' => '0',
                    'wrapping_allow_order' => '1',
                    'wrapping_allow_items' => '1',
                    'allow_gift_receipt' => '1',
                    'allow_printed_card' => '1',
                    'printed_card_price' => null,
                ],
                'msrp' => [
                    'enabled' => '0',
                    'display_price_type' => '1',
                    'explanation_message' => 'Our price is lower than the manufacturer\'s "minimum advertised price." As a result, we cannot show you the price in catalog or the product page. <br /><br /> You have no obligation to purchase the product once you know the price. You can simply remove the item from your cart.',
                    'explanation_message_whats_this' => 'Our price is lower than the manufacturer\'s "minimum advertised price." As a result, we cannot show you the price in catalog or the product page. <br /><br /> You have no obligation to purchase the product once you know the price. You can simply remove the item from your cart.',
                ],
                'magento_salesarchive' => [
                    'active' => '0',
                    'age' => '30',
                    'order_statuses' => 'complete,closed',
                ],
                'product_sku' => [
                    'my_account_enable' => '1',
                    'allowed_groups' => null,
                ],
                'instant_purchase' => [
                    'active' => '1',
                    'button_text' => 'Instant Purchase',
                ],
                'magento_rma' => [
                    'enabled' => '0',
                    'enabled_on_product' => '0',
                    'use_store_address' => '1',
                    'store_name' => null,
                    'address' => null,
                    'address1' => null,
                    'city' => null,
                    'region_id' => null,
                    'zip' => null,
                    'country_id' => 'US',
                ],
                'gift_messages' => [
                    'allow_items' => '0',
                    'allow_order' => '0',
                ],
            ],
            'oauth' => [
                'access_token_lifetime' => [
                    'customer' => '1',
                    'admin' => '4',
                ],
                'cleanup' => [
                    'cleanup_probability' => '100',
                    'expiration_period' => '120',
                ],
                'consumer' => [
                    'expiration_period' => '6000',
                    'post_maxredirects' => '0',
                    'post_timeout' => '5',
                    'enable_integration_as_bearer' => '1',
                ],
                'authentication_lock' => [
                    'max_failures_count' => '6',
                    'timeout' => '1800',
                ],
            ],
            'mp_webhook' => [
                'general' => [
                    'enabled' => '1',
                    'abandoned_time' => '10',
                    'keep_log' => '10',
                    'send_to' => null,
                    'alert_enabled' => '0',
                    'resend' => '0',
                    'time' => '3',
                    'email_template' => 'mp_webhook_general_email_template',
                ],
                'cron' => [
                    'schedule' => '0',
                    'start_time' => '00,00,00',
                ],
            ],
            'sales_email' => [
                'general' => [
                    'async_sending' => '0',
                    'sending_limit' => '50',
                ],
                'order' => [
                    'enabled' => '1',
                    'identity' => 'support',
                    'template' => '5',
                    'guest_template' => '7',
                    'copy_method' => 'bcc',
                ],
                'order_comment' => [
                    'enabled' => '1',
                    'identity' => 'support',
                    'template' => '25',
                    'guest_template' => '27',
                    'copy_method' => 'bcc',
                ],
                'rejected_order' => [
                    'enabled' => '1',
                    'identity' => 'sales',
                    'template' => 'sales_email_rejected_order_template',
                    'guest_template' => 'sales_email_rejected_order_guest_template',
                    'copy_to' => null,
                    'copy_method' => 'bcc',
                ],
                'invoice' => [
                    'enabled' => '1',
                    'identity' => 'support',
                    'template' => '35',
                    'guest_template' => 'sales_email_invoice_guest_template',
                    'copy_method' => 'bcc',
                ],
                'invoice_comment' => [
                    'enabled' => '1',
                    'identity' => 'sales',
                    'template' => '39',
                    'guest_template' => '41',
                    'copy_method' => 'bcc',
                ],
                'shipment' => [
                    'enabled' => '1',
                    'identity' => 'sales',
                    'template' => '53',
                    'guest_template' => '55',
                    'copy_method' => 'bcc',
                ],
                'shipment_comment' => [
                    'enabled' => '1',
                    'identity' => 'sales',
                    'template' => '33',
                    'guest_template' => 'sales_email_shipment_comment_guest_template',
                    'copy_method' => 'bcc',
                ],
                'creditmemo' => [
                    'enabled' => '1',
                    'identity' => 'sales',
                    'template' => '43',
                    'guest_template' => '45',
                    'copy_method' => 'bcc',
                ],
                'creditmemo_comment' => [
                    'enabled' => '1',
                    'identity' => 'sales',
                    'template' => '47',
                    'guest_template' => '49',
                    'copy_method' => 'bcc',
                ],
                'order_ready_for_pickup' => [
                    'enabled' => '1',
                    'identity' => 'sales',
                    'template' => '69',
                    'guest_template' => '69',
                    'copy_to' => 'clint@kayweb.com.au',
                    'copy_method' => 'bcc',
                ],
                'magento_rma' => [
                    'enabled' => '1',
                    'identity' => 'support',
                    'template' => 'sales_email_magento_rma_template',
                    'guest_template' => 'sales_email_magento_rma_guest_template',
                    'copy_method' => 'bcc',
                ],
                'magento_rma_auth' => [
                    'enabled' => '1',
                    'identity' => 'support',
                    'template' => 'sales_email_magento_rma_auth_template',
                    'guest_template' => 'sales_email_magento_rma_auth_guest_template',
                    'copy_method' => 'bcc',
                ],
                'magento_rma_comment' => [
                    'enabled' => '1',
                    'identity' => 'support',
                    'template' => 'sales_email_magento_rma_comment_template',
                    'guest_template' => 'sales_email_magento_rma_comment_guest_template',
                    'copy_method' => 'bcc',
                ],
                'magento_rma_customer_comment' => [
                    'enabled' => '1',
                    'identity' => 'support',
                    'recipient' => 'sales',
                    'template' => 'sales_email_magento_rma_customer_comment_template',
                    'copy_method' => 'bcc',
                ],
                'temando_pickup' => [
                    'copy_to' => null,
                ],
            ],
            'sales_pdf' => [
                'invoice' => [
                    'put_order_id' => '1',
                ],
                'shipment' => [
                    'put_order_id' => '1',
                ],
                'creditmemo' => [
                    'put_order_id' => '1',
                ],
            ],
            'tax' => [
                'classes' => [
                    'shipping_tax_class' => '0',
                    'wrapping_tax_class' => '0',
                    'default_product_tax_class' => '2',
                    'default_customer_tax_class' => '3',
                ],
                'calculation' => [
                    'algorithm' => 'TOTAL_BASE_CALCULATION',
                    'based_on' => 'shipping',
                    'price_includes_tax' => '1',
                    'shipping_includes_tax' => '1',
                    'apply_after_discount' => '0',
                    'discount_tax' => '1',
                    'apply_tax_on' => '0',
                    'cross_border_trade_enabled' => '0',
                ],
                'defaults' => [
                    'country' => 'AU',
                    'region' => '0',
                    'postcode' => null,
                ],
                'display' => [
                    'type' => '1',
                    'shipping' => '1',
                ],
                'cart_display' => [
                    'price' => '1',
                    'subtotal' => '1',
                    'shipping' => '1',
                    'gift_wrapping' => '1',
                    'printed_card' => '1',
                    'grandtotal' => '0',
                    'full_summary' => '0',
                    'zero_tax' => '0',
                    'discount' => '1',
                ],
                'sales_display' => [
                    'price' => '1',
                    'subtotal' => '1',
                    'shipping' => '1',
                    'gift_wrapping' => '1',
                    'printed_card' => '1',
                    'grandtotal' => '0',
                    'full_summary' => '0',
                    'zero_tax' => '0',
                    'discount' => '1',
                ],
                'weee' => [
                    'enable' => '0',
                    'display_list' => '1',
                    'display' => '1',
                    'display_sales' => '1',
                    'display_email' => '1',
                    'apply_vat' => '0',
                    'include_in_subtotal' => '0',
                ],
                'notification' => [
                    'info_url' => 'https://docs.magento.com/user-guide/tax/warning-messages.html',
                    'ignore_discount' => '1',
                    'ignore_price_display' => '0',
                    'ignore_apply_discount' => '0',
                ],
                'vertex_settings' => [
                    'enable_vertex' => '0',
                ],
                'vertex_delivery_terms' => [
                    'override' => [],
                ],
            ],
            'checkout' => [
                'options' => [
                    'enable_guest_checkout_login' => '0',
                    'onepage_checkout_enabled' => '1',
                    'guest_checkout' => '1',
                    'display_billing_address_on' => '0',
                    'enable_agreements' => '0',
                    'max_items_display_count' => '10',
                    'enable_address_search' => '0',
                    'customer_address_limit' => '10',
                    'address_search_page_size' => '50',
                ],
                'cart' => [
                    'delete_quote_after' => '30',
                    'redirect_to_cart' => '0',
                    'number_items_to_display_pager' => '20',
                    'crosssell_enabled' => '1',
                    'grouped_product_image' => 'itself',
                    'configurable_product_image' => 'parent',
                    'preview_quota_lifetime' => '30',
                    'enable_clear_shopping_cart' => '0',
                ],
                'cart_link' => [
                    'use_qty' => '1',
                ],
                'sidebar' => [
                    'display' => '1',
                    'count' => '5',
                    'max_items_display_count' => '10',
                ],
                'payment_failed' => [
                    'identity' => 'general',
                    'receiver' => 'general',
                    'template' => 'checkout_payment_failed_template',
                    'copy_method' => 'bcc',
                ],
                'klarna_kp_design' => [
                    'color_details' => null,
                    'color_border' => null,
                    'color_border_selected' => null,
                    'color_text' => null,
                    'color_radius_border' => null,
                ],
                'klarna_kco' => [
                    'guest_checkout' => '1',
                    'enable_b2b' => '0',
                    'auto_focus' => '0',
                    'merchant_prefill' => '1',
                    'prefill_notice' => '0',
                    'title_mandatory' => '0',
                    'dob_mandatory' => '0',
                    'national_identification_number_mandatory' => '1',
                    'phone_mandatory' => '1',
                    'shipping_in_iframe' => '1',
                    'packstation_enabled' => '0',
                    'separate_address' => '0',
                    'terms_url' => 'terms',
                    'cancellation_terms_url' => null,
                    'failure_url' => null,
                    'merchant_checkbox' => '-1',
                    'merchant_checkbox_text' => null,
                    'merchant_checkbox_required' => '0',
                    'merchant_checkbox_checked' => '0',
                    'custom_checkboxes' => [],
                    'business_id_attribute' => '0',
                ],
                'klarna_kco_design' => [
                    'color_button' => null,
                    'color_button_text' => null,
                    'color_checkbox' => null,
                    'color_checkbox_checkmark' => null,
                    'color_header' => null,
                    'color_link' => null,
                    'radius_border' => null,
                ],
            ],
            'shipping' => [
                'origin' => [
                    'country_id' => 'AU',
                    'region_id' => '583',
                    'postcode' => '3000',
                    'city' => 'Melbourne',
                    'street_line1' => '217 Elizabeth St',
                    'street_line2' => null,
                ],
                'shipping_policy' => [
                    'enable_shipping_policy' => '0',
                    'shipping_policy_content' => null,
                ],
            ],
            'multishipping' => [
                'options' => [
                    'checkout_multiple' => '1',
                    'checkout_multiple_maximum_qty' => '100',
                ],
            ],
            'carriers' => [
                'flatrate' => [
                    'active' => '1',
                    'title' => 'Flat Rate',
                    'name' => '(1 to 3 Days)',
                    'type' => 'O',
                    'price' => '0',
                    'handling_type' => 'F',
                    'handling_fee' => null,
                    'specificerrmsg' => 'This shipping method is not available. To use this shipping method, please contact us.',
                    'sallowspecific' => '1',
                    'specificcountry' => 'AU',
                    'showmethod' => '0',
                    'sort_order' => '99',
                    'model' => 'Magento\\OfflineShipping\\Model\\Carrier\\Flatrate',
                ],
                'collect' => [
                    'active' => '1',
                    'active_on_pdp' => '0',
                    'active_on_cart' => '1',
                    'enable_single_store_in_cart_restriction' => '0',
                    'active_on_checkout' => '1',
                    'name' => 'Click and Collect Shipping',
                    'title' => 'Pick Up in Store',
                    'showmethod' => '0',
                    'specificerrmsg' => 'This shipping method is currently unavailable.',
                    'message_product_is_available_in_previously_store' => 'An item you are going to add to the cart is available in {STORE_NAME}.',
                    'message_product_is_not_available_in_previously_store' => 'An item you are going to add to the cart is not available in {STORE_NAME} as item(s) previously added to the cart.',
                    'variation' => 'single_cart',
                    'stock_update_interface' => '0',
                    'geo_location_method' => 'google_api',
                    'post_code_upload' => null,
                    'import_post_code_data_file' => null,
                    'google_api_key' => 'AIzaSyAKGpKARXYMcOCf34GFaTI7wrv-tVkl-L0',
                    'click_collect_entity' => '79',
                    'click_collect_fields_matrix' => '{"c98a36c1d4489420da4b828bb979763c":{"collect_field_column":"postcode","abstract_entity_attribute_field_column":"postcode"},"8ec34a34944071b1cab4423505aa14a4":{"collect_field_column":"longitude","abstract_entity_attribute_field_column":"longitude"},"9952e8740455bc868d31e84c24020955":{"collect_field_column":"latitude","abstract_entity_attribute_field_column":"latitude"},"50dc2fb4152b56c7d87d1e9ee3ed8eb7":{"collect_field_column":"address","abstract_entity_attribute_field_column":"street"},"fb371f13a2d61ccf1cf1086fac1169b7":{"collect_field_column":"name","abstract_entity_attribute_field_column":"name"},"82ff649fa9d0f46d96dc1df344244704":{"collect_field_column":"msi_sources","abstract_entity_attribute_field_column":"code"}}',
                    'prefill_shipping_address_fields_matrix' => '{"b51c1c6a4e662ea45795d74218d5a335":{"shipping_address_field_column":"city","abstract_entity_attribute_field_column":"city"},"afa6b532c2e625aa2d8512f07a21e8f2":{"shipping_address_field_column":"country_id","abstract_entity_attribute_field_column":"country"},"88162e7e56fa492e789cbd7066dde5e2":{"shipping_address_field_column":"postcode","abstract_entity_attribute_field_column":"postcode"},"c3a026b36afa9c75f76a90919652cf0e":{"shipping_address_field_column":"street","abstract_entity_attribute_field_column":"street"},"c4ba35b7bb17d61601d011035b883249":{"shipping_address_field_column":"region","abstract_entity_attribute_field_column":"state"}}',
                    'sallowspecific' => '1',
                    'default_distance_range' => '25,50,100,250,1000',
                    'specificcountry' => 'AU',
                    'show_unavailable_places' => '0',
                    'places_on_page' => null,
                    'default_shipping_address_label' => null,
                    'default_shipping_first_name' => 'Melbourne',
                    'default_shipping_last_name' => 'Store',
                    'default_shipping_city' => 'Melbourne',
                    'default_shipping_telephone' => '03 9608 6990',
                    'default_shipping_postcode' => '3000',
                    'default_shipping_street' => '217 Elizabeth St',
                    'country_id' => 'AU',
                    'region_id' => '583',
                    'default_address_template' => ' ({{var street}}, {{var city}}, {{var region}} {{var postcode}}, {{var country}})',
                    'enable_tracking_number_adding_notification' => '0',
                    'store_email_attribute' => null,
                    'tracking_number_adding_notification_template' => null,
                    'model' => 'Digidirect\\Collect\\Model\\Carrier\\Collectcarrier',
                ],
                'freeshipping' => [
                    'active' => '0',
                    'title' => 'This item qualifies for free shipping',
                    'name' => 'Free',
                    'free_shipping_subtotal' => '1000000',
                    'tax_including' => '0',
                    'specificerrmsg' => 'This shipping method is not available. To use this shipping method, please contact us.',
                    'sallowspecific' => '0',
                    'specificcountry' => null,
                    'showmethod' => '0',
                    'sort_order' => null,
                    'cutoff_cost' => '50',
                    'model' => 'Magento\\OfflineShipping\\Model\\Carrier\\Freeshipping',
                ],
                'tablerate' => [
                    'active' => '0',
                    'title' => 'Best Way',
                    'name' => 'Table Rate',
                    'condition_name' => 'package_weight',
                    'include_virtual_price' => '1',
                    'export' => null,
                    'import' => null,
                    'handling_type' => 'F',
                    'handling_fee' => null,
                    'specificerrmsg' => 'This shipping method is not available. To use this shipping method, please contact us.',
                    'sallowspecific' => '0',
                    'specificcountry' => null,
                    'showmethod' => '0',
                    'sort_order' => null,
                    'model' => 'Magento\\OfflineShipping\\Model\\Carrier\\Tablerate',
                ],
                'shipping' => [
                    'active' => '0',
                    'title' => 'Standard',
                    'name' => '(4 to 7 Days)',
                    'price' => '10',
                    'handling_type' => 'F',
                    'handling_fee' => null,
                    'specificerrmsg' => 'This shipping method is not available. To use this shipping method, please contact us.',
                    'showmethod' => '0',
                    'sallowspecific' => '1',
                    'specificcountry' => 'AU',
                    'sort_order' => '100',
                    'model' => 'Digidirect\\ExtendedShippingRates\\Model\\Carrier\\Shipping',
                    'type' => 'I',
                ],
                'instore' => [
                    'active' => '0',
                    'name' => 'Pick in Store',
                    'title' => 'In-Store Pickup Delivery',
                    'price' => '0.00',
                    'search_radius' => '200',
                    'specificerrmsg' => 'In-Store Delivery is not available. To use this delivery method, please contact us.',
                    'sallowspecific' => '0',
                    'model' => 'Magento\\InventoryInStorePickupShippingApi\\Model\\Carrier\\InStorePickup',
                ],
                'ups' => [
                    'active' => '0',
                    'active_rma' => '0',
                    'type' => 'UPS',
                    'origin_shipment' => 'Shipments Originating in United States',
                    'mode_xml' => '1',
                    'title' => 'United Parcel Service',
                    'negotiated_active' => '0',
                    'include_taxes' => '0',
                    'shipment_requesttype' => '0',
                    'container' => 'CP',
                    'dest_type' => 'RES',
                    'unit_of_measure' => 'LBS',
                    'max_package_weight' => '150',
                    'pickup' => 'CC',
                    'min_package_weight' => '0.1',
                    'handling_type' => 'F',
                    'handling_action' => 'O',
                    'handling_fee' => null,
                    'allowed_methods' => '1DM,1DML,1DA,1DAL,1DAPI,1DP,1DPL,2DM,2DML,2DA,2DAL,3DS,GND,GNDCOM,GNDRES,STD,XPR,WXS,XPRL,XDM,XDML,XPD',
                    'free_method' => 'GND',
                    'free_shipping_enable' => '0',
                    'free_shipping_subtotal' => null,
                    'specificerrmsg' => 'This shipping method is currently unavailable. If you would like to ship using this shipping method, please contact us.',
                    'sallowspecific' => '0',
                    'specificcountry' => null,
                    'showmethod' => '0',
                    'sort_order' => null,
                    'cutoff_cost' => null,
                    'handling' => '0',
                    'model' => 'Magento\\Ups\\Model\\Carrier',
                    'is_online' => '1',
                ],
                'usps' => [
                    'active' => '0',
                    'active_rma' => '0',
                    'title' => 'United States Postal Service',
                    'mode' => '0',
                    'shipment_requesttype' => '0',
                    'container' => 'VARIABLE',
                    'size' => 'REGULAR',
                    'length' => null,
                    'width' => null,
                    'height' => null,
                    'girth' => null,
                    'machinable' => 'true',
                    'max_package_weight' => '70',
                    'handling_type' => 'F',
                    'handling_action' => 'O',
                    'handling_fee' => null,
                    'allowed_methods' => '0_FCLE,0_FCL,0_FCP,1,2,3,4,6,7,13,16,17,22,23,25,27,28,33,34,35,36,37,42,43,53,57,61,INT_1,INT_2,INT_4,INT_6,INT_7,INT_8,INT_9,INT_10,INT_11,INT_12,INT_13,INT_14,INT_15,INT_16,INT_20',
                    'free_method' => null,
                    'free_shipping_enable' => '0',
                    'free_shipping_subtotal' => null,
                    'specificerrmsg' => 'This shipping method is currently unavailable. If you would like to ship using this shipping method, please contact us.',
                    'sallowspecific' => '0',
                    'specificcountry' => null,
                    'debug' => '0',
                    'showmethod' => '0',
                    'sort_order' => null,
                    'cutoff_cost' => null,
                    'handling' => null,
                    'methods' => null,
                    'model' => 'Magento\\Usps\\Model\\Carrier',
                    'isproduction' => '0',
                    'is_online' => '1',
                ],
                'fedex' => [
                    'active' => '0',
                    'active_rma' => '0',
                    'title' => 'Federal Express',
                    'shipment_requesttype' => '0',
                    'packaging' => 'YOUR_PACKAGING',
                    'dropoff' => 'REGULAR_PICKUP',
                    'unit_of_measure' => 'LB',
                    'max_package_weight' => '150',
                    'handling_type' => 'F',
                    'handling_action' => 'O',
                    'handling_fee' => null,
                    'residence_delivery' => '0',
                    'allowed_methods' => 'EUROPE_FIRST_INTERNATIONAL_PRIORITY,FEDEX_1_DAY_FREIGHT,FEDEX_2_DAY_FREIGHT,FEDEX_2_DAY,FEDEX_2_DAY_AM,FEDEX_3_DAY_FREIGHT,FEDEX_EXPRESS_SAVER,FEDEX_GROUND,FIRST_OVERNIGHT,GROUND_HOME_DELIVERY,INTERNATIONAL_ECONOMY,INTERNATIONAL_ECONOMY_FREIGHT,INTERNATIONAL_FIRST,INTERNATIONAL_GROUND,INTERNATIONAL_PRIORITY,INTERNATIONAL_PRIORITY_FREIGHT,PRIORITY_OVERNIGHT,SMART_POST,STANDARD_OVERNIGHT,FEDEX_FREIGHT,FEDEX_NATIONAL_FREIGHT',
                    'free_method' => 'FEDEX_GROUND',
                    'free_shipping_enable' => '0',
                    'free_shipping_subtotal' => null,
                    'specificerrmsg' => 'This shipping method is currently unavailable. If you would like to ship using this shipping method, please contact us.',
                    'sallowspecific' => '0',
                    'specificcountry' => null,
                    'debug' => '0',
                    'showmethod' => '0',
                    'sort_order' => null,
                    'cutoff_cost' => null,
                    'handling' => '0',
                    'model' => 'Magento\\Fedex\\Model\\Carrier',
                    'is_online' => '1',
                ],
                'dhl' => [
                    'active' => '0',
                    'active_rma' => '0',
                    'title' => 'DHL',
                    'content_type' => 'N',
                    'handling_type' => 'F',
                    'handling_action' => 'O',
                    'handling_fee' => null,
                    'divide_order_weight' => '1',
                    'unit_of_measure' => 'K',
                    'size' => '0',
                    'height' => null,
                    'depth' => null,
                    'width' => null,
                    'doc_methods' => null,
                    'nondoc_methods' => '1,3,4,8,P,Q,E,F,H,J,M,V,Y',
                    'ready_time' => null,
                    'specificerrmsg' => 'This shipping method is currently unavailable. If you would like to ship using this shipping method, please contact us.',
                    'free_method_doc' => null,
                    'free_method_nondoc' => null,
                    'free_shipping_enable' => '0',
                    'free_shipping_subtotal' => null,
                    'sallowspecific' => '0',
                    'specificcountry' => null,
                    'showmethod' => '0',
                    'sandbox_mode' => '0',
                    'sort_order' => null,
                    'model' => 'Magento\\Dhl\\Model\\Carrier',
                    'free_method' => 'G',
                    'sandbox_url' => 'https://xmlpitest-ea.dhl.com/XMLShippingServlet',
                    'shipment_days' => 'Mon,Tue,Wed,Thu,Fri',
                    'is_online' => '1',
                ],
                'm2eproshipping' => [
                    'active' => '1',
                    'title' => 'M2E Pro Shipping',
                ],
                'shipbympcatch' => [
                    'active' => '1',
                    'title' => 'Catch Shipping Method',
                ],
            ],
            'paymentfee' => [
                'general' => [
                    'active' => '1',
                    'title' => 'Payment and Handling Fee',
                    'is_description' => '0',
                    'description' => 'Test Payment Fee Description',
                    'total_sortorder' => '35',
                ],
                'paymentfee_settings' => [
                    'pricetype' => '1',
                    'include_shipping' => '1',
                    'include_discount' => '1',
                    'minorderamount' => '0',
                    'maxorderamount' => '99999',
                    'refund' => '1',
                    'paymentfee' => '{"_1674209048036_36":{"payment_method":"paypal_express","fee":"0.95"},"_1674209055570_570":{"payment_method":"free","fee":"0.95"},"_1674209067898_898":{"payment_method":"free","fee":"0.95"},"_1674209071970_970":{"payment_method":"latipay","fee":"0.95"},"_1674209083178_178":{"payment_method":"braintree","fee":"0.95"},"_1674209088585_585":{"payment_method":"braintree_paypal","fee":"0.95"},"_1674209095683_683":{"payment_method":"braintree_cc_vault","fee":"0.95"},"_1674209102708_708":{"payment_method":"braintree_paypal_vault","fee":"0.95"},"_1674209134300_300":{"payment_method":"braintree_applepay","fee":"0.95"},"_1674209141879_879":{"payment_method":"braintree_googlepay","fee":"0.95"},"_1674209160329_329":{"payment_method":"payflow_express","fee":"0.95"},"_1674209177441_441":{"payment_method":"payflow_express_bml","fee":"0.95"},"_1678334542558_558":{"payment_method":"latitudepay","fee":"0.95"}}',
                    'customers' => '0,1,2,3,10',
                ],
                'tax' => [
                    'enable' => '0',
                    'tax_class' => '2',
                    'display' => '2',
                ],
            ],
            'google' => [
                'analytics' => [
                    'active' => '1',
                    'type' => 'tag_manager',
                    'anonymize' => '0',
                    'experiments' => '0',
                    'catalog_page_list_value' => 'Catalog Page',
                    'crosssell_block_list_value' => 'Cross-sell',
                    'upsell_block_list_value' => 'Up-sell',
                    'related_block_list_value' => 'Related Products',
                    'search_page_list_value' => 'Search Results',
                    'promotions_list_value' => 'Label',
                ],
                'adwords' => [
                    'active' => '1',
                    'conversion_id' => '962495858',
                    'conversion_language' => 'en',
                    'conversion_format' => '2',
                    'conversion_color' => 'FFFFFF',
                    'conversion_label' => 'hqGFCKXItlgQ8or6ygM',
                    'conversion_value_type' => '1',
                    'conversion_value' => '0',
                    'send_currency' => '1',
                    'languages' => [
                        'ar' => 'ar',
                        'bg' => 'bg',
                        'ca' => 'ca',
                        'cs' => 'cs',
                        'da' => 'da',
                        'de' => 'de',
                        'el' => 'el',
                        'en' => 'en',
                        'es' => 'es',
                        'et' => 'et',
                        'fi' => 'fi',
                        'fr' => 'fr',
                        'hi' => 'hi',
                        'hr' => 'hr',
                        'hu' => 'hu',
                        'id' => 'id',
                        'is' => 'is',
                        'it' => 'it',
                        'iw' => 'iw',
                        'ja' => 'ja',
                        'ko' => 'ko',
                        'lt' => 'lt',
                        'lv' => 'lv',
                        'nl' => 'nl',
                        'no' => 'no',
                        'pl' => 'pl',
                        'pt' => 'pt',
                        'ro' => 'ro',
                        'ru' => 'ru',
                        'sk' => 'sk',
                        'sl' => 'sl',
                        'sr' => 'sr',
                        'sv' => 'sv',
                        'th' => 'th',
                        'tl' => 'tl',
                        'tr' => 'tr',
                        'uk' => 'uk',
                        'ur' => 'ur',
                        'vi' => 'vi',
                        'zh_TW' => 'zh_TW',
                        'zh_CN' => 'zh_CN',
                    ],
                    'language_convert' => [
                        'zh_CN' => 'zh_Hans',
                        'zh_TW' => 'zh_Hant',
                        'iw' => 'he',
                    ],
                    'conversion_js_src' => 'https://www.googleadservices.com/pagead/conversion.js',
                    'conversion_img_src' => 'https://www.googleadservices.com/pagead/conversion/%s/?label=%s&guid=ON&script=0',
                ],
                'optimizer' => [
                    'active' => '0',
                ],
            ],
            'swissup_core' => [
                'notification' => [
                    'enabled' => '1',
                    'use_https' => '1',
                    'feed_url' => 'swissuplabs.com/notifier',
                ],
                'troubleshooting' => [
                    'virtualcheck' => null,
                    'fix_all' => null,
                ],
                'modules' => [
                    'use_https' => '0',
                    'url' => 'swissup.github.io/packages',
                ],
                'license' => [
                    'use_https' => '1',
                    'url' => '/license/validate',
                ],
            ],
            'delete_orders' => [
                'general' => [
                    'enabled' => '1',
                    'order_status_color' => '1',
                ],
            ],
            'promo' => [
                'magento_reminder' => [
                    'enabled' => '1',
                    'frequency' => 'I',
                    'interval' => '15',
                    'minutes' => '0',
                    'time' => null,
                    'limit' => '100',
                    'threshold' => '1',
                ],
                'auto_generated_coupon_codes' => [
                    'length' => '12',
                    'quantity_limit' => '250000',
                    'format' => '1',
                    'prefix' => null,
                    'suffix' => null,
                    'dash' => null,
                ],
            ],
            'payment_us' => [
                'paypal_alternative_payment_methods' => [
                    'express_checkout_us' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'braintree_section' => [
                    'braintree' => [
                        'braintree_required' => [
                            'key_validation' => null,
                        ],
                        'braintree_advanced' => [
                            'kount' => [
                                'kount_ens_url' => null,
                            ],
                        ],
                    ],
                ],
                'paypal_group_all_in_one' => [
                    'payflow_advanced' => [
                        'required_settings' => [
                            'payments_advanced' => [
                                'payflow_advanced_info' => null,
                            ],
                            'advanced_advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_payments_advanced' => [
                            'settings_payments_advanced_advanced' => [
                                'settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'frontend' => [
                                    'paypal_pages' => null,
                                ],
                            ],
                        ],
                    ],
                    'wpp_usuk' => [
                        'paypal_payflow_required' => [
                            'enable_in_context_checkout' => null,
                            'merchant_id' => null,
                            'paypal_payflow_advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_paypal_payflow' => [
                            'heading_cc' => null,
                            'settings_paypal_payflow_advanced' => [
                                'paypal_payflow_avs_check' => [
                                    'heading_avs_settings' => null,
                                ],
                                'paypal_payflow_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'paypal_payflow_frontend' => [
                                    'paypal_pages' => null,
                                ],
                            ],
                        ],
                    ],
                    'wps_express' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'paypal_payment_gateways' => [
                    'paypal_payflowpro_with_express_checkout' => [
                        'paypal_payflow_required' => [
                            'enable_in_context_checkout' => null,
                            'merchant_id' => null,
                            'paypal_payflow_advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_paypal_payflow' => [
                            'heading_cc' => null,
                            'settings_paypal_payflow_advanced' => [
                                'paypal_payflow_avs_check' => [
                                    'heading_avs_settings' => null,
                                ],
                                'paypal_payflow_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'paypal_payflow_frontend' => [
                                    'paypal_pages' => null,
                                ],
                            ],
                        ],
                    ],
                    'payflow_link_us' => [
                        'payflow_link_required' => [
                            'payflow_link_payflow_link' => [
                                'payflowlink_info' => null,
                            ],
                            'payflow_link_advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_payflow_link' => [
                            'settings_payflow_link_advanced' => [
                                'payflow_link_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'payflow_link_frontend' => [
                                    'paypal_pages' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'zippayment' => [
                    'active' => null,
                    'title' => null,
                    'environment' => null,
                    'merchant_public_key' => null,
                    'merchant_private_key' => null,
                    'check_validity' => null,
                    'widget_region' => null,
                    'enable_tokenisation' => null,
                    'payment_action' => null,
                    'log_settings' => null,
                    'display_widget_mode' => null,
                    'incontext_checkout' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'zip_advert' => [
                        'homepage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                        'productpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'cartpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'categorypage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                    ],
                ],
                'free' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'payment_action' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'sort_order' => null,
                ],
                'checkmo' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'payable_to' => null,
                    'mailing_address' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'banktransfer' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'cashondelivery' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latitude_section' => [
                    'version' => null,
                ],
                'purchaseorder' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latipay' => [
                    'active' => null,
                    'title' => null,
                    'nzd_wallet_id' => null,
                    'aud_wallet_id' => null,
                    'cny_wallet_id' => null,
                    'user_id' => null,
                    'api_key' => null,
                    'tooltip' => null,
                    'instructions' => null,
                    'sort_order' => null,
                    'is_spotpay' => null,
                    'is_debug' => null,
                ],
            ],
            'payment_gb' => [
                'paypal_alternative_payment_methods' => [
                    'express_checkout_gb' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'braintree_section' => [
                    'braintree' => [
                        'braintree_required' => [
                            'key_validation' => null,
                        ],
                        'braintree_advanced' => [
                            'kount' => [
                                'kount_ens_url' => null,
                            ],
                        ],
                    ],
                ],
                'paypal_group_all_in_one' => [
                    'wpp_usuk' => [
                        'wpp_required_settings' => [
                            'enable_express_checkout_bml' => null,
                            'express_checkout_bml_sort_order' => null,
                        ],
                    ],
                    'payments_pro_hosted_solution_with_express_checkout' => [
                        'pphs_required_settings' => [
                            'express_checkout_bml_sort_order' => null,
                            'pphs_required_settings_pphs' => [
                                'api_wizard' => null,
                            ],
                            'pphs_advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'pphs_settings' => [
                            'pphs_settings_advanced' => [
                                'pphs_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'pphs_frontend' => [
                                    'paypal_pages' => null,
                                ],
                            ],
                        ],
                    ],
                    'wps_express' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'zippayment' => [
                    'active' => null,
                    'title' => null,
                    'environment' => null,
                    'merchant_public_key' => null,
                    'merchant_private_key' => null,
                    'check_validity' => null,
                    'widget_region' => null,
                    'enable_tokenisation' => null,
                    'payment_action' => null,
                    'log_settings' => null,
                    'display_widget_mode' => null,
                    'incontext_checkout' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'zip_advert' => [
                        'homepage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                        'productpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'cartpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'categorypage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                    ],
                ],
                'free' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'payment_action' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'sort_order' => null,
                ],
                'checkmo' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'payable_to' => null,
                    'mailing_address' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'banktransfer' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'cashondelivery' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latitude_section' => [
                    'version' => null,
                ],
                'purchaseorder' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latipay' => [
                    'active' => null,
                    'title' => null,
                    'nzd_wallet_id' => null,
                    'aud_wallet_id' => null,
                    'cny_wallet_id' => null,
                    'user_id' => null,
                    'api_key' => null,
                    'tooltip' => null,
                    'instructions' => null,
                    'sort_order' => null,
                    'is_spotpay' => null,
                    'is_debug' => null,
                ],
            ],
            'payment_de' => [
                'paypal_payment_solutions' => [
                    'express_checkout_de' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'braintree_section' => [
                    'braintree' => [
                        'braintree_required' => [
                            'key_validation' => null,
                        ],
                        'braintree_advanced' => [
                            'kount' => [
                                'kount_ens_url' => null,
                            ],
                        ],
                    ],
                ],
                'zippayment' => [
                    'active' => null,
                    'title' => null,
                    'environment' => null,
                    'merchant_public_key' => null,
                    'merchant_private_key' => null,
                    'check_validity' => null,
                    'widget_region' => null,
                    'enable_tokenisation' => null,
                    'payment_action' => null,
                    'log_settings' => null,
                    'display_widget_mode' => null,
                    'incontext_checkout' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'zip_advert' => [
                        'homepage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                        'productpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'cartpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'categorypage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                    ],
                ],
                'free' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'payment_action' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'sort_order' => null,
                ],
                'checkmo' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'payable_to' => null,
                    'mailing_address' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'banktransfer' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'cashondelivery' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latitude_section' => [
                    'version' => null,
                ],
                'purchaseorder' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latipay' => [
                    'active' => null,
                    'title' => null,
                    'nzd_wallet_id' => null,
                    'aud_wallet_id' => null,
                    'cny_wallet_id' => null,
                    'user_id' => null,
                    'api_key' => null,
                    'tooltip' => null,
                    'instructions' => null,
                    'sort_order' => null,
                    'is_spotpay' => null,
                    'is_debug' => null,
                ],
            ],
            'payment_other' => [
                'express_checkout_other' => [
                    'express_checkout_required' => [
                        'onboarding_wizard' => null,
                        'express_checkout_required_express_checkout' => [
                            'api_wizard' => null,
                        ],
                        'advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'settings_ec' => [
                        'settings_ec_advanced' => [
                            'express_checkout_settlement_report' => [
                                'heading_sftp' => null,
                                'heading_schedule' => null,
                            ],
                            'express_checkout_frontend' => [
                                'paypal_pages' => null,
                                'checkout_display' => null,
                            ],
                        ],
                    ],
                ],
                'braintree_section' => [
                    'braintree' => [
                        'braintree_required' => [
                            'key_validation' => null,
                        ],
                        'braintree_advanced' => [
                            'kount' => [
                                'kount_ens_url' => null,
                            ],
                        ],
                    ],
                ],
                'paypal_group_all_in_one' => [
                    'wps_other' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'zippayment' => [
                    'active' => null,
                    'title' => null,
                    'environment' => null,
                    'merchant_public_key' => null,
                    'merchant_private_key' => null,
                    'check_validity' => null,
                    'widget_region' => null,
                    'enable_tokenisation' => null,
                    'payment_action' => null,
                    'log_settings' => null,
                    'display_widget_mode' => null,
                    'incontext_checkout' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'zip_advert' => [
                        'homepage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                        'productpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'cartpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'categorypage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                    ],
                ],
                'free' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'payment_action' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'sort_order' => null,
                ],
                'checkmo' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'payable_to' => null,
                    'mailing_address' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'banktransfer' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'cashondelivery' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latitude_section' => [
                    'version' => null,
                ],
                'purchaseorder' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latipay' => [
                    'active' => null,
                    'title' => null,
                    'nzd_wallet_id' => null,
                    'aud_wallet_id' => null,
                    'cny_wallet_id' => null,
                    'user_id' => null,
                    'api_key' => null,
                    'tooltip' => null,
                    'instructions' => null,
                    'sort_order' => null,
                    'is_spotpay' => null,
                    'is_debug' => null,
                ],
            ],
            'payment_ca' => [
                'express_checkout_other' => [
                    'express_checkout_required' => [
                        'onboarding_wizard' => null,
                        'express_checkout_required_express_checkout' => [
                            'api_wizard' => null,
                        ],
                        'advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'settings_ec' => [
                        'settings_ec_advanced' => [
                            'express_checkout_settlement_report' => [
                                'heading_sftp' => null,
                                'heading_schedule' => null,
                            ],
                            'express_checkout_frontend' => [
                                'paypal_pages' => null,
                                'checkout_display' => null,
                            ],
                        ],
                    ],
                ],
                'braintree_section' => [
                    'braintree' => [
                        'braintree_required' => [
                            'key_validation' => null,
                        ],
                        'braintree_advanced' => [
                            'kount' => [
                                'kount_ens_url' => null,
                            ],
                        ],
                    ],
                ],
                'paypal_group_all_in_one' => [
                    'wps_other' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'paypal_payment_gateways' => [
                    'wpp_ca' => [
                        'paypal_payflow_required' => [
                            'enable_in_context_checkout' => null,
                            'merchant_id' => null,
                        ],
                        'settings_paypal_payflow' => [
                            'heading_cc' => null,
                            'settings_paypal_payflow_advanced' => [
                                'paypal_payflow_avs_check' => [
                                    'heading_avs_settings' => null,
                                ],
                                'paypal_payflow_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                            ],
                        ],
                    ],
                    'paypal_payflowpro_ca' => [
                        'settings_paypal_payflow' => [
                            'heading_cc' => null,
                            'settings_paypal_payflow_advanced' => [
                                'paypal_payflow_avs_check' => [
                                    'heading_avs_settings' => null,
                                ],
                                'paypal_payflow_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                            ],
                        ],
                    ],
                    'payflow_link_ca' => [
                        'payflow_link_required' => [
                            'payflow_link_payflow_link' => [
                                'payflowlink_info' => null,
                            ],
                            'payflow_link_advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_payflow_link' => [
                            'settings_payflow_link_advanced' => [
                                'payflow_link_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'payflow_link_frontend' => [
                                    'paypal_pages' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'zippayment' => [
                    'active' => null,
                    'title' => null,
                    'environment' => null,
                    'merchant_public_key' => null,
                    'merchant_private_key' => null,
                    'check_validity' => null,
                    'widget_region' => null,
                    'enable_tokenisation' => null,
                    'payment_action' => null,
                    'log_settings' => null,
                    'display_widget_mode' => null,
                    'incontext_checkout' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'zip_advert' => [
                        'homepage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                        'productpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'cartpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'categorypage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                    ],
                ],
                'free' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'payment_action' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'sort_order' => null,
                ],
                'checkmo' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'payable_to' => null,
                    'mailing_address' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'banktransfer' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'cashondelivery' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latitude_section' => [
                    'version' => null,
                ],
                'purchaseorder' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latipay' => [
                    'active' => null,
                    'title' => null,
                    'nzd_wallet_id' => null,
                    'aud_wallet_id' => null,
                    'cny_wallet_id' => null,
                    'user_id' => null,
                    'api_key' => null,
                    'tooltip' => null,
                    'instructions' => null,
                    'sort_order' => null,
                    'is_spotpay' => null,
                    'is_debug' => null,
                ],
            ],
            'payment_au' => [
                'express_checkout_other' => [
                    'express_checkout_required' => [
                        'onboarding_wizard' => null,
                        'express_checkout_required_express_checkout' => [
                            'api_wizard' => null,
                        ],
                        'advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'settings_ec' => [
                        'settings_ec_advanced' => [
                            'express_checkout_settlement_report' => [
                                'heading_sftp' => null,
                                'heading_schedule' => null,
                            ],
                            'express_checkout_frontend' => [
                                'paypal_pages' => null,
                                'checkout_display' => null,
                            ],
                        ],
                    ],
                ],
                'braintree_section' => [
                    'braintree' => [
                        'braintree_required' => [
                            'key_validation' => null,
                        ],
                        'braintree_advanced' => [
                            'kount' => [
                                'kount_ens_url' => null,
                            ],
                        ],
                    ],
                ],
                'paypal_group_all_in_one' => [
                    'payments_pro_hosted_solution_au' => [
                        'pphs_required_settings' => [
                            'express_checkout_bml_sort_order' => null,
                            'pphs_required_settings_pphs' => [
                                'api_wizard' => null,
                            ],
                            'pphs_advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'pphs_settings' => [
                            'pphs_settings_advanced' => [
                                'pphs_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                            ],
                        ],
                    ],
                    'wps_other' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'paypal_payment_gateways' => [
                    'paypal_payflowpro_au' => [
                        'settings_paypal_payflow' => [
                            'heading_cc' => null,
                            'settings_paypal_payflow_advanced' => [
                                'paypal_payflow_avs_check' => [
                                    'heading_avs_settings' => null,
                                ],
                                'paypal_payflow_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'zippayment' => [
                    'active' => null,
                    'title' => null,
                    'environment' => null,
                    'merchant_public_key' => null,
                    'merchant_private_key' => null,
                    'check_validity' => null,
                    'widget_region' => null,
                    'enable_tokenisation' => null,
                    'payment_action' => null,
                    'log_settings' => null,
                    'display_widget_mode' => null,
                    'incontext_checkout' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'zip_advert' => [
                        'homepage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                        'productpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'cartpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'categorypage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                    ],
                ],
                'free' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'payment_action' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'sort_order' => null,
                ],
                'checkmo' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'payable_to' => null,
                    'mailing_address' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'banktransfer' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'cashondelivery' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latitude_section' => [
                    'version' => null,
                ],
                'purchaseorder' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latipay' => [
                    'active' => null,
                    'title' => null,
                    'nzd_wallet_id' => null,
                    'aud_wallet_id' => null,
                    'cny_wallet_id' => null,
                    'user_id' => null,
                    'api_key' => null,
                    'tooltip' => null,
                    'instructions' => null,
                    'sort_order' => null,
                    'is_spotpay' => null,
                    'is_debug' => null,
                ],
            ],
            'payment_jp' => [
                'express_checkout_other' => [
                    'express_checkout_required' => [
                        'onboarding_wizard' => null,
                        'express_checkout_required_express_checkout' => [
                            'api_wizard' => null,
                        ],
                        'advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'settings_ec' => [
                        'settings_ec_advanced' => [
                            'express_checkout_settlement_report' => [
                                'heading_sftp' => null,
                                'heading_schedule' => null,
                            ],
                            'express_checkout_frontend' => [
                                'paypal_pages' => null,
                                'checkout_display' => null,
                            ],
                        ],
                    ],
                ],
                'braintree_section' => [
                    'braintree' => [
                        'braintree_required' => [
                            'key_validation' => null,
                        ],
                        'braintree_advanced' => [
                            'kount' => [
                                'kount_ens_url' => null,
                            ],
                        ],
                    ],
                ],
                'paypal_group_all_in_one' => [
                    'payments_pro_hosted_solution_jp' => [
                        'pphs_required_settings' => [
                            'express_checkout_bml_sort_order' => null,
                            'pphs_required_settings_pphs' => [
                                'api_wizard' => null,
                            ],
                            'pphs_advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'pphs_settings' => [
                            'pphs_settings_advanced' => [
                                'pphs_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                            ],
                        ],
                    ],
                    'wps_other' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'zippayment' => [
                    'active' => null,
                    'title' => null,
                    'environment' => null,
                    'merchant_public_key' => null,
                    'merchant_private_key' => null,
                    'check_validity' => null,
                    'widget_region' => null,
                    'enable_tokenisation' => null,
                    'payment_action' => null,
                    'log_settings' => null,
                    'display_widget_mode' => null,
                    'incontext_checkout' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'zip_advert' => [
                        'homepage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                        'productpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'cartpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'categorypage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                    ],
                ],
                'free' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'payment_action' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'sort_order' => null,
                ],
                'checkmo' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'payable_to' => null,
                    'mailing_address' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'banktransfer' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'cashondelivery' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latitude_section' => [
                    'version' => null,
                ],
                'purchaseorder' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latipay' => [
                    'active' => null,
                    'title' => null,
                    'nzd_wallet_id' => null,
                    'aud_wallet_id' => null,
                    'cny_wallet_id' => null,
                    'user_id' => null,
                    'api_key' => null,
                    'tooltip' => null,
                    'instructions' => null,
                    'sort_order' => null,
                    'is_spotpay' => null,
                    'is_debug' => null,
                ],
            ],
            'payment_fr' => [
                'express_checkout_other' => [
                    'express_checkout_required' => [
                        'onboarding_wizard' => null,
                        'express_checkout_required_express_checkout' => [
                            'api_wizard' => null,
                        ],
                        'advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'settings_ec' => [
                        'settings_ec_advanced' => [
                            'express_checkout_settlement_report' => [
                                'heading_sftp' => null,
                                'heading_schedule' => null,
                            ],
                            'express_checkout_frontend' => [
                                'paypal_pages' => null,
                                'checkout_display' => null,
                            ],
                        ],
                    ],
                ],
                'braintree_section' => [
                    'braintree' => [
                        'braintree_required' => [
                            'key_validation' => null,
                        ],
                        'braintree_advanced' => [
                            'kount' => [
                                'kount_ens_url' => null,
                            ],
                        ],
                    ],
                ],
                'paypal_group_all_in_one' => [
                    'payments_pro_hosted_solution_fr' => [
                        'pphs_required_settings' => [
                            'express_checkout_bml_sort_order' => null,
                            'pphs_required_settings_pphs' => [
                                'api_wizard' => null,
                            ],
                            'pphs_advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'pphs_settings' => [
                            'pphs_settings_advanced' => [
                                'pphs_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                            ],
                        ],
                    ],
                    'wps_other' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'zippayment' => [
                    'active' => null,
                    'title' => null,
                    'environment' => null,
                    'merchant_public_key' => null,
                    'merchant_private_key' => null,
                    'check_validity' => null,
                    'widget_region' => null,
                    'enable_tokenisation' => null,
                    'payment_action' => null,
                    'log_settings' => null,
                    'display_widget_mode' => null,
                    'incontext_checkout' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'zip_advert' => [
                        'homepage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                        'productpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'cartpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'categorypage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                    ],
                ],
                'free' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'payment_action' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'sort_order' => null,
                ],
                'checkmo' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'payable_to' => null,
                    'mailing_address' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'banktransfer' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'cashondelivery' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latitude_section' => [
                    'version' => null,
                ],
                'purchaseorder' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latipay' => [
                    'active' => null,
                    'title' => null,
                    'nzd_wallet_id' => null,
                    'aud_wallet_id' => null,
                    'cny_wallet_id' => null,
                    'user_id' => null,
                    'api_key' => null,
                    'tooltip' => null,
                    'instructions' => null,
                    'sort_order' => null,
                    'is_spotpay' => null,
                    'is_debug' => null,
                ],
            ],
            'payment_it' => [
                'express_checkout_other' => [
                    'express_checkout_required' => [
                        'onboarding_wizard' => null,
                        'express_checkout_required_express_checkout' => [
                            'api_wizard' => null,
                        ],
                        'advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'settings_ec' => [
                        'settings_ec_advanced' => [
                            'express_checkout_settlement_report' => [
                                'heading_sftp' => null,
                                'heading_schedule' => null,
                            ],
                            'express_checkout_frontend' => [
                                'paypal_pages' => null,
                                'checkout_display' => null,
                            ],
                        ],
                    ],
                ],
                'braintree_section' => [
                    'braintree' => [
                        'braintree_required' => [
                            'key_validation' => null,
                        ],
                        'braintree_advanced' => [
                            'kount' => [
                                'kount_ens_url' => null,
                            ],
                        ],
                    ],
                ],
                'paypal_group_all_in_one' => [
                    'payments_pro_hosted_solution_it' => [
                        'pphs_required_settings' => [
                            'express_checkout_bml_sort_order' => null,
                            'pphs_required_settings_pphs' => [
                                'api_wizard' => null,
                            ],
                            'pphs_advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'pphs_settings' => [
                            'pphs_settings_advanced' => [
                                'pphs_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                            ],
                        ],
                    ],
                    'wps_other' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'zippayment' => [
                    'active' => null,
                    'title' => null,
                    'environment' => null,
                    'merchant_public_key' => null,
                    'merchant_private_key' => null,
                    'check_validity' => null,
                    'widget_region' => null,
                    'enable_tokenisation' => null,
                    'payment_action' => null,
                    'log_settings' => null,
                    'display_widget_mode' => null,
                    'incontext_checkout' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'zip_advert' => [
                        'homepage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                        'productpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'cartpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'categorypage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                    ],
                ],
                'free' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'payment_action' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'sort_order' => null,
                ],
                'checkmo' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'payable_to' => null,
                    'mailing_address' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'banktransfer' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'cashondelivery' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latitude_section' => [
                    'version' => null,
                ],
                'purchaseorder' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latipay' => [
                    'active' => null,
                    'title' => null,
                    'nzd_wallet_id' => null,
                    'aud_wallet_id' => null,
                    'cny_wallet_id' => null,
                    'user_id' => null,
                    'api_key' => null,
                    'tooltip' => null,
                    'instructions' => null,
                    'sort_order' => null,
                    'is_spotpay' => null,
                    'is_debug' => null,
                ],
            ],
            'payment_es' => [
                'express_checkout_other' => [
                    'express_checkout_required' => [
                        'onboarding_wizard' => null,
                        'express_checkout_required_express_checkout' => [
                            'api_wizard' => null,
                        ],
                        'advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'settings_ec' => [
                        'settings_ec_advanced' => [
                            'express_checkout_settlement_report' => [
                                'heading_sftp' => null,
                                'heading_schedule' => null,
                            ],
                            'express_checkout_frontend' => [
                                'paypal_pages' => null,
                                'checkout_display' => null,
                            ],
                        ],
                    ],
                ],
                'braintree_section' => [
                    'braintree' => [
                        'braintree_required' => [
                            'key_validation' => null,
                        ],
                        'braintree_advanced' => [
                            'kount' => [
                                'kount_ens_url' => null,
                            ],
                        ],
                    ],
                ],
                'paypal_group_all_in_one' => [
                    'payments_pro_hosted_solution_es' => [
                        'pphs_required_settings' => [
                            'express_checkout_bml_sort_order' => null,
                            'pphs_required_settings_pphs' => [
                                'api_wizard' => null,
                            ],
                            'pphs_advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'pphs_settings' => [
                            'pphs_settings_advanced' => [
                                'pphs_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                            ],
                        ],
                    ],
                    'wps_other' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'zippayment' => [
                    'active' => null,
                    'title' => null,
                    'environment' => null,
                    'merchant_public_key' => null,
                    'merchant_private_key' => null,
                    'check_validity' => null,
                    'widget_region' => null,
                    'enable_tokenisation' => null,
                    'payment_action' => null,
                    'log_settings' => null,
                    'display_widget_mode' => null,
                    'incontext_checkout' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'zip_advert' => [
                        'homepage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                        'productpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'cartpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'categorypage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                    ],
                ],
                'free' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'payment_action' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'sort_order' => null,
                ],
                'checkmo' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'payable_to' => null,
                    'mailing_address' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'banktransfer' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'cashondelivery' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latitude_section' => [
                    'version' => null,
                ],
                'purchaseorder' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latipay' => [
                    'active' => null,
                    'title' => null,
                    'nzd_wallet_id' => null,
                    'aud_wallet_id' => null,
                    'cny_wallet_id' => null,
                    'user_id' => null,
                    'api_key' => null,
                    'tooltip' => null,
                    'instructions' => null,
                    'sort_order' => null,
                    'is_spotpay' => null,
                    'is_debug' => null,
                ],
            ],
            'payment_hk' => [
                'express_checkout_other' => [
                    'express_checkout_required' => [
                        'onboarding_wizard' => null,
                        'express_checkout_required_express_checkout' => [
                            'api_wizard' => null,
                        ],
                        'advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'settings_ec' => [
                        'settings_ec_advanced' => [
                            'express_checkout_settlement_report' => [
                                'heading_sftp' => null,
                                'heading_schedule' => null,
                            ],
                            'express_checkout_frontend' => [
                                'paypal_pages' => null,
                                'checkout_display' => null,
                            ],
                        ],
                    ],
                ],
                'braintree_section' => [
                    'braintree' => [
                        'braintree_required' => [
                            'key_validation' => null,
                        ],
                        'braintree_advanced' => [
                            'kount' => [
                                'kount_ens_url' => null,
                            ],
                        ],
                    ],
                ],
                'paypal_group_all_in_one' => [
                    'payments_pro_hosted_solution_hk' => [
                        'pphs_required_settings' => [
                            'express_checkout_bml_sort_order' => null,
                            'pphs_required_settings_pphs' => [
                                'api_wizard' => null,
                            ],
                            'pphs_advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'pphs_settings' => [
                            'pphs_settings_advanced' => [
                                'pphs_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                            ],
                        ],
                    ],
                    'wps_other' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'zippayment' => [
                    'active' => null,
                    'title' => null,
                    'environment' => null,
                    'merchant_public_key' => null,
                    'merchant_private_key' => null,
                    'check_validity' => null,
                    'widget_region' => null,
                    'enable_tokenisation' => null,
                    'payment_action' => null,
                    'log_settings' => null,
                    'display_widget_mode' => null,
                    'incontext_checkout' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'zip_advert' => [
                        'homepage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                        'productpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'cartpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'categorypage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                    ],
                ],
                'free' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'payment_action' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'sort_order' => null,
                ],
                'checkmo' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'payable_to' => null,
                    'mailing_address' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'banktransfer' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'cashondelivery' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latitude_section' => [
                    'version' => null,
                ],
                'purchaseorder' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latipay' => [
                    'active' => null,
                    'title' => null,
                    'nzd_wallet_id' => null,
                    'aud_wallet_id' => null,
                    'cny_wallet_id' => null,
                    'user_id' => null,
                    'api_key' => null,
                    'tooltip' => null,
                    'instructions' => null,
                    'sort_order' => null,
                    'is_spotpay' => null,
                    'is_debug' => null,
                ],
            ],
            'payment_nz' => [
                'express_checkout_other' => [
                    'express_checkout_required' => [
                        'onboarding_wizard' => null,
                        'express_checkout_required_express_checkout' => [
                            'api_wizard' => null,
                        ],
                        'advertise_bml' => [
                            'bml_wizard' => null,
                        ],
                    ],
                    'settings_ec' => [
                        'settings_ec_advanced' => [
                            'express_checkout_settlement_report' => [
                                'heading_sftp' => null,
                                'heading_schedule' => null,
                            ],
                            'express_checkout_frontend' => [
                                'paypal_pages' => null,
                                'checkout_display' => null,
                            ],
                        ],
                    ],
                ],
                'braintree_section' => [
                    'braintree' => [
                        'braintree_required' => [
                            'key_validation' => null,
                        ],
                        'braintree_advanced' => [
                            'kount' => [
                                'kount_ens_url' => null,
                            ],
                        ],
                    ],
                ],
                'paypal_group_all_in_one' => [
                    'wps_other' => [
                        'express_checkout_required' => [
                            'onboarding_wizard' => null,
                            'express_checkout_required_express_checkout' => [
                                'api_wizard' => null,
                            ],
                            'advertise_bml' => [
                                'bml_wizard' => null,
                            ],
                        ],
                        'settings_ec' => [
                            'settings_ec_advanced' => [
                                'express_checkout_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                                'express_checkout_frontend' => [
                                    'paypal_pages' => null,
                                    'checkout_display' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'paypal_payment_gateways' => [
                    'paypal_payflowpro_nz' => [
                        'settings_paypal_payflow' => [
                            'heading_cc' => null,
                            'settings_paypal_payflow_advanced' => [
                                'paypal_payflow_avs_check' => [
                                    'heading_avs_settings' => null,
                                ],
                                'paypal_payflow_settlement_report' => [
                                    'heading_sftp' => null,
                                    'heading_schedule' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                'zippayment' => [
                    'active' => null,
                    'title' => null,
                    'environment' => null,
                    'merchant_public_key' => null,
                    'merchant_private_key' => null,
                    'check_validity' => null,
                    'widget_region' => null,
                    'enable_tokenisation' => null,
                    'payment_action' => null,
                    'log_settings' => null,
                    'display_widget_mode' => null,
                    'incontext_checkout' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                    'zip_advert' => [
                        'homepage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                        'productpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'cartpage' => [
                            'banner' => null,
                            'banner_selector' => null,
                            'widget' => null,
                            'widget_selector' => null,
                            'tagline' => null,
                            'tagline_selector' => null,
                        ],
                        'categorypage' => [
                            'banner' => null,
                            'banner_selector' => null,
                        ],
                    ],
                ],
                'free' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'payment_action' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'sort_order' => null,
                ],
                'checkmo' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'payable_to' => null,
                    'mailing_address' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'banktransfer' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'cashondelivery' => [
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'instructions' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latitude_section' => [
                    'version' => null,
                ],
                'purchaseorder' => [
                    'model' => null,
                    'active' => null,
                    'title' => null,
                    'order_status' => null,
                    'allowspecific' => null,
                    'specificcountry' => null,
                    'min_order_total' => null,
                    'max_order_total' => null,
                    'sort_order' => null,
                ],
                'latipay' => [
                    'active' => null,
                    'title' => null,
                    'nzd_wallet_id' => null,
                    'aud_wallet_id' => null,
                    'cny_wallet_id' => null,
                    'user_id' => null,
                    'api_key' => null,
                    'tooltip' => null,
                    'instructions' => null,
                    'sort_order' => null,
                    'is_spotpay' => null,
                    'is_debug' => null,
                ],
            ],
            'seo' => [
                'general' => [
                    'is_paging_prevnext' => '0',
                    'is_add_canonical_url' => '1',
                    'is_longest_canonical_url' => '0',
                    'associated_canonical_configurable_product' => '0',
                    'associated_canonical_grouped_product' => '0',
                    'associated_canonical_bundle_product' => '0',
                    'canonical_layered' => '0',
                    'canonical_layered_config' => [],
                    'canonical_store_without_store_code' => '0',
                    'crossdomain' => '0',
                    'crossdomain_prefer_https' => '0',
                    'paginated_canonical' => '0',
                    'canonical_url_ignore_pages' => null,
                    'noindex_pages2' => [],
                    'https_noindex_pages' => '0',
                    'is_alternate_hreflang' => '0',
                    'alternate_configurable' => [],
                    'configurable_hreflang_x_default' => [],
                    'is_hreflang_locale_code_automatical' => null,
                    'is_hreflang_cut_category_additional_data' => null,
                    'hreflang_locale_code' => null,
                    'is_hreflang_x_default' => null,
                    'trailing_slash' => '0',
                    'layered_navigation_friendly_urls' => '1',
                    'is_enable_image_friendly_urls' => '0',
                    'image_url_template' => '[product_name]',
                    'is_enable_image_alt' => '0',
                    'image_alt_template' => '[product_name]',
                    'is_category_meta_tags_used' => '1',
                    'is_product_meta_tags_used' => '1',
                ],
                'seo_content' => [
                    'meta' => [
                        'is_category_meta_tags_used' => '1',
                        'is_product_meta_tags_used' => '1',
                        'is_cms_meta_tags_used' => '1',
                        'is_blog_meta_tags_used' => '1',
                        'is_brand_meta_tags_used' => '1',
                        'is_add_prefix_suffix' => '0',
                    ],
                    'pagination' => [
                        'meta_title_page_number' => '1',
                        'meta_description_page_number' => '0',
                    ],
                    'limiter' => [
                        'is_use_html_symbols_in_meta_tags' => '0',
                        'meta_title_max_length' => null,
                        'meta_description_max_length' => null,
                        'product_name_max_length' => null,
                        'product_short_description_max_length' => null,
                    ],
                ],
                'seo_markup' => [
                    'product' => [
                        'is_rs_enabled' => '1',
                        'is_remove_native_rs' => '1',
                        'is_price_enabled' => '1',
                        'description_type' => '0',
                        'is_image_enabled' => '0',
                        'is_availability_enabled' => '0',
                        'is_accepted_payment_method_enabled' => '0',
                        'is_available_delivery_method_enabled' => '0',
                        'is_category_enabled' => '0',
                        'is_mpn_enabled' => '0',
                        'mpn_attribute' => 'sku',
                        'brand_attribute' => 'manufacturer',
                        'model_attribute' => 'name',
                        'color_attribute' => 'color',
                        'size_attribute' => 'size',
                        'weight_unit_type' => '0',
                        'is_dimensions_enabled' => '0',
                        'dimension_unit' => null,
                        'dimension_height_attribute' => null,
                        'dimension_width_attribute' => null,
                        'dimension_depth_attribute' => null,
                        'item_condition_type' => '0',
                        'item_condition_attribute' => null,
                        'item_condition_attribute_value_new' => null,
                        'item_condition_attribute_value_used' => null,
                        'item_condition_attribute_value_refurbished' => null,
                        'item_condition_attribute_value_damaged' => null,
                        'is_individual_reviews_enabled' => '0',
                        'is_variants_enabled' => '0',
                        'gtin' => null,
                        'gtin8_attribute' => 'sku',
                        'gtin12_attribute' => null,
                        'gtin13_attribute' => null,
                        'gtin14_attribute' => null,
                    ],
                    'category' => [
                        'is_remove_native_rs' => '1',
                        'is_rs_enabled' => '0',
                        'product_offers_type' => '0',
                        'is_og_enabled' => '0',
                        'description_type' => null,
                        'is_image_enabled' => null,
                    ],
                    'page' => [
                        'is_remove_native_rs' => '0',
                        'is_og_enabled' => '0',
                    ],
                    'organization' => [
                        'is_rs_enabled' => '0',
                        'is_custom_name' => null,
                        'custom_name' => null,
                        'is_custom_address_country' => null,
                        'custom_address_country' => null,
                        'is_custom_address_locality' => null,
                        'custom_address_locality' => null,
                        'is_custom_address_region' => null,
                        'custom_address_region' => null,
                        'is_custom_postal_code' => null,
                        'custom_postal_code' => null,
                        'is_custom_street_address' => null,
                        'custom_street_address' => null,
                        'is_custom_telephone' => null,
                        'custom_telephone' => null,
                        'custom_fax_number' => null,
                        'is_custom_email' => null,
                        'custom_email' => null,
                        'youtube_link' => null,
                        'facebook_link' => null,
                        'linkedin_link' => null,
                        'instagram_link' => null,
                        'pinterest_link' => null,
                        'tumblr_link' => null,
                        'twitter_link' => null,
                    ],
                    'breadcrumb_list' => [
                        'is_rs_enabled' => '0',
                    ],
                    'twitter' => [
                        'card_type' => '0',
                        'username' => null,
                    ],
                    'searchbox' => [
                        'searchbox_type' => '0',
                        'blog_search_url' => null,
                    ],
                ],
                'extended' => [
                    'redirect_to_lowercase' => '0',
                    'to_lowercase_allowed_types' => [],
                    'meta_title_page_number' => '1',
                ],
                'url' => [
                    'use_category_short_url' => '0',
                    'apply_category_short_url_button' => null,
                    'product_url_key' => null,
                    'apply_url_key_for_new_products' => '0',
                    'regenerate_url_key_after_visibility_changed' => '0',
                    'trailing_slash' => '0',
                ],
                'image' => [
                    'is_enable_image_friendly_url' => '0',
                    'image_url_template' => null,
                    'is_enable_image_alt' => '0',
                    'image_alt_template' => null,
                    'image_title_template' => null,
                ],
                'seo_toolbar' => [
                    'is_active' => '1',
                    'allowed_ip' => '119.92.142.71',
                ],
            ],
            'three_d_secure' => [
                'cardinal' => [
                    'environment' => 'production',
                    'org_unit_id' => null,
                    'api_key' => null,
                    'api_identifier' => null,
                    'debug' => '0',
                ],
            ],
            'seoautolink' => [
                'autolink' => [
                    'target' => null,
                    'excluded_tags' => null,
                    'skip_links_for_page' => null,
                    'add_links_inside_templates' => null,
                    'links_limit_per_page' => null,
                ],
            ],
            'seositemap' => [
                'frontend' => [
                    'sitemap_base_url' => 'map',
                    'column_count' => '4',
                    'is_capital_letters_enabled' => '1',
                    'sitemap_meta_title' => 'Site Map',
                    'sitemap_meta_keywords' => null,
                    'sitemap_meta_description' => null,
                    'sitemap_h1' => 'Site Map',
                    'is_show_products' => '1',
                    'is_show_categories' => '1',
                    'is_show_non_salable_products' => '1',
                    'is_show_cms_pages' => '1',
                    'ignore_cms_pages' => '1,2,6',
                    'is_show_stores' => '1',
                    'additional_links' => null,
                    'exclude_links' => null,
                    'links_limit' => '10000',
                ],
                'xml' => [
                    'is_ping_after_update' => null,
                ],
                'google' => [
                    'is_add_product_images' => '1',
                    'is_enable_image_friendly_urls' => '1',
                    'image_url_template' => '[product_name]',
                    'is_add_product_tags' => '1',
                    'product_tags_priority' => '0.5',
                    'link_priority' => '0.5',
                ],
            ],
            'magento_giftregistry' => [
                'general' => [
                    'enabled' => '1',
                    'max_registrant' => '5',
                ],
                'owner_email' => [
                    'template' => 'magento_giftregistry_owner_email_template',
                    'identity' => 'general',
                ],
                'sharing_email' => [
                    'template' => 'magento_giftregistry_sharing_email_template',
                    'identity' => 'general',
                    'send_limit' => '3',
                ],
                'update_email' => [
                    'template' => 'magento_giftregistry_update_email_template',
                    'identity' => 'general',
                ],
            ],
            'mst_seo_filter' => [
                'general' => [
                    'is_enabled' => '0',
                    'url_format' => 'options',
                    'name_separator' => null,
                    'prefix' => null,
                    'reset' => null,
                ],
            ],
            'giftcard' => [
                'email' => [
                    'identity' => 'general',
                    'template' => 'giftcard_email_template',
                ],
                'general' => [
                    'is_redeemable' => '1',
                    'lifetime' => '0',
                    'allow_message' => '1',
                    'message_max_length' => '255',
                    'order_item_status' => '9',
                ],
                'giftcardaccount_email' => [
                    'identity' => 'general',
                    'template' => 'giftcard_giftcardaccount_email_template',
                ],
                'giftcardaccount_general' => [
                    'code_length' => '12',
                    'code_format' => 'alphanum',
                    'code_prefix' => null,
                    'code_suffix' => null,
                    'code_split' => '0',
                    'pool_size' => '1000',
                    'pool_threshold' => '100',
                    'generate' => null,
                ],
            ],
            'persistent' => [
                'options' => [
                    'enabled' => '0',
                    'lifetime' => '31536000',
                    'remember_enabled' => '1',
                    'remember_default' => '1',
                    'logout_clear' => '1',
                    'shopping_cart' => '1',
                    'wishlist' => '1',
                    'recently_ordered' => '1',
                    'compare_current' => '1',
                    'compare_history' => '1',
                    'recently_viewed' => '1',
                    'customer' => '1',
                ],
            ],
            'magento_securitytxt_securitytxt' => [
                'general' => [
                    'enabled' => null,
                ],
                'contact_information' => [
                    'email' => null,
                    'phone' => null,
                    'contact_page' => null,
                ],
                'other_information' => [
                    'encryption' => null,
                    'acknowledgements' => null,
                    'preferred_languages' => null,
                    'hiring' => null,
                    'policy' => 'https://magento.com/security',
                    'signature_text' => null,
                ],
            ],
            'recaptcha_backend' => [
                'type_recaptcha' => [
                    'size' => 'normal',
                    'theme' => 'light',
                    'lang' => null,
                    'validation_failure_message' => 'reCAPTCHA verification failed',
                ],
                'type_invisible' => [
                    'position' => 'inline',
                    'theme' => 'light',
                    'lang' => null,
                    'validation_failure_message' => 'reCAPTCHA verification failed',
                ],
                'type_recaptcha_v3' => [
                    'score_threshold' => '0.5',
                    'position' => 'inline',
                    'theme' => 'light',
                    'lang' => null,
                    'validation_failure_message' => 'You cannot proceed with such operation, your reCAPTCHA reputation is too low.',
                ],
                'failure_messages' => [
                    'validation_failure_message' => 'reCAPTCHA verification failed.',
                    'technical_failure_message' => 'Something went wrong with reCAPTCHA. Please contact the store owner.',
                ],
                'type_for' => [
                    'recaptcha_backend_info_heading' => null,
                    'user_login' => null,
                    'user_forgot_password' => null,
                ],
            ],
            'recaptcha_frontend' => [
                'type_recaptcha' => [
                    'size' => 'compact',
                    'theme' => 'light',
                    'lang' => null,
                    'validation_failure_message' => 'reCAPTCHA verification failed',
                ],
                'type_invisible' => [
                    'position' => 'inline',
                    'theme' => 'light',
                    'lang' => null,
                    'validation_failure_message' => 'reCAPTCHA verification failed',
                ],
                'type_recaptcha_v3' => [
                    'score_threshold' => '0.5',
                    'position' => 'inline',
                    'theme' => 'light',
                    'lang' => null,
                    'validation_failure_message' => 'You cannot proceed with such operation, your reCAPTCHA reputation is too low.',
                ],
                'failure_messages' => [
                    'validation_failure_message' => 'reCAPTCHA verification failed.',
                    'technical_failure_message' => 'Something went wrong with reCAPTCHA. Please contact the store owner.',
                ],
                'type_for' => [
                    'recaptcha_frontend_info_heading' => null,
                    'customer_login' => 'invisible',
                    'customer_forgot_password' => 'invisible',
                    'customer_create' => 'invisible',
                    'customer_edit' => null,
                    'contact' => 'invisible',
                    'product_review' => null,
                    'newsletter' => 'invisible',
                    'prnewsletter_popup' => null,
                    'sendfriend' => null,
                    'place_order' => null,
                    'coupon_code' => null,
                    'paypal_payflowpro' => null,
                    'braintree' => null,
                ],
            ],
            'digidirect_seo' => [
                'general' => [
                    'enable_trailing_slash' => '1',
                    'enable_contextual_redirects' => '0',
                    'enable_hreflang_tags' => '0',
                    'hreflang_code_selection' => 'StoreCodeName',
                ],
                'sitemap' => [
                    'exclude_from_sitemap' => '0',
                ],
            ],
            'system' => [
                'cron' => [
                    'index' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '4320',
                        'use_separate_process' => '1',
                    ],
                    'staging' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '1',
                    ],
                    'default' => [
                        'schedule_generate_every' => '5',
                        'schedule_ahead_for' => '6',
                        'schedule_lifetime' => '15',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '1440',
                        'history_failure_lifetime' => '1440',
                        'use_separate_process' => '0',
                    ],
                    'catalog_event' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '0',
                    ],
                    'consumers' => [
                        'schedule_generate_every' => '15',
                        'schedule_ahead_for' => '20',
                        'schedule_lifetime' => '15',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '0',
                    ],
                    'amasty_base' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '0',
                    ],
                    'apptrian_imageoptimizer' => [
                        'schedule_generate_every' => '15',
                        'schedule_ahead_for' => '20',
                        'schedule_lifetime' => '15',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '0',
                    ],
                    'digidirect_ai' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '0',
                    ],
                    'digidirect_customoptions' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '0',
                    ],
                    'digidirect_invoiceemail' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '0',
                    ],
                    'digidirect_productoverlay' => [
                        'schedule_generate_every' => '15',
                        'schedule_ahead_for' => '20',
                        'schedule_lifetime' => '15',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '4320',
                        'use_separate_process' => '0',
                    ],
                    'digidirect_pronto' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '5',
                        'schedule_lifetime' => '15',
                        'history_cleanup_every' => '20',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '0',
                    ],
                    'digidirect_readytopickup' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '0',
                    ],
                    'mst_seo_ai' => [
                        'schedule_generate_every' => '10',
                        'schedule_ahead_for' => '20',
                        'schedule_lifetime' => '15',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '0',
                    ],
                    'mst_seo_audit' => [
                        'schedule_generate_every' => '10',
                        'schedule_ahead_for' => '20',
                        'schedule_lifetime' => '15',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '0',
                    ],
                    'ewave_ai' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '0',
                    ],
                    'ddg_automation' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '120',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '1',
                    ],
                    'ewave_feeds' => [
                        'schedule_generate_every' => '15',
                        'schedule_ahead_for' => '20',
                        'schedule_lifetime' => '15',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '4320',
                        'use_separate_process' => '1',
                    ],
                    'ewave_klevu_search' => [
                        'schedule_generate_every' => '15',
                        'schedule_ahead_for' => '20',
                        'schedule_lifetime' => '15',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '4320',
                        'use_separate_process' => '1',
                    ],
                    'ewave_m2e' => [
                        'schedule_generate_every' => '15',
                        'schedule_ahead_for' => '20',
                        'schedule_lifetime' => '15',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '4320',
                        'use_separate_process' => '1',
                    ],
                    'ewave_productoverlay' => [
                        'schedule_generate_every' => '15',
                        'schedule_ahead_for' => '20',
                        'schedule_lifetime' => '15',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '4320',
                        'use_separate_process' => '1',
                    ],
                    'ewave_preorder' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '10080',
                        'history_failure_lifetime' => '10080',
                        'use_separate_process' => '1',
                    ],
                    'digidirect_feeds' => [
                        'schedule_generate_every' => '15',
                        'schedule_ahead_for' => '20',
                        'schedule_lifetime' => '15',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '4320',
                        'use_separate_process' => '0',
                    ],
                    'ddg_automation_main' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '120',
                        'history_failure_lifetime' => '4320',
                        'use_separate_process' => '1',
                    ],
                    'ddg_automation_ac' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '120',
                        'history_failure_lifetime' => '4320',
                        'use_separate_process' => '1',
                    ],
                    'yotpo_yotpo' => [
                        'schedule_generate_every' => '1',
                        'schedule_ahead_for' => '4',
                        'schedule_lifetime' => '2',
                        'history_cleanup_every' => '10',
                        'history_success_lifetime' => '60',
                        'history_failure_lifetime' => '600',
                        'use_separate_process' => '1',
                    ],
                ],
                'mysqlmq' => [
                    'successful_messages_lifetime' => '10080',
                    'retry_inprogress_after' => '1440',
                    'failed_messages_lifetime' => '10080',
                    'new_messages_lifetime' => '10080',
                ],
                'cron_job_manager' => [
                    'clean_running_schedule' => '1',
                    'email_notification' => '1',
                    'email_identity' => 'general',
                    'email_recipients' => 'clint@kayweb.com.au',
                    'email_template' => 'system_cron_job_manager_email_template',
                ],
                'smtp' => [
                    'disable' => '0',
                    'set_return_path' => '0',
                    'return_path_email' => null,
                ],
                'currency' => [
                    'installed' => 'AUD',
                ],
                'security' => [
                    'max_session_size_admin' => '2560000',
                    'max_session_size_storefront' => '256000',
                ],
                'adminnotification' => [
                    'use_https' => '1',
                    'frequency' => '1',
                    'last_update' => '0',
                    'feed_url' => 'notifications.magentocommerce.com/magento2/enterprise/notifications.rss',
                    'popup_url' => 'widgets.magentocommerce.com/notificationPopup',
                    'severity_icons_url' => 'widgets.magentocommerce.com/%s/%s.gif',
                ],
                'backup' => [
                    'functionality_enabled' => '1',
                    'enabled' => '0',
                    'type' => null,
                    'time' => null,
                    'frequency' => null,
                    'maintenance' => null,
                ],
                'rotation' => [
                    'lifetime' => '60',
                    'frequency' => '1',
                ],
                'full_page_cache' => [
                    'caching_application' => '42',
                    'ttl' => '86400',
                    'handles_size' => '100',
                    'varnish' => [
                        'grace_period' => '300',
                        'export_button_version4' => null,
                        'export_button_version5' => null,
                        'export_button_version6' => null,
                    ],
                    'fastly' => [
                        'fastly_service_id' => '76sHnyh85kG0MAxMs24sxI',
                        'fastly_api_key' => 'QUQXe7fmO2fz_VJCeZmB_zGeTjWtQaFp',
                        'test_connection' => null,
                        'upload_vcl' => null,
                        'fastly_advanced_configuration' => [
                            'force_tls' => null,
                            'admin_path_timeout' => '600',
                            'ignored_url_parameters' => 'utm_.*, gclid, gdftrk, _ga, mc_.*,cfclick',
                            'stale_ttl' => '86400',
                            'stale_error_ttl' => '86400',
                            'x_magento_tags_size' => '16383',
                            'purge_catalog_category' => '1',
                            'purge_catalog_product' => '1',
                            'purge_cms_page' => '1',
                            'preserve_static' => '1',
                            'soft_purge' => '1',
                            'enable_geoip' => '0',
                            'geoip_action' => 'dialog',
                            'geoip_country_mapping' => [],
                            'enable_fastly_edge_modules' => '1',
                        ],
                        'fastly_backend_settings' => [
                            'create_backend' => null,
                            'backends' => null,
                        ],
                        'fastly_basic_auth' => [
                            'enable_basic_auth' => null,
                            'authentication' => null,
                        ],
                        'fastly_blocking' => [
                            'block' => null,
                            'update_blocking_snippet' => null,
                            'blocking_type' => '0',
                            'block_by_country' => null,
                            'block_by_acl' => 'Generated_by_IP_block_list',
                        ],
                        'fastly_error_maintenance_page' => [
                            'force_tls' => null,
                            'waf_page' => null,
                        ],
                        'fastly_custom_snippets' => [
                            'fastly_custom_snippet_upload' => null,
                            'fastly_custom_snippet_files' => null,
                        ],
                        'fastly_domains' => [
                            'fastly_domains_list' => null,
                        ],
                        'fastly_edge_acl' => [
                            'acl' => null,
                        ],
                        'fastly_edge_dictionaries' => [
                            'dictionaries' => null,
                        ],
                        'fastly_image_optimization_configuration' => [
                            'fastly_push_image_config' => null,
                            'automatic_compression' => 'medium',
                            'image_optimizations_default_config_options' => null,
                            'image_optimization_force_lossy' => '1',
                            'image_optimizations' => '1',
                            'image_optimization_image_quality' => '70',
                            'image_optimization_bg_color' => '1',
                            'image_optimization_canvas' => '0',
                            'image_optimizations_pixel_ratio' => '0',
                            'image_optimizations_ratios' => null,
                            'image_serve_placeholder' => '1',
                            'image_verify' => '0',
                        ],
                        'fastly_rate_limiting_settings' => [
                            'enable_rate_limiting_master' => '0',
                            'enable_rate_limiting_logging' => '0',
                            'path_protection' => [
                                'enable_rate_limiting' => '0',
                                'rate_limiting_paths' => '[{"path":"\\^/fastly-io-tester$","comment":"Default Fastly Placeholder"}]',
                                'rate_limiting_limit' => '100',
                                'rate_limiting_ttl' => '3600',
                            ],
                            'crawler_protection' => [
                                'enable_crawler_protection' => '0',
                                'crawler_rate_limiting_limit' => '100',
                                'crawler_rate_limiting_ttl' => '3600',
                                'exempt_good_bots' => '1',
                            ],
                            'fastly_path_protection' => [
                                'rate_limiting_limit' => '10',
                                'rate_limiting_ttl' => '3600',
                            ],
                            'rate_limiting_limit' => '10',
                            'rate_limiting_ttl' => '3600',
                        ],
                        'fastly_tools' => [
                            'versions' => null,
                            'import_file' => null,
                            'import' => null,
                            'export' => null,
                            'logging' => [
                                'create_backend' => null,
                                'backends' => null,
                            ],
                        ],
                        'fastly_maintenance_support' => [
                            'toggle_su' => null,
                            'update_su' => null,
                        ],
                        'fastly_web_application_firewall' => [
                            'toggle_waf' => null,
                            'owasp_allowed_methods' => null,
                            'owasp_restricted_extensions' => null,
                            'waf_bypass' => null,
                            'update_waf_bypass' => null,
                            'waf_allow_by_acl' => 'maint_allowlist,waf_bypass',
                        ],
                        'fastly_web_hooks' => [
                            'enable_webhooks' => '0',
                            'incoming_webhook_url' => null,
                            'publish_purge_events' => '0',
                            'publish_key_url_purge_events' => '1',
                            'publish_purge_all_items_events' => '1',
                            'publish_purge_trace' => '0',
                            'publish_purge_by_key_trace' => '0',
                            'publish_purge_all_trace' => '0',
                            'publish_config_change_events' => '1',
                            'webhook_message_prefix' => null,
                        ],
                        'path' => 'fastly.vcl',
                        'current_version' => '1.0.15',
                        'fastly_ga_cid' => 'efbead6a-13ac-4019-b105-e123a408f241',
                        'last_checked_issues_version' => '1.2.220',
                    ],
                    'fastly_edge_modules' => [
                        'modly_all_modules' => null,
                        'modly_active_modules' => null,
                    ],
                    'varnish6' => [
                        'path' => 'varnish6.vcl',
                    ],
                    'varnish5' => [
                        'path' => 'varnish5.vcl',
                    ],
                    'varnish4' => [
                        'path' => 'varnish4.vcl',
                    ],
                    'default' => [
                        'access_list' => 'localhost',
                        'backend_host' => 'localhost',
                        'backend_port' => '8080',
                        'ttl' => '86400',
                        'grace_period' => '300',
                    ],
                ],
                'bulk' => [
                    'lifetime' => '60',
                ],
                'media_storage_configuration' => [
                    'media_storage' => '0',
                    'media_database' => 'default_setup',
                    'synchronize' => null,
                    'configuration_update_time' => '3600',
                    'allowed_resources' => [
                        'compiled_css_folder' => 'css',
                        'compiled_css_secure_folder' => 'css_secure',
                        'compiled_js_folder' => 'js',
                        'design_theme_folder' => 'theme',
                        'site_favicons' => 'favicon',
                        'site_logos' => 'logo',
                        'customer_address_folder' => 'customer_address',
                        'email_folder' => 'email',
                        'media_gallery_image_folders' => [
                            'wysiwyg_image_folder' => 'wysiwyg',
                            'category_image_folder' => 'catalog/category',
                        ],
                        'preview_folder' => '.thumbs',
                        'tmp_images_folder' => 'tmp',
                        'catalog_images_folder' => 'catalog',
                        'product_custom_options_fodler' => 'custom_options',
                        'sales_logo_folder' => 'sales/store/logo',
                        'sales_logo_html_folder' => 'sales/store/logo_html',
                        'captcha_folder' => 'captcha',
                        'analytics_folder' => 'analytics',
                        'dhl_folder' => 'dhl',
                        'enterprise_folder' => 'enterprise',
                        'sitemap_folder' => 'sitemap',
                        'giftwrapping_folder' => 'wrapping',
                        'renditions_folder' => '.renditions',
                        'template_manager_folder' => '.template-manager',
                        'swatches_folder' => 'attribute',
                    ],
                ],
                'magento_scheduled_import_export_log' => [
                    'save_days' => '5',
                    'enabled' => '0',
                    'clean_now' => null,
                    'time' => '00,00,00',
                    'frequency' => 'D',
                    'error_email_identity' => 'general',
                    'error_email_template' => 'system_magento_scheduled_import_export_log_error_email_template',
                ],
                'upload_configuration' => [
                    'jpeg_quality' => '80',
                    'enable_resize' => '1',
                    'max_width' => '1920',
                    'max_height' => '1200',
                ],
                'media_gallery' => [
                    'enabled' => '0',
                ],
                'media_gallery_renditions' => [
                    'enabled' => '1',
                    'width' => '1000',
                    'height' => '1000',
                ],
                'adobe_stock_integration' => [
                    'adobe_stock_test_connect_wizard' => null,
                ],
                'filesystem' => [
                    'media' => '{{media_dir}}',
                ],
                'emails' => [
                    'forgot_email_template' => 'system_emails_forgot_email_template',
                    'forgot_email_identity' => 'general',
                ],
                'dashboard' => [
                    'enable_charts' => '1',
                ],
                'itorisnotification' => [
                    'feed_url' => 'www.itoris.com/?platform=magento2&get=rss',
                    'severity_icons_url' => 'widgets.magentocommerce.com/%s/%s.gif',
                    'use_https' => '0',
                    'frequency' => '6',
                    'last_update' => '0',
                ],
                'yireo_linkpreload' => [
                    'enabled' => '1',
                    'use_cookie' => '0',
                    'skip_images' => '1',
                ],
            ],
            'adobe_stock' => [
                'integration' => [
                    'enabled' => '0',
                    'environment' => 'PROD',
                    'product_name' => 'Magento',
                    'files_url' => 'https://stock.adobe.io/Rest/Media/1/Files',
                    'default_gallery_id' => 'Kqy5H45lnKzkC5I8JGFXYVwr2ltNwMe7',
                ],
            ],
            'adobe_ims' => [
                'integration' => [
                    'token_url' => 'https://ims-na1.adobelogin.com/ims/token',
                    'logout_url' => 'https://ims-na1.adobelogin.com/ims/logout/v1?redirect_uri=#{redirect_uri}',
                    'image_url' => 'https://cc-api-behance.adobe.io/v2/users/me?api_key=#{api_key}',
                    'auth_url_pattern' => 'https://ims-na1.adobelogin.com/ims/authorize?client_id=#{client_id}&redirect_uri=#{redirect_uri}&locale=#{locale}&scope=openid,creative_sdk,email,profile&response_type=code',
                    'admin_enabled' => '0',
                ],
            ],
            'digidirect_localization' => [
                'localization' => [
                    'use_phone_prefix' => null,
                    'phone_suggestion_upload' => null,
                    'phone_codes' => null,
                    'length_unit' => 'km',
                ],
                'timezone' => [
                    'matrix' => null,
                ],
                'holiday' => [
                    'matrix' => null,
                ],
            ],
            'seo_ai' => [
                'general' => [
                    'is_enabled' => null,
                    'openai_key' => null,
                ],
            ],
            'seo_audit' => [
                'general' => [
                    'is_enabled' => '1',
                    'server_load_threshold' => '80',
                ],
                'ai_helper' => [
                    'is_auto_fix_meta' => null,
                    'is_cron' => null,
                    'is_update' => null,
                    'is_include_store' => null,
                    'store_description' => null,
                ],
            ],
            'commissionfactory' => [
                'tracking' => [
                    'advertiser' => '72917',
                ],
            ],
            'reports' => [
                'dashboard' => [
                    'ytd_start' => '1,1',
                    'mtd_start' => '1',
                ],
                'options' => [
                    'enabled' => '0',
                    'product_view_enabled' => '1',
                    'product_send_enabled' => '1',
                    'product_compare_enabled' => '1',
                    'product_to_cart_enabled' => '1',
                    'product_to_wishlist_enabled' => '1',
                    'wishlist_share_enabled' => '1',
                ],
            ],
            'weltpixel_backend_developer' => [
                'system_information' => [
                    'infromation_details' => null,
                ],
                'notifications' => [
                    'enable_admin_notification' => '1',
                ],
                'csp' => [
                    'change_system_value' => '0',
                    'report_only' => '1',
                ],
            ],
            'cms' => [
                'wysiwyg' => [
                    'enabled' => 'enabled',
                    'editor' => 'mage/adminhtml/wysiwyg/tiny_mce/tinymce5Adapter',
                    'use_static_urls_in_catalog' => '0',
                    'filetypes' => null,
                    'force_valid' => '0',
                    'editor_version' => 'mage/adminhtml/wysiwyg/tiny_mce/tinymce5Adapter',
                ],
                'hierarchy' => [
                    'enabled' => '1',
                    'metadata_enabled' => '1',
                    'menu_layout' => 'content',
                ],
                'pagebuilder' => [
                    'enabled' => '1',
                    'enable_content_preview' => '1',
                    'google_maps_api_key' => null,
                    'google_maps_api_key_validator' => null,
                    'google_maps_style' => null,
                    'column_grid_default' => '12',
                    'column_grid_max' => '16',
                ],
            ],
            'newrelicreporting' => [
                'general' => [
                    'enable' => '0',
                    'app_name' => null,
                    'separate_apps' => null,
                ],
                'cron' => [
                    'enable_cron' => '0',
                ],
            ],
            'analytics' => [
                'subscription' => [
                    'enabled' => '1',
                ],
                'general' => [
                    'collection_time' => '05,42,00',
                    'vertical' => 'Consumer Electronics',
                    'additional_comment' => null,
                ],
                'url' => [
                    'documentation' => 'https://docs.magento.com/user-guide/reports/advanced-reporting.html',
                ],
                'integration_name' => 'Magento Analytics user',
            ],
            'mageplaza' => [
                'general' => [
                    'notice_enable' => '1',
                    'notice_type' => 'announcement,new_update,marketing',
                    'menu' => '0',
                ],
            ],
            'mst_core' => [
                'menu' => [
                    'is_enabled' => '1',
                ],
                'css' => [
                    'include_font_awesome' => '1',
                    'custom' => null,
                ],
            ],
            'digidirect_freegift' => [
                'general' => [
                    'auto_add' => '1',
                    'redirect_to_cart' => '0',
                    'is_hide_free_item_price' => '0',
                    'show_free_item_attributes' => null,
                ],
                'messages' => [
                    'cart_message' => 'FREE!',
                    'prefix' => 'FREE -',
                    'message_type' => 'notice',
                    'add_message' => 'Select your <a href="#" data-role="freegift-popup-show">FREE GIFT</a>!',
                    'auto_open_popup' => '0',
                    'popup_template' => 'default',
                    'display_popup_once' => '0',
                    'display_error_messages' => '1',
                    'display_success_messages' => '1',
                    'message_hidden_free_item_price' => 'Free',
                ],
            ],
            'digidirect_productoverlay' => [
                'general' => [
                    'image_type' => 'jpg,jpeg,png,gif',
                    'image_mime_type' => 'image/jpeg,image/png,image/gif',
                ],
                'display' => [
                    'product' => '.gallery-placeholder',
                    'category' => '.product-item-photo',
                ],
                'on_sale' => [
                    'sale_min' => null,
                    'sale_min_percent' => null,
                    'rounding' => 'round',
                ],
                'new' => [
                    'is_new' => '1',
                    'creation_date' => '0',
                    'days' => '10',
                ],
                'overlay_management' => [
                    'enable_management' => '0',
                ],
                'smart_cache' => [
                    'enable_cache_cleaner' => '0',
                    'cache_cleaner_cron_expr' => '*/5 * * * *',
                ],
            ],
            'prnewsletterpopup' => [
                'toolbox' => [
                    'version' => null,
                ],
                'general' => [
                    'enable' => '1',
                    'serial' => 'mtrODltq5wUUTJTFYa01stcmZDvzcjgM',
                    'enable_analytics' => '0',
                    'gtm_tracking' => '0',
                    'enable_history' => '1',
                    'erase_history' => '30',
                    'ip_skip' => null,
                    'cookies_usage' => '1',
                    'htmltoimage' => null,
                ],
                'size' => [
                    'desktop' => 'eg,992',
                    'tablet' => 'eg,768',
                    'mobile' => 'l,768',
                ],
                'disposable_emails' => [
                    'disable' => '1',
                    'domains' => '10minutemail.com
2-ch.space
33mail.com
6paq.com
DingBone.com
FudgeRub.com
LookUgly.com
SmellFear.com
a45.in
abyssmail.com
anappthat.com
anonymbox.com
anonymousemail.me
anotherdomaincyka.tk
armyspy.com
asdasd.ru
axe.axeprim.eu
azazazatashkent.tk
box.yadavnaresh.com.np
bspamfree.org
bund.us
bundes-li.ga
cachedot.net
ckaazaza.tk
crazymailing.com
cuvox.de
dasdasdascyka.tk
dayrep.com
deadaddress.com
discard.email
discardmail.com
discardmail.de
disposable.pingfu.net
disposableinbox.com
dispostable.com
dropmail.me
dudmail.com
e4ward.com
easytrashmail.com
ebano.campano.cl
ee2.pl
eelmail.com
einrot.com
email-fake.com
email.hideme.be
eml.pp.ua
en.getairmail.com
eqeqeqeqe.tk
eyepaste.com
fake-email.pp.ua
fakeinbox.com
fakemailgenerator.com
fiifke.de
fleckens.hu
freeletter.me
freemail.ms
freundin.ru
gaf.oseanografi.id
get.pp.ua
getairmail.com
gishpuppy.com
googdad.tk
grandmasmail.com
grr.la
guerrillamail.biz
guerrillamail.com
guerrillamail.de
guerrillamail.net
guerrillamail.org
guerrillamailblock.com
gustr.com
hartbot.de
hideme.be
hmamail.com
hulapla.de
inboxdesign.me
inboxstore.me
incognitomail.org
inmynetwork.cf
inmynetwork.ga
inmynetwork.gq
inmynetwork.ml
inmynetwork.tk
jetable.org
jobbikszimpatizans.hu
jourrapide.com
kurzepost.de
labetteraverouge.at
lazyinbox.com
loadby.us
loh.pp.ua
lolitka.cf
lolitka.ga
lolitka.gq
lolito.tk
mailcatch.com
maildrop.cc
mailexpire.com
mailforspam.com
mailinator.com
mailinator.net
mailnesia.com
mailnull.com
meltmail.com
mfsa.ru
mintemail.com
mmmmail.com
moakt.com
msgos.com
mt2015.com
mt2016.com
my.vondata.com.ar
mynetwork.cf
mytempemail.com
mytrashmail.com
no-spam.ws
objectmail.com
odnorazovoe.ru
pfui.ru
poh.pp.ua
postonline.me
proxymail.eu
q314.net
rcpt.at
regspaces.tk
rhyta.com
s0ny.net
safetypost.de
schafmail.de
sdfghyj.tk
send-email.org
sharklasers.com
shitmail.me
showslow.de
spam.la
spam.su
spam4.me
spambog.com
spambog.de
spambog.ru
spambox.us
spamex.com
spamfree24.org
spamgourmet.com
spamobox.com
spamstack.net
squizzy.de
superrito.com
sweetxxx.de
tafmail.com
techgroup.me
teewars.org
teleworm.us
temp-mail.org
temp-mail.ru
tempemail.net
tempinbox.com
tempmailer.com
tempsky.com
thismail.ru
thrma.com
throwawaymail.com
tmail.ws
trash-mail.at
trashmail.at
trashmail.com
trashmail.me
trashmail.net
trbvm.com
ts-by-tashkent.cf
ts-by-tashkent.ga
ts-by-tashkent.gq
ts-by-tashkent.ml
ts-by-tashkent.tk
vaasfc4.tk
vickaentb.cf
vickaentb.ga
vickaentb.gq
vickaentb.ml
vickaentb.tk
vihost.ml
vihost.tk
vmani.com
wasdfgh.cf
wasdfgh.ga
wasdfgh.gq
wasdfgh.ml
wasdfgh.tk
webuser.in
wegwerfmail.de
wegwerfmail.net
wegwerfmail.org
wfgdfhj.tk
wh4f.org
wickmail.net
wimsg.com
xy9ce.tk
yapped.net
yopmail.com
zaktouni.fr
zeta-telecom.com',
                ],
                'google_recaptcha' => [
                    'google_recaptcha_config_type' => 'custom',
                    'google_recaptcha_sitekey' => null,
                    'google_recaptcha_secretkey' => null,
                ],
                'integration' => [
                    'activecampaign' => [
                        'enable' => '0',
                        'url' => 'https://account.api-us1.com/',
                        'key' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"email","firstname":"first_name","middlename":"","lastname":"last_name","suffix":"","dob":"","gender":"","taxvat":"","prefix":"","telephone":"phone","fax":"","company":"orgname","street":"","city":"","country_id":"","region":"","postcode":"","coupon":""}',
                    ],
                    'campaignmonitor' => [
                        'enable' => '0',
                        'key' => null,
                        'client_id' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"email","firstname":"firstname","middlename":"name","lastname":"","suffix":"","dob":"birthdate","gender":"gender","taxvat":"","prefix":"","telephone":"phone","fax":"fax","company":"company","street":"street","city":"city","country_id":"country","region":"state","postcode":"postal_code","coupon":""}',
                    ],
                    'constantcontact' => [
                        'enable' => '0',
                        'url' => null,
                        'key' => null,
                        'secret' => null,
                        'access_token' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"address","firstname":"first_name","middlename":"","lastname":"last_name","suffix":"","dob":"birthday","gender":"","taxvat":"","prefix":"","telephone":"phone_number","fax":"","company":"company_name","street":"street","city":"city","country_id":"country","region":"state","postcode":"postal_code","coupon":""}',
                        'custom_fields' => null,
                    ],
                    'convertkit' => [
                        'enable' => '0',
                        'key' => null,
                        'secret_id' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"email","firstname":"first_name","middlename":"name","lastname":"","suffix":"","dob":"birthdate","gender":"gender","taxvat":"","prefix":"","telephone":"telephone","fax":"fax","company":"company","street":"street","city":"city","country_id":"country","region":"state","postcode":"postal_code","coupon":""}',
                    ],
                    'dotmailer' => [
                        'enable' => '0',
                        'url' => null,
                        'app_name' => null,
                        'key' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"EMAIL","firstname":"FIRSTNAME","middlename":"","lastname":"LASTNAME","suffix":"","dob":"","gender":"GENDER","taxvat":"","prefix":"","telephone":"","fax":"","company":"","street":"","city":"","country_id":"","region":"","postcode":"POSTCODE","coupon":""}',
                    ],
                    'egoi' => [
                        'enable' => '0',
                        'key' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"email","firstname":"first_name","middlename":"first_name","lastname":"last_name","suffix":"","dob":"birth_date","gender":"gender","taxvat":"","prefix":"","telephone":"telephone","fax":"fax","company":"company","street":"street","city":"city","country_id":"country","region":"state","postcode":"postal_code","coupon":""}',
                    ],
                    'emma' => [
                        'enable' => '0',
                        'key' => null,
                        'secret_id' => null,
                        'account_id' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"email","firstname":"first_name","middlename":"","lastname":"last_name","suffix":"","dob":"birthdate","gender":"gender","taxvat":"","prefix":"","telephone":"phone","fax":"fax","company":"company","street":"street","city":"city","country_id":"country","region":"state","postcode":"zip","coupon":""}',
                    ],
                    'getresponse' => [
                        'enable' => '0',
                        'key' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"email","firstname":"","middlename":"name","lastname":"","suffix":"","dob":"birthdate","gender":"gender","taxvat":"","prefix":"","telephone":"phone","fax":"fax","company":"company","street":"street","city":"city","country_id":"country","region":"state","postcode":"postal_code","coupon":""}',
                    ],
                    'hubspot' => [
                        'enable' => '0',
                        'url' => 'https://api.hubapi.com/',
                        'key' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"email","firstname":"firstname","middlename":"","lastname":"lastname","suffix":"","dob":"date_of_birth","gender":"gender","taxvat":"","prefix":"","telephone":"phone","fax":"fax","company":"company","street":"address","city":"city","country_id":"country","region":"state","postcode":"zip","coupon":""}',
                    ],
                    'icontact' => [
                        'enable' => '0',
                        'key' => null,
                        'app_name' => null,
                        'secret_id' => null,
                        'account_id' => null,
                        'client_folder_id' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"email","firstname":"firstName","middlename":"","lastname":"lastName","suffix":"","dob":"birthdate","gender":"gender","taxvat":"","prefix":"","telephone":"phone","fax":"fax","company":"company","street":"street","city":"city","country_id":"country","region":"state","postcode":"postal_code","coupon":""}',
                    ],
                    'infusionsoft' => [
                        'enable' => '0',
                        'app_name' => null,
                        'key' => null,
                        'test_connection' => null,
                        'fields_tag' => '{"email":"Email","firstname":"FirstName","middlename":"MiddleName","lastname":"LastName","suffix":"Suffix","dob":"Birthday","gender":"","taxvat":"","prefix":"","telephone":"Phone1","fax":"Fax1","company":"Company","street":"StreetAddress1","city":"City","country_id":"Country","region":"State","postcode":"PostalCode","coupon":""}',
                    ],
                    'klaviyo' => [
                        'enable' => '0',
                        'key' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"email","firstname":"first_name","middlename":"","lastname":"last_name","suffix":"","dob":"","gender":"","taxvat":"","prefix":"","telephone":"phone_number","fax":"","company":"organization","street":"","city":"","country_id":"","region":"","postcode":"","coupon":""}',
                    ],
                    'madmimi' => [
                        'enable' => '0',
                        'key' => null,
                        'app_name' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"email","firstname":"first_name","middlename":"","lastname":"last_name","suffix":"","dob":"birthdate","gender":"gender","taxvat":"","prefix":"","telephone":"phone","fax":"fax","company":"company","street":"street","city":"city","country_id":"country","region":"state","postcode":"zip","coupon":""}',
                    ],
                    'mailchimp' => [
                        'enable' => '0',
                        'key' => null,
                        'test_connection' => null,
                        'list' => null,
                        'send_email' => '0',
                        'info' => null,
                        'fields_tag' => '{"email":"EMAIL","firstname":"FNAME","middlename":"MNAME","lastname":"LNAME","suffix":"SUFFIX","dob":"DOB","gender":"GENDER","taxvat":"TAXVAT","prefix":"PRENAME","telephone":"TELEPHONE","fax":"FAX","company":"COMPANY","street":"STREET","city":"CITY","country_id":"COUNTRY","region ":"STATE","postcode":"ZIPCODE","coupon":"COUPON"}',
                    ],
                    'mailjet' => [
                        'enable' => '0',
                        'key' => null,
                        'secret_id' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"email","firstname":"firstname","middlename":"","lastname":"lastname","suffix":"","dob":"birthdate","gender":"gender","taxvat":"","prefix":"","telephone":"phone","fax":"fax","company":"company","street":"street","city":"city","country_id":"country","region":"state","postcode":"postal_code","coupon":""}',
                    ],
                    'mautic' => [
                        'enable' => '0',
                        'url' => null,
                        'app_name' => null,
                        'key' => null,
                        'test_connection' => null,
                        'fields_tag' => '{"email":"email","firstname":"firstname","middlename":"","lastname":"lastname","suffix":"","dob":"birthdate","gender":"gender","taxvat":"","prefix":"","telephone":"phone","fax":"fax","company":"company","street":"street","city":"city","country_id":"country","region":"state","postcode":"zipcode","coupon":""}',
                    ],
                    'ontraport' => [
                        'enable' => '0',
                        'key' => null,
                        'secret_id' => null,
                        'test_connection' => null,
                        'fields_tag' => '{"email":"email","firstname":"firstname","middlename":"","lastname":"lastname","suffix":"","dob":"birthday","gender":"gender","taxvat":"","prefix":"","telephone":"office_phone","fax":"fax","company":"company","street":"address","city":"city","country_id":"country","region":"state","postcode":"zip","coupon":""}',
                    ],
                    'salesforce' => [
                        'enable' => '0',
                        'user_id' => null,
                        'secret_id' => null,
                        'app_name' => null,
                        'api_password' => null,
                        'api_security_token' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"email","firstname":"FirstName","middlename":"MiddleName","lastname":"LastName","suffix":"","dob":"Birthdate","gender":"gender","taxvat":"","prefix":"","telephone":"Phone","fax":"fax","company":"company","street":"MailingStreet","city":"MailingCity","country_id":"MailingCountry","region":"MailingState","postcode":"MailingPostalCode","coupon":""}',
                    ],
                    'sendinblue' => [
                        'enable' => '0',
                        'key' => null,
                        'test_connection' => null,
                        'list' => null,
                        'fields_tag' => '{"email":"email","firstname":"firstname","middlename":"middlename","lastname":"lastname","suffix":"","dob":"BIRTHDATE","gender":"gender","taxvat":"","prefix":"","telephone":"telephone","fax":"fax","company":"company","street":"street","city":"city","country_id":"country","region":"state","postcode":"postal_code","coupon":""}',
                    ],
                    'sendy' => [
                        'enable' => '0',
                        'key' => null,
                        'url' => null,
                        'test_connection' => null,
                        'list' => [],
                        'fields_tag' => '{"email":"email","name":"first_name","country":"country","ipaddress":"","referrer":"","gdpr":"","silent":"","hp":"","boolean":""}',
                    ],
                ],
            ],
            'plumbase' => [
                'installed_extensions' => [
                    'enabled' => null,
                ],
                'notifications' => [
                    'enabled' => '1',
                    'subscribed_to' => 'promotions,announcements,product_updates',
                ],
                'menu' => [
                    'enabled' => '1',
                ],
                'system' => [
                    'enabled_statistic' => '1',
                ],
                'developer' => [
                    'magento_mode' => null,
                    'magento_path' => null,
                    'php_server_logs_status' => null,
                    'php_server_logs_path' => null,
                    'time' => null,
                    'debug_info' => null,
                ],
            ],
            'theme' => [
                'customization' => [
                    'css' => 'Magento\\Framework\\View\\Design\\Theme\\Customization\\File\\Css',
                    'js' => 'Magento\\Framework\\View\\Design\\Theme\\Customization\\File\\Js',
                    'custom_css' => 'Magento\\Theme\\Model\\Theme\\Customization\\File\\CustomCss',
                ],
            ],
            'indexer' => [
                'catalog_product_price' => [
                    'dimensions_mode' => 'none',
                ],
            ],
            'import' => [
                'format_v1' => [
                    'page_size' => '5000',
                ],
                'format_v2' => [
                    'page_size' => '10000',
                    'bunch_size' => '100',
                ],
            ],
            'dashboard' => [
                'use_aggregated_data' => '0',
            ],
            'captcha' => [
                'frontend' => [
                    'areas' => [
                        'sales_rule_coupon_request' => [
                            'label' => 'Applying coupon code',
                        ],
                        'payment_processing_request' => [
                            'label' => 'Checkout/Placing Order',
                        ],
                        'user_create' => [
                            'label' => 'Create user',
                        ],
                        'user_login' => [
                            'label' => 'Login',
                        ],
                        'user_forgotpassword' => [
                            'label' => 'Forgot password',
                        ],
                        'contact_us' => [
                            'label' => 'Contact Us',
                        ],
                        'user_edit' => [
                            'label' => 'Change password',
                        ],
                        'share_wishlist_form' => [
                            'label' => 'Share Wishlist Form',
                        ],
                        'gift_code_request' => [
                            'label' => 'Add Gift Card Code',
                        ],
                        'product_sendtofriend_form' => [
                            'label' => 'Send To Friend Form',
                        ],
                        'co-payment-form' => [
                            'label' => 'Payflow Pro',
                        ],
                    ],
                ],
                'fonts' => [
                    'linlibertine' => [
                        'label' => 'LinLibertine',
                        'path' => 'LinLibertineFont/LinLibertine_Bd-2.8.1.ttf',
                    ],
                ],
                'backend' => [
                    'areas' => [
                        'backend_login' => [
                            'label' => 'Admin Login',
                        ],
                        'backend_forgotpassword' => [
                            'label' => 'Admin Forgot Password',
                        ],
                    ],
                ],
            ],
            'export' => [
                'customer_page_size' => [
                    'finance' => '10000',
                    'customer' => '10000',
                    'address' => '5000',
                ],
            ],
            'crontab' => [
                'default' => [
                    'jobs' => [
                        'sitemap_generate' => [
                            'schedule' => [
                                'cron_expr' => '13 2 * * *',
                            ],
                            'run' => [
                                'model' => null,
                            ],
                        ],
                        'catalog_product_alert' => [
                            'schedule' => [
                                'cron_expr' => '0 0 * * *',
                            ],
                            'run' => [
                                'model' => null,
                            ],
                        ],
                        'magento_scheduled_import_export_log_clean' => [
                            'schedule' => [
                                'cron_expr' => '0 0 * * *',
                            ],
                        ],
                        'ewave_checkout_fields' => [
                            'schedule' => [
                                'cron_expr' => '0 0 * * *',
                            ],
                            'run' => [
                                'model' => null,
                            ],
                        ],
                        'ced_mpcatch_full_offer_sync' => [
                            'status' => '0',
                        ],
                        'ced_mpcatch_order_import' => [
                            'status' => '0',
                        ],
                        'ced_mpcatch_order_sync' => [
                            'status' => '0',
                        ],
                        'ced_mpcatch_product_feed_sync' => [
                            'status' => '0',
                        ],
                        'ced_mpcatch_shipment_cron' => [
                            'status' => '0',
                        ],
                        'ess_m2epro' => [
                            'status' => '0',
                        ],
                        'klevu_search_clear_sync_lock' => [
                            'status' => '1',
                        ],
                        'klevu_search_order_sync' => [
                            'status' => '1',
                        ],
                        'klevu_search_product_sync' => [
                            'status' => '0',
                        ],
                        'digidirect_checkout_fields' => [
                            'schedule' => [
                                'cron_expr' => '0 0 * * *',
                            ],
                            'run' => [
                                'model' => null,
                            ],
                        ],
                    ],
                ],
                'digidirect_customoptions' => [
                    'jobs' => [
                        'customoptions_deleteoptions' => [
                            'status' => '0',
                            'schedule' => [
                                'cron_expr' => null,
                            ],
                        ],
                        'tec_deletecustomoption' => [
                            'schedule' => [
                                'cron_expr' => '0 21 * * *',
                            ],
                            'run' => [
                                'model' => 'Digidirect\\CustomOptions\\Cron\\DeleteOptions::execute',
                            ],
                            'status' => '0',
                            'is_user' => '1',
                        ],
                        'customoptions_createoptions' => [
                            'status' => '0',
                            'schedule' => [
                                'cron_expr' => null,
                            ],
                        ],
                        'tec_createcustomoption' => [
                            'schedule' => [
                                'cron_expr' => '0 22 * * *',
                            ],
                            'run' => [
                                'model' => 'Digidirect\\CustomOptions\\Cron\\CreateOptions::execute',
                            ],
                            'status' => '1',
                            'is_user' => '1',
                        ],
                    ],
                ],
                'digidirect_pronto' => [
                    'jobs' => [
                        'pronto_order_sync' => [
                            'status' => '0',
                            'schedule' => [
                                'cron_expr' => '*/3 * * * *',
                            ],
                        ],
                        'pronto_test_sync' => [
                            'status' => '0',
                        ],
                        'pronto_shipping_sync' => [
                            'status' => '0',
                        ],
                        'pronto_productstock_sync' => [
                            'status' => '0',
                        ],
                        'pronto_product_sync' => [
                            'status' => '0',
                            'schedule' => [
                                'cron_expr' => '1 1 * * *',
                            ],
                        ],
                        'pronto_inventory_sync' => [
                            'status' => '1',
                        ],
                        'tec_pronto_productsync' => [
                            'schedule' => [
                                'cron_expr' => '10 0 * * *',
                            ],
                            'run' => [
                                'model' => 'Digidirect\\Pronto\\Cron\\ProductEnquiry::execute',
                            ],
                            'status' => '1',
                            'is_user' => '1',
                        ],
                        'tec_pronto_orderlive' => [
                            'schedule' => [
                                'cron_expr' => '* * * * *',
                            ],
                            'run' => [
                                'model' => 'Digidirect\\Pronto\\Cron\\SendOrder::execute',
                            ],
                            'status' => '1',
                            'is_user' => '1',
                        ],
                        'pronto_enableproduct' => [
                            'status' => '0',
                            'schedule' => [
                                'cron_expr' => null,
                            ],
                        ],
                        'pronto_disableproduct' => [
                            'status' => '0',
                            'schedule' => [
                                'cron_expr' => null,
                            ],
                        ],
                        'tec_pronto_disableproducts' => [
                            'schedule' => [
                                'cron_expr' => '0 3 * * *',
                            ],
                            'run' => [
                                'model' => 'Digidirect\\Pronto\\Cron\\DisableProductCron::execute',
                            ],
                            'status' => '0',
                            'is_user' => '1',
                        ],
                        'tec_readytopickup' => [
                            'schedule' => [
                                'cron_expr' => '* * * * *',
                            ],
                            'run' => [
                                'model' => 'Digidirect\\ReadytoPickup\\Cron\\SendReadytoPickupEmail::execute',
                            ],
                            'status' => '1',
                            'is_user' => '1',
                        ],
                        'pronto_retail_sync' => [
                            'schedule' => [
                                'cron_expr' => null,
                            ],
                        ],
                        'pronto_wiserdata' => [
                            'schedule' => [
                                'cron_expr' => null,
                            ],
                        ],
                        'pronto_wiserdata1' => [
                            'schedule' => [
                                'cron_expr' => null,
                            ],
                        ],
                        'pronto_wiserdata2' => [
                            'schedule' => [
                                'cron_expr' => null,
                            ],
                        ],
                        'pronto_order_sync_processing' => [
                            'schedule' => [
                                'cron_expr' => '*/2 * * * *',
                            ],
                        ],
                    ],
                ],
                'digidirect_ai' => [
                    'jobs' => [
                        'ai_run_by_schedule' => [
                            'status' => '1',
                        ],
                        'ai_run_queue' => [
                            'status' => '1',
                        ],
                    ],
                ],
                'staging' => [
                    'jobs' => [
                        'staging_apply_version' => [
                            'status' => '0',
                        ],
                        'staging_synchronize_entities_period' => [
                            'status' => '0',
                        ],
                    ],
                ],
                'digidirect_invoiceemail' => [
                    'jobs' => [
                        'send_invoiceemail' => [
                            'status' => '1',
                        ],
                    ],
                ],
            ],
            'advanced' => [
                'in_store_pickup_api' => [
                    'search_term_delimiter' => ':',
                ],
            ],
            'paypal_onboarding' => [
                'middleman_domain' => null,
            ],
            'url_rewrite' => [
                'entity_types' => [
                    'product' => [
                        'generator' => 'Magento\\Framework\\DataObject',
                    ],
                    'category' => [
                        'generator' => 'Magento\\Framework\\DataObject',
                    ],
                    'cms-page' => [
                        'generator' => 'Magento\\Framework\\DataObject',
                    ],
                ],
            ],
            'support' => [
                'backup_items' => [
                    'code' => [
                        'class' => 'Magento\\Support\\Model\\Backup\\Item\\Code',
                        'params' => [
                            'output_file_extension' => 'tar.gz',
                            'type' => '1',
                        ],
                    ],
                    'db' => [
                        'class' => 'Magento\\Support\\Model\\Backup\\Item\\Db',
                        'params' => [
                            'output_file_extension' => 'sql.gz',
                            'type' => '2',
                        ],
                    ],
                ],
                'output_path' => 'var/support/',
            ],
            'swat' => [
                'url' => 'https://swat.magento.com/',
                'jwt' => [
                    'alg' => 'RS256',
                    'exp' => '600',
                ],
                'rsa_keypair' => '0:3:6KU/0bDVGrP3MSdEhy79lsmHQ+ooc8eSsrq68M0H4zMoVStGMEtIz/FhHEn4z3/riN04Hki+dfB9cD7ZRVFEynLhus2QvTMncagO1ajaNlWUh0FKFO7EbmMSL+yn57yFKrCJtQptdqAfx8h4lLrq1r3Y1Ll2tIDKN68qKUlLkBC+ew3096SuSlm9qbXMLbl16v9tvPCLeqcR1LU7lSq0pPMbfmPDBWVHNwCipTRs7hH9PlImVIWX7fo3hON8E3koO1VEW2pEHjYTkjJnliCjo7Tp4P6tqzhj8W4rr8nV+9/RdgGdhHqh1SkAx8VQg4g84t3+uHmqiSHl7Ln43kI5cyCy9Zp5WdbKipAgheIpycVQcYxf8f22evly+XQFm4C7WTb71YSAbkXy+BTCq8XiQdYzdZP46iWYuKm4FSaysNB5FVh3jC5NbTZkGW6O4++NnCJ17Jb0ieh3YMQ/HKFn4blQHZaaatZ618c2mrvkqO9S1GC5+uqi4Sk+kfIzyoem8WZz5BMIl8ywAHjVddVhZ5jBlkF5wZYEw6hnAe1eHMtTQx740ndxYZzXP9iWyHKJ+0BlbYA5e+t+0gLB/yaCqIuGxwR0xxZiDoZzkHPNff6sXM5tOgLd/jR4czgQcQBbKahI17pHzD4x4mn5ir4N/008GC5p5jfng107eQHYnIlTBO+XL02xIjxTl0S5Ym38CRTReTV2+CFdxF4xshoRANk5CQ5lf3SumlsyFvhHtLj7gtLfBbf06I6JZUEGOnhZW1ea4yCFnk3pZITv5/c1frX1dTwEJ+Em4dA3wPvyeUbagvd61GO3FD3UkdHz4XDTfygOosGDhQUdyk0tuoFYl86c0z3YIdDrJMVpp9h4Dp7RlylwgAv1iA9O6GmHlrKLmUOlk4iKXhQv+b4IJg/slvlGoR0YpxF5s9h0IgpzsUMQFFq6ANCErwVngKd42DuCGVZhiv+QsN27Pk/k9BqYgNNjhyUXzSKRdQ5KewLU4NQOZpZYIH7AzDBnwYMr4kfE9tMzEiYQrRkjX42ybmSEw15fzdLuoaCT8IZ3Zsf0gMbswtxs1HHyM5BkEIEF9dMZKDo7qkbaLho1etrJt9+2SUm13sV7BCZJvncvqEtAM9cdX9RsHYGiJoO0IWURT6I+XZNgkadgwTQuuiazdQbNqGJS0LwfKkHViKTrzwnqQuVikL4qoCVd6EFE69TTKsFImcNp5e0OR37mAjK6vPunrGTWwFqsCf3s0NlfauNooGCyU3o6Yz/UBEkWAvZedTdDi5Ys2b6B+K9qXPW/+8Q1WFKk/WdqNPmN1PYjrNAnq5IRDKkumCWqJQtq1ggE91IInk5xVz7rCFoYNxmA1jpTrjDaRJsUsUgkm5MYf8iFhb6FHd2PjA/EM77M1yc18bSi7bJyZ3Iuhhg/95OfV7AbB3ZARpBssVczLpa6KqapIsOd+b9GDHP7GzKT+gj9tYxmhJhUlL3ZRLiQqz9zZ0NNLcdtI9BWFyaa9CxCYLSZ6vLCpDN2vxDjxEIgVYRaZdZMOpUEpXlnISNyuHnphyadicKqB+HAChG4ruKElkefgyZM5zNL42LEFxghyfyaEkctU/ELlp3ym3kEhfmlGstJ9yBjIltot5X0DDpl0CTDHbgtGf2w2BtIMumftdobxVaQCDRGpdJwk896teRPA/AkgKQ3PlZbBw/ZLADnZ96YmXujWEYYQscu6vlueVGsDr7BvcEsYMBIgvRCLISoAfqO5ArK0GyD878e+pgQIVpNHnYg6KhK3N1yQMGsBU1sqI1iKAWUMwKSzxgRPW0OnQ5+TiJwkVlRxGyi8GucCYovj29Hwvc6SCya5ZN4/fHOxNemlSS/VCzOukLbNctM8GiS9yH6Dd6j0ASLMVwGrhzsLyZNTEqLV31vfWB4IY4jnPBfeMCS6b+bzLeZ1KLu596qbcUqIbKWILO3d1U0K8f1EiVE1pCCUEANhazxIR1zFCARq0/Whn/nQD70f1YF2mow13jvPm6rUc9rZfr51+QFp3lfstmF40w2Wb2n7Qv7nPaVnPks07PN2BO6m3S/rcYFcV/LYoKEMdhsxu08T7Qrd2uCpy5rF0GeuY9BOdwzxUKhulXaycHnJa5sKIsonD/X2c2F5FGliYUCLBWfuSaIwiMBdhoVpypeUv3mmH1k/m2VBNqIJwhROiREhqQhomT5us3No/eKn7OIg7YB2mpfIqzSeD7c9ZemDPBcC++Ufxl58BgijsXxWBn9WbAHQwmVmpLXPP+uZ4iFIcgDq9zKYqAXS6HLM9FDeV5S4+a0B+HwyYUrAQ==',
                'jwks' => '0:3:AIDdA7IODKea5cgKfOD3OuAJm6a6/mFqnp/nTalWbVklC00UsSwLkOCKmvx0ubq+JCLmMWCOgi7vocEOqdPWezoJ3ceP9boazpZZYnRWwXAxs9Rq2ngZLhK0GvezNYK+QSGiCDeZztGfJFRBNdnj4hkeNGq7mIKIsHePNjYDTcYQl1d6k2SgcnEwRCmd5Cs63dH/7U0fGoK7GShDHXCXF3ENq+w27+ZC9gSCRIM6W77Mb8DhOxlIpJq8WF33gS7wJf1psVN1tz0HxKqzpWKjlRAj0h5zi1hBWUrK581wseZfNxaYwV9R/lVokdmlYq7A1DTZhWdF505SiDWqrSEjejRALcrxlDo1pN5NsnA23H2jsBKl3Acjnv6fd9qmUW6f4577oeAK2R/UlbSL6hXDGAiORK73S27Lxtr388kV0eRyLtxI+2iJwBzrrRD0xoghKBtmgoZ2kiYcNU1nvzw=',
            ],
            'enable_tracking_number_adding_notification' => '0',
            'store_email_attribute' => null,
            'tracking_number_adding_notification_template' => 'carriers_collect_tracking_number_adding_notification_template',
            'csp' => [
                'mode' => [
                    'storefront' => [
                        'report_only' => '0',
                    ],
                    'admin' => [
                        'report_only' => '0',
                    ],
                ],
            ],
            'module' => [
                'user_guide' => 'webhook',
            ],
            'mgz_chooser_widget' => [
                'chooser_defaults' => [
                    'catalog_product_widget_chooser' => [
                        'input_label' => 'Product',
                        'button_text' => 'Select Product...',
                    ],
                    'catalog_category_widget_chooser' => [
                        'input_label' => 'Category',
                        'button_text' => 'Select Category...',
                    ],
                    'cms_block_widget_chooser' => [
                        'input_label' => 'Block',
                        'button_text' => 'Select Block...',
                    ],
                    'cms_page_widget_chooser' => [
                        'input_label' => 'CMS Page',
                        'button_text' => 'Select CMS Page...',
                    ],
                    'default' => [
                        'input_label' => 'Element',
                        'button_text' => 'Select...',
                    ],
                ],
            ],
            'mirasvit' => [
                'seo' => [
                    'enabled' => '1',
                ],
                'seoautolink' => [
                    'enabled' => '1',
                ],
                'seositemap' => [
                    'enabled' => '1',
                ],
            ],
            'attributepages' => [
                'image' => [
                    'background' => '255,255,255',
                ],
                'seo' => [
                    'allow_direct_option_link' => '0',
                ],
                'option_list' => [
                    'hide_description_when_filter_is_used' => '1',
                ],
                'product_list' => [
                    'use_layered_navigation' => '1',
                    'layer_block_name' => 'catalog.leftnav,enterprisecatalog.leftnav,tm.catalog.left.navigation',
                    'hide_description_when_filter_is_used' => '1',
                ],
            ],
            'swissup_amp' => [
                'whitelist' => [
                    'layout_updates' => [
                        'attributepages' => 'Swissup_Attributepages',
                    ],
                    'block_types' => [
                        'attributepages' => [
                            'attribute_view' => [
                                'class' => 'Swissup\\Attributepages\\Block\\Attribute\\View',
                            ],
                            'attribute_list' => [
                                'class' => 'Swissup\\Attributepages\\Block\\Attribute\\PagesList',
                            ],
                            'option_list' => [
                                'class' => 'Swissup\\Attributepages\\Block\\Option\\OptionList',
                            ],
                            'product_option' => [
                                'class' => 'Swissup\\Attributepages\\Block\\Product\\Option',
                            ],
                            'widget_attribute_list' => [
                                'class' => 'Swissup\\Attributepages\\Block\\Widget\\Attribute\\PagesList',
                            ],
                            'widget_option_list' => [
                                'class' => 'Swissup\\Attributepages\\Block\\Widget\\Option\\OptionList',
                            ],
                            'abstract' => [
                                'class' => 'Swissup\\Attributepages\\Block\\AbstractBlock',
                            ],
                        ],
                    ],
                ],
                'includes' => [
                    'blocks' => [
                        'attribute_list' => [
                            'class' => 'Swissup\\Attributepages\\Block\\Attribute\\PagesList',
                            'styles' => [
                                'attributepages' => 'Swissup_Attributepages::attributepages.scss',
                            ],
                        ],
                        'option_list' => [
                            'class' => 'Swissup\\Attributepages\\Block\\Option\\OptionList',
                            'styles' => [
                                'attributepages' => 'Swissup_Attributepages::attributepages.scss',
                            ],
                        ],
                        'abstract' => [
                            'class' => 'Swissup\\Attributepages\\Block\\AbstractBlock',
                            'styles' => [
                                'attributepages' => 'Swissup_Attributepages::attributepages.scss',
                            ],
                        ],
                        'product_option' => [
                            'class' => 'Swissup\\Attributepages\\Block\\Product\\Option',
                            'styles' => [
                                'attributepages' => 'Swissup_Attributepages::attributepages.scss',
                            ],
                        ],
                    ],
                ],
            ],
            'subscriptionchecker' => [
                'ignore' => [
                    'swissup_core' => null,
                ],
            ],
            'weltpixel' => [
                'crontab' => [
                    'license' => '4 2 * * 0',
                ],
            ],
            'msp_securitysuite_twofactorauth' => [
                'duo' => [
                    'application_key' => 'be2USw3Hr7KXQQjoCsN7YuEqti5ZbHtlYfZ4FTuxHMpntkRSb79lJIVK6AalEw8X',
                ],
            ],
            'connector_dynamic_content' => [
                'external_dynamic_content_urls' => [
                    'passcode' => 'Laeu4fORLFL89UB3udaJUOXjsFaEg5mL',
                    'abandoned_cart_url' => 'https://www.digidirect.com.au/connector/email/basket/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/quote_id/@LAST_QUOTE_ID@',
                    'review_url' => 'https://www.digidirect.com.au/connector/email/review/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/order_id/@LAST_ORDER_ID@',
                    'coupon_code_url' => 'https://www.digidirect.com.au/connector/email/coupon/id/[INSERT ID HERE]/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/expire_days/[INSERT NUMBER OF DAYS HERE]/@EMAIL@',
                    'related_products_url' => 'https://www.digidirect.com.au/connector/product/related/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/order_id/@LAST_ORDER_ID@',
                    'upsell_products_url' => 'https://www.digidirect.com.au/connector/product/upsell/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/order_id/@LAST_ORDER_ID@',
                    'crosssell_products_url' => 'https://www.digidirect.com.au/connector/product/crosssell/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/order_id/@LAST_ORDER_ID@',
                    'cart_related_products_url' => 'https://www.digidirect.com.au/connector/quoteproducts/related/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/quote_id/@LAST_QUOTE_ID@',
                    'cart_upsell_products_url' => 'https://www.digidirect.com.au/connector/quoteproducts/upsell/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/quote_id/@LAST_QUOTE_ID@',
                    'cart_crosssell' => 'https://www.digidirect.com.au/connector/quoteproducts/crosssell/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/quote_id/@LAST_QUOTE_ID@',
                    'best_sellers_url' => 'https://www.digidirect.com.au/connector/report/bestsellers/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL',
                    'most_viewed_url' => 'https://www.digidirect.com.au/connector/report/mostviewed/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL',
                    'product_push_url' => 'https://www.digidirect.com.au/connector/product/push/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL',
                    'product_recently_viewed_url' => 'https://www.digidirect.com.au/connector/report/recentlyviewed/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/customer_id/@CUSTOMER_ID@',
                    'wishlist_product_url' => 'https://www.digidirect.com.au/connector/email/wishlist/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/customer_id/@CUSTOMER_ID@',
                    'wishlist_related' => 'https://www.digidirect.com.au/connector/wishlist/related/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/customer_id/@CUSTOMER_ID@',
                    'wishlist_upsell_url' => 'https://www.digidirect.com.au/connector/wishlist/upsell/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/customer_id/@CUSTOMER_ID@',
                    'wishlist_crosssell_url' => 'https://www.digidirect.com.au/connector/wishlist/crosssell/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/customer_id/@CUSTOMER_ID@',
                ],
                'products' => [
                    'wishlist_display_type' => 'grid',
                    'review_display_type' => 'grid',
                    'related_display_type' => 'grid',
                    'related_items_to_display' => '2',
                    'upsell_display_type' => 'grid',
                    'upsell_items_to_display' => '2',
                    'crosssell_display_type' => 'grid',
                    'crosssell_items_to_display' => '2',
                    'bestsellers_display_type' => 'grid',
                    'bestsellers_items_to_display' => '4',
                    'bestsellers_time_period' => 'M',
                    'most_viewed_display_type' => 'grid',
                    'most_viewed_items_to_display' => '2',
                    'most_viewed_time_period' => 'M',
                    'recently_viewed_display_type' => 'grid',
                    'recenlty_viewed_items_to_display' => '4',
                ],
                'manual_product_push' => [
                    'display_type' => 'list',
                    'items_to_display' => '2',
                    'products_push_items' => null,
                ],
                'fallback_products' => [
                    'product_ids' => null,
                ],
            ],
            'connector_automation' => [
                'review_settings' => [
                    'allow_non_subscribers' => '1',
                    'enabled' => '0',
                    'status' => null,
                    'delay' => null,
                    'campaign' => '0',
                    'anchor' => null,
                ],
                'visitor_automation' => [
                    'customer_automation' => '0',
                    'subscriber_automation' => '7773',
                    'first_order_automation' => '0',
                    'order_automation' => '0',
                    'guest_order_automation' => '0',
                    'review_automation' => '0',
                    'wishlist_automation' => '0',
                ],
                'order_status_automation' => [
                    'program' => [],
                ],
                'feefo_feedback_engine' => [
                    'logon' => null,
                    'reviews_per_product' => null,
                    'template' => null,
                    'service_score_url' => 'https://www.digidirect.com.au/connector/feefo/score/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL',
                    'product_reviews_url' => 'https://www.digidirect.com.au/connector/feefo/reviews/code/Laeu4fORLFL89UB3udaJUOXjsFaEg5mL/quote_id/@QUOTE_ID@',
                ],
            ],
            'connector_configuration' => [
                'abandoned_carts' => [
                    'allow_non_subscribers' => '1',
                    'email_capture' => '1',
                    'easy_capture_newsletter' => '1',
                    'cart_limit' => '12',
                    'link_back_to_cart' => '1',
                    'link_text' => null,
                    'cart_url' => null,
                    'login_url' => null,
                ],
                'data_fields' => [
                    'order_status' => 'collected,complete',
                    'brand_attribute' => 'brand',
                ],
                'tracking' => [
                    'roi_enabled' => '1',
                    'page_enabled' => '1',
                    'tracking_profile_id' => 'DM-3747180427-03',
                ],
                'consent' => [
                    'dotmailer_consent_subscriber_enabled' => '0',
                    'dotmailer_consent_subscriber_text' => null,
                    'dotmailer_consent_customer_text' => null,
                ],
                'transactional_data' => [
                    'order_statuses' => 'complete,pending,processing',
                    'order_custom_attributes' => '0,days_to_ship_last_order',
                    'order_product_attributes' => '0',
                    'order_product_custom_options' => '0',
                ],
                'dynamic_content_style' => [
                    'dynamic_styling' => 'table{font-family: Arial, Helvetica, sans-serif; font-size:12px;}',
                    'font_color' => '#000000',
                    'font_size' => '12px',
                    'font_style' => '0',
                    'price_color' => '#000000',
                    'price_font_size' => '12px',
                    'price_font_style' => '0',
                    'product_link_color' => '#000000',
                    'product_link_font_size' => '12px',
                    'product_link_style' => '0',
                    'font' => 'Arial, Helvetica, sans-serif',
                    'color' => '#FFFFFF',
                    'other_color' => '#000000',
                    'other_font_size' => '12px',
                    'other_font_style' => '0',
                    'coupon_font_color' => '#000000',
                    'coupon_font_size' => '14px',
                    'coupon_font_style' => '0',
                    'coupon_font_picker' => 'Arial, Helvetica, sans-serif',
                    'coupon_background_color' => '#FFFFFF',
                ],
                'dynamic_content' => [
                    'link_text' => null,
                ],
                'admin' => [
                    'disable_newsletter_success' => '0',
                    'disable_customer_success' => '0',
                ],
                'catalog_sync' => [
                    'catalog_values' => '2',
                    'catalog_visibility' => '4',
                    'catalog_type' => 'simple,virtual',
                ],
                'customer_addressbook' => [
                    'show_preferences' => '0',
                    'can_change' => '0',
                    'show_books' => '0',
                    'can_show_fields' => '0',
                    'fields_to_show' => '0',
                ],
            ],
            'sync_settings' => [
                'addressbook' => [
                    'allow_non_subscribers' => '1',
                    'customers' => '822046',
                    'subscribers' => '822047',
                    'guests' => '822049',
                ],
                'sync' => [
                    'customer_enabled' => '1',
                    'guest_enabled' => '1',
                    'subscriber_enabled' => '1',
                    'order_enabled' => '1',
                    'wishlist_enabled' => '1',
                    'review_enabled' => '0',
                    'catalog_enabled' => '1',
                ],
                'dynamic_addressbook' => [
                    'addressbook_name' => null,
                    'visibility' => 'Private',
                ],
            ],
            'ewave_quickview' => [
                'general' => [
                    'quickview_enabled' => '1',
                    'close_after_add_to_cart' => '0',
                ],
            ],
            'ewave_extendedcart' => [
                'add_to_cart_confirmation_popup' => [
                    'enabled' => '0',
                    'promotion_block' => 'related',
                    'promotion_block_products_count' => '2',
                ],
                'cart_page' => [
                    'use_ajax_for_cart_update' => '1',
                ],
            ],
            'ewave_cartsuggestions' => [
                'general' => [
                    'cartsuggestions_enabled' => '1',
                    'product_number' => '5',
                    'suggestions_link' => 'WE CAN RECOMMEND...',
                ],
            ],
            'ewave_freegift' => [
                'general' => [
                    'auto_add' => '1',
                    'redirect_to_cart' => '0',
                    'is_hide_free_item_price' => '0',
                    'show_free_item_attributes' => null,
                ],
                'messages' => [
                    'cart_message' => 'FREE!',
                    'prefix' => 'FREE -',
                    'add_message' => 'Free Gift!',
                    'auto_open_popup' => '0',
                    'popup_template' => 'default',
                    'display_error_messages' => '1',
                    'display_success_messages' => '1',
                    'message_hidden_free_item_price' => 'Free',
                    'display_popup_once' => '0',
                    'message_type' => 'notice',
                ],
            ],
            'ewave_blog' => [
                'general' => [
                    'active' => '1',
                    'breadcrumb' => '0',
                    'postonlist' => '15',
                    'postonlist_mobile' => '0',
                    'post_sorting' => 'desc',
                    'date_format' => 'd-m-Y',
                    'post_layout' => '2columns-left',
                    'post_list_layout' => '2columns-left',
                    'cat_layout' => '2columns-left',
                    'list_url' => 'blog',
                    'url_prefix' => null,
                    'url_suffix' => null,
                    'cat_prefix' => 'blog',
                    'top_menu_link' => '1',
                    'footer_link' => '1',
                    'top_menu_title' => 'Blog',
                ],
                'comments' => [
                    'type_of_comment' => 'default',
                    'no_of_comments' => '5',
                    'fb_app_id' => null,
                    'login_require' => '0',
                    'autoapprove' => '0',
                    'loginapprove' => '0',
                    'emailoncomment' => '0',
                    'email_template' => 'ewave_blog_comments_email_template',
                    'admin_email' => null,
                    'commentcount' => '1',
                ],
                'related_setting' => [
                    'related_posts' => [
                        'enabled' => '1',
                        'number_of_posts' => '6',
                    ],
                    'related_products' => [
                        'enabled' => '1',
                        'number_of_products' => '20',
                        'show_addtocart' => '1',
                        'show_whishlist_icon' => '1',
                        'show_compare_icon' => '1',
                    ],
                ],
                'display_settings' => [
                    'display_views' => '0',
                    'display_tags' => '1',
                    'display_share' => '1',
                    'share_above' => '1',
                    'share_below' => '0',
                    'list_page' => [
                        'show_type' => 'list',
                        'display_tags_listing' => '1',
                        'category_onlist_left' => '1',
                        'category_onlist_right' => '1',
                        'popular_onlist_left' => '0',
                        'popular_onlist_right' => '0',
                        'latest_onlist_left' => '0',
                        'latest_onlist_right' => '0',
                        'archive_onlist_left' => '0',
                        'archive_onlist_right' => '0',
                        'show_category_name' => '1',
                    ],
                    'view_page' => [
                        'category_onpost_left' => '1',
                        'category_onpost_right' => '0',
                        'popular_onpost_left' => '0',
                        'popular_onpost_right' => '0',
                        'latest_onpost_left' => '0',
                        'latest_onpost_right' => '0',
                        'archive_onpost_left' => '0',
                        'archive_onpost_right' => '0',
                        'show_category_name_onpost' => '0',
                    ],
                    'cat_page' => [
                        'show_type' => 'grid',
                        'categorylist_oncat_left' => '1',
                        'categorylist_oncat_right' => '1',
                        'popular_oncat_left' => '0',
                        'popular_oncat_right' => '0',
                        'latest_oncat_left' => '0',
                        'latest_oncat_right' => '0',
                        'archive_oncat_left' => '0',
                        'archive_oncat_right' => '0',
                        'show_category_name_oncat' => '1',
                    ],
                ],
                'rss_feed' => [
                    'title' => 'Blog RSS Feed',
                    'description' => null,
                ],
                'infinite_scroll' => [
                    'enable_category_listing' => '0',
                    'enable_post_listing' => '0',
                ],
                'design' => [
                    'theme' => '0',
                    'update_xml' => null,
                ],
                'opengraph_settings' => [
                    'enable_opengraph_on_category_page' => '1',
                    'enable_display_alternative_locales_category_page' => '1',
                    'enable_opengraph_on_post_page' => '1',
                    'enable_display_alternative_locales_post_page' => '1',
                ],
                'related_posts' => [
                    'enabled' => '1',
                    'number_of_posts' => '5',
                ],
                'related_products' => [
                    'enabled' => '1',
                    'number_of_products' => '5',
                    'show_addtocart' => '1',
                    'show_whishlist_icon' => '1',
                    'show_compare_icon' => '1',
                ],
                'list_page' => [
                    'show_type' => 'list',
                    'display_tags_listing' => '0',
                    'category_onlist_left' => '1',
                    'category_onlist_right' => '0',
                    'popular_onlist_left' => '0',
                    'popular_onlist_right' => '0',
                    'latest_onlist_left' => '0',
                    'latest_onlist_right' => '0',
                    'archive_onlist_left' => '0',
                    'archive_onlist_right' => '0',
                    'show_category_name' => '0',
                ],
                'view_page' => [
                    'category_onpost_left' => '1',
                    'category_onpost_right' => '0',
                    'popular_onpost_left' => '0',
                    'popular_onpost_right' => '0',
                    'latest_onpost_left' => '0',
                    'latest_onpost_right' => '0',
                    'archive_onpost_left' => '0',
                    'archive_onpost_right' => '0',
                    'show_category_name_onpost' => '0',
                ],
                'cat_page' => [
                    'show_type' => 'grid',
                    'categorylist_oncat_left' => '1',
                    'categorylist_oncat_right' => '1',
                    'popular_oncat_left' => '0',
                    'popular_oncat_right' => '0',
                    'latest_oncat_left' => '0',
                    'latest_oncat_right' => '0',
                    'archive_oncat_left' => '0',
                    'archive_oncat_right' => '0',
                    'show_category_name_oncat' => '0',
                ],
            ],
            'ewave_extendedminicart' => [
                'general' => [
                    'show_line_item_subtotal' => '1',
                ],
            ],
            'ewave_outofstocknotif' => [
                'stock_subscription' => [
                    'enabled_for_guest' => '1',
                    'enabled_for_backorder' => '0',
                ],
            ],
            'klarna' => [
                'api' => [
                    'api_version' => 'kp_oc',
                    'merchant_id' => 'A001333_8ff0bc00dfae',
                    'shared_secret' => '0:3:8ew25PiC0a7irxHaxs3ivW5jIAZwzD6GQS/wvot4gUezQ6FomuG9JyGyfu8=',
                    'test_mode' => '0',
                    'debug' => '0',
                    'request_logging' => '1',
                    'delete_request_logs_after' => '90',
                ],
                'osm' => [
                    'enabled' => '0',
                    'data_id' => null,
                    'theme' => 'default',
                    'product_enabled' => '1',
                    'product_placement_select' => 'credit-promotion-small',
                    'cart_enabled' => '1',
                    'cart_placement_select' => 'top-strip-promotion-standard',
                ],
                'shipping' => [
                    'product_unit' => 'mm',
                    'length' => 'name',
                    'width' => 'name',
                    'height' => 'name',
                ],
                'keb' => [
                    'enabled' => '0',
                ],
            ],
            'ewave_productoverlay' => [
                'general' => [
                    'image_type' => 'jpg,jpeg,png,gif',
                    'image_mime_type' => 'image/jpeg,image/png,image/gif',
                ],
                'display' => [
                    'product' => '.gallery-placeholder',
                    'category' => '.product-item-photo',
                ],
                'on_sale' => [
                    'sale_min' => null,
                    'sale_min_percent' => null,
                    'rounding' => 'floor',
                ],
                'new' => [
                    'is_new' => '1',
                    'creation_date' => '0',
                ],
                'overlay_management' => [
                    'enable_management' => '1',
                ],
                'smart_cache' => [
                    'enable_cache_cleaner' => '0',
                    'cache_cleaner_cron_expr' => '0 * * * *',
                ],
            ],
            'ewave_utilities_config' => [
                'wysiwyg' => [
                    'wrap_in_parent_tag' => '0',
                    'allowed_childs' => '+body[style],+div[*],+a[*],+span[*],+i[*]',
                    'allowed_tags' => 'svg[*],use[*],g[*],path[*],defs[*],mask[*],a[*]',
                    'allowed_filetypes' => [],
                ],
                'sales_prefix' => [
                    'active' => '0',
                ],
                'frontend_settings' => [
                    'show_theme_switcher' => '0',
                ],
            ],
            'ewave_storelocator' => [
                'dev' => [
                    'name_history' => '{"top":"12","default":"4","bag":"18","key feature":"19","audio & visual":"59","bags & cases":"55","camera accessories":"47","cameras":"23","drones":"41","flash & accessories":"31","home automation":"61","lens accessories":"29","lens filters":"27","lenses":"25","light modifiers & reflectors":"37","memory & storage":"49","monolights & strobes":"35","on-camera lighting":"33","optics":"45","power & batteries":"51","printers & scanners":"57","stabilizers":"43","studio equipment":"39","tripods & support":"53","seo brand description":"63","downloadable":"17","store":"11","games":"69","gaming":"69","computers & it":"74"}',
                ],
            ],
            'ewave_preorder' => [
                'general' => [
                    'enabled' => '1',
                    'allowemptyqty' => '1',
                    'disableforpositiveqty' => '0',
                ],
                'display' => [
                    'addtocartbuttontext' => 'Pre-order',
                    'defaultpreordernote' => 'Pre Order',
                    'orderpreorderwarning' => 'Please be aware this is a preorder. The products will be shipped to you once they become available.',
                ],
                'additional' => [
                    'discovercompositeoptions' => '0',
                    'backordersforavailabilitydate' => '2',
                ],
            ],
            'ewave_productmultiview' => [
                'product_multi_view' => [
                    'enabled' => '1',
                    'display_random_hover' => '1',
                ],
            ],
            'lc_block_config' => [
                'account' => [
                    'license_email' => 'shant@digidirect.com.au',
                    'license_id' => '1886002',
                ],
                'custom_params' => [
                    'cart_products' => '1',
                    'total_cart_value' => '1',
                    'total_orders_count' => '1',
                    'last_order_details' => '1',
                ],
            ],
            'ewave_social_sharing' => [
                'general' => [
                    'enable' => '1',
                ],
                'sharing_facebook' => [
                    'enable' => '1',
                    'id' => null,
                    'like' => '1',
                    'count' => '1',
                ],
                'sharing_google' => [
                    'enable_plus_one' => '0',
                    'enable_share' => '1',
                    'count' => '1',
                ],
                'sharing_pin' => [
                    'enable' => '1',
                    'count' => '1',
                ],
                'sharing_twitter' => [
                    'enable' => '1',
                ],
            ],
            'ewave_infinitescroll_config' => [
                'general' => [
                    'enabled' => '1',
                    'limit' => '28',
                ],
                'catalog' => [
                    'enabled' => '1',
                    'action' => 'click',
                    'limit' => '28',
                ],
                'search' => [
                    'enabled' => '1',
                    'action' => 'click',
                    'limit' => '28',
                ],
                'store_locator_listing_page' => [
                    'enable' => '1',
                    'action' => 'click',
                    'limit' => null,
                ],
            ],
            'ewave_layerednavigation' => [
                'general' => [
                    'ajax_enabled' => '1',
                    'enable_apply_button' => '0',
                    'enable_filter_remember' => '0',
                    'enable_mobile_apply_button' => '1',
                    'hide_options_after_select' => '0',
                ],
                'category' => [
                    'display_mode' => '0',
                    'allow_multiselect' => '1',
                    'enable_show_more' => '1',
                    'options_per_filter' => '4',
                ],
                'url' => [
                    'mode' => '1',
                ],
                'robots' => [
                    'control_robots' => '1',
                ],
            ],
            'ewave_product_attachment' => [
                'general' => [
                    'product_attachment_formats' => null,
                ],
            ],
            'ewave_faq' => [
                'general' => [
                    'faq_enabled' => '1',
                    'ajax_category' => '1',
                    'faq_page_url' => 'faq',
                    'faq_per_page' => null,
                    'faq_tag_enable' => '1',
                    'faq_search_enable' => '1',
                    'faq_meta_title' => null,
                    'faq_meta_description' => null,
                ],
                'customer_questions' => [
                    'enabled' => '1',
                ],
                'notification' => [
                    'receiver' => null,
                    'template' => 'ewave_faq_notification_template',
                    'sender' => 'general',
                    'customer_answer_template' => 'ewave_faq_notification_customer_answer_template',
                ],
            ],
            'connector_api_credentials' => [
                'api' => [
                    'enabled' => '1',
                    'username' => 'apiuser-2a7b1801bebd@apiconnector.com',
                    'password' => '0:3:ygHH+9REhmmEnICYcLUKAXvVby2dvk/mL5mEayT51MTI/NXBMrFUYA==',
                    'endpoint' => 'https://r3-api.dotmailer.com',
                ],
            ],
            'transactional_emails' => [
                'ddg_transactional' => [
                    'enabled' => '0',
                    'send_mode' => 'smtp',
                    'host' => '3',
                    'username' => 'transactionalemail-29d466be829f@apiconnector.com',
                    'password' => '0:3:HttOJqTGGw3CZYesB76UPwP5mphvPFN3/DSz7zN+OFOWfZWL',
                    'port' => '2525',
                    'debug' => '1',
                ],
            ],
            'connector_data_mapping' => [
                'customer_data' => [
                    'title' => 'TITLE',
                    'customer_id' => 'CUSTOMER_ID',
                    'firstname' => 'FIRSTNAME',
                    'lastname' => 'LASTNAME',
                    'dob' => 'DOB',
                    'gender' => 'GENDER',
                    'created_at' => 'ACCOUNT_CREATED_DATE',
                    'last_logged_date' => 'LAST_LOGGEDIN_DATE',
                    'customer_group' => 'CUSTOMER_GROUP',
                    'review_count' => 'REVIEW_COUNT',
                    'last_review_date' => 'LAST_REVIEW_DATE',
                    'subscriber_status' => 'SUBSCRIBER_STATUS',
                    'billing_address_1' => 'BILLING_ADDRESS_1',
                    'billing_address_2' => 'BILLING_ADDRESS_2',
                    'billing_state' => 'BILLING_STATE',
                    'billing_country' => 'BILLING_COUNTRY',
                    'billing_postcode' => 'BILLING_POSTCODE',
                    'billing_telephone' => 'BILLING_TELEPHONE',
                    'billing_company' => 'BILLING_COMPANY',
                    'delivery_address_1' => 'DELIVERY_ADDRESS_1',
                    'delivery_address_2' => 'DELIVERY_ADDRESS_2',
                    'delivery_city' => 'DELIVERY_CITY',
                    'delivery_state' => 'DELIVERY_STATE',
                    'delivery_country' => 'DELIVERY_COUNTRY',
                    'delivery_postcode' => 'DELIVERY_POSTCODE',
                    'delivery_telephone' => 'DELIVERY_TELEPHONE',
                    'delivery_company' => 'DELIVERY_COMPANY',
                    'number_of_orders' => 'NUMBER_OF_ORDERS',
                    'average_order_value' => 'AVERAGE_ORDER_VALUE',
                    'total_spend' => 'TOTAL_SPEND',
                    'last_order_date' => 'LAST_ORDER_DATE',
                    'last_increment_id' => 'LAST_INCREMENT_ID',
                    'total_refund' => 'TOTAL_REFUND',
                    'most_pur_category' => 'MOST_PUR_CATEGORY',
                    'most_pur_brand' => 'MOST_PUR_BRAND',
                    'most_freq_pur_day' => 'MOST_FREQ_PUR_DAY',
                    'most_freq_pur_mon' => 'MOST_FREQ_PUR_MON',
                    'first_category_pur' => 'FIRST_CATEGORY_PUR',
                    'last_category_pur' => 'LAST_CATEGORY_PUR',
                    'first_brand_pur' => 'FIRST_BRAND_PUR',
                    'last_brand_pur' => 'LAST_BRAND_PUR',
                    'website_name' => 'WEBSITE_NAME',
                    'store_name' => 'STORE_NAME',
                    'last_order_id' => 'LAST_ORDER_ID',
                    'last_quote_id' => 'LAST_QUOTE_ID',
                    'abandoned_prod_name' => 'ABANDONED_PROD_NAME',
                    'custom_attributes' => [],
                    'billing_city' => 'BILLING_CITY',
                ],
                'extra_data' => [
                    'reward_points' => 'REWARD_POINTS',
                    'reward_ammount' => 'REWARD_AMOUNT',
                    'expiration_date' => 'REWARD_EXP_DATE',
                    'last_used_date' => 'LAST_USED_DATE',
                    'customer_segments' => 'CUSTOMER_SEGMENTS',
                    'reward_amount' => 'REWARD_AMOUNT',
                ],
                'dynamic_datafield' => [
                    'datafield_name' => null,
                    'datafield_type' => 'String',
                    'datafield_default' => null,
                    'datafield_access' => 'Private',
                ],
            ],
            'ewave_storelocator_config' => [
                'general' => [
                    'enabled' => '1',
                    'api' => 'google',
                    'ask_to_use_geolocation_on_first_visit' => '1',
                ],
                'list_settings' => [
                    'detail_click_action' => 'redirect',
                    'page_url' => 'store-locator',
                    'page_meta_description' => 'Store Locator',
                    'default_radius' => '50',
                    'radius_options' => '10,15,20,25,30,50,75,100',
                    'default_country' => 'AU',
                    'available_countries' => 'AU',
                    'stores_on_locator_page' => '10',
                    'max_stores_to_show' => '100',
                    'group_search_results_by_parent_entity' => '1',
                    'default_image' => null,
                ],
                'search_settings' => [
                    'extend_radius' => '1',
                    'sort_order' => '1',
                    'show_featured_stores_at_the_top' => '1',
                    'search_attributes' => 'name,street,city,country,state,postcode,longitude,latitude,phone_number',
                    'search_min_length' => '3',
                    'entities' => '11',
                ],
                'store_details' => [
                    'enable_directions' => '1',
                ],
                'dev' => [
                    'main_entity' => '11',
                    'entity_layout_mapping' => '{"3003416b166500ad429e4d5ac2c908ec":{"entity_column":"11","additional_layout_column":""}}',
                    'load_all_children_for_parent' => '1',
                    'guess_state' => '0',
                    'guess_country' => '0',
                ],
                'exclusive_management' => [
                    'exclusive_icon' => null,
                ],
            ],
            'ewave_googleapi_config' => [
                'general' => [
                    'google_api_key' => 'AIzaSyAQMJ4R2QcT0ohrTK3fvAq0AAAzffi8Xno',
                ],
            ],
            'ewave_abstractentity' => [
                'general' => [
                    'enable' => '1',
                    'entities' => '11',
                ],
            ],
            'ewave_aa_config' => [
                'general' => [
                    'url_suffix' => null,
                ],
            ],
            'ewave_pronto' => [
                'api' => [
                    'endpoint' => 'https://digi-pronto.abtonline.com.au:8084/rest/abtws',
                    'compcode' => 'DIG',
                    'user' => 'ewaveapi',
                    'token' => '0:3:wNpLfqOGwgJvR6YkT87HGrxFpdJ50adwStZFxQGNS380Xi1c2/Ny4Q+VTV8=',
                    'timezone' => 'UTC',
                ],
                'api_products' => [
                    'products_get_uri' => 'stock-master?call-type=full_enquiry',
                    'disabled_products_percent_skip_update' => '10',
                ],
                'api_inventory' => [
                    'inventory_get_uri' => 'stock-master?call-type=change_enquiry&check-warehouse-change=Y&check-price-change=Y&date-time-change-max',
                    'disabled_products_percent_skip_update' => '10',
                    'diff_last_minutes' => '60',
                    'decrease_quantity_for_all_sources' => '1',
                    'buffer_action' => '1',
                    'buffer_quantity_for_all_sources' => '0',
                ],
                'api_order' => [
                    'order_post_uri' => 'sales?call-type=create_orders',
                    'order_get_uri' => 'sales?call-type=get_order',
                    'collect_place_to_repcode' => '{"6a24e5eb91baa77ceb39a8aec1642062":{"collect_place_column":"11","rep_code_column":"S7P"},"cd24a9aad26538476f4de710c44b654b":{"collect_place_column":"13","rep_code_column":"B4P"},"c239aac69f672c15908e7f8972c7b33a":{"collect_place_column":"17","rep_code_column":"M1P"},"0279837acc2273e5e50c58bc58d5e57e":{"collect_place_column":"21","rep_code_column":"M6P"},"42dcdcf5c153c1283ac16d0e6303087e":{"collect_place_column":"23","rep_code_column":"C3P"},"4f24ed54c2fc45dc5e974427bcffe23c":{"collect_place_column":"19","rep_code_column":"B5P"},"ee0c10f0eb370f9e0d43b366c6b5fa53":{"collect_place_column":"550","rep_code_column":"B4P"}}',
                    'territory_code' => 'WEBS',
                    'payment_method_to_payment_type' => '{"ad97210c351bd761fc98666105a6f339":{"payment_method_column":"braintree","payment_type_column":"BT"},"a8ce8f92bef4f5d7a98c3f1ed2cd9122":{"payment_method_column":"braintree_paypal","payment_type_column":"PY"},"6eba5914fdba14e519ccec11a57d283d":{"payment_method_column":"zipmoneypayment","payment_type_column":"ZM"},"83c67a6194399e55ae24efeda4849d0b":{"payment_method_column":"free","payment_type_column":"VI"},"9a6bc330846b92cfc9e8c2cc87d0f7f9":{"payment_method_column":"banktransfer","payment_type_column":"Y"},"97c27d79a2a1c81448e1ea5d9582237f":{"payment_method_column":"braintree_googlepay","payment_type_column":"BT"},"0c1c9d6568f3a806426de24db7cdbe0e":{"payment_method_column":"paybympcatch","payment_type_column":"CA"}}',
                    'm2epro_payment_method_to_payment_type' => '{"1445ad5d357edbd7a163faa8275d7b49":{"m2epro_payment_method_column":"ebay-PayPal","payment_type_column":"PY"},"788c28c1afc8779471057a2f980a7ea4":{"m2epro_payment_method_column":"amazon","payment_type_column":"AM"}}',
                ],
                'debug' => [
                    'is_debug_update_request' => '0',
                ],
            ],
            'connector_developer_settings' => [
                'import_settings' => [
                    'batch_size' => '500',
                    'transactional_data' => '500',
                    'subscriber_sales_data_enabled' => '0',
                    'strip_pub_from_media_paths' => '0',
                ],
                'debug' => [
                    'debug_enabled' => '1',
                    'api_log_time' => '60',
                ],
                'oauth' => [
                    'client_id' => null,
                    'client_key' => null,
                    'custom_domain' => null,
                    'custom_authorization' => null,
                ],
                'ip_restriction' => [
                    'ip_addresses' => '104.210.118.87, 20.53.120.175, 20.53.121.40, 20.53.122.178, 20.53.124.79, 52.243.70.174, 13.77.44.160, 52.243.66.251, 52.243.65.25,',
                ],
                'cron_schedules' => [
                    'importer' => '3-59/5 * * * *',
                    'contact' => '12-59/15 * * * *',
                    'order' => '8-59/15 * * * *',
                    'catalog' => '6-59/15 * * * *',
                    'review_wishlist' => '14-59/15 * * * *',
                    'customer' => '11-59/15 * * * *',
                    'subscriber' => '8-59/15 * * * *',
                    'guest' => '6-59/15 * * * *',
                ],
                'system_alerts' => [
                    'user_roles' => '1',
                    'system_messages' => '1',
                    'email_notifications' => '0',
                    'frequency' => '24',
                ],
                'pwa_settings' => [
                    'pwa_url' => null,
                ],
            ],
            'klevu_search' => [
                'general' => [
                    'enabled' => '0',
                ],
                'product_sync' => [
                    'enabled' => '1',
                    'frequency' => '0 * * * *',
                    'enabledcms' => '0',
                    'sync_options' => '2',
                    'catalogvisibility' => '0',
                    'category_sync_enabled' => '1',
                    'order_sync_enabled' => '0',
                    'lockfile' => '172800',
                    'rating_sync_enabled' => '1',
                    'include_oos' => '1',
                ],
                'searchlanding' => [
                    'landenabled' => '2',
                    'relevance_label' => 'Relevance',
                    'klevu_search_sort_orders' => [],
                ],
                'image_setting' => [
                    'image_width' => '200',
                    'image_height' => '200',
                ],
                'developer' => [
                    'collection_method' => '0',
                    'force_log' => '1',
                    'log_level' => '7',
                    'tagging_options' => '0',
                    'trigger_options_info' => '0',
                    'preserve_layout_log_enabled' => '0',
                    'orderip' => 'remote_ip',
                    'theme_version' => 'v1',
                ],
                'attributes' => [
                    'categoryanchor' => '0',
                ],
                'price_per_customer_group' => [
                    'enabled' => '0',
                ],
                'notification' => [
                    'object_vs_collection' => '1',
                    'lock_file' => '1',
                    'orders_with_same_ip' => '1',
                ],
                'metadata' => [
                    'enabled' => '0',
                ],
            ],
            'ewave_address_suggestion' => [
                'general' => [
                    'enabled' => '2',
                    'api_key' => 'AIzaSyAKGpKARXYMcOCf34GFaTI7wrv-tVkl-L0',
                    'postcode_length' => [],
                ],
            ],
            'ewave_ai' => [
                'backups' => [
                    'backups_path' => null,
                ],
                'logs' => [
                    'remove_db_logs_older_than_days' => null,
                    'remove_logs_cron_expr' => '0 1 * * *',
                    'logs_path' => null,
                    'backup_logs_path' => null,
                    'cron_expr' => '0 2 * * *',
                    'logs_files_backup_days' => '7',
                    'logs_email_template' => 'ewave_ai_logs_logs_email_template',
                ],
                'exceptions' => [
                    'exceptions_emails' => '24x7support@ewave.com,teamtwo@ewave.com,eugene.lepeshko@ewave.com',
                    'fail_run_email_template' => 'ewave_ai_exceptions_fail_run_email_template',
                ],
                'queue' => [
                    'cron_expr' => '*/5 * * * *',
                    'number_of_attempts' => '5',
                    'number_of_attempts_per_process' => [],
                    'interval_of_attempts' => '300',
                    'interval_of_attempts_for_pending_depends' => '900',
                    'fail_emails' => null,
                    'fail_email_template' => 'ewave_ai_queue_fail_email_template',
                ],
            ],
            'cto_onetag_section' => [
                'general' => [
                    'cto_partner' => '44042',
                    'cto_enable_home' => '1',
                    'cto_enable_listing' => '1',
                    'cto_enable_product' => '1',
                    'cto_enable_basket' => '1',
                    'cto_enable_sale' => '1',
                    'cto_use_sku' => '0',
                    'cto_feed_pwd' => 'digiDirectFeed',
                ],
            ],
            'product_priority' => [
                'sort_settings' => [
                    'is_active' => '1',
                    'sort_by' => 'number_of_sale',
                    'sort_by_period' => '90',
                    'push_out_of_stock_bottom' => '0',
                    'push_new_product_to_the_top' => '1',
                    'period_for_new_products' => '7',
                ],
                'cron_settings' => [
                    'cron_value' => '0',
                    'cron_time' => '0 * * * *',
                ],
            ],
            'abandoned_carts' => [
                'customers' => [
                    'enabled_1' => '0',
                    'send_after_1' => '15',
                    'campaign_1' => '113436',
                    'enabled_2' => '0',
                    'send_after_2' => '24',
                    'campaign_2' => '113629',
                    'enabled_3' => '0',
                    'send_after_3' => '72',
                    'campaign_3' => '113630',
                ],
                'guests' => [
                    'enabled_1' => '0',
                    'send_after_1' => '60',
                    'campaign_1' => '113436',
                    'enabled_2' => '0',
                    'send_after_2' => '24',
                    'campaign_2' => '113629',
                    'enabled_3' => '0',
                    'send_after_3' => '72',
                    'campaign_3' => '113630',
                ],
                'program' => [
                    'id' => '7832',
                    'send_after' => '60',
                ],
            ],
            'm2epro' => [
                'maintenance' => '0',
            ],
            'ewave_related_product' => [
                'shown_parameters' => [
                    'filter_tab_enable' => '1',
                    'category_attributes' => 'name',
                    'attributes' => 'name,sku,description',
                    'visibility_products' => '2,4',
                    'images_enable' => '1',
                    'attribute_sets' => '4,23',
                    'display_one_category' => '1',
                ],
            ],
            'Fastly' => [
                'Cdn' => [
                    'updated_VCL_to_Fastly_flag' => '1',
                ],
            ],
            'msp_securitysuite_recaptcha' => [
                'general' => [
                    'public_key' => '6LdIab0UAAAAAD54pzi1aec0I0WHMn4FUYPtxpqf',
                    'private_key' => '6LdIab0UAAAAAEnMh7PfQE2-3EsDgZ8g1miCNCH0',
                ],
                'backend' => [
                    'enabled' => '0',
                ],
                'frontend' => [
                    'enabled' => '0',
                    'type' => 'recaptcha',
                    'theme' => 'light',
                    'lang' => null,
                    'size' => 'compact',
                    'enabled_login' => '1',
                    'enabled_forgot' => '1',
                    'enabled_contact' => '1',
                    'enabled_create' => '1',
                ],
            ],
            'ewave_extendedshippingrates' => [
                'main' => [
                    'multiple_rates_price' => '0',
                ],
                'hide_methods' => [
                    'hided_methods_relations' => [],
                ],
            ],
            'ewave_mystorewidget' => [
                'general' => [
                    'enable' => '1',
                    'entities' => '11',
                    'search_type' => '0',
                    'search_attributes' => 'name,city,country,state,search_postcode',
                    'response_attributes' => 'name,city,country,postcode,search_postcode',
                    'search_min_length' => '3',
                    'remember_store_cookie_lifetime' => '36000',
                    'show_widget_on_checkout' => '1',
                ],
                'click_and_collect' => [
                    'disable_not_full_c_c' => '0',
                    'c_c_relation_enable' => '1',
                ],
                'shipping_address' => [
                    'enable' => '0',
                    'fields_info' => '{"postcode_postcode":{"shipping_field":"postcode","entity_attribute":"postcode"}}',
                    'verify_on_checkout' => '0',
                ],
                'google_auto_suggest' => [
                    'enable' => '0',
                    'api_key' => null,
                ],
                'geo_location' => [
                    'enable' => '0',
                    'behavior' => 'keep',
                    'disable_autocomplite_mobile_divice' => '0',
                ],
            ],
            'ewave_abstract_gift_card' => [
                'settings' => [
                    'active' => '1',
                    'allow_native_gift_cards' => '0',
                    'accept_gift_cards_for_paid_orders' => '0',
                ],
            ],
            'ewave_shippingavailabilitycheck' => [
                'general' => [
                    'enable' => '1',
                    'all_methods' => '0',
                    'display_for_out_of_stock' => '1',
                ],
            ],
            'mpcatch_config' => [
                'mpcatch_setting' => [
                    'enable' => '0',
                    'mode' => 'live',
                    'sandbox_endpoint' => 'https://catch-dev.mirakl.net/',
                    'api_key' => '0f748223-e1d9-4968-a603-6ff177acbe39',
                    'storeid' => '1',
                    'debug_mode' => '0',
                    'valid' => '1',
                    'live_endpoint' => 'https://marketplace.catch.com.au/',
                ],
                'mpcatch_product' => [
                    'price_settings' => [
                        'price' => 'final_price',
                    ],
                    'inventory_settings' => [
                        'advanced_threshold_status' => '0',
                        'use_msi' => '1',
                        'msi_source_code' => 'SWHS',
                    ],
                    'mpcatch_other_prod_setting' => [
                        'mpcatch_skip_from_validation' => null,
                        'mpcatch_use_other_parent' => null,
                        'mpcatch_merge_parent_images' => '0',
                        'mpcatch_upload_config_as_simple' => '0',
                    ],
                ],
                'mpcatch_order' => [
                    'order_id_prefix' => 'CATCH-',
                    'order_notify_email' => 'sales@digidirect.com.au',
                    'auto_accept_order' => '0',
                    'hold_order_until_shipping' => '0',
                    'enable_default_customer' => '0',
                    'mpcatch_refund_from_core' => '0',
                ],
                'mpcatch_cron' => [
                    'order_cron' => '0',
                    'order_sync_cron' => '0',
                    'inventory_price_cron' => '0',
                    'feed_sync_cron' => '0',
                    'full_offer_sync_cron' => '0',
                    'order_shipment_cron' => '0',
                ],
            ],
            'yotpo' => [
                'module_info' => [
                    'yotpo_installation_date' => '2021-07-05',
                ],
                'sync_settings' => [
                    'orders_sync_start_date' => '2021-07-05',
                ],
            ],
            'digidirect_infinitescroll_config' => [
                'general' => [
                    'enabled' => '1',
                    'limit' => '100',
                ],
                'catalog' => [
                    'enabled' => '1',
                    'action' => 'scroll',
                    'limit' => '50',
                ],
                'search' => [
                    'enabled' => '1',
                    'action' => 'scroll',
                    'limit' => '50',
                ],
                'store_locator_listing_page' => [
                    'enable' => '0',
                    'action' => 'scroll',
                    'limit' => null,
                ],
            ],
            'digidirect_storelocator' => [
                'dev' => [
                    'name_history' => '{"store":"79","computers & it":"74","testtec":"86","default":"4","audio & visual":"59","bags & cases":"55","camera accessories":"47","cameras":"23","drones":"41","flash & accessories":"31","gaming":"69","home automation":"61","lens accessories":"29","lens filters":"27","lenses":"25","light modifiers & reflectors":"37","memory & storage":"49","monolights & strobes":"35","on-camera lighting":"33","power & batteries":"51","printers & scanners":"57","stabilizers":"43","studio equipment":"39","tripods & support":"53","seo brand description":"85","optics":"45","downloadable":"17","default marketplacer":"88","new seo brand description":"91","marketplacer default":"88"}',
                ],
            ],
            'digidirect_faq' => [
                'general' => [
                    'ajax_category' => '1',
                    'faq_enabled' => '0',
                    'faq_page_url' => 'old-faq',
                    'main_url_suffix' => null,
                    'faq_per_page' => null,
                    'faq_tag_enable' => '0',
                    'faq_search_enable' => '0',
                    'faq_meta_title' => null,
                    'faq_meta_description' => null,
                    'category_url_suffix' => null,
                ],
                'customer_questions' => [
                    'enabled' => '0',
                    'category_id' => '16',
                ],
                'notification' => [
                    'receiver' => null,
                    'template' => 'digidirect_faq_notification_template',
                    'sender' => 'general',
                    'customer_answer_template' => 'digidirect_faq_notification_customer_answer_template',
                ],
            ],
            'digidirect_layerednavigation' => [
                'general' => [
                    'ajax_enabled' => '1',
                    'enable_apply_button' => '0',
                    'enable_mobile_apply_button' => '0',
                    'enable_filter_remember' => '0',
                    'hide_options_after_select' => '1',
                ],
                'category' => [
                    'display_mode' => '0',
                    'allow_multiselect' => '0',
                    'enable_show_more' => '0',
                    'options_per_filter' => '10',
                ],
                'url' => [
                    'mode' => '1',
                ],
                'robots' => [
                    'control_robots' => '1',
                ],
            ],
            'free' => [
                'module' => [
                    'email' => 'clint@kayweb.com.au',
                    'name' => 'Clint Mercado',
                    'create' => '1',
                    'subscribe' => '0',
                ],
            ],
            'mpcronschedule' => [
                'module' => [
                    'active' => '1',
                    'product_key' => 'COJZM5HP4NTXW0MC7J70G06HIP070NSJM3AX6VNP',
                    'email' => 'clint@kayweb.com.au',
                    'name' => 'Clint Mercado',
                    'create' => '1',
                    'subscribe' => '0',
                ],
                'general' => [
                    'backend_notification' => '1',
                    'email_notification' => '1',
                    'send_from' => 'general',
                    'send_to' => null,
                    'email_template' => 'mpcronschedule_general_email_template',
                    'schedule' => '0 * * * *',
                    'clear_schedule' => '30',
                    'clear_schedule_expr' => '0 */720 * * *',
                ],
            ],
            'productlabels' => [
                'general' => [
                    'enabled' => '0',
                ],
            ],
            'shopbybrand' => [
                'general' => [
                    'enabled' => '0',
                    'attribute' => 'brand',
                    'route' => 'brand',
                    'link_title' => 'Brands',
                    'show_position' => null,
                    'show_dropdown' => '0',
                    'show_brand_name' => '0',
                    'show_brand_info' => 'not-show',
                    'logo_width_on_product_page' => '100',
                    'logo_height_on_product_page' => '50',
                    'show_brand_info_in_listing' => 'not-show',
                ],
                'brandpage' => [
                    'name' => 'Brands',
                    'brandlist_style' => '0',
                    'display' => '1',
                    'brand_logo_width' => '100',
                    'brand_logo_height' => '100',
                    'color' => '#ff3c00',
                    'show_description' => '0',
                    'show_brands_without_products' => '1',
                    'show_product_qty' => '1',
                    'show_quick_view' => '0',
                    'custom_css' => null,
                    'brand_filter' => [
                        'enabled_cat_filter' => '1',
                        'enabled_alpha_filter' => '1',
                        'alpha_bet' => 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z',
                        'encode_key' => 'UTF-8',
                    ],
                    'search' => [
                        'enable' => '1',
                        'min_search_chars' => '3',
                        'max_query_results' => '10',
                        'visible_images' => '1',
                    ],
                    'feature' => [
                        'enable' => '1',
                        'style' => '0',
                        'title' => 'Featured Brands',
                        'display' => '0',
                    ],
                    'related_products' => [
                        'enabled' => '0',
                    ],
                ],
                'brandview' => [
                    'default_block' => null,
                    'show_image' => '0',
                    'show_description' => '0',
                    'show_block' => '0',
                    'default_image' => null,
                ],
                'sidebar' => [
                    'feature' => [
                        'enable' => '0',
                        'title' => 'Featured Brands',
                        'show_title' => '1',
                    ],
                    'brand_thumbnail' => [
                        'enable' => '0',
                        'title' => 'Brand List',
                        'limit_brands' => '10',
                    ],
                    'category_brand' => [
                        'enable' => '0',
                        'title' => 'Brand Category',
                        'limit_categories' => '10',
                        'show_brand_qty' => '1',
                    ],
                ],
                'brand_seo' => [
                    'seo_pages' => '0',
                ],
            ],
            'deferjs' => [
                'general' => [
                    'active' => '0',
                ],
                'config' => [
                    'iframe' => '0',
                    'show_path' => '0',
                    'controller' => [],
                    'path' => [],
                    'home_page' => '0',
                    'in_body' => '1',
                ],
            ],
            'wesupply_api' => [
                'integration' => [
                    'access_key' => 'DwtSckYa3r8ezmlsQHQc03Xefk7BZoAwob24qe7s',
                ],
            ],
            'wp' => [
                'info' => [
                    'WeSupply_Toolbox' => '0',
                    'WeltPixel_Backend' => '0',
                    'WeltPixel_GA4' => '1',
                    'WeltPixel_GA4_Pro' => '1',
                ],
                'flag' => [
                    'info' => '1',
                ],
            ],
            'sparsh_abandoned_cart' => [
                'customers' => [
                    'enabled_1' => '0',
                    'send_after_1' => '15',
                    'template1' => 'sparsh_abandoned_cart_customers_template1',
                    'enabled_2' => '0',
                ],
                'guests' => [
                    'enabled_1' => '0',
                    'send_after_1' => '15',
                    'template1' => 'sparsh_abandoned_cart_guests_template1',
                    'enabled_2' => '0',
                ],
            ],
            'Newsletterpopup' => [
                'module' => [
                    'data' => '5096188308679a561243824e08c2549b2d06408421',
                ],
            ],
            'magepow_infinitescroll' => [
                'general' => [
                    'enabled' => '0',
                ],
            ],
        ],
        'stores' => [
            'admin' => [
                'design' => [
                    'package' => [
                        'name' => 'default',
                    ],
                    'theme' => [
                        'default' => 'default',
                    ],
                ],
            ],
            'digi_store_view_au' => [
                'design' => [
                    'head' => [
                        'title_suffix' => '| digiDirect',
                        'includes' => '<meta name="google-site-verification" content="vC3tWVhzwmQYAZbVDiLLFyIsUe6HXWlsKxktlm5TobQ" />
<meta name="google-site-verification" content="RaVhoTbxODwRYurXY8-fKrNs8opEorTM6pUpeuANydw" />
<meta name="google-site-verification" content="q7_uKi1L77WH3GSLFyu969j5y9vOkFxSFNOUIMVlPsQ" />
<meta name="p:domain_verify" content="r1FCcgBm7HkfCnHdIinJ9iRsuQBbivXn"/>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<style>
.product-info-main .stock {display:none;}
.item.-available {display:none;}
@media screen and (min-width: 1799px) {
    .catalog-product-view .product-overview .tabpanel {
        display: flex !important;
    }
}

</style>

<script type=\'text/javascript\'>
window.__lo_site_id = 157223;

	(function() {
		var wa = document.createElement(\'script\'); wa.type = \'text/javascript\'; wa.async = true;
		wa.src = \'https://d10lpsik1i8c69.cloudfront.net/w.js\';
		var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(wa, s);
	  })();
	</script>

<script async src="https://js.testfreaks.com/onpage/digidirect.com.au/head.js"></script>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':
new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,\'script\',\'dataLayer\',\'GTM-M7HJ7PQ\');</script>
<!-- End Google Tag Manager -->
',
                        'default_title' => 'digiDirect Cameras & Imaging',
                        'default_keywords' => 'Cameras, Photos, Lenses',
                    ],
                    'footer' => [
                        'absolute_footer' => '<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M7HJ7PQ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<script>window.addPAC = function (c, e) { document.cookie = \'PAC=\' + c + \';\' + e + \';\' }</script>
<script src="https://cdn.particularaudience.com/js/ddr/t.js" defer></script>

<!--sdouS9Zi7ZtXq3po6XjHWnPDLloXRvTL-->


<!-- Studio19 Rental Widget -->
<script type="text/javascript">
(function() { var c = document.querySelector("link[rel=\'canonical\']");
var url = encodeURIComponent((c !== null ? c.href : null) || document.URL);
var tag = document.createElement(\'script\'); tag.type=\'text/javascript\'; tag.async=true;
tag.src = \'https://secure\'+\'.studio19.com.au/script/digidirect2.js?url=\'+url;
document.body.appendChild(tag);})();
</script>

',
                        'copyright' => '© digiDirect 2024. All rights reserved.',
                    ],
                    'watermark' => [
                        'swatch_image_size' => null,
                        'swatch_image_imageOpacity' => null,
                    ],
                    'email' => [
                        'header_template' => '15',
                        'footer_template' => '19',
                    ],
                ],
                'klevu_search' => [
                    'general' => [
                        'js_api_key' => 'klevu-15598172717559511',
                        'rest_api_key' => 'a2xldnUtMTU1OTgxNzI3MTc1NTk1MTE6S2xldnUtNTl2Y2dnNW1vdg==',
                        'hostname' => 'box.klevu.com',
                        'cloud_search_url' => 'uscs13.ksearchnet.com',
                        'analytics_url' => 'stats.ksearchnet.com',
                        'js_url' => 'js.klevu.com',
                        'rest_hostname' => 'rest.klevu.com',
                        'tiers_url' => 'tiers.klevu.com',
                        'rating_flag' => '1',
                        'upgrade_features' => 'a:6:{s:11:"upgrade_url";s:90:"https://box.klevu.com/analytics/km?cmd=loginPg&action=upgrade&email=haig@digidirect.com.au";s:15:"upgrade_message";s:74:"If you want to enable this feature, please contact us at support@klevu.com";s:23:"preserve_layout_message";s:131:"If you want to use <b>Native search result page (which preserves your theme layout)</b> then please contact us at support@klevu.com";s:7:"enabled";s:104:"enabledaddtocartfront,boosting,enabledpopulartermfront,enabledcmsfront,preserves_layout,allowgroupprices";s:8:"disabled";s:25:"enabledcategorynavigation";s:19:"user_plan_for_store";s:10:"Enterprise";}',
                    ],
                    'secureurl_setting' => [
                        'enabled' => '1',
                    ],
                    'attributes' => [
                        'automatic' => 'a:45:{i:0;a:2:{s:15:"klevu_attribute";s:4:"name";s:17:"magento_attribute";s:4:"name";}i:1;a:2:{s:15:"klevu_attribute";s:3:"sku";s:17:"magento_attribute";s:3:"sku";}i:2;a:2:{s:15:"klevu_attribute";s:5:"image";s:17:"magento_attribute";s:5:"image";}i:3;a:2:{s:15:"klevu_attribute";s:11:"small_image";s:17:"magento_attribute";s:11:"small_image";}i:4;a:2:{s:15:"klevu_attribute";s:13:"media_gallery";s:17:"magento_attribute";s:13:"media_gallery";}i:5;a:2:{s:15:"klevu_attribute";s:6:"status";s:17:"magento_attribute";s:6:"status";}i:6;a:2:{s:15:"klevu_attribute";s:4:"desc";s:17:"magento_attribute";s:11:"description";}i:7;a:2:{s:15:"klevu_attribute";s:9:"shortDesc";s:17:"magento_attribute";s:17:"short_description";}i:8;a:2:{s:15:"klevu_attribute";s:5:"price";s:17:"magento_attribute";s:5:"price";}i:9;a:2:{s:15:"klevu_attribute";s:9:"salePrice";s:17:"magento_attribute";s:5:"price";}i:10;a:2:{s:15:"klevu_attribute";s:9:"salePrice";s:17:"magento_attribute";s:12:"tax_class_id";}i:11;a:2:{s:15:"klevu_attribute";s:6:"weight";s:17:"magento_attribute";s:6:"weight";}i:12;a:2:{s:15:"klevu_attribute";s:6:"rating";s:17:"magento_attribute";s:6:"rating";}i:13;a:2:{s:15:"klevu_attribute";s:12:"rating_count";s:17:"magento_attribute";s:12:"review_count";}i:14;a:2:{s:15:"klevu_attribute";s:13:"special_price";s:17:"magento_attribute";s:13:"special_price";}i:15;a:2:{s:15:"klevu_attribute";s:17:"special_from_date";s:17:"magento_attribute";s:17:"special_from_date";}i:16;a:2:{s:15:"klevu_attribute";s:15:"special_to_date";s:17:"magento_attribute";s:15:"special_to_date";}i:17;a:2:{s:15:"klevu_attribute";s:10:"visibility";s:17:"magento_attribute";s:10:"visibility";}i:18;a:2:{s:15:"klevu_attribute";s:9:"dateAdded";s:17:"magento_attribute";s:10:"created_at";}i:19;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:14:"accessory_type";}i:20;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:15:"adapter_type_pc";}i:21;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:29:"audio_interface_functionality";}i:22;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:11:"batterytype";}i:23;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:13:"battery_model";}i:24;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:19:"built_in_microphone";}i:25;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:17:"built_in_speakers";}i:26;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:9:"clearance";}i:27;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:9:"condition";}i:28;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:11:"filter_size";}i:29;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:9:"hot_deals";}i:30;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:13:"lens_coverage";}i:31;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:10:"lens_gears";}i:32;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:18:"marketplacer_brand";}i:33;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:19:"marketplacer_seller";}i:34;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:10:"media_type";}i:35;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:16:"media_type_audio";}i:36;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:26:"media_type_audio_recorders";}i:37;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:16:"microphone_input";}i:38;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:5:"mount";}i:39;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:9:"pre_order";}i:40;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:20:"qantas_special_offer";}i:41;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:11:"sensor_size";}i:42;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:9:"time_code";}i:43;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:7:"t_stops";}i:44;a:2:{s:15:"klevu_attribute";s:5:"other";s:17:"magento_attribute";s:16:"video_resolution";}}',
                        'other' => 'apn,clearance,qantas_special_offer,qff_bonus_points',
                        'boosting' => null,
                    ],
                    'searchlanding' => [
                        'klevu_search_relevance' => '0',
                    ],
                    'add_to_cart' => [
                        'enabledaddtocartfront' => '1',
                    ],
                    'cmscontent' => [
                        'enabledcmsfront' => '0',
                        'excludecms_pages' => 'a:0:{}',
                    ],
                    'image_setting' => [
                        'enabled' => '0',
                    ],
                    'product_sync' => [
                        'sync_options' => '2',
                    ],
                ],
                'tax' => [
                    'display' => [
                        'typeinsearch' => null,
                    ],
                ],
            ],
        ],
        'websites' => [
            'admin' => [
                'web' => [
                    'routers' => [
                        'frontend' => [
                            'disabled' => 'true',
                        ],
                    ],
                    'default' => [
                        'no_route' => 'admin/noroute/index',
                    ],
                ],
            ],
            'digi_website_au' => [
                'payment' => [
                    'authorizenet_acceptjs' => [
                        'cctypes' => 'AE,VI,MC,DI,JCB,DN',
                        'order_status' => 'processing',
                        'payment_action' => 'authorize',
                        'currency' => 'USD',
                    ],
                ],
                'general' => [
                    'locale' => [
                        'weight_unit' => 'kgs',
                        'firstday' => '1',
                    ],
                    'store_information' => [
                        'country_id' => 'AU',
                        'region_id' => '581',
                        'postcode' => '2022',
                    ],
                ],
                'design' => [
                    'watermark' => [
                        'swatch_image_size' => null,
                        'swatch_image_imageOpacity' => null,
                    ],
                    'search_engine_robots' => [
                        'custom_instructions' => 'User-agent: *
Crawl-delay: 10

Disallow: /index.php/
Disallow: /*?
Disallow: /checkout/
Disallow: /app/
Disallow: /lib/
Disallow: /*.php$
Disallow: /pkginfo/
Disallow: /report/
Disallow: /var/
Disallow: /catalog/
Disallow: /customer/
Disallow: /sendfriend/
Disallow: /review/
Disallow: /customer/account/login/*?
Disallow: /*SID=

Disallow: /page_cache/
Disallow: /*?*product_list_mode=
Disallow: /*?*product_list_order=
Disallow: /*?*product_list_limit=
Disallow: /*?*product_list_dir=
Disallow: /*?*clearFilters=1
Disallow: /*?*q=
Disallow: /*?*cat=
Disallow: /*?*sort=
Disallow: /*?*filter=
Disallow: /*?*brand=
Disallow: /*?*color=
Disallow: /*?*price=
Disallow: /*?*find=


#Block bot section

# go away DotBot
User-agent: Dotbot
Disallow: /

# go away Exabot
User-agent: Exabot
Disallow: /

# go away Gigabot
User-agent: Gigabot
Disallow: /

# go away ICCrawler
User-agent: ICCrawler
Disallow: /

# go away Snappy
User-agent: Snappy
Disallow: /

# go away Yandex
User-agent: Yandex
Disallow: /

# go away yandexbot
User-agent: yandexbot
Disallow: /

# go away Yeti
User-agent: Yeti
Disallow: /

# go away Mb2345Browser
User-agent: Mb2345Browser
Disallow: /

# go away QQBrowser
User-agent: QQBrowser
Disallow: /

# go away LieBaoFast
User-agent: LieBaoFast
Disallow: /

# go away MicroMessenger
User-agent: MicroMessenger
Disallow: /

# go away Kinza
User-agent: Kinza
Disallow: /

# go away slurp
User-agent: slurp
Disallow: /

# go away TheWorld
User-agent: TheWorld
Disallow: /

# go away YoudaoBot
User-agent: YoudaoBot
Disallow: /

User-agent: Googlebot
Disallow: /*?*page_id
Disallow: /catalogsearch/
Disallow: /*SID=
Disallow: /*product_list_dir
Disallow: /*price=
Disallow: /*option=

User-agent: Googlebot-image
Disallow:

',
                    ],
                ],
                'carriers' => [
                    'tablerate' => [
                        'import' => '1591075107,,,,4,0',
                    ],
                ],
                'connector_automation' => [
                    'visitor_automation' => [
                        'subscriber_automation' => '13390',
                    ],
                ],
            ],
            'marketplaces' => [
                'catalog' => [
                    'placeholder' => [
                        'image_placeholder' => 'default/ICS_placeHolder.fw.png',
                        'small_image_placeholder' => 'default/ICS_placeHolder.fw_1.png',
                        'thumbnail_placeholder' => 'default/ICS_placeHolder.fw_2.png',
                    ],
                ],
                'design' => [
                    'pagination' => [
                        'pagination_frame_skip' => null,
                        'anchor_text_for_previous' => null,
                        'anchor_text_for_next' => null,
                    ],
                    'head' => [
                        'title_prefix' => null,
                        'title_suffix' => null,
                        'default_description' => null,
                    ],
                    'header' => [
                        'logo_width' => null,
                        'logo_height' => null,
                        'logo_alt' => null,
                    ],
                    'footer' => [
                        'absolute_footer' => null,
                    ],
                    'watermark' => [
                        'image_size' => null,
                        'image_imageOpacity' => null,
                        'small_image_size' => null,
                        'small_image_imageOpacity' => null,
                        'thumbnail_size' => null,
                        'thumbnail_imageOpacity' => null,
                        'swatch_image_size' => null,
                        'swatch_image_imageOpacity' => null,
                    ],
                    'email' => [
                        'logo_alt' => null,
                        'logo_width' => null,
                        'logo_height' => null,
                    ],
                ],
            ],
            'retail_stores' => [
                'system' => [
                    'smtp' => [
                        'disable' => '1',
                    ],
                ],
            ],
        ],
        'website' => [
            'admin' => [
                'connector_configuration' => [
                    'transactional_data' => [
                        'order_statuses' => 'canceled,closed,complete,fraud,holded,payment_review,paypal_canceled_reversal,paypal_reversed,pending,pending_payment,pending_paypal,processing',
                    ],
                    'catalog_sync' => [
                        'catalog_type' => 'simple,virtual,bundle,downloadable,giftcard,configurable,grouped',
                        'catalog_visibility' => '1,2,3,4',
                    ],
                ],
            ],
        ],
        'store' => [
            'digi_store_view_au' => [
                'zendesk' => [
                    'web_widget' => [
                        'saved_widget_snippet' => '<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=88672bb4-20de-4954-9aae-cec47a62429f"> </script>',
                    ],
                ],
            ],
            'retail_store_view' => [
                'zendesk' => [
                    'web_widget' => [
                        'saved_widget_snippet' => '<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=88672bb4-20de-4954-9aae-cec47a62429f"> </script>',
                    ],
                ],
            ],
            'digidirectmarketplaces' => [
                'zendesk' => [
                    'web_widget' => [
                        'saved_widget_snippet' => '<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=88672bb4-20de-4954-9aae-cec47a62429f"> </script>',
                    ],
                ],
            ],
            'MELB' => [
                'zendesk' => [
                    'web_widget' => [
                        'saved_widget_snippet' => '<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=88672bb4-20de-4954-9aae-cec47a62429f"> </script>',
                    ],
                ],
            ],
            'SYDN' => [
                'zendesk' => [
                    'web_widget' => [
                        'saved_widget_snippet' => '<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=88672bb4-20de-4954-9aae-cec47a62429f"> </script>',
                    ],
                ],
            ],
            'CANN' => [
                'zendesk' => [
                    'web_widget' => [
                        'saved_widget_snippet' => '<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=88672bb4-20de-4954-9aae-cec47a62429f"> </script>',
                    ],
                ],
            ],
            'BOND' => [
                'zendesk' => [
                    'web_widget' => [
                        'saved_widget_snippet' => '<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=88672bb4-20de-4954-9aae-cec47a62429f"> </script>',
                    ],
                ],
            ],
            'BRISB' => [
                'zendesk' => [
                    'web_widget' => [
                        'saved_widget_snippet' => '<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=88672bb4-20de-4954-9aae-cec47a62429f"> </script>',
                    ],
                ],
            ],
            'MIRA' => [
                'zendesk' => [
                    'web_widget' => [
                        'saved_widget_snippet' => '<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=88672bb4-20de-4954-9aae-cec47a62429f"> </script>',
                    ],
                ],
            ],
            'PARR' => [
                'zendesk' => [
                    'web_widget' => [
                        'saved_widget_snippet' => '<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=88672bb4-20de-4954-9aae-cec47a62429f"> </script>',
                    ],
                ],
            ],
        ],
    ],
    'modules' => [
        'Magento_AdminAnalytics' => 1,
        'Magento_Store' => 1,
        'Magento_AdminGwsConfigurableProduct' => 1,
        'Magento_AdminGwsStaging' => 1,
        'Magento_Directory' => 1,
        'Magento_AdobeIms' => 1,
        'Magento_AdobeImsApi' => 1,
        'Magento_AdobeStockAdminUi' => 1,
        'Magento_MediaGallery' => 1,
        'Magento_AdobeStockAssetApi' => 1,
        'Magento_AdobeStockClient' => 1,
        'Magento_AdobeStockClientApi' => 1,
        'Magento_AdobeStockImage' => 1,
        'Magento_Theme' => 1,
        'Magento_AdobeStockImageApi' => 1,
        'Magento_Eav' => 1,
        'Magento_Customer' => 1,
        'Magento_AdvancedPricingImportExport' => 1,
        'Magento_Rule' => 1,
        'Magento_AdminNotification' => 1,
        'Magento_Backend' => 1,
        'Magento_Amqp' => 1,
        'Magento_AmqpStore' => 1,
        'Magento_Config' => 1,
        'Magento_Indexer' => 1,
        'Magento_Authorization' => 1,
        'Magento_User' => 1,
        'Magento_Variable' => 1,
        'Magento_Cms' => 1,
        'Magento_AwsS3CustomerCustomAttributes' => 1,
        'Magento_GiftCardImportExport' => 1,
        'Magento_Catalog' => 1,
        'Magento_ImportExport' => 1,
        'Magento_GraphQl' => 1,
        'Magento_Backup' => 1,
        'Magento_CatalogRule' => 1,
        'Magento_Quote' => 1,
        'Magento_SalesSequence' => 1,
        'Magento_Payment' => 1,
        'Magento_Sales' => 1,
        'Magento_Bundle' => 1,
        'Magento_EavGraphQl' => 1,
        'Magento_BundleImportExport' => 1,
        'Magento_BundleImportExportStaging' => 1,
        'Magento_CatalogInventory' => 1,
        'Magento_CacheInvalidate' => 1,
        'Magento_Checkout' => 1,
        'Magento_CardinalCommerce' => 1,
        'Magento_AdvancedCatalog' => 1,
        'Magento_Security' => 1,
        'Magento_CmsGraphQl' => 1,
        'Magento_Search' => 1,
        'Magento_CatalogSearch' => 1,
        'Magento_SalesArchive' => 1,
        'Magento_CatalogImportExport' => 1,
        'Magento_CatalogImportExportStaging' => 1,
        'Magento_StoreGraphQl' => 1,
        'Magento_CatalogInventoryGraphQl' => 1,
        'Magento_Ui' => 1,
        'Magento_CatalogPageBuilderAnalytics' => 1,
        'Magento_CatalogPageBuilderAnalyticsStaging' => 1,
        'Magento_CatalogUrlRewrite' => 1,
        'Magento_CatalogGraphQl' => 1,
        'Magento_CustomerCustomAttributes' => 1,
        'Magento_Msrp' => 1,
        'Magento_CatalogRuleGraphQl' => 1,
        'Magento_SalesRule' => 1,
        'Magento_Captcha' => 1,
        'Magento_Downloadable' => 1,
        'Magento_Staging' => 1,
        'Magento_GiftCard' => 1,
        'Magento_Wishlist' => 1,
        'Magento_UrlRewriteGraphQl' => 1,
        'Magento_MediaStorage' => 1,
        'Magento_Robots' => 1,
        'Magento_ConfigurableProduct' => 1,
        'Magento_CheckoutAddressSearch' => 1,
        'Magento_GiftRegistry' => 1,
        'Magento_CheckoutAgreements' => 1,
        'Magento_CheckoutAgreementsGraphQl' => 1,
        'Magento_CheckoutStaging' => 1,
        'Magento_CloudComponents' => 1,
        'Magento_MediaGalleryUi' => 1,
        'Magento_CatalogCmsGraphQl' => 1,
        'Magento_CmsPageBuilderAnalytics' => 1,
        'Magento_CmsPageBuilderAnalyticsStaging' => 1,
        'Magento_Widget' => 1,
        'Magento_CmsUrlRewrite' => 1,
        'Magento_CmsUrlRewriteGraphQl' => 1,
        'Magento_CompareListGraphQl' => 1,
        'Magento_Integration' => 1,
        'Magento_ConfigurableImportExport' => 1,
        'Magento_CatalogRuleConfigurable' => 1,
        'Magento_QuoteGraphQl' => 1,
        'Magento_ConfigurableProductSales' => 1,
        'Magento_PageCache' => 1,
        'Magento_Contact' => 1,
        'Magento_Cookie' => 1,
        'Magento_Cron' => 1,
        'Magento_Csp' => 0,
        'Magento_CurrencySymbol' => 1,
        'Magento_CustomAttributeManagement' => 1,
        'Magento_AdvancedCheckout' => 1,
        'Magento_Analytics' => 1,
        'Magento_CustomerBalance' => 1,
        'Magento_CustomerBalanceGraphQl' => 1,
        'Magento_CustomerSegment' => 1,
        'Magento_DownloadableGraphQl' => 1,
        'Magento_CustomerFinance' => 1,
        'Magento_CustomerGraphQl' => 1,
        'Magento_CustomerImportExport' => 1,
        'Magento_CatalogWidget' => 1,
        'Magento_DeferredTotalCalculating' => 1,
        'Magento_Deploy' => 1,
        'Magento_Developer' => 1,
        'Magento_Dhl' => 1,
        'Magento_AdvancedSearch' => 1,
        'Magento_DirectoryGraphQl' => 1,
        'Magento_ProductAlert' => 1,
        'Magento_CustomerDownloadableGraphQl' => 1,
        'Magento_DownloadableImportExport' => 1,
        'Magento_TargetRule' => 1,
        'Magento_AdvancedRule' => 1,
        'Magento_BundleGraphQl' => 1,
        'Magento_Elasticsearch' => 1,
        'Magento_Elasticsearch6' => 1,
        'Magento_Elasticsearch7' => 1,
        'Magento_WebsiteRestriction' => 1,
        'Magento_ElasticsearchCatalogPermissionsGraphQl' => 1,
        'Magento_Email' => 1,
        'Magento_EncryptionKey' => 1,
        'Magento_Enterprise' => 1,
        'Magento_Fedex' => 1,
        'Magento_Tax' => 1,
        'Magento_GiftCardAccount' => 1,
        'Magento_GiftCardAccountGraphQl' => 1,
        'Magento_WishlistGraphQl' => 1,
        'Magento_Sitemap' => 1,
        'Magento_CatalogEvent' => 1,
        'Magento_GiftMessage' => 1,
        'Magento_GiftMessageGraphQl' => 1,
        'Magento_GiftMessageStaging' => 1,
        'Magento_VisualMerchandiser' => 1,
        'Magento_GiftRegistryGraphQl' => 1,
        'Magento_GiftWrapping' => 1,
        'Magento_GiftWrappingGraphQl' => 1,
        'Magento_GiftWrappingStaging' => 1,
        'Magento_GoogleAdwords' => 1,
        'Magento_GoogleAnalytics' => 1,
        'Magento_GoogleOptimizer' => 1,
        'Magento_GoogleOptimizerStaging' => 1,
        'Magento_GoogleShoppingAds' => 1,
        'Magento_VersionsCms' => 1,
        'Magento_CatalogCustomerGraphQl' => 1,
        'Magento_GraphQlCache' => 1,
        'Magento_GroupedProduct' => 1,
        'Magento_GroupedImportExport' => 1,
        'Magento_GroupedCatalogInventory' => 1,
        'Magento_GroupedProductGraphQl' => 1,
        'Magento_CatalogPermissions' => 1,
        'Magento_RemoteStorage' => 1,
        'Magento_Weee' => 1,
        'Magento_InstantPurchase' => 1,
        'Magento_CatalogAnalytics' => 1,
        'Magento_Inventory' => 1,
        'Magento_InventoryAdminUi' => 1,
        'Magento_InventoryAdvancedCheckout' => 1,
        'Magento_InventoryApi' => 1,
        'Magento_InventoryBundleImportExport' => 1,
        'Magento_InventoryBundleProduct' => 1,
        'Magento_InventoryBundleProductAdminUi' => 1,
        'Magento_InventoryBundleProductIndexer' => 1,
        'Magento_InventoryCatalog' => 1,
        'Magento_InventorySales' => 1,
        'Magento_InventoryCatalogAdminUi' => 1,
        'Magento_InventoryCatalogApi' => 1,
        'Magento_InventoryCatalogFrontendUi' => 1,
        'Magento_InventoryCatalogSearch' => 1,
        'Magento_InventoryCatalogSearchBundleProduct' => 1,
        'Magento_InventoryCatalogSearchConfigurableProduct' => 1,
        'Magento_ConfigurableProductGraphQl' => 1,
        'Magento_InventoryConfigurableProduct' => 1,
        'Magento_InventoryConfigurableProductFrontendUi' => 1,
        'Magento_InventoryConfigurableProductIndexer' => 1,
        'Magento_InventoryConfiguration' => 1,
        'Magento_InventoryConfigurationApi' => 1,
        'Magento_InventoryDistanceBasedSourceSelection' => 1,
        'Magento_InventoryDistanceBasedSourceSelectionAdminUi' => 1,
        'Magento_InventoryDistanceBasedSourceSelectionApi' => 1,
        'Magento_InventoryElasticsearch' => 1,
        'Magento_InventoryExportStockApi' => 1,
        'Magento_InventoryIndexer' => 1,
        'Magento_InventorySalesApi' => 1,
        'Magento_InventoryGroupedProduct' => 1,
        'Magento_InventoryGroupedProductAdminUi' => 1,
        'Magento_InventoryGroupedProductIndexer' => 1,
        'Magento_InventoryImportExport' => 1,
        'Magento_InventoryInStorePickupApi' => 1,
        'Magento_InventoryInStorePickupAdminUi' => 1,
        'Magento_InventorySourceSelectionApi' => 1,
        'Magento_InventoryInStorePickup' => 1,
        'Magento_InventoryInStorePickupGraphQl' => 1,
        'Magento_Shipping' => 1,
        'Magento_InventoryInStorePickupShippingApi' => 1,
        'Magento_InventoryInStorePickupQuoteGraphQl' => 1,
        'Magento_InventoryInStorePickupSales' => 1,
        'Magento_InventoryInStorePickupSalesApi' => 1,
        'Magento_InventoryInStorePickupQuote' => 1,
        'Magento_InventoryInStorePickupShipping' => 1,
        'Magento_InventoryInStorePickupShippingAdminUi' => 1,
        'Magento_Multishipping' => 1,
        'Magento_Webapi' => 1,
        'Magento_InventoryCache' => 1,
        'Magento_InventoryLowQuantityNotification' => 1,
        'Magento_Reports' => 1,
        'Magento_InventoryLowQuantityNotificationApi' => 1,
        'Magento_InventoryMultiDimensionalIndexerApi' => 1,
        'Magento_InventoryProductAlert' => 1,
        'Magento_InventoryQuoteGraphQl' => 1,
        'Magento_InventoryRequisitionList' => 1,
        'Magento_InventoryReservations' => 1,
        'Magento_InventoryReservationCli' => 1,
        'Magento_InventoryReservationsApi' => 1,
        'Magento_InventoryExportStock' => 1,
        'Magento_InventorySalesAdminUi' => 1,
        'Magento_InventoryGraphQl' => 1,
        'Magento_InventorySalesFrontendUi' => 1,
        'Magento_InventorySetupFixtureGenerator' => 1,
        'Magento_InventoryShipping' => 1,
        'Magento_InventoryShippingAdminUi' => 1,
        'Magento_InventorySourceDeductionApi' => 1,
        'Magento_InventorySourceSelection' => 1,
        'Magento_InventoryInStorePickupFrontend' => 1,
        'Magento_InventorySwatchesFrontendUi' => 1,
        'Magento_InventoryVisualMerchandiser' => 1,
        'Magento_InventoryWishlist' => 1,
        'Magento_Invitation' => 1,
        'Magento_JwtFrameworkAdapter' => 1,
        'Magento_JwtUserToken' => 1,
        'Magento_LayeredNavigation' => 1,
        'Magento_LayeredNavigationStaging' => 1,
        'Magento_Logging' => 1,
        'Magento_LoginAsCustomer' => 1,
        'Magento_LoginAsCustomerAdminUi' => 1,
        'Magento_LoginAsCustomerApi' => 1,
        'Magento_LoginAsCustomerAssistance' => 1,
        'Magento_LoginAsCustomerFrontendUi' => 1,
        'Magento_LoginAsCustomerGraphQl' => 1,
        'Magento_LoginAsCustomerLog' => 1,
        'Magento_LoginAsCustomerLogging' => 1,
        'Magento_LoginAsCustomerPageCache' => 1,
        'Magento_LoginAsCustomerQuote' => 1,
        'Magento_LoginAsCustomerSales' => 1,
        'Magento_LoginAsCustomerWebsiteRestriction' => 1,
        'Magento_Marketplace' => 1,
        'Magento_MediaContent' => 1,
        'Magento_MediaContentApi' => 1,
        'Magento_MediaContentCatalog' => 1,
        'Magento_MediaContentCatalogStaging' => 1,
        'Magento_MediaContentCms' => 1,
        'Magento_MediaContentSynchronization' => 1,
        'Magento_MediaContentSynchronizationApi' => 1,
        'Magento_MediaContentSynchronizationCatalog' => 1,
        'Magento_MediaContentSynchronizationCms' => 1,
        'Magento_AdobeStockAsset' => 1,
        'Magento_MediaGalleryApi' => 1,
        'Magento_MediaGalleryCatalog' => 1,
        'Magento_MediaGalleryCatalogIntegration' => 1,
        'Magento_MediaGalleryCatalogUi' => 1,
        'Magento_MediaGalleryCmsUi' => 1,
        'Magento_MediaGalleryIntegration' => 1,
        'Magento_MediaGalleryMetadata' => 1,
        'Magento_MediaGalleryMetadataApi' => 1,
        'Magento_MediaGalleryRenditions' => 1,
        'Magento_MediaGalleryRenditionsApi' => 1,
        'Magento_MediaGallerySynchronization' => 1,
        'Magento_MediaGallerySynchronizationApi' => 1,
        'Magento_MediaGallerySynchronizationMetadata' => 1,
        'Magento_AdobeStockImageAdminUi' => 1,
        'Magento_MediaGalleryUiApi' => 1,
        'Magento_AwsS3' => 1,
        'Magento_MessageQueue' => 1,
        'Magento_CatalogStaging' => 1,
        'Magento_MsrpConfigurableProduct' => 1,
        'Magento_MsrpGroupedProduct' => 1,
        'Magento_MsrpStaging' => 1,
        'Magento_MultipleWishlist' => 1,
        'Magento_SalesGraphQl' => 1,
        'Magento_InventoryInStorePickupMultishipping' => 1,
        'Magento_MysqlMq' => 1,
        'Magento_NewRelicReporting' => 1,
        'Magento_Newsletter' => 1,
        'Magento_NewsletterGraphQl' => 1,
        'Magento_OfflinePayments' => 1,
        'Magento_OfflineShipping' => 1,
        'Magento_Banner' => 1,
        'Magento_PageBuilder' => 1,
        'Magento_AdminGws' => 1,
        'Magento_PageBuilderAnalytics' => 0,
        'Magento_CatalogStagingPageBuilder' => 1,
        'Magento_PageBuilderAdminGwsAdminUi' => 1,
        'Magento_PaymentGraphQl' => 1,
        'Magento_PaymentStaging' => 1,
        'Magento_Vault' => 1,
        'Magento_Paypal' => 1,
        'Magento_PaypalGraphQl' => 1,
        'Magento_PaypalOnBoarding' => 1,
        'Magento_Persistent' => 1,
        'Magento_PersistentHistory' => 1,
        'Magento_PricePermissions' => 1,
        'Magento_DownloadableStaging' => 1,
        'Magento_ProductVideo' => 1,
        'Magento_ProductVideoStaging' => 1,
        'Magento_PromotionPermissions' => 1,
        'Magento_BannerGraphQl' => 1,
        'Magento_QuoteAnalytics' => 1,
        'Magento_QuoteBundleOptions' => 1,
        'Magento_QuoteConfigurableOptions' => 1,
        'Magento_QuoteDownloadableLinks' => 1,
        'Magento_QuoteGiftCardOptions' => 1,
        'Magento_AsyncOrder' => 1,
        'Magento_QuoteStaging' => 1,
        'Magento_ReCaptchaAdminUi' => 1,
        'Magento_ReCaptchaCheckout' => 1,
        'Magento_ReCaptchaCheckoutSalesRule' => 1,
        'Magento_ReCaptchaContact' => 1,
        'Magento_ReCaptchaCustomer' => 1,
        'Magento_ReCaptchaFrontendUi' => 1,
        'Magento_ReCaptchaMigration' => 1,
        'Magento_ReCaptchaNewsletter' => 1,
        'Magento_ReCaptchaPaypal' => 1,
        'Magento_ReCaptchaReview' => 1,
        'Magento_ReCaptchaSendFriend' => 1,
        'Magento_ReCaptchaStorePickup' => 1,
        'Magento_ReCaptchaUi' => 1,
        'Magento_ReCaptchaUser' => 1,
        'Magento_ReCaptchaValidation' => 1,
        'Magento_ReCaptchaValidationApi' => 1,
        'Magento_ReCaptchaVersion2Checkbox' => 1,
        'Magento_ReCaptchaVersion2Invisible' => 1,
        'Magento_ReCaptchaVersion3Invisible' => 1,
        'Magento_ReCaptchaWebapiApi' => 1,
        'Magento_ReCaptchaWebapiGraphQl' => 1,
        'Magento_ReCaptchaWebapiRest' => 1,
        'Magento_ReCaptchaWebapiUi' => 1,
        'Magento_RelatedProductGraphQl' => 1,
        'Magento_ReleaseNotification' => 1,
        'Magento_Reminder' => 1,
        'Magento_AwsS3GiftCardImportExport' => 1,
        'Magento_RemoteStorageCommerce' => 1,
        'Magento_InventoryLowQuantityNotificationAdminUi' => 1,
        'Magento_RequireJs' => 1,
        'Magento_ResourceConnections' => 1,
        'Magento_Review' => 1,
        'Magento_ReviewAnalytics' => 1,
        'Magento_ReviewGraphQl' => 1,
        'Magento_ReviewStaging' => 1,
        'Magento_Reward' => 0,
        'Magento_RewardGraphQl' => 0,
        'Magento_AdvancedSalesRule' => 1,
        'Magento_Rma' => 1,
        'Magento_RmaGraphQl' => 0,
        'Magento_RmaStaging' => 1,
        'Magento_ScheduledImportExport' => 1,
        'Magento_Rss' => 1,
        'Magento_SalesRuleStaging' => 1,
        'Magento_BannerPageBuilderAnalytics' => 1,
        'Magento_SalesAnalytics' => 1,
        'Magento_AsyncOrderGraphQl' => 1,
        'Magento_MultipleWishlistGraphQl' => 1,
        'Magento_SalesInventory' => 1,
        'Magento_CatalogRuleStaging' => 1,
        'Magento_RewardStaging' => 0,
        'Magento_BannerCustomerSegment' => 1,
        'Magento_UrlRewrite' => 1,
        'Magento_ScalableCheckout' => 1,
        'Magento_ScalableInventory' => 1,
        'Magento_ScalableOms' => 1,
        'Magento_AwsS3ScheduledImportExport' => 1,
        'Magento_InventoryConfigurableProductAdminUi' => 1,
        'Magento_SearchStaging' => 1,
        'Magento_CustomerAnalytics' => 1,
        'Magento_Securitytxt' => 1,
        'Magento_SendFriend' => 1,
        'Magento_SendFriendGraphQl' => 1,
        'Magento_InventoryInStorePickupSalesAdminUi' => 1,
        'Magento_AwsS3PageBuilder' => 1,
        'Magento_StagingGraphQl' => 1,
        'Magento_CatalogStagingGraphQl' => 1,
        'Magento_StagingPageBuilder' => 0,
        'Magento_GoogleTagManager' => 1,
        'Magento_CatalogPermissionsGraphQl' => 1,
        'Magento_Support' => 1,
        'Magento_Swagger' => 1,
        'Magento_SwaggerWebapi' => 1,
        'Magento_SwaggerWebapiAsync' => 1,
        'Magento_Swat' => 1,
        'Magento_Swatches' => 1,
        'Magento_SwatchesGraphQl' => 1,
        'Magento_SwatchesLayeredNavigation' => 1,
        'Magento_CatalogInventoryStaging' => 1,
        'Magento_TargetRuleGraphQl' => 1,
        'Magento_GiftCardStaging' => 1,
        'Magento_TaxGraphQl' => 1,
        'Magento_TaxImportExport' => 1,
        'Magento_CatalogUrlRewriteStaging' => 1,
        'Magento_ThemeGraphQl' => 1,
        'Magento_Translation' => 1,
        'Magento_TwoFactorAuth' => 0,
        'Magento_ElasticsearchCatalogPermissions' => 1,
        'Magento_Ups' => 1,
        'Magento_SampleData' => 0,
        'Magento_CatalogUrlRewriteGraphQl' => 1,
        'Magento_AsynchronousOperations' => 1,
        'Magento_Usps' => 1,
        'Magento_PageBuilderAdminAnalytics' => 1,
        'Magento_PaypalCaptcha' => 1,
        'Magento_VaultGraphQl' => 1,
        'Magento_Version' => 0,
        'Magento_CmsStaging' => 1,
        'Magento_VersionsCmsPageCache' => 1,
        'Magento_VersionsCmsUrlRewrite' => 1,
        'Magento_VersionsCmsUrlRewriteGraphQl' => 1,
        'Magento_GroupedProductStaging' => 1,
        'Magento_InventoryInStorePickupWebapiExtension' => 1,
        'Magento_WebapiAsync' => 1,
        'Magento_WebapiSecurity' => 1,
        'Magento_BundleStaging' => 1,
        'Magento_ConfigurableProductStaging' => 1,
        'Magento_WeeeGraphQl' => 1,
        'Magento_WeeeStaging' => 1,
        'Magento_BannerPageBuilder' => 1,
        'Magento_CheckoutAddressSearchGiftRegistry' => 1,
        'Magento_WishlistAnalytics' => 1,
        'Magento_WishlistGiftCard' => 1,
        'Magento_WishlistGiftCardGraphQl' => 1,
        'Magento_GiftCardGraphQl' => 1,
        'Amasty_Base' => 1,
        'Amasty_BannerSlider' => 1,
        'Amasty_BannerSliderGraphql' => 1,
        'Apptrian_ImageOptimizer' => 1,
        'Bss_DeleteOrder' => 1,
        'Bss_FacebookPixel' => 1,
        'Bss_PreOrder' => 1,
        'CommissionFactory_Tracking' => 1,
        'Digidirect_Utilities' => 1,
        'Digidirect_AbstractAttributes' => 1,
        'Digidirect_AbstractEntity' => 1,
        'Digidirect_AbstractGiftCard' => 1,
        'Digidirect_AI' => 1,
        'Digidirect_GoogleApi' => 1,
        'Digidirect_BestsellersProducts' => 1,
        'Digidirect_Blog' => 1,
        'Digidirect_InfiniteScroll' => 0,
        'Digidirect_Catalog' => 1,
        'Digidirect_LayeredNavigation' => 0,
        'Digidirect_Checkout' => 1,
        'Digidirect_CheckoutFields' => 1,
        'Digidirect_CollaborateForm' => 1,
        'Digidirect_Collect' => 1,
        'Digidirect_CollectAbstractEntity' => 1,
        'Digidirect_CollectAbstractEntityMsi' => 1,
        'Digidirect_Localization' => 1,
        'Digidirect_CustomGiftCardLog' => 1,
        'Digidirect_CustomInventoryLog' => 1,
        'Digidirect_CustomLog' => 1,
        'Digidirect_CustomOptions' => 1,
        'Digidirect_CustomOrderLog' => 1,
        'Digidirect_Customer' => 1,
        'Digidirect_DealsTest' => 1,
        'Digidirect_MSI' => 1,
        'Digidirect_DigiClubCompetition' => 1,
        'Digidirect_DigiClubMember' => 1,
        'Digidirect_DigiDeals' => 1,
        'Digidirect_DigiEvent' => 1,
        'Digidirect_DigiMarketSeller' => 1,
        'Digidirect_DigiSecondsForm' => 0,
        'Digidirect_DigiSecondsMenu' => 1,
        'Digidirect_ExtendedCartPriceRules' => 0,
        'Digidirect_ExtendedCartPriceRulesMSI' => 0,
        'Digidirect_ExtendedCatalogPriceRule' => 1,
        'Digidirect_ExtendedShippingRates' => 1,
        'Digidirect_ExtendedShippingRatesLocalization' => 1,
        'Digidirect_Feed' => 0,
        'Digidirect_FilterShipping' => 1,
        'Digidirect_FreeGift' => 1,
        'Digidirect_AddressVerification' => 1,
        'Digidirect_HotDealsProducts' => 1,
        'Digidirect_BlogInfiniteScroll' => 1,
        'Digidirect_InvoiceEmail' => 1,
        'Digidirect_InvoiceIncrementId' => 1,
        'Digidirect_CategoryFilter' => 0,
        'Digidirect_Locator' => 1,
        'Digidirect_Store' => 1,
        'Digidirect_Digi' => 1,
        'Digidirect_MarketplacerProducts' => 1,
        'Digidirect_MarketplacerProductsTest' => 1,
        'Digidirect_MyStoreWidget' => 1,
        'Digidirect_MyStoreWidgetCollect' => 1,
        'Digidirect_Newsletter' => 1,
        'Digidirect_NotFound' => 0,
        'Digidirect_OnSaleProducts' => 1,
        'Digidirect_Order' => 1,
        'Digidirect_PaSalesForceProductRecommendation' => 1,
        'Digidirect_ParticularAudienceAPI' => 1,
        'Digidirect_PricePermissions' => 1,
        'Digidirect_ProductOverlay' => 1,
        'Digidirect_Pronto' => 1,
        'Digidirect_Qantas' => 1,
        'Digidirect_QuickView' => 1,
        'Digidirect_ReadytoPickup' => 1,
        'Digidirect_RecommendedProducts' => 1,
        'Digidirect_RelatedProduct' => 1,
        'Digidirect_SEO' => 1,
        'Digidirect_Sales' => 1,
        'Digidirect_SellerShipping' => 1,
        'Digidirect_ShippingAvailabilityCheck' => 1,
        'Digidirect_ShippingAvailabilityCheckCollect' => 1,
        'Digidirect_ShopByBrandMenu' => 0,
        'Digidirect_ShopByCategory' => 1,
        'Digidirect_SingleCheckoutButton' => 1,
        'Digidirect_SocialSharing' => 1,
        'Digidirect_StoreLocator' => 1,
        'Digidirect_CollectStoreLocator' => 1,
        'Digidirect_StoreLocatorInfiniteScroll' => 0,
        'Digidirect_AbstractGiftCardLogger' => 1,
        'Digidirect_Vii' => 1,
        'Digidirect_YouMayAlsoLikeProducts' => 1,
        'EthanYehuda_CronjobManager' => 1,
        'Experius_WysiwygDownloads' => 1,
        'Fastly_Cdn' => 1,
        'Itoris_Core' => 1,
        'Itoris_PriceMatch' => 1,
        'LatitudeNew_Payment' => 1,
        'LiveChat_LiveChat' => 0,
        'Lof_Paymentfee' => 1,
        'Magento5_Latipay' => 1,
        'Mageplaza_Core' => 1,
        'Mageplaza_Shopbybrand' => 0,
        'Mageplaza_Webhook' => 1,
        'Magezon_Core' => 1,
        'Magezon_Builder' => 1,
        'Magezon_Newsletter' => 1,
        'Magezon_NinjaMenus' => 1,
        'Magezon_PageBuilder' => 1,
        'Magezon_PageBuilderIconBox' => 1,
        'Magezon_PageBuilderPageableContainer' => 1,
        'Magezon_PageBuilderPreview' => 1,
        'Magezon_UiBuilder' => 1,
        'Marketplacer_Base' => 1,
        'Marketplacer_BrandApi' => 1,
        'Marketplacer_Brand' => 1,
        'Marketplacer_SellerApi' => 1,
        'Marketplacer_Seller' => 1,
        'Marketplacer_Marketplacer' => 1,
        'Mirasvit_Core' => 1,
        'Mirasvit_Seo' => 1,
        'Mirasvit_SeoAi' => 1,
        'Mirasvit_SeoAudit' => 1,
        'Mirasvit_SeoAutolink' => 1,
        'Mirasvit_SeoContent' => 1,
        'Mirasvit_SeoFilter' => 1,
        'Mirasvit_SeoMarkup' => 1,
        'Mirasvit_SeoSitemap' => 1,
        'Mirasvit_SeoToolbar' => 1,
        'OlegKoval_RegenerateUrlRewrites' => 1,
        'Onsport_CustomListing' => 1,
        'OuterEdge_StructuredData' => 1,
        'PayPal_Braintree' => 1,
        'PayPal_BraintreeGraphQl' => 1,
        'Plumrocket_Base' => 1,
        'Plumrocket_ExtendedAdminUi' => 1,
        'Plumrocket_Newsletterpopup' => 1,
        'Studio19_Variants' => 1,
        'Swissup_Attributepages' => 1,
        'Swissup_Core' => 1,
        'Swissup_DeleteOrders' => 1,
        'Swissup_Marketplace' => 1,
        'Swissup_Swiper' => 1,
        'Temando_ShippingRemover' => 1,
        'WeSupply_Toolbox' => 0,
        'WebPanda_SalesProductImage' => 1,
        'Webkul_MyCustomCollection' => 1,
        'WeltPixel_Backend' => 1,
        'WeltPixel_GA4' => 1,
        'ZV_SeoCompatible' => 1,
        'Zendesk_Zendesk' => 1,
    ],
    'admin_user' => [
        'locale' => [
            'code' => [
                0 => 'en_AU',
            ],
        ],
    ],
    'themes' => [
        'frontend/Magento/blank' => [
            'parent_id' => null,
            'theme_path' => 'Magento/blank',
            'theme_title' => 'Magento Blank',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Magento/blank',
        ],
        'adminhtml/Magento/backend' => [
            'parent_id' => null,
            'theme_path' => 'Magento/backend',
            'theme_title' => 'Magento 2 backend',
            'is_featured' => '0',
            'area' => 'adminhtml',
            'type' => '0',
            'code' => 'Magento/backend',
        ],
        'frontend/Magento/luma' => [
            'parent_id' => 'Magento/blank',
            'theme_path' => 'Magento/luma',
            'theme_title' => 'Magento Luma',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Magento/luma',
        ],
        'frontend/Ewave/lego-base' => [
            'parent_id' => null,
            'theme_path' => 'Ewave/lego-base',
            'theme_title' => 'Lego Base theme',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '1',
            'code' => 'Ewave/lego-base',
        ],
        'frontend/Ewave/lego-default' => [
            'parent_id' => null,
            'theme_path' => 'Ewave/lego-default',
            'theme_title' => 'Lego Default theme',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '1',
            'code' => 'Ewave/lego-default',
        ],
        'frontend/Ewave/default' => [
            'parent_id' => null,
            'theme_path' => 'Ewave/default',
            'theme_title' => 'Default theme',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '1',
            'code' => 'Ewave/default',
        ],
        'frontend/Ewave/digi' => [
            'parent_id' => null,
            'theme_path' => 'Ewave/digi',
            'theme_title' => 'digiDirect theme',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '1',
            'code' => 'Ewave/digi',
        ],
        'frontend/Digidirect/digi' => [
            'parent_id' => 'Magento/blank',
            'theme_path' => 'Digidirect/digi',
            'theme_title' => 'Digidirect',
            'is_featured' => '0',
            'area' => 'frontend',
            'type' => '0',
            'code' => 'Digidirect/digi',
        ],
    ],
    'i18n' => [

    ],
];
