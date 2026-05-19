<?php
/**
 * Plugin Name: WhatsApp Click Tracker
 * Description: Tracks WhatsApp button clicks, stores statistics in the database, and sends weekly email reports.
 * Version: 1.0.0
 * Author: Arsen Kazydub
 * Author URI: https://github.com/arsen-kazydub
 * License: GPL v2 or later
 * Text Domain: whatsapp-click-tracker
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}


require_once plugin_dir_path( __FILE__ ) . 'tracking.php';
require_once plugin_dir_path( __FILE__ ) . 'cron.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/admin-page.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/reports.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/table.php';


const WCT_TABLE_NAME         = 'whatsapp_click_tracker';
const WCT_DEFAULT_SELECTOR   = 'a[href*="whatsapp"]';
const WCT_DEFAULT_RECIPIENTS = 'admin_email';
const WCT_REPORT_DAYS        = 7;


/**
 * Enqueue assets for the WhatsApp Click Tracker.
 */
add_action( 'wp_enqueue_scripts', function(): void {
  // main script
  wp_enqueue_script(
    'whatsapp-click-tracker',
    plugin_dir_url( __FILE__ ) . 'js/tracker.js',
    [],
    '1.0.0',
    true
  );

  // AJAX API
  wp_localize_script(
    'whatsapp-click-tracker',
    'whatsappClickTracker',
    [
      'ajax_url' => admin_url( 'admin-ajax.php' ),
      'nonce'    => wp_create_nonce( 'wct_track_click' ),
      'selector' => get_option( 'wct_selector', WCT_DEFAULT_SELECTOR ),
    ]
  );
} );


/**
 * jQuery DataTables for sorting of report tables.
 * It is loaded only on the plugin page.
 */
add_action( 'admin_enqueue_scripts', function( $hook ): void {
  if ( $hook !== 'toplevel_page_wct-reports' ) return;

  wp_enqueue_style(
    'jquery-datatables',
    'https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css'
  );

  wp_enqueue_script(
    'jquery-datatables',
    'https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js',
    [ 'jquery' ],
    null,
    true
  );

  wp_add_inline_script(
    'jquery-datatables',
    'jQuery(function($){ $(".wct-table").DataTable({ paging:false, info:false, searching:false }); });'
  );
} );


/**
 * Register plugin settings.
 */
add_action( 'admin_init', function(): void {
  register_setting(
    'wct_settings_group',
    'wct_selector',
    [
      'type' => 'string',
      'sanitize_callback' => 'sanitize_text_field',
    ]
  );
  register_setting(
    'wct_settings_group',
    'wct_recipients',
    [
      'type' => 'string',
      'sanitize_callback' => 'sanitize_textarea_field',
    ]
  );
});


/**
 * Activation - create a DB table and enable cron.
 */
register_activation_hook( __FILE__, function(): void {
  require_once ABSPATH . 'wp-admin/includes/upgrade.php';

  // create a DB table
  global $wpdb;

  $table_name = $wpdb->prefix . WCT_TABLE_NAME;
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE `$table_name` (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    clicked_at DATETIME NOT NULL,
    user_ip VARCHAR(45) NOT NULL,
    user_country_code VARCHAR(2) NULL,
    user_country_name VARCHAR(100) NULL,
    user_agent VARCHAR(255) NULL,
    page_lang VARCHAR(2) NOT NULL,
    page_url TEXT NULL,
    PRIMARY KEY (id)
  ) $charset_collate;";

  dbDelta( $sql );

  // add default plugin options to the database if they don't exist
  add_option( 'wct_selector', WCT_DEFAULT_SELECTOR );
  add_option( 'wct_recipients', WCT_DEFAULT_RECIPIENTS );

  // cron
  if ( ! wp_next_scheduled( 'wct_cron_report_event' ) ) {
    $first_run = strtotime( 'next monday midnight' );
    wp_schedule_event( $first_run, 'weekly', 'wct_cron_report_event' );
  }
} );


/**
 * Deactivation - disable cron.
 */
register_deactivation_hook( __FILE__, function(): void {
  wp_clear_scheduled_hook( 'wct_cron_report_event' );
} );


/**
 * Uninstallation - delete the DB table.
 */
register_uninstall_hook( __FILE__, 'wct_uninstall_plugin' );

function wct_uninstall_plugin(): void {
  global $wpdb;

  $table_name = $wpdb->prefix . WCT_TABLE_NAME;

  $wpdb->query( "DROP TABLE IF EXISTS `$table_name`" );

  // remove plugin options
  delete_option( 'wct_selector' );
  delete_option( 'wct_recipients' );
}