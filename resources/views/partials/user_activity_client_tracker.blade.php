{{-- Sends tab / page visibility to server for User activity & performance (optional; config user_activity.client_tab_events) --}}
<script>
(function() {
  var c = {
    url: @json(route('user_activity.client_event')),
    token: document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null
  };
  if (!c.url || !c.token) { return; }
  var last = null;
  var cool = 4000;
  var lastT = 0;
  function send(kind) {
    var n = Date.now();
    if (n - lastT < cool && kind === last) { return; }
    lastT = n; last = kind;
    var body = 'kind=' + encodeURIComponent(kind) + '&path=' + encodeURIComponent(window.location.pathname || '/');
    fetch(c.url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'X-CSRF-TOKEN': c.token,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: body
    }).catch(function() {});
  }
  document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'hidden') { send('tab_hidden'); }
    if (document.visibilityState === 'visible') { send('tab_visible'); }
  });
  window.addEventListener('pagehide', function() { send('page_hide'); });
})();
</script>
