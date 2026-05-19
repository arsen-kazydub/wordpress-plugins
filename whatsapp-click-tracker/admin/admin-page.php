<?php
/**
 * Add plugin pages to the admin menu.
 */
add_action( 'admin_menu', function () {
  add_menu_page(
    'WhatsApp Click Tracker',
    'WhatsApp Tracker',
    'manage_options',
    'wct-reports',
    'wct_render_reports_page',
    'dashicons-chart-line',
    80
  );

  add_submenu_page(
    'wct-reports',
    'WhatsApp Click Tracker — Reports',
    'Reports',
    'manage_options',
    'wct-reports',
    'wct_render_reports_page'
  );

  add_submenu_page(
    'wct-reports',
    'WhatsApp Click Tracker — Settings',
    'Settings',
    'manage_options',
    'wct-settings',
    'wct_render_settings_page'
  );
});


/**
 * Admin page - Reports.
 */
function wct_render_reports_page(): void {
  echo '<div class="wrap">';

  echo '<h1>WhatsApp Click Tracker — Reports</h1>';

  echo '<h2>Clicks by IP (Last 7 Days)</h2>';
  echo wct_get_table(
    wct_get_ip_clicks_report(),
    'No clicks in the last 7 days.'
  );

  echo '<h2>Clicks Log (Last 7 Days)</h2>';
  echo wct_get_table(
    wct_get_clicks_log(),
    'No clicks logged in the last 7 days.'
  );

  echo '</div>';
}


/**
 * Admin page - Settings.
 */
function wct_render_settings_page(): void {
  $selector   = get_option( 'wct_selector', WCT_DEFAULT_SELECTOR );
  $recipients = get_option( 'wct_recipients', WCT_DEFAULT_RECIPIENTS );
  ?>

  <div class="wrap">
    <h1>WhatsApp Click Tracker — Settings</h1>

    <form method="post" action="options.php">

      <?php settings_fields( 'wct_settings_group' ); ?>

      <table class="form-table">
        <tr>
          <th scope="row">
            <label for="wct_selector">CSS selector</label>
          </th>
          <td>
            <input type="text" id="wct_selector" name="wct_selector" class="regular-text"
                   placeholder="<?= esc_attr( WCT_DEFAULT_SELECTOR ) ?>"
                   value="<?php echo esc_attr( $selector ); ?>">
            <p class="description">Any valid selector to target your WhatsApp button(s)</p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="wct_recipients">Report recipients</label>
          </th>
          <td>
            <textarea id="wct_recipients" name="wct_recipients" class="regular-text" rows="5"><?php
              echo esc_textarea( $recipients );
            ?></textarea>
            <p class="description">One email per line (no commas)<br>
              Use <b><?= esc_html( WCT_DEFAULT_RECIPIENTS ) ?></b> to include the site admin address</p>
          </td>
        </tr>
      </table>

      <?php submit_button(); ?>

    </form>
  </div>

  <?php
}