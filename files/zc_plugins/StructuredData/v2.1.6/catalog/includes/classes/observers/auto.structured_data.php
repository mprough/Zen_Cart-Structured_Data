<?php

declare(strict_types=1);

/**
 * @author: torvista
 * @link: https://github.com/torvista/Zen_Cart-Structured_Data
 * @license https://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version auto.structured_data.php 17 Aug 2026
 */
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

class zcObserverStructuredData extends base
{
    public bool $enabled;

    protected bool $debug;
    protected string $zcPluginDir;

    public function __construct()
    {
        $this->debug = false;
        $this->enabled = (defined('PLUGIN_SDATA_ENABLE') && PLUGIN_SDATA_ENABLE === 'true');
        if ($this->enabled === false) {
            return;
        }

        // This observer is in catalog/includes/classes/observers.
        // Resolve the plugin's catalog directory directly without relying on
        // Plugin Manager repository classes that are unavailable in some
        // supported Zen Cart versions.
        $this->zcPluginDir = dirname(__DIR__, 3) . '/';

        // Observers
        $this->attach(
            $this,
            [
                /* From /includes/templates/{template}/common/html_header.php */
                'NOTIFY_HTML_HEAD_END',
            ]
        );
    }

    /**
     * Issued at the end of the active template's html_header.php just before the </head> tag.
     * Inserts the plugin's JS file.
     * @param $class
     * @param  string  $e
     * @return void
     */
    protected function notify_html_head_end(&$class, string $e): void
    {
        global $breadcrumb, $canonicalLink, $current_page_base, $db, $lng, $product_info, $reviewsArray, $sniffer;
        include $this->getZcPluginDir() . DIR_WS_TEMPLATES . 'default/jscript/structured_data_jscript.php';
    }

    /**
     * Return the plugin's currently-installed zc_plugin directory for the catalog.
     * @return string
     */
    public function getZcPluginDir(): string
    {
        return $this->zcPluginDir;
    }
}
