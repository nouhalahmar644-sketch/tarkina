/**
 * Site-wide image lightbox.
 *
 * Any image inside one of the selectors below becomes clickable. Clicking
 * opens a full-screen overlay; the prev / next arrows cycle through every
 * image that shares the same parent gallery (or the whole page for unscoped
 * images).
 *
 * Esc, the close button, or clicking the backdrop closes the overlay.
 * Arrow keys (← →) navigate.
 */
(function () {
  // Selectors that should make their images clickable. Each MATCHING ELEMENT
  // is treated as a gallery — its child <img> tags become the slideshow.
  var GALLERY_SELECTORS = [
    '.service-gallery-full',
    '.service-gallery-asym',
    '.service-gallery-grid6',
    '.region-hero',
    '.region-packs__grid',
    '.gallery-flip-grid',
    '.regions-slider',
    '.gallery-slider',
    '.services-grid',
    '.blog-grid',
    '.popular-grid'
  ];

  function init() {
    var galleries = document.querySelectorAll(GALLERY_SELECTORS.join(','));
    if (!galleries.length) return;

    // Singleton overlay
    var overlay = document.createElement('div');
    overlay.className = 'svc-lightbox';
    overlay.innerHTML =
      '<button type="button" class="svc-lightbox__close" aria-label="Fermer">&times;</button>' +
      '<button type="button" class="svc-lightbox__nav svc-lightbox__nav--prev" aria-label="Précédent">&#8249;</button>' +
      '<img class="svc-lightbox__img" alt="">' +
      '<button type="button" class="svc-lightbox__nav svc-lightbox__nav--next" aria-label="Suivant">&#8250;</button>' +
      '<div class="svc-lightbox__caption"></div>';
    document.body.appendChild(overlay);

    var imgEl   = overlay.querySelector('.svc-lightbox__img');
    var closeBt = overlay.querySelector('.svc-lightbox__close');
    var prevBt  = overlay.querySelector('.svc-lightbox__nav--prev');
    var nextBt  = overlay.querySelector('.svc-lightbox__nav--next');
    var captionEl = overlay.querySelector('.svc-lightbox__caption');

    var currentList = []; // [{src, alt}]
    var currentIdx  = 0;

    function show(idx) {
      if (!currentList.length) return;
      currentIdx = ((idx % currentList.length) + currentList.length) % currentList.length;
      var item = currentList[currentIdx];
      imgEl.src = item.src;
      imgEl.alt = item.alt || '';
      captionEl.textContent = item.alt || '';
      var multi = currentList.length > 1;
      prevBt.style.display = multi ? 'flex' : 'none';
      nextBt.style.display = multi ? 'flex' : 'none';
    }

    function open(list, idx) {
      currentList = list;
      show(idx);
      overlay.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }

    function close() {
      overlay.classList.remove('is-open');
      imgEl.src = '';
      captionEl.textContent = '';
      document.body.style.overflow = '';
    }

    function next() { show(currentIdx + 1); }
    function prev() { show(currentIdx - 1); }

    galleries.forEach(function (g) {
      var imgs = Array.prototype.slice.call(g.querySelectorAll('img'));
      if (!imgs.length) return;
      // Build the gallery list once; index used at click time.
      imgs.forEach(function (img, i) {
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', function (e) {
          // Always preview the image. If it sits inside a card linked to a
          // detail page, the rest of the card (title, body, footer) still
          // navigates — only the image opens the lightbox.
          e.preventDefault();
          e.stopPropagation();
          var list = imgs.map(function (im) {
            return { src: im.currentSrc || im.src, alt: im.alt };
          });
          open(list, i);
        });
      });
    });

    closeBt.addEventListener('click', close);
    prevBt.addEventListener('click', function (e) { e.stopPropagation(); prev(); });
    nextBt.addEventListener('click', function (e) { e.stopPropagation(); next(); });
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) close();
    });
    document.addEventListener('keydown', function (e) {
      if (!overlay.classList.contains('is-open')) return;
      if (e.key === 'Escape')      close();
      else if (e.key === 'ArrowRight') next();
      else if (e.key === 'ArrowLeft')  prev();
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
