<?php
/**
 * Add a record to DB when the WhatsApp button is clicked.
 */
add_action( 'wp_ajax_wct_track_click', 'wct_track_click' );
add_action( 'wp_ajax_nopriv_wct_track_click', 'wct_track_click' );

function wct_track_click(): void {
  check_ajax_referer( 'wct_track_click', 'nonce' );

  global $wpdb;

  $table_name = $wpdb->prefix . WCT_TABLE_NAME;

  $user_ip           = $_SERVER[ 'REMOTE_ADDR' ] ?? '';
  $user_country_code = '';
  $user_country_name = '';
  $user_agent        = substr( $_SERVER[ 'HTTP_USER_AGENT' ] ?? '', 0, 255 );
  $page_lang         = $_POST[ 'lang' ] ?? 'en';
  $page_url          = $_POST[ 'url' ] ?? '';

  // get country code
  if ( $user_ip ) {
    $geo_response = wp_remote_get(
      "https://freeipapi.com/api/json/$user_ip",
      [ 'timeout' => 3 ]
    );
    if ( ! is_wp_error( $geo_response ) ) {
      $body = json_decode( wp_remote_retrieve_body( $geo_response ), true );
      $user_country_code = $body[ 'countryCode' ] ?? '';
      $user_country_name = $body[ 'countryName' ] ?? '';
    }
  }

  $result = $wpdb->insert(
    $table_name,
    [
      'clicked_at'        => current_time( 'mysql' ),
      'user_ip'           => sanitize_text_field( $user_ip ),
      'user_country_code' => sanitize_text_field( $user_country_code ),
      'user_country_name' => sanitize_text_field( $user_country_name ),
      'user_agent'        => sanitize_text_field( $user_agent ),
      'page_lang'         => sanitize_text_field( $page_lang ),
      'page_url'          => esc_url_raw( $page_url ),
    ],
    [ '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
  );

  if ( $result === false ) {
    wp_send_json_error( $wpdb->last_error );
  }

  wp_send_json_success( [
    'inserted' => true,
    'id'       => $wpdb->insert_id,
  ] );
}