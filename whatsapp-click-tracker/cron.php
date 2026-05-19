<?php
/**
 * Register a custom "weekly" cron interval (not built into WordPress).
 */
add_filter( 'cron_schedules', function( array $schedules ): array {
  $schedules[ 'weekly' ] = [
    'interval' => WCT_REPORT_DAYS * DAY_IN_SECONDS,
    'display'  => 'Once Weekly',
  ];
  return $schedules;
} );


/**
 * Send cron report to email.
 */
add_action( 'wct_cron_report_event', 'wct_send_cron_report' );

function wct_send_cron_report(): void {
  $to = [];

  $recipients_raw = get_option( 'wct_recipients', WCT_DEFAULT_RECIPIENTS );

  foreach ( explode( "\n", $recipients_raw ) as $line ) {
    $line = trim( $line );

    // special token: "admin_email" is replaced with the site admin email
    if ( $line === 'admin_email' ) {
      $to[] = get_option( 'admin_email' );
    }
    elseif ( is_email( $line ) ) {
      $to[] = $line;
    }
  }

  $to = array_unique( $to );

  if ( empty( $to ) ) {
    return;
  }

  $title = 'WhatsApp Clicks Report';

  $message = '<h2>Clicks by IP (Last 7 Days)</h2>';
  $message .= wct_get_table(
    wct_get_ip_clicks_report(),
    'No clicks in the last 7 days.',
    true
  );

  wp_mail( $to, $title, $message, [ 'Content-Type: text/html; charset=UTF-8' ] );
}

// Change email sender name
add_filter( 'wp_mail_from_name', function() {
  return get_bloginfo( 'name' );
} );