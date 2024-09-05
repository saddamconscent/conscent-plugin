<?php
/**
 * conscent_uninstall
 * When user uninstalls the plugin
 * @global type $wpdb
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}
global $wpdb;
$meta_keys = ['conscent_price', 'conscent_duration'];

foreach ($meta_keys as $meta_key) {
    delete_post_meta_by_key($meta_key);
}