/**
 * Tiny lightbox for service-page galleries.
 * Click any image inside .service-gallery-full / .service-gallery-asym /
 * .service-gallery-grid6 to view it full-size in an overlay.
 * Esc, the close button or a click on the backdrop dismisses it.
 */
(function () {
  function init() {
    var galleries = document.querySelectorAll(
      '.service-gallery-full, .service-gallery-asym, .service-gallery-grid6'
    );
    if (!galleries.length) return;

    // Singleton overlay
    var overlay = document.createElement('div');
    overlay.className = 'svc-lightbox';
    overlay.innerHTML =
      '<button type="button" class="svc-lightbox__close" aria-label="Close">&times;</button>' +
      '<img class="svc-lightbox__img" alt="">';
    document.body.appendChild(overlay);

    var imgEl = overlay.querySelector('.svc-lightbox__img');
    var closeBtn = overlay.querySelector('.svc-lightbox__close');

    function open(src, alt) {
      imgEl.src = src;
      imgEl.alt = alt || '';
      overlay.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }
    function close() {
      overlay.classList.remove('is-open');
      imgEl.src = '';
      document.body.style.overflow = '';
    }

    galleries.forEach(function (g) {
      g.querySelectorAll('img').forEach(function (img) {
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', function () {
          open(img.currentSrc || img.src, img.alt);
        });
      });
    });

    closeBtn.addEventListener('click', close);
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) close();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && overlay.classList.contains('is-open')) close();
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
