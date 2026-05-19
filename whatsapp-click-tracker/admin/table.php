<?php
/**
 * Generates an HTML table from structured table data.
 */
function wct_get_table( array $table, string $empty_message, bool $inline_styles = false ): string {
  if ( empty( $table[ 'rows' ] ) ) {
    return '<p>' . esc_html( $empty_message ) . '</p>';
  }

  // inline styles for the email report
  $styles = [
    'table' => [
      'width: 100%',
      'border-collapse: collapse',
      'border-top: 1px solid rgba(0,0,0,0.3)',
      'border-left: 1px solid rgba(0,0,0,0.3)',
      'font-family: Arial,sans-serif',
      'font-size: 13px',
    ],
    'th' => [
      'border-bottom: 1px solid rgba(0, 0, 0, 0.3)',
      'border-right: 1px solid rgba(0, 0, 0, 0.3)',
      'padding: 8px',
      'text-align: left',
      'background: #f8f8f8',
    ],
    'td' => [
      'border-bottom: 1px solid rgba(0, 0, 0, 0.3)',
      'border-right: 1px solid rgba(0, 0, 0, 0.3)',
      'padding: 8px',
    ],
  ];

  // table CSS classes for the admin page
  $attrs = [
    'table' => 'class="wct-table widefat striped"',
    'th' => '',
    'td' => '',
  ];

  // replace CSS classes with inline styles for the email report
  if ( $inline_styles ) {
    $attrs[ 'table' ] = 'style="' . implode( ';', $styles[ 'table' ] ) . '"';
    $attrs[ 'th' ]    = 'style="' . implode( ';', $styles[ 'th' ] ) . '"';
    $attrs[ 'td' ]    = 'style="' . implode( ';', $styles[ 'td' ] ) . '"';
  }

  // table starts
  $output = '<table ' . $attrs[ 'table' ] . '>';

  // thead
  $output .= '<thead>';
  $output .= '<tr>';
  foreach ( $table[ 'columns' ] as $col ) {
    $col_name = is_array( $col ) ? $col[ 'label' ] : $col;
    $output .= '<th ' . $attrs[ 'th' ] . '>' . esc_html( $col_name ) . '</th>';
  }
  $output .= '</tr>';
  $output .= '</thead>';

  // tbody
  $output .= '<tbody>';
  foreach ( $table[ 'rows' ] as $row ) {
    $output .= '<tr>';

    foreach ( $table[ 'columns' ] as $key => $_val ) {
      $value    = $row[ $key ] ?? '';
      $col_cell = $table[ 'columns' ][ $key ] ?? [];
      $type     = $col_cell[ 'type' ] ?? 'text';

      $output .= '<td ' . $attrs[ 'td' ] . '>';
      switch ( $type ) {
        case 'url':
          $output .= '<a href="' . esc_url( $value ) . '" target="_blank" rel="noopener noreferrer">' .
            esc_html( wp_parse_url( $value, PHP_URL_PATH ) ) .
          '</a>';
          break;
        default:
          $output .= esc_html( $value );
      }
      $output .= '</td>';

    }
    $output .= '</tr>';

  }
  $output .= '</tbody>';

  // table ends
  $output .= '</table>';

  return $output;
}