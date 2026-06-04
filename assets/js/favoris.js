/**
 * Heart-icon favoris toggle on any element with `.fav-btn`.
 * Expected data attrs: data-type (hebergement|repas|guide|evenement|artisanat),
 *                       data-id (service id),
 *                       data-logged ("1" if user is logged in)
 * Initial state: .is-fav class means the user has already liked it.
 */
(function () {
  function init() {
    document.querySelectorAll('.fav-btn').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (btn.dataset.logged !== '1') { window.location.href = 'login.php'; return; }
        if (btn.dataset.busy === '1') return;
        btn.dataset.busy = '1';

        var fd = new FormData();
        fd.append('type', btn.dataset.type || '');
        fd.append('id',   btn.dataset.id   || '');
        fetch('favoris-toggle.php', { method: 'POST', body: fd })
          .then(function (r) { return r.json(); })
          .then(function (d) {
            if (d && d.success) btn.classList.toggle('is-fav', !!d.liked);
            else if (d && d.error === 'auth') window.location.href = 'login.php';
          })
          .catch(function () {})
          .then(function () { btn.dataset.busy = '0'; });
      });
    });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
