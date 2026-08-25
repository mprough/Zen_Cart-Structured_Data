<?php

declare(strict_types=1);

/**
 * @author: torvista
 * @link: https://github.com/torvista/Zen_Cart-Structured_Data
 * @license https://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version ZenExpert 20 Dec 2025
 * @version markbrittain 20 Dec 2025
 */
use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    protected string $configPageKey = 'configSuperData';

    protected string $configGroupTitle = 'SuperData';

    protected int $cgi;

    protected string $template_dir;

    public string $pluginKey = 'SuperData';

    public string $version = '3.0.1';



    /**
     * @return bool
     */
    protected function executeInstall(): bool
    {
        global $template_dir;
        $this->template_dir = $template_dir;

        if (!$this->purgeOldFiles()) {
            return false;
        }

        $this->cgi = $this->getOrCreateConfigGroupId($this->configGroupTitle, $this->configGroupTitle, null);

        // -----
        // If the plugin's configuration settings aren't present, add them now.
        //
        $this->executeInstallerSql(
            "INSERT IGNORE INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function)
             VALUES
                ('Enable SuperData generation', 'PLUGIN_SUPERDATA_ENABLE', 'true', 'Enable the SuperData plugin code', $this->cgi, 1, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
                ('Enable Schema markup', 'PLUGIN_SUPERDATA_SCHEMA_ENABLE', 'true', 'Show Schema markup?<br>Shows JSON-LD blocks for Organisation and Breadcrumbs on all pages, Product on product pages.', $this->cgi, 2, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
                ('Enable Facebook-Open Graph markup', 'PLUGIN_SUPERDATA_FOG_ENABLE', 'true', 'Show Facebook-Open Graph markup?<br>Shows Facebook og tags on all pages with additional product-specific tags on product pages.', $this->cgi, 3, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
                ('Enable Twitter Card markup', 'PLUGIN_SUPERDATA_TWITTER_CARD_ENABLE', 'true', 'Show Twitter Card markup?<br>Shows on all pages.', $this->cgi, 4, 'zen_cfg_select_option(array(\'true\', \'false\'),'),

                ('Facebook Application ID', 'PLUGIN_SUPERDATA_FOG_APPID', '', 'Enter your Facebook application ID (<a href=\"https://developers.facebook.com/docs/development/create-an-app\" target=\"_blank\">Get an application ID</a>).', $this->cgi, 5, NULL),
                ('Facebook Admin ID (optional)', 'PLUGIN_SUPERDATA_FOG_ADMINID', '', 'Enter the Admin ID(s) of the Facebook user(s) that administer your Facebook fan page separated by commas. <a href=\"https://business.facebook.com\" target=\"_blank\">Facebook Business</a>.', $this->cgi, 6, null),
                ('Facebook Page (optional)', 'PLUGIN_SUPERDATA_FOG_PAGE', '', 'Enter the full url/link to your facebook page e.g.: https://www.facebook.com/zencart/.', $this->cgi, 7, null),

                ('Organisation Type', 'PLUGIN_SUPERDATA_ORGANIZATION_TYPE', 'Organization', 'If you have a physical store and want to specify it, choose LocalBusiness instead of the generic Organization.', $this->cgi, 8, 'zen_cfg_select_option(array(\'Organization\', \'LocalBusiness\', \'OnlineBusiness\'),'),

                ('LocalBusiness Type', 'PLUGIN_SUPERDATA_LOCAL_BUSINESS_TYPE', 'Store', 'This option is used ONLY if Organization Type is set to LocalBusiness. The list is not complete because there is a large number of options. Choose the one that fits best, or choose Store as a generic option.', $this->cgi, 9, 'zen_cfg_select_option(array(\'Store\', \'ShoppingCenter\', \'BikeStore\', \'BookStore\', \'ClothingStore\', \'ComputerStore\', \'ConvenienceStore\', \'DepartmentStore\', \'ElectronicsStore\', \'Florist\', \'FurnitureStore\', \'GardenStore\', \'GroceryStore\', \'HardwareStore\', \'HobbyShop\', \'HomeGoodsStore\', \'JewelryStore\', \'LiquorStore\', \'MensClothingStore\', \'MobilePhoneStore\', \'MovieRentalStore\', \'MusicStore\', \'OfficeEquipmentStore\', \'OutletStore\', \'PawnShop\', \'PetStore\', \'ShoeStore\', \'SportingGoodsStore\', \'TireShop\', \'ToyStore\', \'WholesaleStore\'),'),

                ('Legal Name (Schema, optional)', 'PLUGIN_SUPERDATA_LEGAL_NAME', '', 'The registered company name.', $this->cgi, 15, null),
                ('Dun & Bradstreet DUNS number (Schema, optional)', 'PLUGIN_SUPERDATA_DUNS', '', 'The Dun & Bradstreet DUNS number for identifying an organization or a business person.', $this->cgi, 16, null),

                ('Name (Schema)', 'PLUGIN_SUPERDATA_LOCAL_BUSINESS_NAME', '', 'If you chose LocalBusiness, enter the name (this can be different than your Legal Name).', $this->cgi, 20, null),
                ('Short Description (Schema)', 'PLUGIN_SUPERDATA_DESCRIPTION', '', 'Enter a short description of your business.', $this->cgi, 21, null),
                ('Property Image (Schema)', 'PLUGIN_SUPERDATA_PROPERTY_IMAGE', '', 'If you selected LocalBusiness as your Business Type, you must include a photo of your storefront or building entrance to help customers find you. Best practice is to include 3 photos in different dimensions (1x1, 4x3, 16x9). Example: https://example.com/photos/1x1/photo.jpg, https://example.com/photos/4x3/photo.jpg, https://example.com/photos/16x9/photo.jpg', $this->cgi, 25, null),

                ('Logo (Schema)', 'PLUGIN_SUPERDATA_LOGO', '', 'Enter the complete url to your logo image.', $this->cgi, 30, null),

                ('Price Range (Schema)', 'PLUGIN_SUPERDATA_PRICE_RANGE', '', 'Use currency symbols to indicate your price range. The standard is a scale from 1 to 4, where 1 stands for inexpensive, 2 for moderate/average, 3 for expensive and 4 means luxury. Example: $$.', $this->cgi, 31, null),

                ('Street Address (Schema/OG)', 'PLUGIN_SUPERDATA_STREET_ADDRESS', '', 'Enter the business street address.', $this->cgi, 35, null),
                ('City (Schema/OG)', 'PLUGIN_SUPERDATA_LOCALITY', '', 'Enter the business town/city.', $this->cgi, 40, null),
                ('State (Schema/OG)', 'PLUGIN_SUPERDATA_REGION', '', 'Enter the business state/province.', $this->cgi, 45, null),
                ('Postal Code (Schema/OG)', 'PLUGIN_SUPERDATA_POSTALCODE', '', 'Enter the business postal code/zip', $this->cgi, 50, null),
                ('Country (Schema/OG)', 'PLUGIN_SUPERDATA_COUNTRYNAME', '', 'Enter the country <a href=\"https://en.wikipedia.org/wiki/ISO_3166-1\" target=\"_blank\">2 letter ISO code</a>', $this->cgi, 55, null),
                ('Email (Schema, optional)', 'PLUGIN_SUPERDATA_EMAIL', '', 'Enter your Customer Service email address (lower case).', $this->cgi, 60, null),
                ('Telephone (Schema)', 'PLUGIN_SUPERDATA_TELEPHONE', '', 'Enter the Customer Service phone number in international format eg.: +1-330-871-4357. The format (spaces/dashes) is not important.', $this->cgi, 65, null),
                ('Fax (Schema, optional)', 'PLUGIN_SUPERDATA_FAX', '', 'Enter the Customer Service fax number in international format e.g. +1-877-453-1304). The format (spaces/dashes) is not important.', $this->cgi, 70, null),

                ('Available Languages (Schema, optional)', 'PLUGIN_SUPERDATA_AVAILABLE_LANGUAGE', '', 'Languages spoken (for Schema contact point). Enter the language\'s name in English, separated by commas. If omitted, the language defaults to English.', $this->cgi, 75, null),

                ('Locales (OG)', 'PLUGIN_SUPERDATA_FOG_LOCALES', '', 'Enter a comma-separated list of the database language_id and equivalent locale for each defined language e.g.: 1,en_GB,2,es_ES, etc. (no spaces).<br>Separate the parameters with commas.', $this->cgi, 80, null),

                ('Area Served (Schema-Customer Service, optional)', 'PLUGIN_SUPERDATA_AREA_SERVED', '', 'The geographical region served (<a href=\"https://schema.org/areaServed\" target=\"_blank\">further details here</a>).<br>If omitted, the area is assumed to be global.)', $this->cgi, 85, null),

                ('Hours Available (Schema-Customer Service, optional)', 'PLUGIN_SUPERDATA_HOURS', '', 'Customer service working hours (<a href=\"https://schema.org/hoursAvailable\" target=\"_blank\">further details here</a>).<br>If omitted, it will be skipped.)<br>Supports simple and complex scenarios.<br><strong>REQUIREMENTS:</strong> days are listed first, using 3-letter English abbreviation and separated with a comma. Semicolon is used as delimiter between days and times. Times are entered in 24-hour format with a minus indicating range. Split shifts are separated with a comma. Different rules are separated with a pipe.<br>Examples:<br><strong>Simple:</strong> Mon,Tue,Wed,Thu,Fri;09:00-17:00<br><strong>Weekend different:</strong> Mon,Tue,Wed,Thu,Fri;09:00-17:00|Sat;10:00-14:00<br><strong>Split Shift (Lunch break):</strong> Mon,Tue,Wed,Thu,Fri;09:00-12:00,13:00-17:00<br><strong>Complex:</strong> Mon,Wed,Fri;09:00-17:00|Tue,Thu;09:00-12:00,13:00-17:00|Sat;10:00-12:00', $this->cgi, 90, null),

                ('Accepted Payment Methods (Schema)', 'PLUGIN_SUPERDATA_ACCEPTED_PAYMENT_METHODS', 'ByBankTransferInAdvance, ByInvoice, Cash, CheckInAdvance, COD, DirectDebit, GoogleCheckout, PayPal, PaySwarm, AmericanExpress, DinersClub, Discover, JCB, MasterCard, VISA', 'List/delete as applicable the <a href=\"https://www.heppnetz.de/ontologies/goodrelations/v1#PaymentMethod\" target=\"_blank\">accepted payment methods</a>. e.g. ByBankTransferInAdvance, ByInvoice, Cash, CheckInAdvance, COD, DirectDebit, GoogleCheckout, PayPal, PaySwarm, AmericanExpress, DinersClub, Discover, JCB, MasterCard, VISA.', $this->cgi, 95, null),

                ('Tax ID (Schema, optional)', 'PLUGIN_SUPERDATA_TAXID', '', 'The Tax/Fiscal ID of the business (e.g. the TIN in the US or the CIF in Spain).', $this->cgi, 100, null),
                ('VAT Number (Schema, optional)', 'PLUGIN_SUPERDATA_VATID', '', 'Value-added Tax ID of the business.', $this->cgi, 105, null),

                ('Profile/Social Pages (Schema-sameAs, optional)', 'PLUGIN_SUPERDATA_SAMEAS', '', 'Enter a list of URLs to other (NOT Facebook, Twitter or Google Plus) profile or social pages related to your business (e.g. Instagram, TikTok, LinkedIn, Dun & Bradstreet, Yelp etc.).<br>Separate the URLs with commas.', $this->cgi, 110, null),

                ('Product Shipping Area (Schema, optional)', 'PLUGIN_SUPERDATA_ELIGIBLE_REGION', '', 'Area to which you ship products.<br >Use the ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, or the GeoShape for the geo-political region(s).', $this->cgi, 115, null),

                ('Currency (Schema/OG)', 'PLUGIN_SUPERDATA_PRICE_CURRENCY', '', 'Enter the currency code of the product price e.g.: EUR.', $this->cgi, 120, null),

                ('Product Delivery Time when in stock (Schema)', 'PLUGIN_SUPERDATA_DELIVERYLEADTIME', '', 'Enter the average days from order to delivery when product is in stock (e.g.:2).', $this->cgi, 125, null),

                ('Product Delivery Time when out of stock (Schema)', 'PLUGIN_SUPERDATA_DELIVERYLEADTIME_OOS', '', 'Enter the average days from order to delivery when product is out of stock (e.g.:7).', $this->cgi, 130, null),

                ('Offer validFrom', 'PLUGIN_SUPERDATA_VALID_FROM_ENABLE', 'true', 'Add validFrom to every Offer. SuperData uses the future product available date when present, otherwise the product creation date.', $this->cgi, 131, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
                ('Offer shippingDetails', 'PLUGIN_SUPERDATA_SHIPPING_DETAILS_ENABLE', 'true', 'Enable shipping information in product Offer markup. The Shipping rate mode below determines whether SuperData outputs an OfferShippingDetails block or relies on Google Merchant Center.', $this->cgi, 132, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
                ('Shipping rate mode', 'PLUGIN_SUPERDATA_SHIPPING_RATE_MODE', 'MerchantCenter', '<strong>MerchantCenter:</strong> Do not publish a possibly inaccurate product-page rate; use shipping rules configured in Google Merchant Center.<br><strong>Free:</strong> Publish a 0.00 shipping rate. Choose this only when shipping is genuinely free for the destination below.<br><strong>FlatRate:</strong> Publish the exact amount entered in Shipping flat rate for every product covered by this rule.', $this->cgi, 133, 'zen_cfg_select_option(array(\'MerchantCenter\', \'Free\', \'FlatRate\'),'),
                ('Shipping destination country', 'PLUGIN_SUPERDATA_SHIPPING_COUNTRY', 'US', 'Two-letter ISO 3166-1 destination country, for example US, CA, or GB. Used only with Free or FlatRate mode.', $this->cgi, 134, null),
                ('Shipping flat rate', 'PLUGIN_SUPERDATA_SHIPPING_RATE', '', 'Used only when Shipping rate mode is FlatRate. Enter the exact shipping charge applied to every product covered by this rule, for example 5.95. Do not enter an average or estimated amount. Leave blank for MerchantCenter or Free mode.', $this->cgi, 135, null),
                ('Shipping handling time minimum', 'PLUGIN_SUPERDATA_HANDLING_MIN_DAYS', '0', 'Minimum business days before an order ships.', $this->cgi, 135, null),
                ('Shipping handling time maximum', 'PLUGIN_SUPERDATA_HANDLING_MAX_DAYS', '1', 'Maximum business days before an order ships.', $this->cgi, 136, null),
                ('Shipping transit time minimum', 'PLUGIN_SUPERDATA_TRANSIT_MIN_DAYS', '2', 'Minimum business days in transit.', $this->cgi, 137, null),
                ('Shipping transit time maximum', 'PLUGIN_SUPERDATA_TRANSIT_MAX_DAYS', '7', 'Maximum business days in transit.', $this->cgi, 138, null),

                ('Product Condition (Schema/OG)', 'PLUGIN_SUPERDATA_FOG_PRODUCT_CONDITION', 'new', 'Choose your product\'s condition.', $this->cgi, 135, 'zen_cfg_select_option(array(\'new\', \'used\', \'refurbished\'),'),

                ('Default Product Weight', 'PLUGIN_SUPERDATA_DEFAULT_WEIGHT', '0.5', 'If product has no weight defined, use this value.', $this->cgi, 140, null),

                ('Out of Stock Status', 'PLUGIN_SUPERDATA_OOS_DEFAULT', 'BackOrder', 'The default OOS status if a product is out of stock and has no custom field defined for OOS status.', $this->cgi, 145, 'zen_cfg_select_option(array(\'BackOrder\', \'Discontinued\', \'OutOfStock\', \'PreOrder\', \'PreSale\', \'SoldOut\'),'),
                ('Out of Stock - BackOrder/PreOrder Date', 'PLUGIN_SUPERDATA_OOS_AVAILABILITY_DELAY', '10', 'The OOS BackOrder/PreSales conditions require an availability date.<br>Set the number of days to add to today\'s date, to create a new date.', $this->cgi, 150, null),

                ('Limit - Product Name', 'PLUGIN_SUPERDATA_MAX_NAME', '150', 'The maximum number of characters allowed in a product name.<br>Google permits up to 150.', $this->cgi, 170, null),
                ('Limit - Product Description', 'PLUGIN_SUPERDATA_MAX_DESCRIPTION', '5000', 'The maximum number of characters allowed in a product description.<br>Google permits up to 5000.', $this->cgi, 175, null),

                ('Reviews - Default Review Date', 'PLUGIN_SUPERDATA_REVIEW_DEFAULT_DATE', '2020-09-23 13:48:39', 'In the case of a review having no date set (null), use this date.', $this->cgi, 180, null),
                ('No Review - Add One?', 'PLUGIN_SUPERDATA_REVIEW_USE_DEFAULT', 'true', 'If a product has no reviews, use a default value/dummy review to prevent Google Tool warnings.', $this->cgi, 185, null),
                ('No Review - Average Rating', 'PLUGIN_SUPERDATA_REVIEW_DEFAULT_VALUE', '4', 'Average rating for the default review (1-5).', $this->cgi, 190, null),

                ('Returns - Policy', 'PLUGIN_SUPERDATA_RETURNS_POLICY', 'Finite', 'The type of return policy.', $this->cgi, 250, 'zen_cfg_select_option(array(\'Finite\', \'NotPermitted\', \'Unlimited\'),'),
                ('Returns - Days', 'PLUGIN_SUPERDATA_RETURNS_DAYS', '14', 'In the case of the Finite return policy, the period (days limit) during which the product can be returned.', $this->cgi, 255, null),
                ('Returns - Methods', 'PLUGIN_SUPERDATA_RETURNS_METHOD', 'Mail', 'In the case of the Finite/Unlimited return policies, the method of returning the product.', $this->cgi, 260, 'zen_cfg_select_option(array(\'Kiosk\', \'Mail\', \'Store\'),'),
                ('Returns - Type', 'PLUGIN_SUPERDATA_RETURNS_TYPE', 'FreeReturn', 'The type of fee for returns.', $this->cgi, 265, 'zen_cfg_select_option(array(\'FreeReturn\', \'OriginalShippingFees\', \'RestockingFees\', \'ReturnFeesCustomerResponsibility\', \' ReturnShippingFees\'),'),
                ('Returns - Fee', 'PLUGIN_SUPERDATA_RETURNS_FEE', '0', 'The charge to the customer for returning the product. You can enter a fixed amount or percentage. If you add percentage, the value will be calculated as percentage of the item price.', $this->cgi, 270, null),
                ('Returns - Applicable Country', 'PLUGIN_SUPERDATA_RETURNS_APPLICABLE_COUNTRY', '', 'The country in which the returns policy is applicable (2-char ISO e.g. ES) For worldwide, enter **.', $this->cgi, 275, null),
                ('Returns - Returns Country', 'PLUGIN_SUPERDATA_RETURNS_POLICY_COUNTRY', '', 'The country to which the product must be returned (2-char ISO e.g. ES).', $this->cgi, 280, null),

                ('Custom Product Field - Google Product Category', 'PLUGIN_SUPERDATA_GPC_FIELD', 'products_google_product_category', 'The name of the custom field used in the <strong>products</strong> table for the Google Product Category.', $this->cgi, 285, null),
                ('Custom Product Field - GTIN', 'PLUGIN_SUPERDATA_GTIN_FIELD', 'products_gtin', 'The name of the custom field used in the <strong>products</strong> table for the product-specific code GTIN (EAN, ISBN etc.).', $this->cgi, 290, null),

                ('Custom POS Field - GTIN', 'PLUGIN_SUPERDATA_POS_GTIN_FIELD', 'pos_gtin', 'The name of the custom field used in the <strong>products_options_stock</strong> table for the product-specific GTIN code (EAN, ISBN etc.).', $this->cgi, 295, null),
                ('Custom POS Field - MPN', 'PLUGIN_SUPERDATA_POS_MPN_FIELD', 'pos_mpn', 'The name of the custom field used in the <strong>products_options_stock</strong> table for the manufacturers product code.', $this->cgi, 300, null),

                ('Facebook Default Image: Product (optional)', 'PLUGIN_SUPERDATA_FOG_DEFAULT_PRODUCT_IMAGE', '', 'Fallback image used in Facebook when there is no product image. Enter the full URL or leave blank to use the no-image file defined in the Admin->Images configuration.', $this->cgi, 350, null),
                ('Facebook Default Image: non Product (optional)', 'PLUGIN_SUPERDATA_FOG_DEFAULT_IMAGE', '', 'Fallback image used in Facebook when there is no image on any page other than a product page. Enter the full URL or leave blank to use the logo file defined above.', $this->cgi, 355, null),
                ('Facebook Type - Non Product Page', 'PLUGIN_SUPERDATA_FOG_TYPE_SITE', 'business.business', 'Enter an Open Graph type for your site - non-product pages (<a href=\"https://developers.facebook.com/docs/reference/opengraph/\" target=\"_blank\">Open Graph Types</a>)', $this->cgi, 360, null),
                ('Facebook Type - Product Page', 'PLUGIN_SUPERDATA_FOG_TYPE_PRODUCT', 'product', 'Enter an Open Graph type for your site - product pages (<a href=\"https://developers.facebook.com/docs/reference/opengraph/\" target=\"_blank\">Open Graph Types</a>)', $this->cgi, 365, null),

                ('Twitter Default Image (optional)', 'PLUGIN_SUPERDATA_TWITTER_DEFAULT_IMAGE', '', 'Fallback image used in Twitter when there is no image defined. Enter the full URL.', $this->cgi, 370, null),
                ('Twitter Username', 'PLUGIN_SUPERDATA_TWITTER_USERNAME', '', 'Enter your Twitter username (e.g.: @zencart).', $this->cgi, 375, null),
                ('Twitter Page URL', 'PLUGIN_SUPERDATA_TWITTER_PAGE', '', 'Enter the full URL to your Twitter page (e.g.: https://twitter.com/zencart)', $this->cgi, 380, null),
                ('Google - Publisher URL', 'PLUGIN_SUPERDATA_GOOGLE_PUBLISHER', '', 'Enter your Google Publisher URL/link (e.g. https://plus.google.com/+Pro-websNet/).', $this->cgi, 385, null),

                ('Google - Default Product Category', 'PLUGIN_SUPERDATA_GOOGLE_PRODUCT_CATEGORY', '', 'Fallback/default Google product category ID (up to 6 digits).<br>Used when a product does not have a GPC defined as an custom product field (e.g. 5613 = Vehicles & Parts, Vehicle Parts & Accessories).<br><a href=\"https://support.google.com/merchants/answer/6324436?hl=en\">Google Product Taxonomy</a>', $this->cgi, 390, null)
                ");

        // Preserve matching values from the former Structured Data plugin.
        $this->executeInstallerSql(
            "UPDATE " . TABLE_CONFIGURATION . " new_config
             INNER JOIN " . TABLE_CONFIGURATION . " old_config
                ON old_config.configuration_key = REPLACE(new_config.configuration_key, 'PLUGIN_SUPERDATA_', 'PLUGIN_SDATA_')
             SET new_config.configuration_value = old_config.configuration_value
             WHERE new_config.configuration_key LIKE 'PLUGIN_SUPERDATA_%'"
        );

        if (!zen_page_key_exists($this->configPageKey)) {
            // -----
            // Register the plugin's configuration page for the admin menus.
            //
            zen_register_admin_page($this->configPageKey, 'BOX_CONFIGURATION_SUPER_DATA', 'FILENAME_CONFIGURATION', "gID=$this->cgi", 'configuration', 'Y');
        }

        return true;
    }

    /**
     * @param $oldVersion
     * @return bool
     */
    protected function executeUpgrade($oldVersion): bool
    {
        $this->cgi = $this->getOrCreateConfigGroupId($this->configGroupTitle, $this->configGroupTitle, null);
        switch ($oldVersion) {
            case "v3.0.0":
                $this->executeInstallerSql("INSERT IGNORE INTO " . TABLE_CONFIGURATION . "
                    (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function)
                    VALUES
                    ('Shipping rate mode', 'PLUGIN_SUPERDATA_SHIPPING_RATE_MODE', 'MerchantCenter', '<strong>MerchantCenter:</strong> Do not publish a possibly inaccurate product-page rate; use shipping rules configured in Google Merchant Center.<br><strong>Free:</strong> Publish a 0.00 shipping rate only when shipping is genuinely free.<br><strong>FlatRate:</strong> Publish the exact configured flat rate.', $this->cgi, 133, 'zen_cfg_select_option(array(\'MerchantCenter\', \'Free\', \'FlatRate\'),')");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET configuration_title = 'Shipping flat rate', configuration_value = '', configuration_description = 'Used only when Shipping rate mode is FlatRate. Enter the exact shipping charge applied to every covered product. Do not enter an average or estimated amount.', sort_order = 135 WHERE configuration_key = 'PLUGIN_SUPERDATA_SHIPPING_RATE'");
                break;

            case "v2.0.0":
                // Add new values
                $this->executeInstallerSql("INSERT IGNORE INTO " . TABLE_CONFIGURATION . "
                    (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function)
                VALUES

                ('Organisation Type', 'PLUGIN_SUPERDATA_ORGANIZATION_TYPE', 'Organization', 'If you have a physical store and want to specify it, choose LocalBusiness instead of the generic Organization.', $this->cgi, 8, 'zen_cfg_select_option(array(\'Organization\', \'LocalBusiness\', \'OnlineBusiness\'),'),

                ('LocalBusiness Type', 'PLUGIN_SUPERDATA_LOCAL_BUSINESS_TYPE', 'Store', 'This option is used ONLY if Organization Type is set to LocalBusiness. The list is not complete because there is a large number of options. Choose the one that fits best, or choose Store as a generic option.', $this->cgi, 9, 'zen_cfg_select_option(array(\'Store\', \'ShoppingCenter\', \'BikeStore\', \'BookStore\', \'ClothingStore\', \'ComputerStore\', \'ConvenienceStore\', \'DepartmentStore\', \'ElectronicsStore\', \'Florist\', \'FurnitureStore\', \'GardenStore\', \'GroceryStore\', \'HardwareStore\', \'HobbyShop\', \'HomeGoodsStore\', \'JewelryStore\', \'LiquorStore\', \'MensClothingStore\', \'MobilePhoneStore\', \'MovieRentalStore\', \'MusicStore\', \'OfficeEquipmentStore\', \'OutletStore\', \'PawnShop\', \'PetStore\', \'ShoeStore\', \'SportingGoodsStore\', \'TireShop\', \'ToyStore\', \'WholesaleStore\'),'),

                ('Name (Schema)', 'PLUGIN_SUPERDATA_LOCAL_BUSINESS_NAME', '', 'If you chose LocalBusiness, enter the name (this can be different than your Legal Name).', $this->cgi, 20, null),
                ('Short Description (Schema)', 'PLUGIN_SUPERDATA_DESCRIPTION', '', 'Enter a short description of your business.', $this->cgi, 21, null),
                ('Property Image (Schema)', 'PLUGIN_SUPERDATA_PROPERTY_IMAGE', '', 'If you selected LocalBusiness as your Business Type, you must include a photo of your storefront or building entrance to help customers find you. Best practice is to include 3 photos in different dimensions (1x1, 4x3, 16x9). Example: https://example.com/photos/1x1/photo.jpg, https://example.com/photos/4x3/photo.jpg, https://example.com/photos/16x9/photo.jpg', $this->cgi, 25, null),

                ('Price Range (Schema)', 'PLUGIN_SUPERDATA_PRICE_RANGE', '', 'Use currency symbols to indicate your price range. The standard is a scale from 1 to 4, where 1 stands for inexpensive, 2 for moderate/average, 3 for expensive and 4 means luxury. Example: $$.', $this->cgi, 31, null),

                ('Area Served (Schema-Customer Service, optional)', 'PLUGIN_SUPERDATA_AREA_SERVED', '', 'The geographical region served (<a href=\"https://schema.org/areaServed\" target=\"_blank\">further details here</a>).<br>If omitted, the area is assumed to be global.)', $this->cgi, 85, null),

                ('Hours Available (Schema-Customer Service, optional)', 'PLUGIN_SUPERDATA_HOURS', '', 'Customer service working hours (<a href=\"https://schema.org/hoursAvailable\" target=\"_blank\">further details here</a>).<br>If omitted, it will be skipped.)<br>Supports simple and complex scenarios.<br><strong>REQUIREMENTS:</strong> days are listed first, using 3-letter English abbreviation and separated with a comma. Semicolon is used as delimiter between days and times. Times are entered in 24-hour format with a minus indicating range. Split shifts are separated with a comma. Different rules are separated with a pipe.<br>Examples:<br><strong>Simple:</strong> Mon,Tue,Wed,Thu,Fri;09:00-17:00<br><strong>Weekend different:</strong> Mon,Tue,Wed,Thu,Fri;09:00-17:00|Sat;10:00-14:00<br><strong>Split Shift (Lunch break):</strong> Mon,Tue,Wed,Thu,Fri;09:00-12:00,13:00-17:00<br><strong>Complex:</strong> Mon,Wed,Fri;09:00-17:00|Tue,Thu;09:00-12:00,13:00-17:00|Sat;10:00-12:00', $this->cgi, 90, null),

                ('Returns - Type', 'PLUGIN_SUPERDATA_RETURNS_TYPE', 'FreeReturn', 'The type of fee for returns.', $this->cgi, 265, 'zen_cfg_select_option(array(\'FreeReturn\', \'OriginalShippingFees\', \'RestockingFees\', \'ReturnFeesCustomerResponsibility\', \' ReturnShippingFees\'),')
                ");

                // Update Sort order
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 95 WHERE configuration_key = 'PLUGIN_SUPERDATA_ACCEPTED_PAYMENT_METHODS'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 15 WHERE configuration_key = 'PLUGIN_SUPERDATA_LEGAL_NAME'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 16 WHERE configuration_key = 'PLUGIN_SUPERDATA_DUNS'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 30 WHERE configuration_key = 'PLUGIN_SUPERDATA_LOGO'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 35 WHERE configuration_key = 'PLUGIN_SUPERDATA_STREET_ADDRESS'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 40 WHERE configuration_key = 'PLUGIN_SUPERDATA_LOCALITY'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 45 WHERE configuration_key = 'PLUGIN_SUPERDATA_REGION'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 50 WHERE configuration_key = 'PLUGIN_SUPERDATA_POSTALCODE'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 55 WHERE configuration_key = 'PLUGIN_SUPERDATA_COUNTRYNAME'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 60 WHERE configuration_key = 'PLUGIN_SUPERDATA_EMAIL'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 65 WHERE configuration_key = 'PLUGIN_SUPERDATA_TELEPHONE'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 70 WHERE configuration_key = 'PLUGIN_SUPERDATA_FAX'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 75 WHERE configuration_key = 'PLUGIN_SUPERDATA_AVAILABLE_LANGUAGE'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 80 WHERE configuration_key = 'PLUGIN_SUPERDATA_FOG_LOCALES'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 100 WHERE configuration_key = 'PLUGIN_SUPERDATA_TAXID'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 105 WHERE configuration_key = 'PLUGIN_SUPERDATA_VATID'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 110 WHERE configuration_key = 'PLUGIN_SUPERDATA_SAMEAS'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 115 WHERE configuration_key = 'PLUGIN_SUPERDATA_ELIGIBLE_REGION'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 120 WHERE configuration_key = 'PLUGIN_SUPERDATA_PRICE_CURRENCY'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 125 WHERE configuration_key = 'PLUGIN_SUPERDATA_DELIVERYLEADTIME'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 130 WHERE configuration_key = 'PLUGIN_SUPERDATA_DELIVERYLEADTIME_OOS'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 135 WHERE configuration_key = 'PLUGIN_SUPERDATA_FOG_PRODUCT_CONDITION'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 140 WHERE configuration_key = 'PLUGIN_SUPERDATA_DEFAULT_WEIGHT'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 145 WHERE configuration_key = 'PLUGIN_SUPERDATA_OOS_DEFAULT'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 150 WHERE configuration_key = 'PLUGIN_SUPERDATA_OOS_AVAILABILITY_DELAY'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 170 WHERE configuration_key = 'PLUGIN_SUPERDATA_MAX_NAME'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 175 WHERE configuration_key = 'PLUGIN_SUPERDATA_MAX_DESCRIPTION'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 180 WHERE configuration_key = 'PLUGIN_SUPERDATA_REVIEW_DEFAULT_DATE'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 185 WHERE configuration_key = 'PLUGIN_SUPERDATA_REVIEW_USE_DEFAULT'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 190 WHERE configuration_key = 'PLUGIN_SUPERDATA_REVIEW_DEFAULT_VALUE'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 250 WHERE configuration_key = 'PLUGIN_SUPERDATA_RETURNS_POLICY'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 255 WHERE configuration_key = 'PLUGIN_SUPERDATA_RETURNS_DAYS'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 260 WHERE configuration_key = 'PLUGIN_SUPERDATA_RETURNS_METHOD'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 280 WHERE configuration_key = 'PLUGIN_SUPERDATA_RETURNS_POLICY_COUNTRY'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 285 WHERE configuration_key = 'PLUGIN_SUPERDATA_GPC_FIELD'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 290 WHERE configuration_key = 'PLUGIN_SUPERDATA_GTIN_FIELD'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 295 WHERE configuration_key = 'PLUGIN_SUPERDATA_POS_GTIN_FIELD'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 300 WHERE configuration_key = 'PLUGIN_SUPERDATA_POS_MPN_FIELD'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 350 WHERE configuration_key = 'PLUGIN_SUPERDATA_FOG_DEFAULT_PRODUCT_IMAGE'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 355 WHERE configuration_key = 'PLUGIN_SUPERDATA_FOG_DEFAULT_IMAGE'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 360 WHERE configuration_key = 'PLUGIN_SUPERDATA_FOG_TYPE_SITE'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 365 WHERE configuration_key = 'PLUGIN_SUPERDATA_FOG_TYPE_PRODUCT'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 370 WHERE configuration_key = 'PLUGIN_SUPERDATA_TWITTER_DEFAULT_IMAGE'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 375 WHERE configuration_key = 'PLUGIN_SUPERDATA_TWITTER_USERNAME'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 380 WHERE configuration_key = 'PLUGIN_SUPERDATA_TWITTER_PAGE'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 385 WHERE configuration_key = 'PLUGIN_SUPERDATA_GOOGLE_PUBLISHER'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET sort_order = 390 WHERE configuration_key = 'PLUGIN_SUPERDATA_GOOGLE_PRODUCT_CATEGORY'");
                // Update Sort order and description
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET configuration_description = 'The charge to the customer for returning the product. You can enter a fixed amount or percentage. If you add percentage, the value will be calculated as percentage of the item price.', sort_order = 270 WHERE configuration_key = 'PLUGIN_SUPERDATA_RETURNS_FEE'");
                $this->executeInstallerSql("UPDATE " . TABLE_CONFIGURATION . " SET configuration_description = 'The country in which the returns policy is applicable (2-char ISO e.g. ES) For worldwide, enter **.', sort_order = 275 WHERE configuration_key = 'PLUGIN_SUPERDATA_RETURNS_APPLICABLE_COUNTRY'");
                break;
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function executeUninstall(): bool
    {
        zen_deregister_admin_pages($this->configPageKey);
        $this->deleteConfigurationGroup($this->configGroupTitle, true);
        return true;
    }

    /**
     * @return bool
     */
    protected function purgeOldFiles(): bool
    {
        $files_to_remove = [
            DIR_FS_ADMIN . 'includes/extra_datafiles/plugin_structured_data.php',
            DIR_FS_CATALOG . 'includes/templates/' . $this->template_dir . '/jscript/jscript_plugin_structured_data.php',
        ];

        $error = false;
        foreach ($files_to_remove as $key => $next_file) {
            if (file_exists($next_file)) {
                echo $next_file;
                $result = unlink($next_file);
                if (!$result && file_exists($next_file)) {
                    $error = true;
                    $this->errorContainer->addError(
                        0,
                        sprintf(ERROR_UNABLE_TO_DELETE_FILE, $next_file),
                        false,
                        // this str_replace has to do DIR_FS_ADMIN before CATALOG because catalog is contained within admin, so the results are wrong.
                        // also, '[admin_directory]' is used to obfuscate the admin dir name, in case the user copy/pastes output to a public forum for help.
                        sprintf(ERROR_UNABLE_TO_DELETE_FILE, str_replace([DIR_FS_ADMIN, DIR_FS_CATALOG], ['[admin_directory]/', ''], $next_file))
                    );
                }
            }
        }
        return !$error;
    }
}
