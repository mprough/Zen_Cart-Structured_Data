# SuperData for Zen Cart

SuperData generates Google-ready Product, Offer, Organization, WebSite and breadcrumb JSON-LD together with Open Graph and social metadata.

This is the successor to Structured Data for Zen Cart. Version 3 is renamed, reorganized, and designed to keep one consistent Offer structure across simple products, attribute-priced products, aggregate offers, and Products' Options' Stock Manager offers.

## Compatibility

- Zen Cart 2.0.x, 2.1.x, and 2.2.x: encapsulated Plugin Manager package
- Zen Cart 1.5.6 and 1.5.7: traditional template package
- Legacy package: PHP 7.3 through PHP 8.5
- Plugin Manager package: PHP 7.4 through PHP 8.5

Always test on a development store and back up the files and database before installation.

## Google fields fixed in SuperData 3.0.2

Every generated Offer path now supports:

- `validFrom`
- `shippingDetails`
- `shippingDestination.addressCountry`
- `shippingRate` with value and currency
- `deliveryTime.handlingTime`
- `deliveryTime.transitTime`

Organization, OnlineBusiness, OnlineStore, and LocalBusiness markup also includes `image`. SuperData uses the configured Business Image when present and falls back to the configured Logo.

`hasMerchantReturnPolicy` is added to the Organization and to every Offer when Returns - Applicable Country is configured. The optional Return Destination Country is included as `returnPolicyCountry`, but leaving it blank no longer suppresses the entire policy.

`validFrom` uses a future product availability date when one is set. Otherwise, it uses the product creation date. If neither contains a valid date, SuperData safely uses the current date.

Shipping details are configurable through an explicit rate mode:

- `MerchantCenter`: Google Merchant Center shipping rules remain authoritative. SuperData does not publish a possibly false product-page rate.
- `Free`: SuperData publishes a `0.00` shipping rate. Use this only when shipping is genuinely free for the configured destination.
- `FlatRate`: SuperData publishes the exact configured charge for every covered product. Do not enter an average or estimate.

The safe default is `MerchantCenter`. The initial destination is `US`, handling is 0-1 days, and transit is 2-7 days. Review these values immediately after installation.

## Installation: Zen Cart 2.x

1. Copy the contents of `files/zc_plugins` to the store's `zc_plugins` directory.
2. In Admin, open Modules > Plugin Manager.
3. Install SuperData 3.0.2.
4. Open Configuration > SuperData and review every store-specific setting.
5. Validate at least one simple product, one product with attributes, and one out-of-stock product.

The installer copies matching values from an existing Structured Data installation into the renamed `PLUGIN_SUPERDATA_*` settings. The original `PLUGIN_SDATA_*` values are not deleted.

## Installation: Zen Cart 1.5.6 and 1.5.7

1. Copy `files/legacy/includes/templates/YOUR_TEMPLATE/jscript/jscript_super_data.php` to the matching folder for the active template, replacing `YOUR_TEMPLATE` with the real template directory.
2. Copy `files/legacy/YOUR_ADMIN/includes/extra_datafiles/super_data.php` to the matching admin folder, replacing `YOUR_ADMIN` with the store's real admin directory.
3. In Admin, open Tools > Install SQL Patches and run `files/legacy/sql/install.sql`.
4. Open Configuration > SuperData and review every setting, especially the shipping rate and destination.
5. If the former Structured Data plugin is installed, matching `PLUGIN_SDATA_*` values are migrated automatically and retained for rollback.
6. Remove or rename an older `jscript_structured_data.php` file so both plugins do not emit duplicate markup.
7. Validate the output before deploying it to the live store.

The legacy file avoids PHP 8-only functions and syntax. It uses traditional template autoloading because Zen Cart 1.5.x does not provide the modern Plugin Manager package lifecycle.

To uninstall the legacy edition, run `files/legacy/sql/uninstall.sql`, then remove the two copied PHP files.

Existing legacy SuperData installations should copy the updated PHP files and run `files/legacy/sql/upgrade_to_3.0.2.sql`. The upgrade adds missing settings and refreshes their instructions without resetting existing configuration values.

## Required configuration review

At minimum, verify:

- business name, URL, logo, country, and contact details
- currency
- shipping destination, representative rate, handling time, and transit time
- return policy and applicable country
- product condition and out-of-stock behavior
- GTIN, MPN, and Google Product Category field names if those columns exist

SuperData checks optional product columns before using them. A custom GTIN or Google Product Category column is not installed automatically.

## Validation

Use Google's Rich Results Test and Schema Markup Validator. View the page source and confirm that each object under `offers` includes `validFrom` and a complete `shippingDetails` object.

Test product types separately because each has its own pricing and inventory path:

- simple product
- product with attributes at one price
- product with attributes at a price range
- POSM product, if installed
- in-stock and out-of-stock product

## Upgrade and rollback

Version 2 remains available in Git history. SuperData does not erase the former Structured Data configuration keys, so rolling back does not require reconstructing those values. Do not run both output files at the same time.

## License and support

GNU General Public License v2.0. Provided without warranty. Report reproducible bugs through the repository issue tracker. Store-specific configuration, customization, and installation support are separate services.
