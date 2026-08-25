#!/usr/bin/env bash
set -euo pipefail

modern='files/zc_plugins/SuperData/v3.0.2/catalog/includes/templates/default/jscript/super_data_jscript.php'
legacy='files/legacy/includes/templates/YOUR_TEMPLATE/jscript/jscript_super_data.php'

for file in "$modern" "$legacy"; do
    grep -Fq "['validFrom']" "$file"
    grep -Fq "['shippingDetails']" "$file"
    grep -Fq "'@type' => 'OfferShippingDetails'" "$file"
    grep -Fq "'@type' => 'ShippingDeliveryTime'" "$file"
    grep -Fq "['Free', 'FlatRate']" "$file"
    grep -Fq "'MerchantCenter'" "$file"
    grep -Fq "\$schema['image']" "$file"
    grep -Fq "PLUGIN_SUPERDATA_LOGO" "$file"
    grep -Fq "'hasMerchantReturnPolicy' =>" "$file"
    grep -Fq "PLUGIN_SUPERDATA_RETURNS_APPLICABLE_COUNTRY" "$file"

    test "$(grep -Fc 'array_merge($offer, $offerEnhancements)' "$file")" -eq 3
done

if grep -Eq 'str_(contains|starts_with|ends_with)|\bfn[[:space:]]*\(|:[[:space:]]*mixed' "$legacy"; then
    echo 'Legacy build contains PHP 8-only syntax or functions.' >&2
    exit 1
fi
