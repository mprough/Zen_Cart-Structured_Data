# SuperData legacy upgrade to 3.0.2
# Run with Admin > Tools > Install SQL Patches.
# Existing configuration values are preserved.

SET @superdata_group_id := (
    SELECT configuration_group_id
    FROM configuration_group
    WHERE configuration_group_title = 'SuperData'
    ORDER BY configuration_group_id DESC
    LIMIT 1
);

# Add settings introduced after the original legacy package. INSERT IGNORE does
# not replace values that the administrator has already configured.
INSERT IGNORE INTO configuration
    (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function)
VALUES
    ('Offer validFrom', 'PLUGIN_SUPERDATA_VALID_FROM_ENABLE', 'true', 'Add validFrom to every Offer. SuperData uses the future product available date when present, otherwise the product creation date.', @superdata_group_id, 131, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
    ('Offer shippingDetails', 'PLUGIN_SUPERDATA_SHIPPING_DETAILS_ENABLE', 'true', 'Enable shipping information in product Offer markup. Shipping rate mode determines whether SuperData publishes OfferShippingDetails or relies on Google Merchant Center.', @superdata_group_id, 132, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
    ('Shipping rate mode', 'PLUGIN_SUPERDATA_SHIPPING_RATE_MODE', 'MerchantCenter', '<strong>MerchantCenter:</strong> Use shipping rules configured in Google Merchant Center.<br><strong>Free:</strong> Publish 0.00 only when shipping is genuinely free.<br><strong>FlatRate:</strong> Publish the exact configured charge for every covered product.', @superdata_group_id, 133, 'zen_cfg_select_option(array(\'MerchantCenter\', \'Free\', \'FlatRate\'),'),
    ('Shipping destination country', 'PLUGIN_SUPERDATA_SHIPPING_COUNTRY', 'US', 'Two-letter ISO destination country. Used only with Free or FlatRate mode.', @superdata_group_id, 134, null),
    ('Shipping flat rate', 'PLUGIN_SUPERDATA_SHIPPING_RATE', '', 'Used only with FlatRate mode. Enter the exact charge applied to every covered product. Do not enter an average or estimate.', @superdata_group_id, 135, null),
    ('Shipping handling time minimum', 'PLUGIN_SUPERDATA_HANDLING_MIN_DAYS', '0', 'Minimum business days before an order ships.', @superdata_group_id, 136, null),
    ('Shipping handling time maximum', 'PLUGIN_SUPERDATA_HANDLING_MAX_DAYS', '1', 'Maximum business days before an order ships.', @superdata_group_id, 137, null),
    ('Shipping transit time minimum', 'PLUGIN_SUPERDATA_TRANSIT_MIN_DAYS', '2', 'Minimum business days in transit.', @superdata_group_id, 138, null),
    ('Shipping transit time maximum', 'PLUGIN_SUPERDATA_TRANSIT_MAX_DAYS', '7', 'Maximum business days in transit.', @superdata_group_id, 139, null);

# Refresh labels, help text, and corrected selection values without changing
# the store's selected configuration values.
UPDATE configuration
SET configuration_title = 'Business Image (Schema, optional)',
    configuration_description = 'Image for Organization, OnlineBusiness, OnlineStore, or LocalBusiness markup. Enter one complete image URL or multiple URLs separated by commas. If blank, SuperData uses the configured Logo so the Schema image field is not missing.'
WHERE configuration_key = 'PLUGIN_SUPERDATA_PROPERTY_IMAGE';

UPDATE configuration
SET configuration_description = 'Enable shipping information in product Offer markup. Shipping rate mode determines whether SuperData publishes OfferShippingDetails or relies on Google Merchant Center.'
WHERE configuration_key = 'PLUGIN_SUPERDATA_SHIPPING_DETAILS_ENABLE';

UPDATE configuration
SET configuration_description = 'Two-letter ISO destination country, for example US, CA, or GB. Used only with Free or FlatRate mode.',
    sort_order = 134
WHERE configuration_key = 'PLUGIN_SUPERDATA_SHIPPING_COUNTRY';

UPDATE configuration
SET configuration_title = 'Shipping flat rate',
    configuration_description = 'Used only with FlatRate mode. Enter the exact shipping charge applied to every covered product, for example 5.95. Do not enter an average or estimate.',
    sort_order = 135
WHERE configuration_key = 'PLUGIN_SUPERDATA_SHIPPING_RATE';

UPDATE configuration
SET configuration_description = '<strong>Required to publish hasMerchantReturnPolicy.</strong> Enter the two-letter ISO country code where this policy applies, for example US. Separate multiple countries with commas.'
WHERE configuration_key = 'PLUGIN_SUPERDATA_RETURNS_APPLICABLE_COUNTRY';

UPDATE configuration
SET configuration_title = 'Returns - Return Destination Country',
    configuration_description = 'Optional two-letter ISO country code where returned products must be sent. This no longer controls whether the return policy is published.'
WHERE configuration_key = 'PLUGIN_SUPERDATA_RETURNS_POLICY_COUNTRY';

UPDATE configuration
SET configuration_description = 'The type of fee for returns. Ignored when returns are not permitted.',
    set_function = 'zen_cfg_select_option(array(\'FreeReturn\', \'OriginalShippingFees\', \'RestockingFees\', \'ReturnFeesCustomerResponsibility\', \'ReturnShippingFees\'),'
WHERE configuration_key = 'PLUGIN_SUPERDATA_RETURNS_TYPE';
