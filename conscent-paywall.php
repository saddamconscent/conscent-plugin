<?php
/*
 * Plugin Name:       Conscent Paywall
 * Plugin URI:        https://conscent.ai/
 * Description:       Conscent.ai is the world’s fastest growing advanced analytics and revenue optimization solutions for the media and news publishing industry.
 * Version:           2.0
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Author:            ConsCent Developers
 * Author URI:        https://conscent.ai
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Tags:              conscent-paywall, conscent, paywall, subscriptions, micro-payment, revenue, media, content
 * Text Domain:       conscent-paywall
 */



 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define("CONSCENT_CLIENT_ID", get_option('clientId1'));
define("CONSCENT_SDK_URL", get_option('sdkURL'));
define("CONSCENT_API_URL", get_option('CONSCENT_API_URL'));
define("CONSCENT_API_KEY", get_option('CONSCENT_API_KEY'));
define("CONSCENT_API_SECRET", get_option('CONSCENT_API_SECRET'));
define("ANALYTICS_ALL_PAGES_ON", 'Yes');
define("CONTENT_VISIBLE_PERCENT_BEFORE_PAYMENT", 2);
define("CONSCENT_DEFAULT_STORY_DURATION", 30);
define("CONSCENT_DEFAULT_STORY_PRICE", 5.00);
define("CONSCENT_AMP_SDK_URL", get_option('conscent_amp_sdk_url'));
define("CONSCENT_AMP_API_URL", get_option('conscent_amp_api_url'));
register_uninstall_hook(__FILE__, 'conscent_uninstall');

/**
 * conscent_uninstall
 * When user uninstalls the plugin
 * @global type $wpdb
 */

function conscent_uninstall() {
    global $wpdb;

	// Delete the 'conscent_price' meta key for all posts
	delete_post_meta_by_key('conscent_price');

	// Delete the 'conscent_duration' meta key for all posts
	delete_post_meta_by_key('conscent_duration');
}

function conscent_plugin_enqueue_styles() {
    // Check if we are in the front-end as needed
    if (!is_admin()) {
        wp_enqueue_style('style-css', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), '1.0.0');
    }
}
add_action('wp_enqueue_scripts', 'conscent_plugin_enqueue_styles');
// add_action('admin_enqueue_scripts', 'my_plugin_enqueue_styles');

define('CONSCENT_PAYWALL_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once (CONSCENT_PAYWALL_PLUGIN_DIR . 'conscent-function.php');
require_once (CONSCENT_PAYWALL_PLUGIN_DIR . 'conscent-class.php');
require_once (CONSCENT_PAYWALL_PLUGIN_DIR . 'conscent-js.php');
require_once (CONSCENT_PAYWALL_PLUGIN_DIR . 'conscent-amp-js.php');
require_once (CONSCENT_PAYWALL_PLUGIN_DIR . 'conscent-login-function.php');
require_once (CONSCENT_PAYWALL_PLUGIN_DIR . 'admin/conscent-sections.php');
require_once (CONSCENT_PAYWALL_PLUGIN_DIR . 'admin/conscent-category.php');
require_once (CONSCENT_PAYWALL_PLUGIN_DIR . 'admin/conscent-metabox.php');
require_once (CONSCENT_PAYWALL_PLUGIN_DIR . 'admin/conscent-setting.php');