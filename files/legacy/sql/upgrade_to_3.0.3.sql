# SuperData legacy upgrade to 3.0.3
# Run with Admin > Tools > Install SQL Patches.
# Existing configuration values are preserved.

SET @superdata_group_id := (
    SELECT configuration_group_id
    FROM configuration_group
    WHERE configuration_group_title = 'SuperData'
    ORDER BY configuration_group_id DESC
    LIMIT 1
);

INSERT IGNORE INTO configuration
    (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function)
VALUES
    ('Returns - Refund Type', 'PLUGIN_SUPERDATA_RETURNS_REFUND_TYPE', 'FullRefund', 'Refund provided for an accepted return. Choose FullRefund for a full monetary refund, StoreCreditRefund for store credit, or ExchangeRefund when the item is exchanged for the same product. Ignored when returns are not permitted.', @superdata_group_id, 268, 'zen_cfg_select_option(array(\'FullRefund\', \'StoreCreditRefund\', \'ExchangeRefund\'),');

UPDATE configuration
SET configuration_title = 'Organization Type'
WHERE configuration_key = 'PLUGIN_SUPERDATA_ORGANIZATION_TYPE';
