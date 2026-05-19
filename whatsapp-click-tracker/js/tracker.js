/**
 * Track clicks on the configured WhatsApp button.
 */
document.addEventListener( 'click', function( event ) {
  const wct = window.whatsappClickTracker;
  if ( ! wct ) return;

  let link = null;
  try {
    link = event.target.closest( wct.selector );
  } catch ( error ) {
    return;
  }
  if ( ! link ) return;

  fetch( wct.ajax_url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: new URLSearchParams( {
      action: 'wct_track_click',
      nonce: wct.nonce,
      url: window.location.href,
      lang: window.location.pathname.startsWith( '/fr/' ) ? 'fr' : 'en',
    } )
  } ).catch( () => {} );
} );