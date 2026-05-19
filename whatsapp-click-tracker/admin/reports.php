<?php
/**
 * Retrieves click statistics grouped by user IP (and country data) for the last 7 days.
 */
function wct_get_ip_clicks_report(): array {
  global $wpdb;

  $table = $wpdb->prefix . WCT_TABLE_NAME;

  $query = $wpdb->prepare( "
    SELECT
      user_ip,
      user_country_code,
      user_country_name,
      COUNT(*) AS total_clicks
    FROM `$table`
    WHERE clicked_at >= DATE_SUB( NOW(), INTERVAL %d DAY )
    GROUP BY user_ip, user_country_code, user_country_name
    ORDER BY total_clicks DESC, user_country_code, user_country_name
  ", WCT_REPORT_DAYS );

  $result = $wpdb->get_results( $query );

  if ( empty( $result ) ) {
    return [];
  }

  $array = [
    'columns' => [
      'user_ip'      => 'User IP',
      'country_code' => 'Country Code',
      'country_name' => 'Country Name',
      'total_clicks' => 'Total Clicks',
    ],
    'rows' => [],
  ];

  foreach ( $result as $row ) {
    $array[ 'rows' ][] = [
      'user_ip'      => $row->user_ip,
      'country_code' => $row->user_country_code,
      'country_name' => $row->user_country_name,
      'total_clicks' => $row->total_clicks,
    ];
  }

  return $array;
}


/**
 * Retrieves all click events for the last 7 days.
 */
function wct_get_clicks_log(): array {
  global $wpdb;

  $table = $wpdb->prefix . WCT_TABLE_NAME;

  $query = $wpdb->prepare( "
    SELECT
      clicked_at,
      user_ip,
      user_country_code,
      user_country_name,
      page_lang,
      page_url
    FROM `$table`
    WHERE clicked_at >= DATE_SUB( NOW(), INTERVAL %d DAY )
    ORDER BY clicked_at DESC
  ", WCT_REPORT_DAYS );

  $result = $wpdb->get_results( $query );

  if ( empty( $result ) ) {
    return [];
  }

  $array = [
    'columns' => [
      'clicked_at'   => 'Time',
      'user_ip'      => 'User IP',
      'country_code' => 'Country Code',
      'country_name' => 'Country Name',
      'page_lang'    => 'Page Language',
      'page_url'     => [ 'label' => 'Page URL', 'type' => 'url' ],
    ],
    'rows' => [],
  ];

  foreach ( $result as $row ) {
    $array[ 'rows' ][] = [
      'clicked_at'   => $row->clicked_at,
      'user_ip'      => $row->user_ip,
      'country_code' => $row->user_country_code,
      'country_name' => $row->user_country_name,
      'page_lang'    => $row->page_lang,
      'page_url'     => $row->page_url,
    ];
  }

  return $array;
}